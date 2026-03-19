@extends('layouts.app')

@section('content')

<div class="space-y-6">

<h2 class="text-lg font-semibold text-center">
Edit Invoice
</h2>

<!-- INVOICE INFO -->

<div class="bg-white p-4 rounded shadow text-center">

<div class="text-sm">

<strong>Invoice:</strong> {{ $billing->invoiceNo }}

  |  

<strong>Company:</strong> {{ $billing->company->name ?? '' }}

  |  

<strong>Date:</strong> {{ \Carbon\Carbon::parse($billing->date)->format('Y-m-d') }}

</div>

</div>

<!-- EXISTING INVOICE TRIPS -->

<div class="bg-white p-5 rounded shadow">

<h3 class="font-semibold mb-3">Invoice Trips</h3>

<div class="overflow-x-auto">

<table class="min-w-full border text-sm">

<thead class="bg-gray-100 text-center">

<tr>

<th class="border px-2 py-2">#</th>
<th class="border px-2 py-2">Date</th>
<th class="border px-2 py-2">Description</th>
<th class="border px-2 py-2">Vehicle</th>
<th class="border px-2 py-2">Qty</th>
<th class="border px-2 py-2">Rent</th>
<th class="border px-2 py-2">Taxable</th>
<th class="border px-2 py-2">VAT</th>
<th class="border px-2 py-2">Amount</th>
<th class="border px-2 py-2">Action</th>

</tr>

</thead>

<tbody>

@php $sr = 1; @endphp

@foreach($billing->items as $item)

<tr class="text-center">

<td class="border px-2 py-1">{{ $sr++ }}</td>

<td class="border px-2 py-1">
{{ \Carbon\Carbon::parse($billing->date)->format('Y-m-d') }}
</td>

<td class="border px-2 py-1">
{{ $item->description }}
</td>

<td class="border px-2 py-1">
{{ $item->vehicleNo }}
</td>

<td class="border px-2 py-1">
{{ $item->quantity }}
</td>

<td class="border px-2 py-1">
{{ number_format($item->rent,2) }}
</td>

<td class="border px-2 py-1">
{{ number_format($item->taxableAmount,2) }}
</td>

<td class="border px-2 py-1">
{{ $item->vat }}
</td>

<td class="border px-2 py-1">
{{ number_format($item->totalAmount,2) }}
</td>

<td class="border px-2 py-1">

<form
method="POST"
action="{{ route('billing.item.delete',$item->id) }}"
onsubmit="return confirm('Delete this trip from invoice?')">

@csrf
@method('DELETE')

<button
class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">

Delete

</button>

</form>

</td>

</tr>

@endforeach

<tr class="bg-gray-50 font-semibold text-center">

<td colspan="8" class="px-2 py-2">

Grand Total

</td>

<td class="px-2 py-2">

{{ number_format($billing->grandTotal,2) }}

</td>

<td></td>

</tr>

</tbody>

</table>

</div>

</div>

<!-- ADD NEW TRIPS -->

<form method="POST" action="{{ route('billing.addTrips',$billing->id) }}">

@csrf

<div class="bg-white p-5 rounded shadow">

<h3 class="font-semibold mb-3">Add Trips To Invoice</h3>

<div class="overflow-x-auto">

<table class="min-w-full border text-sm">

<thead class="bg-gray-100 text-center">

<tr>

<th class="border px-2 py-2">Select</th>
<th class="border px-2 py-2">Date</th>
<th class="border px-2 py-2">Vehicle</th>
<th class="border px-2 py-2">Destination</th>
<th class="border px-2 py-2">Rent</th>

</tr>

</thead>

<tbody>

@foreach($trips as $i => $trip)

<tr class="text-center">

<td class="border px-2 py-1">

<input type="checkbox" name="trips[{{ $i }}][id]" value="{{ $trip->id }}">

<input type="hidden" name="trips[{{ $i }}][destination]" value="{{ $trip->destination }}">

<input type="hidden" name="trips[{{ $i }}][vehicleNo]" value="{{ $trip->truck->truckNumber }}">

<input type="hidden" name="trips[{{ $i }}][qty]" value="1">

<input type="hidden" name="trips[{{ $i }}][rent]" value="{{ $trip->amount }}">

<input type="hidden" name="trips[{{ $i }}][taxable]" value="{{ $trip->amount }}">

<input type="hidden" name="trips[{{ $i }}][vat]" value="{{ $trip->amount * 0.05 }}">

<input type="hidden" name="trips[{{ $i }}][total]" value="{{ $trip->amount * 1.05 }}">

</td>

<td class="border px-2 py-1">

{{ \Carbon\Carbon::parse($trip->date)->format('Y-m-d') }}

</td>

<td class="border px-2 py-1">

{{ $trip->truck->truckNumber }}

</td>

<td class="border px-2 py-1">

{{ $trip->destination }}

</td>

<td class="border px-2 py-1">

{{ number_format($trip->amount,2) }}

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

<button
type="submit"
class="mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">

Add Selected Trips

</button>

</div>

</form>

</div>

@endsection
