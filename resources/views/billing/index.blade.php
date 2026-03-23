@extends('layouts.app')

@section('content')

<div class="p-6">

    <h2 class="text-2xl font-bold mb-6">Invoices List</h2>

    <!-- 🔥 AJAX URL (CRITICAL) -->
    <script>
        window.filterUrl = "{{ url('/billing/filter') }}";
    </script>

    <!-- =========================
         FILTER SECTION
    ========================== -->
    <div class="mb-4 flex gap-3 items-end">

        <!-- Invoice -->
        <input type="text" name="invoiceNo"
            placeholder="Invoice No"
            class="border px-3 py-2 rounded w-40">

        <!-- Company -->
        <select name="companyId" class="border px-3 py-2 rounded w-48">
            <option value="">All Companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">
                    {{ $company->name }}
                </option>
            @endforeach
        </select>

        <!-- Status -->
        <select name="status" class="border px-3 py-2 rounded">
            <option value="">All</option>
            <option value="PAID">Paid</option>
            <option value="UNPAID">Unpaid</option>
        </select>

        <!-- Clear -->
        <button id="clearFilter"
            class="bg-gray-500 text-white px-4 py-2 rounded">
            Clear
        </button>

    </div>


    <!-- =========================
         TABLE SECTION
    ========================== -->
    <table class="w-full border text-sm">

        <tbody id="invoiceTableBody">

            <!-- 🔄 INITIAL LOADING -->
            <tr>
                <td colspan="5" class="p-4 text-center">
                    Loading...
                </td>
            </tr>

        </tbody>

    </table>

</div>


<!-- =========================
     IMAGE MODAL (🔥 IMPORTANT)
========================= -->
<div id="imageModal"
     class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">

    <img id="modalImg"
         class="max-h-[80vh] rounded shadow-lg">

</div>


<!-- =========================
     JS FILE
========================= -->
<script src="{{ asset('js/invoice-filter.js') }}"></script>

@endsection