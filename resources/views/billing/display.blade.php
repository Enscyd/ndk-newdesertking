@extends('layouts.app')

@section('content')

<div class="space-y-6">

<h2 class="text-lg font-semibold text-center">Billing List</h2>

<!-- FILTER BAR -->

<div class="bg-white p-4 rounded shadow">

<form method="GET" action="{{ route('billing.display') }}">

<div class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">

<div>
<label class="text-sm font-semibold">Invoice No</label>

<input
type="text"
name="invoiceNo"
value="{{ request('invoiceNo') }}"
class="w-full border rounded px-2 py-1">

</div>

<div>
<label class="text-sm font-semibold">Company</label>

<select
name="companyId"
class="w-full border rounded px-2 py-1">

<option value="">All</option>

@foreach($companies as $company)

<option
value="{{ $company->id }}"
{{ request('companyId') == $company->id ? 'selected' : '' }}>

{{ $company->name }}

</option>

@endforeach

</select>

</div>

<div>
<button
class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
Filter
</button>
</div>

<div>
<a
href="{{ route('billing.display') }}"
class="block text-center w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600">
Reset
</a>
</div>

</div>

</form>

</div>

<!-- BILLING GRID -->

<div class="bg-white p-5 rounded shadow">

<div class="overflow-x-auto">

<table class="min-w-full border text-sm">

<thead class="bg-gray-100 sticky top-0 z-10 text-center">

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

@foreach($billings as $bill)

<!-- INVOICE HEADER -->

<tr class="bg-blue-50 font-semibold text-center">

<td colspan="7" class="px-3 py-2">

Invoice :

<a
href="{{ route('billing.edit',$bill->id) }}"
class="text-blue-700 hover:underline font-semibold">

{{ $bill->invoiceNo }}

</a>

  |  

Company :

<span class="text-gray-700">

{{ $bill->company->name ?? '' }}

</span>

  |  

Date :

<span class="text-gray-700">

{{ \Carbon\Carbon::parse($bill->date)->format('Y-m-d') }}

</span>

</td>

<td colspan="3" class="px-3 py-2">

<a
href="{{ route('billing.addTrip',$bill->id) }}"
class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">

* Add Trip

</a>

</td>

</tr>

@php $sr = 1; @endphp

@foreach($bill->items as $item)

<tr class="text-center">

<td class="border px-2 py-1">
{{ $sr++ }}
</td>

<td class="border px-2 py-1">
{{ \Carbon\Carbon::parse($bill->date)->format('Y-m-d') }}
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

<td class="border px-2 py-1 space-x-1">

<a
href="{{ route('billing.edit',$bill->id) }}"
class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">

Edit

</a>

<form
action="{{ route('billing.item.delete',$item->id) }}"
method="POST"
class="inline">

@csrf
@method('DELETE')

<button
onclick="return confirm('Delete this trip from invoice?')"
class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">

Delete

</button>

</form>

</td>

</tr>

@endforeach

<!-- GRAND TOTAL -->

<tr class="bg-gray-50 font-semibold text-center">

<td colspan="8" class="px-3 py-2">
Grand Total
</td>

<td class="px-3 py-2">
{{ number_format($bill->grandTotal,2) }}
</td>

<td></td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

@endsection
