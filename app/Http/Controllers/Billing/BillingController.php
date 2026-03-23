<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\Company;
use App\Models\Truck;
use App\Models\InvoiceCounter;

class BillingController extends Controller
{
    public function create()
    {
        $companies = Company::all();
        $trucks = Truck::all();

        $year = Carbon::now()->year;

        $counter = InvoiceCounter::firstOrCreate(
            ['year' => $year],
            ['last_number' => 0]
        );

        $nextNumber = $counter->last_number + 1;

        $nextInvoice = 'NDK-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('billing.billing', compact('companies', 'trucks', 'nextInvoice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'billImage' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'trips' => 'required|array|min:1',
            'trips.*.id' => 'required|exists:trips,id',
            'trips.*.rent' => 'required|numeric|min:0',
            'trips.*.qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $year = now()->year;

            $tripIds = collect($request->trips)->pluck('id');

            if ($tripIds->duplicates()->count()) {
                throw new \Exception('Duplicate trips selected.');
            }

            if (BillingItem::whereIn('tripId', $tripIds)->exists()) {
                throw new \Exception('Trips already billed.');
            }

            $companyIds = DB::table('trips')
                ->whereIn('id', $tripIds)
                ->pluck('companyId')
                ->unique();

            if ($companyIds->count() > 1) {
                throw new \Exception('Trips must belong to same company.');
            }

            $imagePath = $request->file('billImage')->store('billing', 'public');

            $counter = InvoiceCounter::where('year', $year)->lockForUpdate()->first();
            $counter->increment('last_number');

            $invoiceNo = 'NDK-' . $year . '-' . str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);

            $billing = Billing::create([
                'invoiceNo' => $invoiceNo,
                'companyId' => $companyIds->first(),
                'date' => now(),
                'billImage' => $imagePath,
                'grandTotal' => 0
            ]);

            $grandTotal = 0;

            foreach ($request->trips as $trip) {
                $taxable = $trip['qty'] * $trip['rent'];
                $vat = $taxable * 0.05;
                $total = $taxable + $vat;

                BillingItem::create([
                    'billingId' => $billing->id,
                    'tripId' => $trip['id'],
                    'description' => $trip['destination'] ?? '',
                    'vehicleNo' => $trip['vehicleNo'] ?? '',
                    'quantity' => $trip['qty'],
                    'rent' => $trip['rent'],
                    'taxableAmount' => $taxable,
                    'vat' => $vat,
                    'totalAmount' => $total
                ]);

                $grandTotal += $total;
            }

            $billing->update(['grandTotal' => $grandTotal]);

            DB::commit();

            return back()->with('success', 'Invoice Saved Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }
}