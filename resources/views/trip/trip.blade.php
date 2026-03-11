@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

/* responsive table */
#tripTable{
    table-layout: fixed;
    width: 100%;
    border-collapse: collapse;
}

/* sticky header */
#tripTable thead th{
    position: sticky;
    top: 0;
    background: #f3f4f6;
    z-index: 10;
}

/* header styling */
#tripTable th{
    text-align: left;
    white-space: normal;
    word-break: keep-all;
    overflow-wrap: anywhere;
    line-height: 1.2;
}

/* cell styling */
#tripTable td{
    overflow-wrap: anywhere;
}

/* sticky actions column */
#tripTable th:last-child,
#tripTable td:last-child{
    position: sticky;
    right: 0;
    background: white;
}

/* row hover */
#tripTable tbody tr:hover{
    background: #f9fafb;
}

/* column widths */
#tripTable th:nth-child(1), #tripTable td:nth-child(1){width:190px;}
#tripTable th:nth-child(2), #tripTable td:nth-child(2){width:150px;}
#tripTable th:nth-child(3), #tripTable td:nth-child(3){width:100px;}
#tripTable th:nth-child(4), #tripTable td:nth-child(4){width:100px;}
#tripTable th:nth-child(5), #tripTable td:nth-child(5){width:80px;}
#tripTable th:nth-child(6), #tripTable td:nth-child(6){width:80px;}
#tripTable th:nth-child(7), #tripTable td:nth-child(7){width:110px;}
#tripTable th:nth-child(8), #tripTable td:nth-child(8){width:100px;}
#tripTable th:nth-child(9), #tripTable td:nth-child(9){width:60px;}
#tripTable th:nth-child(10), #tripTable td:nth-child(10){width:120px;}
#tripTable th:nth-child(11), #tripTable td:nth-child(11){width:100px;}
#tripTable th:nth-child(12), #tripTable td:nth-child(12){width:70px;}
#tripTable th:nth-child(13), #tripTable td:nth-child(13){width:120px;}

@media (max-width:1024px){

#tripTable th,
#tripTable td{
    font-size:11px;
    padding:6px;
}

}

</style>


<div class="p-6 grid grid-cols-1 lg:grid-cols-5 gap-6">


<!-- ADD / EDIT TRIP FORM -->
<div class="bg-white p-4 rounded-lg shadow lg:col-span-1">

<h2 class="text-lg font-bold mb-4 text-black">Add / Edit Trip</h2>

<form id="tripForm" enctype="multipart/form-data">

@csrf

<input type="hidden" id="tripId">


<div class="mb-2">
<label>Company</label>
<select name="companyId" class="w-full border px-2 py-1 rounded text-sm" required>
<option value="">-- Select Company --</option>
@foreach($companies as $company)
<option value="{{ $company->id }}">{{ $company->name }}</option>
@endforeach
</select>
</div>


<div class="mb-2">
<label>Destination</label>
<select name="destinationId" class="w-full border px-2 py-1 rounded text-sm" required>
<option value="">-- Select Destination --</option>
@foreach($destinations as $destination)
<option value="{{ $destination->id }}">{{ $destination->name }}</option>
@endforeach
</select>
</div>


<div class="mb-2">
<label>Driver</label>
<select name="employeeId" class="w-full border px-2 py-1 rounded text-sm" required>
<option value="">-- Select Driver --</option>
@foreach($employees as $employee)
<option value="{{ $employee->id }}">{{ $employee->employeeName }}</option>
@endforeach
</select>
</div>


<div class="mb-2">
<label>Truck</label>
<select name="truckId" class="w-full border px-2 py-1 rounded text-sm" required>
<option value="">-- Select Truck --</option>
@foreach($trucks as $truck)
<option value="{{ $truck->id }}">{{ $truck->truckNumber }}</option>
@endforeach
</select>
</div>


<div class="mb-2">
<label>Trip Type</label>
<select id="tripType" name="tripType" class="w-full border px-2 py-1 rounded text-sm">
<option value="Go Trip">Go Trip</option>
<option value="Return Trip">Return Trip</option>
</select>
</div>


<div class="mb-2" id="driverAmountBox">
<label>Driver Amount</label>
<input type="number" name="driverAmount" step="0.01" class="w-full border px-2 py-1 rounded text-sm">
</div>


<div class="mb-2">
<label>Trip Date</label>
<input type="date" name="tripDate" class="w-full border px-2 py-1 rounded text-sm" required>
</div>


<div class="mb-2">
<label>Trip Amount</label>
<input type="number" name="tripAmount" step="0.01" class="w-full border px-2 py-1 rounded text-sm" required>
</div>


<div class="mb-2">
<label>Is Omani</label>
<select id="isOmani" name="isOmani" class="w-full border px-2 py-1 rounded text-sm" required>
<option value="No">No</option>
<option value="Yes">Yes</option>
</select>
</div>


<div class="mb-2 omaniFields hidden">
<label>Omani Name</label>
<input type="text" name="omaniName" class="w-full border px-2 py-1 rounded text-sm">
</div>


<div class="mb-2 omaniFields hidden">
<label>Omani Amount</label>
<input type="number" name="omaniAmount" step="0.01" class="w-full border px-2 py-1 rounded text-sm">
</div>


<div class="mb-3">
<label>Trip Image</label>
<input type="file" id="imageInput" name="image" class="w-full text-sm">
<img id="previewImage" class="mt-2 hidden w-24 rounded">
</div>


<button type="submit" class="w-full bg-indigo-600 text-white py-1 rounded text-sm hover:bg-indigo-700">
Save Trip
</button>

</form>

</div>



<!-- TRIP LIST -->
<div class="bg-white p-4 rounded-lg shadow lg:col-span-4">


<div class="flex justify-between mb-3">

<h2 class="text-lg font-bold text-black">Trip List</h2>

<input type="text" id="search" placeholder="Search..." class="border px-2 py-1 rounded text-sm w-60">

</div>



<!-- FILTERS -->
<div class="flex gap-2 mb-3">

<input type="date" id="dateFilter" class="border px-2 py-1 rounded text-sm">

<input type="month" id="monthFilter" class="border px-2 py-1 rounded text-sm">


<select id="companyFilter" class="border px-2 py-1 rounded text-sm">

<option value="">All Companies</option>

@foreach($companies as $company)

<option value="{{ $company->id }}">{{ $company->name }}</option>

@endforeach

</select>


<button id="filterBtn" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm">Filter</button>

<button id="exportPDF"
class="bg-red-600 text-white px-3 py-1 rounded text-sm">
Export PDF
</button>

</div>



<div class="overflow-x-auto max-h-[70vh] text-xs">

<table class="w-full border text-xs" id="tripTable">

<thead class="bg-gray-100">

<tr>

<th class="border p-2">Company</th>
<th class="border p-2">Destination</th>
<th class="border p-2">Driver</th>
<th class="border p-2">Truck</th>
<th class="border p-2">Trip Type</th>
<th class="border p-2">Driver Amount</th>
<th class="border p-2">Trip Date</th>
<th class="border p-2">Trip<br>Amount</th>
<th class="border p-2">Omani</th>
<th class="border p-2">Omani<br>Name</th>
<th class="border p-2">Omani Amount</th>
<th class="border p-2">Image</th>
<th class="border p-2">Actions</th>

</tr>

</thead>


<tbody id="tripTableBody">

@include('partials.trip_rows')

</tbody>

</table>

</div>


</div>

</div>



<!-- IMAGE POPUP -->

<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center">

<img id="popupImage" class="max-h-[80vh] rounded">

</div>


<script src="{{ asset('js/trip.js') }}"></script>

@endsection