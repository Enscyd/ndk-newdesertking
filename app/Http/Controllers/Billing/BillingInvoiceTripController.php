<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\InvoiceCounter;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BillingInvoiceTripController extends Controller
{
    public function BillingStore(Request $request)
    {
        if ($request->filled('invoice_id')) {
            return $this->addTripToExistingInvoice($request);
        }

        $request->validate([
            'companyId'             => 'required|integer|exists:companies,id',
            'grandTotal'            => 'required|numeric|min:0',
            'paymentStatus'         => 'required|in:PAID,UNPAID',
            'billImage'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items'                 => 'required|array|min:1',
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
            if ($request->hasFile('billImage')) {
                $file = $request->file('billImage');
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $destination = public_path('storage/billing');

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $fileName);
                $imagePath = 'storage/billing/' . $fileName;
            }

            $year = now()->year;

            $counter = InvoiceCounter::where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = InvoiceCounter::create([
                    'year'        => $year,
                    'last_number' => 0,
                ]);
            }

            $counter->increment('last_number');

            $invoiceNo = 'NDK-' . $year . '-' . str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);
            $calculatedTotal = collect($request->items)->sum('totalAmount');

            // ✅ Resolve trip dates so the invoice uses the real trip date (not now())
            $tripIds = collect($request->items)
                ->pluck('tripId')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $tripsById = $tripIds->isNotEmpty()
                ? Trip::whereIn('id', $tripIds)->pluck('tripDate', 'id')
                : collect();

            $billingDate = $tripsById->filter()->isNotEmpty()
                ? Carbon::parse($tripsById->filter()->max())
                : now();

            $billing = Billing::create([
                'invoiceNo'     => $invoiceNo,
                'companyId'     => $request->companyId,
                'grandTotal'    => $calculatedTotal,
                'paymentStatus' => $request->paymentStatus,
                'billImage'     => $imagePath,
                'date'          => $billingDate,
            ]);

            $items = collect($request->items)->map(function ($item) use ($billing, $tripsById) {
                $tripId = isset($item['tripId']) ? (int) $item['tripId'] : null;

                $tripDate = ($tripId && $tripsById->get($tripId))
                    ? Carbon::parse($tripsById->get($tripId))
                    : $billing->date;

                return [
                    'billingId'     => $billing->id,
                    'tripId'        => $tripId,
                    'tripDate'      => $tripDate,
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

            return redirect()
                ->route('billing.create')
                ->with('success', "Billing saved successfully ✅ Invoice: {$invoiceNo}");
        } catch (\Throwable $e) {
            DB::rollBack();

            if (!empty($imagePath)) {
                $fullPath = public_path($imagePath);

                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function addTripToExistingInvoice(Request $request)
    {
        DB::beginTransaction();

        try {
            $billing = Billing::findOrFail($request->invoice_id);

            $lastManualTripId = BillingItem::query()
                ->where('tripId', '<', 0)
                ->min('tripId');

            $nextTripId = $lastManualTripId ? ($lastManualTripId - 1) : -1;

            $tripDate = $request->filled('tripDate')
                ? Carbon::parse($request->tripDate)
                : $billing->date;

            BillingItem::create([
                'billingId'     => $billing->id,
                'tripId'        => $nextTripId,
                'tripDate'      => $tripDate,
                'description'   => $request->description,
                'vehicleNo'     => $request->vehicleNo,
                'quantity'      => $request->quantity,
                'rent'          => $request->rent,
                'taxableAmount' => $request->taxableAmount,
                'vat'           => $request->vat,
                'totalAmount'   => $request->totalAmount,
            ]);

            $billing->grandTotal = BillingItem::where('billingId', $billing->id)
                ->sum('totalAmount');

            $billing->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Trip added successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function BillingDelete($id)
    {
        DB::beginTransaction();

        try {
            $item = BillingItem::findOrFail($id);
            $billingId = $item->billingId;

            $item->delete();

            $total = BillingItem::where('billingId', $billingId)
                ->sum('totalAmount');

            Billing::where('id', $billingId)->update([
                'grandTotal' => $total,
            ]);

            DB::commit();

            return back()->with('success', 'Trip removed successfully');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
