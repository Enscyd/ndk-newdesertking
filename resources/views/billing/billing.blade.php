@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="space-y-6">

{{-- SUCCESS MESSAGE --}}
@if(session('success'))

<div class="bg-green-100 text-green-800 px-4 py-3 rounded shadow">
{{ session('success') }}
</div>
@endif

{{-- ERROR MESSAGE --}}
@if($errors->any())

<div class="bg-red-100 text-red-800 px-4 py-3 rounded shadow">
<ul class="list-disc pl-5">
@foreach($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<!-- FILTER BAR -->

<div class="bg-white p-4 rounded shadow">

<div class="grid grid-cols-6 gap-3 items-end">

<div>
<label class="text-sm font-semibold">Company</label>
<select id="companyId" class="w-full border rounded px-2 py-1">
<option value="">All</option>
@foreach($companies as $company)
<option value="{{ $company->id }}">{{ $company->name }}</option>
@endforeach
</select>
</div>

<div>
<label class="text-sm font-semibold">Vehicle</label>
<select id="vehicleNo" class="w-full border rounded px-2 py-1">
<option value="">All</option>
@foreach($trucks as $truck)
<option value="{{ $truck->id }}">{{ $truck->truckNumber }}</option>
@endforeach
</select>
</div>

<div>
<label class="text-sm font-semibold">Date</label>
<input type="date" id="tripDate" class="w-full border rounded px-2 py-1">
</div>

<div>
<label class="text-sm font-semibold">Month</label>
<input type="month" id="tripMonth" class="w-full border rounded px-2 py-1">
</div>

<div>
<button type="button"
onclick="filterTrips(1)"
class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
Filter
</button>
</div>

<div>
<button type="button"
onclick="resetFilters()"
class="w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600">
Reset
</button>
</div>

</div>

</div>

<!-- TRIPS GRID -->

<div class="bg-white p-5 rounded shadow">

<h2 class="text-lg font-semibold mb-3">Trips</h2>

<div class="overflow-x-auto">

<table class="w-full border text-sm">

<thead class="bg-gray-100">
<tr>
<th class="border px-2 py-1">Select</th>
<th class="border px-2 py-1">Date</th>
<th class="border px-2 py-1">Vehicle</th>
<th class="border px-2 py-1">Company</th>
<th class="border px-2 py-1">Destination</th>
<th class="border px-2 py-1">Amount</th>
</tr>
</thead>

<tbody id="tripGrid"></tbody>

</table>

</div>

<div id="tripPagination"
class="mt-6 flex justify-center items-center gap-2 flex-wrap"></div>

<!-- ITEM FORM -->

<div class="bg-gray-50 p-4 rounded mt-4">

<div class="grid grid-cols-7 gap-3">

<div>
<label class="text-sm font-semibold">Invoice No</label>
<input type="text"
id="invoiceNoDisplay"
value="{{ $nextInvoice }}"
readonly
class="w-full border rounded px-2 py-1 bg-gray-100">
</div>

<div>
<label class="text-sm font-semibold">Destination</label>
<input type="text" id="destInput"
class="w-full border rounded px-2 py-1">
</div>

<div>
<label class="text-sm font-semibold">Quantity</label>
<input type="number" id="qtyInput"
value="1"
min="1"
class="w-full border rounded px-2 py-1">
</div>

<div>
<label class="text-sm font-semibold">Rent</label>
<input type="number" id="rentInput"
min="0"
step="0.01"
class="w-full border rounded px-2 py-1">
</div>

<div>
<label class="text-sm font-semibold">Taxable</label>
<input type="number" id="taxableInput"
readonly
class="w-full border rounded px-2 py-1 bg-gray-100">
</div>

<div>
<label class="text-sm font-semibold">VAT (5%)</label>
<input type="text" id="vatInput"
value="5%"
readonly
class="w-full border rounded px-2 py-1 bg-gray-100">
</div>

<div>
<label class="text-sm font-semibold">Total</label>
<input type="number" id="totalInput"
readonly
class="w-full border rounded px-2 py-1 bg-gray-100">
</div>

</div>

</div>

<button type="button"
onclick="addTrips()"
class="mt-3 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
Add Selected Trips To Bill Book
</button>

</div>

<form id="invoiceForm"
method="POST"
action="{{ route('billing.store') }}"
enctype="multipart/form-data"
onsubmit="return validateInvoice()">

@csrf

<input type="hidden" name="grandTotal" id="grandTotalInput">
<input type="hidden" name="invoiceNo" id="invoiceNo" value="{{ $nextInvoice }}">

<div class="bg-white p-5 rounded shadow">

<h2 class="text-lg font-semibold mb-4">Invoice Trips</h2>

<div class="overflow-x-auto">

<table class="w-full border text-sm">

<thead class="bg-gray-100">

<tr>

<th class="border px-2 py-1">Sr</th>
<th class="border px-2 py-1">Invoice No</th>
<th class="border px-2 py-1">Company</th>
<th class="border px-2 py-1">Date</th>
<th class="border px-2 py-1">Description</th>
<th class="border px-2 py-1">Vehicle</th>
<th class="border px-2 py-1">Qty</th>
<th class="border px-2 py-1">Rent</th>
<th class="border px-2 py-1">Taxable</th>
<th class="border px-2 py-1">VAT</th>
<th class="border px-2 py-1">Total</th>
<th class="border px-2 py-1">Action</th>

</tr>

</thead>

<tbody id="invoiceGrid"></tbody>

<tfoot>

<tr>

<td colspan="10"
class="text-right font-semibold px-2 py-2">

Grand Total

</td>

<td id="grandTotal"
class="font-semibold px-2 py-2">

0

</td>

<td></td>

</tr>

</tfoot>

</table>

</div>

<div class="flex justify-end items-center gap-4 mt-4">

<input type="file"
name="billImage"
accept="image/*"
required
class="border rounded px-3 py-2">

<button
type="submit"
class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
Save To Bill Book
</button>

</div>

</div>

</form>

</div>

<script>

window.invoiceNo = "{{ $nextInvoice }}";

function validateInvoice(){

if(typeof selectedTrips !== "undefined" && selectedTrips.length === 0){

alert("Please add at least one trip to invoice");

return false;

}

return true;

}

</script>

<script src="{{ asset('js/billing.js') }}"></script>

@endsection