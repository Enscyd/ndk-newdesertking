@extends('layouts.app')

@section('content')

<style>
    body { background: #f5f6f8; }

    .card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .input {
        padding: 8px 10px;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        font-size: 14px;
    }

    .btn-primary {
        width: 100%;
        padding: 12px;
        background: #4f46e5;
        color: white;
        border-radius: 8px;
        border: none;
    }

    .btn-add { background:#2563eb; color:white; padding:6px 10px; border-radius:6px; }
    .btn-edit { background:#2563eb; color:white; padding:4px 8px; border-radius:6px; }
    .btn-delete { background:#dc2626; color:white; padding:4px 8px; border-radius:6px; }

    table { width:100%; border-collapse: collapse; }
    th, td { padding:10px; border:1px solid #eee; text-align:left; }

    tr:hover { background:#f1f5f9; }

    .filters {
        display:flex;
        gap:10px;
        margin-bottom:15px;
        align-items:center;
        flex-wrap:wrap;
    }

    nav svg {
        max-height: 1.25rem;
        display: inline-block;
    }

    .th-sort {
        user-select: none;
        white-space: nowrap;
    }

    .th-sort a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        color: #374151;
        text-decoration: none;
        font-weight: 600;
    }

    .th-sort a:hover {
        color: #2563eb;
    }

</style>

@php
    $currentSort = $sort ?? 'id';
    $currentDir = $direction ?? 'desc';

    $sortUrl = function($col) use ($currentSort, $currentDir) {
        $nextDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery([
            'sort' => $col,
            'direction' => $nextDir,
            'page' => 1
        ]);
    };

    $sortIcon = function($col) use ($currentSort, $currentDir) {
        if ($currentSort !== $col) {
            return '<span style="color:#9ca3af; font-size:11px; margin-left:4px;">⇅</span>';
        }
        return $currentDir === 'asc'
            ? '<span style="color:#2563eb; font-weight:bold; font-size:11px; margin-left:4px;">▲</span>'
            : '<span style="color:#2563eb; font-weight:bold; font-size:11px; margin-left:4px;">▼</span>';
    };
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="p-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

    <!-- LEFT FORM -->
    <div class="card">

        @if(session('success'))
            <div class="text-green-600 mb-2">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="text-red-600 mb-2">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('workshop.store') }}">
            @csrf

            <input type="text" name="bill_no" value="{{ $nextBillNo }}"class="input w-full mb-2" readonly>
            <input type="text" name="vehicle_no" placeholder="Vehicle No" class="input w-full mb-2" required>
            <input type="text" name="name" placeholder="Name" class="input w-full mb-2">

            <div style="position:relative;">
                <input type="text" id="description" placeholder="Item Description" class="input w-full mb-2" autocomplete="off">
                <div id="suggestionsBox"
                    style="position:absolute; top:100%; left:0; right:0; background:white; border:1px solid #ddd; border-radius:6px; display:none; max-height:150px; overflow-y:auto; z-index:999;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 8px;" class="mb-2">
                <input type="number" id="qty" placeholder="Qty" value="1" step="any" min="0.01" class="input w-full">
                <input type="number" id="rate" placeholder="Rate ({{ \App\Helpers\CurrencyHelper::symbol() }})" step="any" min="0" class="input w-full">
            </div>

            <input type="number" id="price" placeholder="Amount (Qty × Rate)" step="any" min="0" class="input w-full mb-2">

            <button type="button" onclick="addItem()" class="btn-add mb-3">+ Add Item</button>

            <table style="font-size:12px; margin-bottom:10px;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th>Desc</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amt</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsTable"></tbody>
            </table>

            <input type="hidden" name="items" id="itemsInput">

            <input type="number" id="total_amount" name="total_amount" class="input w-full mt-2" readonly>

            <input type="date" name="date" class="input w-full mt-2" required>

            <select name="payment_status" class="input w-full mt-2">
                <option value="">Status</option>
                <option value="PAID">Paid</option>
                <option value="UNPAID">Unpaid</option>
            </select>

            <button class="btn-primary mt-3">Save</button>

        </form>

    </div>

    <!-- RIGHT TABLE -->
    <div class="card lg:col-span-3">

        <!-- FILTERS -->
        <form method="GET" action="{{ route('workshop.create') }}" class="filters">

            <input type="hidden" name="sort" value="{{ $currentSort }}">
            <input type="hidden" name="direction" value="{{ $currentDir }}">

            <input type="text" name="vehicle_no"
                value="{{ request('vehicle_no') }}"
                placeholder="Vehicle No"
                class="input">

            <input type="text" name="bill_no"
                value="{{ request('bill_no') }}"
                placeholder="Bill No"
                class="input">

            <input type="text" name="name"
                value="{{ request('name') }}"
                placeholder="Name"
                class="input">

            <select name="payment_status" class="input">
                <option value="">All Status</option>
                <option value="PAID" {{ request('payment_status') == 'PAID' ? 'selected' : '' }}>Paid</option>
                <option value="UNPAID" {{ request('payment_status') == 'UNPAID' ? 'selected' : '' }}>Unpaid</option>
            </select>

            <button type="submit"
                style="background:#2563eb;color:white;padding:7px 12px;border-radius:6px;border:none;cursor:pointer;">
                Apply
            </button>

            <a href="{{ route('workshop.create') }}"
                style="background:#6b7280;color:white;padding:7px 12px;border-radius:6px;text-decoration:none;">
                Reset
            </a>

            <div class="ml-auto flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-500 font-medium">Show:</label>
                <select name="per_page" id="per_page" class="input" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

        </form>

        <!-- TABLE -->
        <table>

            <thead>
                <tr style="background:#f8fafc;">
                    <th class="th-sort">
                        <a href="{{ $sortUrl('date') }}">
                            <span>Date</span>
                            {!! $sortIcon('date') !!}
                        </a>
                    </th>
                    <th class="th-sort">
                        <a href="{{ $sortUrl('bill_no') }}">
                            <span>Bill</span>
                            {!! $sortIcon('bill_no') !!}
                        </a>
                    </th>
                    <th class="th-sort">
                        <a href="{{ $sortUrl('vehicle_no') }}">
                            <span>Vehicle</span>
                            {!! $sortIcon('vehicle_no') !!}
                        </a>
                    </th>
                    <th class="th-sort">
                        <a href="{{ $sortUrl('name') }}">
                            <span>Name</span>
                            {!! $sortIcon('name') !!}
                        </a>
                    </th>
                    <th class="th-sort">
                        <a href="{{ $sortUrl('total_amount') }}">
                            <span>Total</span>
                            {!! $sortIcon('total_amount') !!}
                        </a>
                    </th>
                    <th class="th-sort">
                        <a href="{{ $sortUrl('payment_status') }}">
                            <span>Status</span>
                            {!! $sortIcon('payment_status') !!}
                        </a>
                    </th>
                    <th style="width:40px; text-align:center;">▼</th>
                </tr>
            </thead>

            <tbody>

            @forelse($bills as $bill)

            <tr onclick="toggleItems({{ $bill->id }})" style="cursor:pointer;">
                <td>{{ \Carbon\Carbon::parse($bill->date)->format('d-m-Y') }}</td>
                <td>{{ $bill->bill_no }}</td>
                <td>{{ $bill->vehicle_no }}</td>
                <td>{{ $bill->name ?? '-' }}</td>
                <td class="bill-total">{{ $bill->total_amount }}</td>
                <td id="status-cell-{{ $bill->id }}">
                    @if($bill->payment_status == 'PAID')
                        <button type="button"
                            onclick="event.stopPropagation(); markUnpaid({{ $bill->id }})"
                            title="Click to mark Unpaid"
                            style="background:#16a34a;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;font-weight:600;font-size:12px;">
                            Paid
                        </button>
                    @else
                        <button type="button"
                            onclick="event.stopPropagation(); markPaid({{ $bill->id }})"
                            title="Click to mark Paid"
                            style="background:#dc2626;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;font-weight:600;font-size:12px;">
                            Mark Paid
                        </button>
                    @endif
                </td>
                <td style="text-align:center; color:#6b7280;">▼</td>
            </tr>

            <!-- EXPAND -->
            <tr id="items-{{ $bill->id }}" style="display:none;">
                <td colspan="7">

                    <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                        <strong>Items</strong>

                        <div style="display:flex; gap:6px; align-items:center;">
                            <a href="/workshop/print/{{ $bill->id }}" target="_blank"
                                style="background:#2563eb;color:white;padding:6px 12px;border-radius:5px;text-decoration:none;font-weight:500;">
                                🖨️ Print
                            </a>

                            <a href="/workshop/pdf/{{ $bill->id }}" target="_blank"
                                style="background:#059669;color:white;padding:6px 12px;border-radius:5px;text-decoration:none;font-weight:500;">
                                PDF
                            </a>

                            <button onclick="deleteBill({{ $bill->id }})"
                                class="btn-delete"
                                style="padding:6px 12px;">
                                Delete Bill
                            </button>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th>Description</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:110px;">Rate ({{ \App\Helpers\CurrencyHelper::symbol() }})</th>
                                <th style="width:110px;">Amount ({{ \App\Helpers\CurrencyHelper::symbol() }})</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($bill->items as $item)
                            @php
                                $itemQty = isset($item->qty) && (float)$item->qty > 0 ? (float)$item->qty : 1;
                                $itemRate = isset($item->rate) && (float)$item->rate > 0 ? (float)$item->rate : (float)($item->price ?? 0);
                                $itemPrice = isset($item->price) ? (float)$item->price : ($itemQty * $itemRate);
                            @endphp
                            <tr data-item-id="{{ $item->id }}">
                                <td class="item-desc">{{ $item->description }}</td>
                                <td class="item-qty">{{ $itemQty == (int)$itemQty ? (int)$itemQty : number_format($itemQty, 2) }}</td>
                                <td class="item-rate">{{ number_format($itemRate, 3) }}</td>
                                <td class="item-price">{{ number_format($itemPrice, 3) }}</td>
                                <td>

                                    <button 
                                        class="btn-edit"
                                        data-id="{{ $item->id }}"
                                        data-description="{{ $item->description }}"
                                        data-qty="{{ $itemQty }}"
                                        data-rate="{{ $itemRate }}"
                                        data-price="{{ $itemPrice }}"
                                        onclick="handleEditItem(this)">
                                        Edit
                                    </button>

                                    <button onclick="deleteItemServer({{ $item->id }})"
                                        class="btn-delete">
                                        Delete
                                    </button>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#6b7280; padding:10px;">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </td>
            </tr>

            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#6b7280;">No records found</td>
            </tr>
            @endforelse

            </tbody>

        </table>

        <!-- PAGINATION FOOTER -->
        <div class="mt-4 pt-3 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="text-sm text-gray-600">
                @if($bills->total() > 0)
                    Showing <span class="font-semibold text-gray-800">{{ $bills->firstItem() }}</span> to <span class="font-semibold text-gray-800">{{ $bills->lastItem() }}</span> of <span class="font-semibold text-gray-800">{{ $bills->total() }}</span> records
                @else
                    No records to display
                @endif
            </div>

            <div>
                {{ $bills->links() }}
            </div>
        </div>

    </div>

</div>

<!-- MODAL -->
<div id="editItemModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.4); justify-content:center; align-items:center; z-index:9999;">

    <div style="background:white; padding:20px; border-radius:10px; width:380px;">

        <h3 class="font-bold text-lg mb-2">Edit Item</h3>

        <input type="hidden" id="edit_item_id">

        <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
        <input type="text" id="edit_description" class="input w-full mb-2">

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 8px;" class="mb-2">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Qty</label>
                <input type="number" id="edit_qty" class="input w-full" step="any" min="0.01">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Rate ({{ \App\Helpers\CurrencyHelper::symbol() }})</label>
                <input type="number" id="edit_rate" class="input w-full" step="any" min="0">
            </div>
        </div>

        <label class="block text-xs font-semibold text-gray-600 mb-1">Amount ({{ \App\Helpers\CurrencyHelper::symbol() }})</label>
        <input type="number" id="edit_price" class="input w-full mb-3" step="any" min="0">

        <div style="display:flex; justify-content:flex-end; gap:10px;">

            <button onclick="closeItemModal()"
                style="background:gray;color:white;padding:6px 12px;border-radius:5px;border:none;cursor:pointer;">
                Cancel
            </button>

            <button onclick="updateItem()"
                style="background:#2563eb;color:white;padding:6px 12px;border-radius:5px;border:none;cursor:pointer;">
                Save
            </button>

        </div>

    </div>
</div>

<script src="{{ asset('js/workshop.js') }}"></script>

@endsection