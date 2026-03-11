@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6 grid grid-cols-1 lg:grid-cols-5 gap-6">


<!-- ADD / EDIT EXPENSE FORM -->

<div class="bg-white p-4 rounded-lg shadow lg:col-span-1">

<h2 class="text-lg font-bold mb-4 text-black">Add / Edit Expense</h2>

<form id="expenseForm" enctype="multipart/form-data">

@csrf

<input type="hidden" id="expenseId">

<!-- DRIVER -->

<div class="mb-2">

<label>Driver</label>

<select name="employeeId" class="w-full border px-2 py-1 rounded text-sm">

<option value="">-- Select Driver --</option>

@foreach($employees as $employee)

<option value="{{ $employee->id }}">

{{ $employee->employeeName }}

</option>

@endforeach

</select>

</div>


<!-- TRUCK -->

<div class="mb-2">

<label>Truck</label>

<select name="truckId" class="w-full border px-2 py-1 rounded text-sm">

<option value="">-- Select Truck --</option>

@foreach($trucks as $truck)

<option value="{{ $truck->id }}">

{{ $truck->truckNumber }}

</option>

@endforeach

</select>

</div>


<div class="mb-2">

<label>Expense Date</label>

<input type="date" name="expenseDate" class="w-full border px-2 py-1 rounded text-sm">

</div>


<div class="mb-2">

<label>Category</label>

<select name="category" class="w-full border px-2 py-1 rounded text-sm">

<option>Fuel</option>
<option>Toll</option>
<option>Maintenance</option>
<option>Parking</option>
<option>Other</option>

</select>

</div>


<div class="mb-2">

<label>Details</label>

<input type="text" name="details" class="w-full border px-2 py-1 rounded text-sm">

</div>


<div class="mb-2">

<label>Amount</label>

<input type="number" name="amount" step="0.01" class="w-full border px-2 py-1 rounded text-sm">

</div>


<div class="mb-3">

<label>Bill Image</label>

<input type="file" id="imageInput" name="image" class="w-full text-sm">

<img id="previewImage" class="mt-2 hidden w-24 rounded">

</div>


<button type="submit"

class="w-full bg-indigo-600 text-white py-1 rounded text-sm hover:bg-indigo-700">

Save Expense

</button>

</form>

</div>



<!-- EXPENSE LIST -->

<div class="bg-white p-4 rounded-lg shadow lg:col-span-4">


<div class="flex justify-between mb-3">

<h2 class="text-lg font-bold text-black">Expense List</h2>

<input type="text" id="search" placeholder="Search..."

class="border px-2 py-1 rounded text-sm w-60">

</div>


<div class="flex gap-2 mb-3">

<input type="date" id="dateFilter" class="border px-2 py-1 rounded text-sm">

<select id="categoryFilter" class="border px-2 py-1 rounded text-sm">

<option value="">All Categories</option>
<option>Fuel</option>
<option>Toll</option>
<option>Maintenance</option>
<option>Parking</option>

</select>

<button id="filterBtn"

class="bg-indigo-600 text-white px-3 py-1 rounded text-sm">

Filter

</button>

</div>


<div class="overflow-x-auto max-h-[70vh] text-xs">

<table class="w-full border text-xs" id="expenseTable">

<thead class="bg-gray-100">

<tr>

<th class="border p-2">Driver</th>
<th class="border p-2">Truck</th>
<th class="border p-2">Date</th>
<th class="border p-2">Category</th>
<th class="border p-2">Details</th>
<th class="border p-2">Amount</th>
<th class="border p-2">Image</th>
<th class="border p-2">Actions</th>

</tr>

</thead>


<tbody id="expenseTableBody">

@include('partials.expense_rows')

</tbody>

</table>

</div>


</div>

</div>


<div id="imageModal"

class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center">

<img id="popupImage" class="max-h-[80vh] rounded">

</div>


<script src="{{ asset('js/expense.js') }}"></script>

@endsection