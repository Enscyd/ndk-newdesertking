@extends('layouts.app')

@section('content')

<div class="p-6">

    <h2 class="text-2xl font-bold mb-6">Invoices List</h2>

    <!-- =========================
         🔐 CSRF TOKEN
    ========================== -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- =========================
         🔥 AJAX URLS (UPDATED)
    ========================== -->
    <script>
        window.filterUrl = "{{ route('billing.filter') }}";
        window.updateItemUrl = "{{ route('billing.item.update', ':id') }}"; // ✅ NEW
    </script>

    <!-- =========================
         FILTER SECTION
    ========================== -->
    <div class="flex flex-wrap gap-3 items-center mb-4">

        <!-- Invoice No -->
        <input 
            type="text" 
            name="invoiceNo"
            placeholder="Invoice No"
            class="border p-2 rounded w-40"
        >

        <!-- Company -->
        <select name="companyId" class="border p-2 rounded w-44">
            <option value="">All Companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>

        <!-- Status -->
        <select name="status" class="border p-2 rounded w-36">
            <option value="">All Status</option>
            <option value="PAID">Paid</option>
            <option value="UNPAID">Unpaid</option>
        </select>

        <!-- DATE -->
        <input 
            type="date" 
            name="date"
            value="{{ date('Y-m-d') }}" 
            class="border p-2 rounded"
        >

        <!-- Clear -->
        <button 
            id="clearFilter"
            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Clear
        </button>

    </div>

    <!-- =========================
         TABLE SECTION
    ========================== -->
    <div class="bg-white shadow rounded overflow-hidden">

        <table class="w-full border text-sm">

            <!-- HEADER -->
            <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="p-3 border">Invoice</th>
                    <th class="p-3 border">Company</th>
                    <th class="p-3 border">Date</th>
                    <th class="p-3 border">Image</th>
                    <th class="p-3 border">Action</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody id="invoiceTable">

                <!-- ✅ INITIAL DATA LOAD -->
                @include('billing.partials.table', ['invoices' => $invoices])

            </tbody>

        </table>

    </div>

</div>


<!-- =========================
     IMAGE MODAL
========================= -->
<div id="imageModal"
     class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 cursor-pointer">

    <img id="modalImg"
         class="max-h-[80vh] rounded shadow-lg">

</div>


<!-- =========================
     JS FILE
========================= -->
<script src="{{ asset('js/invoice-filter.js') }}"></script>

@endsection