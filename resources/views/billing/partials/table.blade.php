@if($invoices->count())

@foreach($invoices as $inv)

<!-- =========================
     INVOICE ROW
========================= -->
<tr class="bg-gray-50 hover:bg-gray-100 transition">

    <!-- INVOICE NO -->
    <td class="p-4 border font-semibold text-gray-800">
        {{ $inv->invoiceNo }}
    </td>

    <!-- COMPANY -->
    <td class="p-4 border text-gray-700">
        {{ $inv->company->name ?? '-' }}
    </td>

    <!-- DATE -->
    <td class="p-4 border text-gray-600">
        {{ $inv->date ? \Carbon\Carbon::parse($inv->date)->format('d M Y') : '-' }}
    </td>

    <!-- IMAGE -->
    <td class="p-4 border text-center">
        @if($inv->billImage)
            <img src="{{ asset($inv->billImage) }}"
                class="w-14 h-14 object-cover rounded-lg shadow cursor-pointer hover:scale-105 transition"
                onclick="openImage(this.src)">
        @else
            <span class="text-gray-400 text-sm">No Image</span>
        @endif
    </td>

    <!-- ACTION -->
    <td class="p-4 border">
        <div class="flex items-center gap-2 flex-wrap">

            @if($inv->paymentStatus === 'UNPAID')

                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">
                    Unpaid
                </span>

                <button 
                    class="markPaidBtn bg-green-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-green-700 transition shadow-sm"
                    data-id="{{ $inv->id }}">
                    Mark Paid
                </button>

            @else

                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                    Paid
                </span>

            @endif

            <!-- NEW ADD TRIP BUTTON -->
            <button 
                type="button"
                class="addTripBtn bg-purple-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-purple-700 transition shadow-sm"
                data-id="{{ $inv->id }}"
                data-invoice="{{ $inv->invoiceNo }}">
                Add Trip
            </button>

            <button 
                class="deleteInvoiceBtn bg-red-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-red-700 transition shadow-sm"
                data-id="{{ $inv->id }}">
                Delete
            </button>

            <a href="{{ route('billing.print', $inv->id) }}" 
               target="_blank"
               class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs hover:bg-blue-700 transition shadow-sm">
               Print
            </a>

        </div>
    </td>

</tr>


<!-- =========================
     ITEMS SECTION
========================= -->
<tr>
<td colspan="5" class="p-4 bg-white border-t">

<div class="rounded-lg border shadow-sm overflow-hidden">

<table class="w-full text-sm text-gray-700">

<thead class="bg-gray-100 text-gray-600 uppercase text-xs">
<tr>
    <th class="p-3 border">#</th>
    <th class="p-3 border text-left">Description</th>
    <th class="p-3 border">Vehicle</th>
    <th class="p-3 border">Qty</th>
    <th class="p-3 border">Rent</th>
    <th class="p-3 border">Taxable</th>
    <th class="p-3 border">VAT</th>
    <th class="p-3 border">Total</th>
    <th class="p-3 border text-center">Action</th>
</tr>
</thead>

<tbody>

@if($inv->items && $inv->items->count())

@foreach($inv->items as $i => $item)
<tr class="hover:bg-gray-50 transition">

    <td class="p-3 border text-center">{{ $i+1 }}</td>

    <td class="p-3 border font-medium">
        {{ $item->description ?? '-' }}
    </td>

    <td class="p-3 border text-center">
        {{ $item->vehicleNo ?? '-' }}
    </td>

    <td class="p-3 border text-center">
        {{ $item->quantity ?? 0 }}
    </td>

    <td class="p-3 border text-center">
        {{ number_format($item->rent ?? 0, 2) }}
    </td>

    <td class="p-3 border text-center">
        {{ number_format($item->taxableAmount ?? 0, 2) }}
    </td>

    <td class="p-3 border text-center">
        {{ number_format($item->vat ?? 0, 2) }}
    </td>

    <td class="p-3 border text-center font-semibold text-gray-800">
        {{ number_format($item->totalAmount ?? 0, 2) }}
    </td>

    <td class="p-3 border text-center">
        <button 
            type="button"
            class="deleteItemBtn bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-600 transition"
            data-id="{{ $item->id }}">
            Delete
        </button>

        <button 
            type="button"
            class="editItemBtn bg-blue-500 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-600 transition"
            data-id="{{ $item->id }}">
            Edit
        </button>
    </td>

</tr>
@endforeach

@else

<tr>
    <td colspan="9" class="p-4 text-center text-gray-400">
        No items found
    </td>
</tr>

@endif


<!-- =========================
     GRAND TOTAL
========================= -->
<tr class="bg-gray-50 font-semibold">

    <td colspan="7" class="p-3 border text-right text-gray-700">
        Grand Total
    </td>

    <td class="p-3 border text-center text-green-700 text-sm">
        {{ number_format($inv->grandTotal ?? 0, 2) }}
    </td>

    <td class="border"></td>

</tr>

</tbody>

</table>

</div>

</td>
</tr>

@endforeach

@else

<!-- EMPTY STATE -->
<tr>
<td colspan="5" class="p-10 text-center text-gray-500">
    <div class="flex flex-col items-center gap-2">
        <span class="text-4xl">📄</span>
        <p class="text-lg font-medium">No invoices found</p>
        <p class="text-sm text-gray-400">Create your first invoice</p>
    </div>
</td>
</tr>

@endif


<!-- =========================
     PAGINATION (FIXED FOR SPEED)
========================= -->
<tr>
<td colspan="5" class="p-4 bg-white border-t">

    <div class="flex justify-between items-center">

        <span class="text-sm text-gray-500">
            Showing {{ $invoices->firstItem() ?? 0 }} 
            to {{ $invoices->lastItem() ?? 0 }}
        </span>

        {{ $invoices->links() }}

    </div>

</td>
</tr>
