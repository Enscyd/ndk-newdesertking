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
<label>Date</label>
<input type="date" id="tripDate" class="w-full border rounded px-2 py-1">
</div>

<div>
<label>Month</label>
<input type="month" id="tripMonth" class="w-full border rounded px-2 py-1">
</div>

<div>
<button id="filterBtn" class="w-full bg-blue-600 text-white py-2 rounded">
Filter
</button>
</div>

<div>
<button id="resetBtn" class="w-full bg-gray-500 text-white py-2 rounded">
Reset
</button>
</div>

</div>
</div>

<!-- TRIPS GRID -->
<div class="bg-white p-5 rounded shadow mt-4">

<h2 class="text-lg font-semibold mb-3">Trips</h2>

<table class="w-full border text-sm">
<thead class="bg-gray-100">
<tr>
<th>Select</th>
<th>Date</th>
<th>Vehicle</th>
<th>Company</th>
<th>Destination</th>
<th>Amount</th>
</tr>
</thead>

<tbody id="tripGrid"></tbody>
</table>

<div id="tripPagination" class="mt-4 flex justify-center"></div>



<!-- ITEM FORM -->
<div class="bg-gray-50 p-4 rounded mt-4">

<div class="grid grid-cols-7 gap-3">

<div>
<label>Invoice No</label>
<input type="text"
id="invoiceNoDisplay"
value="{{ $nextInvoice }}"
readonly
class="w-full border px-2 py-1 bg-gray-100">
</div>

<div>
<label>Destination</label>
<input type="text" id="destInput"
class="w-full border px-2 py-1">
</div>

<div>
<label>Quantity</label>
<input type="number" id="qtyInput" value="1"
class="w-full border px-2 py-1">
</div>

<div>
<label>Rent</label>
<input type="number" id="rentInput"
class="w-full border px-2 py-1">
</div>

<div>
<label>Taxable</label>
<input type="number" id="taxableInput"
readonly
class="w-full border px-2 py-1 bg-gray-100">
</div>

<div>
<label>VAT</label>
<input type="text" id="vatInput"
value="5%" readonly
class="w-full border px-2 py-1 bg-gray-100">
</div>

<div>
<label>Total</label>
<input type="number" id="totalInput"
readonly
class="w-full border px-2 py-1 bg-gray-100">
</div>

</div>
</div>

<button id="addTripsBtn"
class="mt-3 bg-green-600 text-white px-4 py-2 rounded">
Add Selected Trips
</button>

</div>