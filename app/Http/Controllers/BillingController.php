<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\Company;
use App\Models\Truck;
use App\Models\Trip;
use App\Models\InvoiceCounter;

class BillingController extends Controller
{

    /* =========================
       BILLING PAGE
    ========================= */

    public function create()
    {
        $companies = Company::all();
        $trucks    = Truck::all();

        $year = Carbon::now()->year;

        $counter = InvoiceCounter::firstOrCreate(
            ['year' => $year],
            ['last_number' => 0]
        );

        $nextNumber  = $counter->last_number + 1;
        $nextInvoice = 'NDK-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('billing.billing', compact(
            'companies',
            'trucks',
            'nextInvoice'
        ));
    }



    /* =========================
       FILTER TRIPS
       (AUTO HIDE ALREADY BILLED)
    ========================= */


public function filterTrips(Request $request)
{

    // Handle JSON request from fetch()
    $request->merge($request->json()->all());

    $query = Trip::query()

        // Hide trips already billed
        ->whereDoesntHave('billingItems')

        // Load related data efficiently
        ->with([
            'company:id,name',
            'destination:id,name',
            'truck:id,truckNumber'
        ])

        // Select only required fields (important for performance)
        ->select([
            'id',
            'companyId',
            'destinationId',
            'truckId',
            'tripDate',
            'tripAmount'
        ]);


    /* =========================
       FILTERS
    ========================= */

    if ($request->companyId) {
        $query->where('companyId', $request->companyId);
    }

    if ($request->vehicleNo) {
        $query->where('truckId', $request->vehicleNo);
    }

    if ($request->tripDate) {
        $query->whereDate('tripDate', $request->tripDate);
    }

    if ($request->tripMonth) {

        $date = Carbon::parse($request->tripMonth);

        $query->whereMonth('tripDate', $date->month)
              ->whereYear('tripDate', $date->year);
    }


    /* =========================
       PAGINATION
    ========================= */

    $trips = $query
        ->orderByDesc('tripDate')
        ->paginate(10);


    /* =========================
       FORMAT RESPONSE
    ========================= */

    $formatted = $trips->getCollection()->map(function ($trip) {

        return [
            'id' => $trip->id,
            'companyName' => $trip->company->name ?? '',
            'vehicleNo' => $trip->truck->truckNumber ?? '',
            'destination' => $trip->destination->name ?? '',
            'tripDate' => Carbon::parse($trip->tripDate)->format('Y-m-d'),
            'tripAmount' => $trip->tripAmount
        ];
    });

    $trips->setCollection($formatted);

    return response()->json($trips);
}


    /* =========================
       SAVE NEW INVOICE
    ========================= */

    public function store(Request $request)
    {

        $request->validate([
            'billImage'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'trips'      => 'required|array|min:1',
            'grandTotal' => 'required|numeric'
        ]);

        DB::beginTransaction();

        try {

            $year = Carbon::now()->year;

            $tripIds = collect($request->trips)->pluck('id');

            if ($tripIds->duplicates()->count()) {
                throw new \Exception('Duplicate trips selected.');
            }

            // Prevent already billed trips
            $alreadyBilled = BillingItem::whereIn('tripId', $tripIds)->exists();

            if ($alreadyBilled) {
                throw new \Exception('One or more trips are already billed.');
            }

            // Upload image
            $imagePath = $request->file('billImage')
                ->store('billing', 'public');

            // Invoice counter
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

            $invoiceNumber = 'NDK-' . $year . '-' . str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);

            // Detect company
            $firstTripId = $request->trips[0]['id'];

            $companyId = DB::table('trips')
                ->where('id', $firstTripId)
                ->value('companyId');

            if (!$companyId) {
                throw new \Exception('Company not found for selected trip.');
            }

            // Create invoice
            $billing = Billing::create([
                'invoiceNo'  => $invoiceNumber,
                'companyId'  => $companyId,
                'date'       => Carbon::now(),
                'billImage'  => $imagePath,
                'grandTotal' => $request->grandTotal
            ]);

            // Save trips
            foreach ($request->trips as $trip) {

                BillingItem::create([
                    'billingId'     => $billing->id,
                    'tripId'        => $trip['id'],
                    'description'   => $trip['destination'] ?? '',
                    'vehicleNo'     => $trip['vehicleNo'] ?? '',
                    'quantity'      => $trip['qty'] ?? 1,
                    'rent'          => $trip['rent'] ?? 0,
                    'taxableAmount' => $trip['taxable'] ?? 0,
                    'vat'           => $trip['vat'] ?? 0,
                    'totalAmount'   => $trip['total'] ?? 0
                ]);
            }

            DB::commit();

            return redirect()
                ->route('billing.create')
                ->with('success', 'Invoice Saved Successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }



    /* =========================
       ADD TRIPS TO EXISTING INVOICE
    ========================= */

    public function addTrips(Request $request, $id)
    {

        $billing = Billing::findOrFail($id);

        DB::beginTransaction();

        try {

            $tripIds = collect($request->trips)->pluck('id');

            $alreadyBilled = BillingItem::whereIn('tripId', $tripIds)->exists();

            if ($alreadyBilled) {
                throw new \Exception('One or more trips already billed.');
            }

            foreach ($request->trips as $trip) {

                BillingItem::create([
                    'billingId'     => $billing->id,
                    'tripId'        => $trip['id'],
                    'description'   => $trip['destination'] ?? '',
                    'vehicleNo'     => $trip['vehicleNo'] ?? '',
                    'quantity'      => $trip['qty'] ?? 1,
                    'rent'          => $trip['rent'] ?? 0,
                    'taxableAmount' => $trip['taxable'] ?? 0,
                    'vat'           => $trip['vat'] ?? 0,
                    'totalAmount'   => $trip['total'] ?? 0
                ]);
            }

            $total = BillingItem::where('billingId', $billing->id)
                ->sum('totalAmount');

            $billing->update([
                'grandTotal' => $total
            ]);

            DB::commit();

            return back()->with('success', 'Trips added to invoice');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors($e->getMessage());
        }
    }



    /* =========================
       DELETE INVOICE TRIP
    ========================= */

    public function deleteItem($id)
    {

        $item = BillingItem::findOrFail($id);

        $billingId = $item->billingId;

        $item->delete();

        $total = BillingItem::where('billingId', $billingId)
            ->sum('totalAmount');

        Billing::where('id', $billingId)
            ->update([
                'grandTotal' => $total
            ]);

        return back()->with('success', 'Trip removed successfully');
    }



    /* =========================
       UPDATE INVOICE TRIP
    ========================= */

    public function updateItem(Request $request, $id)
    {

        $item = BillingItem::findOrFail($id);

        $item->update([
            'quantity'      => $request->qty,
            'rent'          => $request->rent,
            'taxableAmount' => $request->taxable,
            'vat'           => $request->vat,
            'totalAmount'   => $request->total
        ]);

        $total = BillingItem::where('billingId', $item->billingId)
            ->sum('totalAmount');

        Billing::where('id', $item->billingId)
            ->update([
                'grandTotal' => $total
            ]);

        return back()->with('success', 'Trip updated successfully');
    }

  
   /* =========================
       Display Billing List
    ========================= */

    public function display(Request $request)
{
    // Start query and eager load company and items
    $query = Billing::with(['company', 'items']);

    // Filter by invoice number if provided
    if ($request->invoiceNo) {
        $query->where('invoiceNo', 'like', '%' . $request->invoiceNo . '%');
    }

    // Filter by company if selected
    if ($request->companyId) {
        $query->where('companyId', $request->companyId);
    }

    // Get all matching billings, latest first
    $billings = $query->latest()->get();

    // Get all companies for the dropdown filter
    $companies = Company::all();

    // Return the view with billings, companies, and filters
    return view('billing.display', [
        'billings' => $billings,
        'companies' => $companies,
        'filters' => [
            'invoiceNo' => $request->invoiceNo,
            'companyId' => $request->companyId
        ]
    ]);
}

public function edit($id)
{
    $billing = Billing::with(['company','items'])->findOrFail($id);

    $trips = Trip::with('truck')
        ->where('companyId', $billing->companyId)
        ->orderBy('tripDate','desc')   // correct column
        ->get();

    return view('billing.edit', compact('billing','trips'));
}

public function addTrip($id)
{
    $billing = Billing::with(['company','items'])->findOrFail($id);

    $existingTrips = BillingItem::where('billingId',$id)->pluck('tripId');

    $trips = Trip::where('companyId',$billing->companyId)
        ->whereNotIn('id',$existingTrips)
        ->get();

    return view('billing.addTrip', compact('billing','trips'));
}

}