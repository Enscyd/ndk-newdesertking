<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\InvoiceCounter;

class BillingInvoiceTripController extends Controller
{
    // ✅ SAVE BILLING
    public function BillingStore(Request $request)
    {
        $request->validate([
            // ❌ REMOVED invoiceNo from validation (handled in backend)

            'companyId' => 'required|integer|exists:companies,id',
            'grandTotal' => 'required|numeric|min:0',
            'paymentStatus' => 'required|in:PAID,UNPAID',
            'billImage' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'items' => 'required|array|min:1',

            'items.*.tripId' => 'nullable|integer',
            'items.*.description' => 'required|string|max:255',
            'items.*.vehicleNo' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rent' => 'required|numeric|min:0',
            'items.*.taxableAmount' => 'required|numeric|min:0',
            'items.*.vat' => 'required|numeric|min:0',
            'items.*.totalAmount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 📷 Upload Image
            $imagePath = null;
            if ($request->hasFile('billImage')) {
                $imagePath = $request->file('billImage')->store('billing', 'public');
            }

            // 🔥 SAFE INVOICE NUMBER GENERATION
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

            // 💾 Create Billing
            $billing = Billing::create([
                'invoiceNo'     => $invoiceNo,
                'companyId'     => $request->companyId,
                'grandTotal'    => $request->grandTotal,
                'paymentStatus' => $request->paymentStatus,
                'billImage'     => $imagePath,
                'date'          => now(), // ✅ explicitly set
            ]);

            // 📦 Save Items
            foreach ($request->items as $item) {
                BillingItem::create([
                    'billingId'     => $billing->id,
                    'tripId'        => $item['tripId'] ?? null,
                    'description'   => $item['description'],
                    'vehicleNo'     => $item['vehicleNo'],
                    'quantity'      => $item['quantity'],
                    'rent'          => $item['rent'],
                    'taxableAmount' => $item['taxableAmount'],
                    'vat'           => $item['vat'],
                    'totalAmount'   => $item['totalAmount'],
                ]);
            }

            DB::commit();

            // 🔁 Redirect to regenerate new invoice
            return redirect()->route('billing.create')
                ->with('success', "Billing saved successfully ✅ Invoice: {$invoiceNo}");

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }


    // ✅ DELETE ITEM (UNCHANGED + SAFE)
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