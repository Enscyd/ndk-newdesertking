<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\InvoiceCounter;

class BillingInvoiceTripController extends Controller
{
    // ===============================
    // ✅ SAVE BILLING
    // ===============================
    public function BillingStore(Request $request)
    {
        $request->validate([
            'companyId'     => 'required|integer|exists:companies,id',
            'grandTotal'    => 'required|numeric|min:0',
            'paymentStatus' => 'required|in:PAID,UNPAID',
            'billImage'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'items' => 'required|array|min:1',

            'items.*.tripId'        => 'nullable|integer',
            'items.*.description'   => 'required|string|max:255',
            'items.*.vehicleNo'     => 'required|string|max:50',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.rent'          => 'required|numeric|min:0',
            'items.*.taxableAmount' => 'required|numeric|min:0',
            'items.*.vat'           => 'required|numeric|min:0',
            'items.*.totalAmount'   => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        $imagePath = null;

        try {
            // ===============================
            // 📷 UPLOAD IMAGE
            // ===============================
            if ($request->hasFile('billImage')) {
                $imagePath = $request->file('billImage')->store('billing', 'public');
            }

            // ===============================
            // 🔢 GENERATE INVOICE NUMBER (SAFE LOCK)
            // ===============================
            $year = now()->year;

            $counter = InvoiceCounter::where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = InvoiceCounter::create([
                    'year' => $year,
                    'last_number' => 0
                ]);
            }

            $counter->increment('last_number');

            $invoiceNo = 'NDK-' . $year . '-' . str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);

            // ===============================
            // 💰 (OPTIONAL SAFE TOTAL CALCULATION)
            // ===============================
            $calculatedTotal = collect($request->items)->sum('totalAmount');

            // ===============================
            // 💾 CREATE BILLING
            // ===============================
            $billing = Billing::create([
                'invoiceNo'     => $invoiceNo,
                'companyId'     => $request->companyId,
                'grandTotal'    => $calculatedTotal, // 🔥 safer than trusting frontend
                'paymentStatus' => $request->paymentStatus,
                'billImage'     => $imagePath,
                'date'          => now(),
            ]);

            // ===============================
            // 📦 SAVE ITEMS (BULK INSERT)
            // ===============================
            $items = collect($request->items)->map(function ($item) use ($billing) {
                return [
                    'billingId'     => $billing->id,
                    'tripId'        => $item['tripId'] ?? null,
                    'description'   => $item['description'],
                    'vehicleNo'     => $item['vehicleNo'],
                    'quantity'      => $item['quantity'],
                    'rent'          => $item['rent'],
                    'taxableAmount' => $item['taxableAmount'],
                    'vat'           => $item['vat'],
                    'totalAmount'   => $item['totalAmount'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            })->toArray();

            BillingItem::insert($items);

            DB::commit();

            // ===============================
            // 🔁 REDIRECT
            // ===============================
            return redirect()->route('billing.create')
                ->with('success', "Billing saved successfully ✅ Invoice: {$invoiceNo}");

        } catch (\Throwable $e) {

            DB::rollBack();

            // ===============================
            // 🗑 DELETE UPLOADED IMAGE (SAFE CLEANUP)
            // ===============================
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }


    // ===============================
    // ✅ DELETE ITEM + UPDATE TOTAL
    // ===============================
    public function BillingDelete($id)
    {
        DB::beginTransaction();

        try {
            $item = BillingItem::findOrFail($id);
            $billingId = $item->billingId;

            // 🗑 Delete item
            $item->delete();

            // 🔄 Recalculate total
            $total = BillingItem::where('billingId', $billingId)
                ->sum('totalAmount');

            // 💾 Update billing
            Billing::where('id', $billingId)
                ->update([
                    'grandTotal' => $total
                ]);

            DB::commit();

            return back()->with('success', 'Trip removed successfully');

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}