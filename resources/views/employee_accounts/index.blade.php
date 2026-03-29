@extends('layouts.app')

@section('content')

<!-- CDN -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="p-6">

<div class="flex flex-col lg:flex-row gap-6">

```
<!-- LEFT: FORM -->
<div class="w-full lg:w-1/3 bg-white p-6 rounded-lg shadow h-fit">

    <h2 class="text-xl font-bold mb-4">Employee Entry</h2>

    <form id="accountForm" class="space-y-4">
        @csrf

        <select id="employeeId" required class="w-full border px-3 py-2 rounded">
            <option value="">Select Employee</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->employeeName }}</option>
            @endforeach
        </select>

        <input type="date" id="date" required class="w-full border px-3 py-2 rounded">

        <select id="type" class="w-full border px-3 py-2 rounded">
            <option value="CREDIT">Credit</option>
            <option value="DEBIT">Debit</option>
        </select>

        <input type="number" step="0.01" id="amount" required
            class="w-full border px-3 py-2 rounded"
            placeholder="Amount">

        <input type="text" id="remarks"
            class="w-full border px-3 py-2 rounded"
            placeholder="Remarks">

        <button type="submit"
            class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
            Save Entry
        </button>
    </form>

</div>


<!-- RIGHT: DATA -->
<div class="w-full lg:w-2/3 bg-white p-6 rounded-lg shadow">

    <!-- FILTERS -->
    <div class="flex flex-wrap gap-3 mb-4">

        <select id="filterEmployee" class="border px-3 py-2 rounded">
            <option value="">All Employees</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->employeeName }}</option>
            @endforeach
        </select>

        <input type="month" id="filterMonth"
            class="border px-3 py-2 rounded">

        <button type="button" id="filterBtn"
            class="bg-indigo-600 text-white px-4 rounded">
            Filter
        </button>

    </div>


    <!-- SUMMARY -->
    <div class="grid grid-cols-3 md:grid-cols-3 gap-4 mb-4">

        <div class="bg-green-100 p-3 rounded text-center">
            <p class="text-sm">Credit</p>
            <p id="creditVal" class="font-bold text-green-700">{{ $credits }}</p>
        </div>

        <div class="bg-red-100 p-3 rounded text-center">
            <p class="text-sm">Debit</p>
            <p id="debitVal" class="font-bold text-red-700">{{ $debits }}</p>
        </div>

        <div class="bg-blue-100 p-3 rounded text-center">
            <p class="text-sm">Net</p>
            <p id="netVal" class="font-bold text-blue-700">{{ $net }}</p>
        </div>

    </div>


    <!-- SEARCH -->
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold">Employee Ledger</h2>

        <input type="text" id="search"
            placeholder="Search..."
            class="border px-3 py-2 rounded">
    </div>


    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table class="w-full border" id="accountTable">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Type</th>
                    <th class="p-2 border">Amount</th>
                    <th class="p-2 border">Balance</th> <!-- ✅ NEW -->
                    <th class="p-2 border">Remarks</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>

            <tbody id="tableBody">

                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500">
                        Apply filters to view data
                    </td>
                </tr>

            </tbody>

        </table>

        <button id="pdfBtn"
    class="bg-green-600 text-white px-4 py-2 rounded">
    Salary Slip (PDF)
</button>
    </div>

</div>



</div>

</div>

@endsection

@push('scripts')

<script src="{{ asset('js/employee-accounts.js') }}"></script>

@endpush
