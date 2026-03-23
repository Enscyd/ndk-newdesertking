<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Trip;

class BillingFilterController extends Controller
{
    public function filterTrips(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'companyId'  => 'nullable|exists:companies,id',
            'vehicleNo'  => 'nullable|exists:trucks,id',
            'tripDate'   => 'nullable|date',
            'tripMonth'  => 'nullable|date_format:Y-m',
        ]);


        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */
        $query = Trip::query()
            ->whereDoesntHave('billingItems') // exclude already billed trips
            ->with([
                'company:id,name',
                'destination:id,name',
                'truck:id,truckNumber'
            ])
            ->select([
                'id',
                'companyId',
                'destinationId',
                'truckId',
                'tripDate',
                'tripAmount'
            ]);


        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        // Company filter
        if (!empty($validated['companyId'])) {
            $query->where('companyId', $validated['companyId']);
        }

        // Vehicle filter
        if (!empty($validated['vehicleNo'])) {
            $query->where('truckId', $validated['vehicleNo']);
        }

        // Exact date filter
        if (!empty($validated['tripDate'])) {
            $query->whereDate('tripDate', $validated['tripDate']);
        }

        // Month filter
        if (!empty($validated['tripMonth'])) {
            try {
                $start = Carbon::createFromFormat('Y-m', $validated['tripMonth'])->startOfMonth();
                $end   = Carbon::createFromFormat('Y-m', $validated['tripMonth'])->endOfMonth();

                $query->whereBetween('tripDate', [$start, $end]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid month format'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $trips = $query
            ->orderByDesc('tripDate')
            ->paginate(10);


        /*
        |--------------------------------------------------------------------------
        | TRANSFORM DATA (API FORMAT)
        |--------------------------------------------------------------------------
        */
        $trips->getCollection()->transform(function ($trip) {

            return [
                'id'           => $trip->id,
                'companyId'    => $trip->companyId, // 🔥 FIX
                'companyName'  => $trip->company->name ?? 'N/A',
                'vehicleNo'    => $trip->truck->truckNumber ?? 'N/A',
                'destination'  => $trip->destination->name ?? 'N/A',
                'tripDate'     => optional($trip->tripDate)->format('Y-m-d'),
                'tripAmount'   => $trip->tripAmount ?? 0,
                'qty'          => 1
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        return response()->json($trips);
    }
}