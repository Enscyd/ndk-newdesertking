@if($invoices->count())

@foreach($invoices as $inv)

<tr style="background:#f3f4f6; font-weight:600;">
    <td class="p-3 border">{{ $inv->invoiceNo }}</td>

    <td class="p-3 border">
        {{ $inv->company->name ?? '' }}
    </td>

    <td class="p-3 border">
        {{ \Carbon\Carbon::parse($inv->date)->format('d M Y') }}
    </td>

    <!-- IMAGE -->
    <td class="p-3 border text-center">
        @if($inv->billImage)
            <img src="{{ url('/storage-file/'.$inv->billImage) }}"
                 class="w-12 h-12 object-cover rounded cursor-pointer border"
                 onclick="openImage('{{ url('/storage-file/'.$inv->billImage) }}')">
        @else
            -
        @endif
    </td>

    <!-- STATUS + ACTIONS -->
    <td class="p-3 border">

        <div class="flex items-center gap-2 flex-wrap">

            @if($inv->paymentStatus == 'UNPAID')

                <span class="text-red-600 font-semibold">
                    UNPAID
                </span>

                <!-- MARK PAID -->
                <button 
                    class="markPaidBtn bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700"
                    data-id="{{ $inv->id }}">
                    Mark Paid
                </button>

            @else

                <span class="text-green-600 font-semibold">
                    PAID
                </span>

            @endif

            <!-- DELETE INVOICE -->
            <button 
                class="deleteInvoiceBtn bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700"
                data-id="{{ $inv->id }}">
                Delete
            </button>

        </div>

    </td>
</tr>


<tr>
<td colspan="5" class="p-0">

<table class="w-full border text-sm">

<thead>
<tr>
<th class="border p-2">#</th>
<th class="border p-2">Description</th>
<th class="border p-2">Vehicle</th>
<th class="border p-2">Qty</th>
<th class="border p-2">Rent</th>
<th class="border p-2">Taxable</th>
<th class="border p-2">VAT</th>
<th class="border p-2">Total</th>
<th class="border p-2 text-center">Action</th> <!-- NEW -->
</tr>
</thead>

<tbody>

@foreach($inv->items as $i => $item)
<tr>
<td class="border p-2">{{ $i+1 }}</td>
<td class="border p-2">{{ $item->description }}</td>
<td class="border p-2">{{ $item->vehicleNo }}</td>
<td class="border p-2">{{ $item->quantity }}</td>
<td class="border p-2">{{ $item->rent }}</td>
<td class="border p-2">{{ $item->taxableAmount }}</td>
<td class="border p-2">{{ $item->vat }}</td>
<td class="border p-2">{{ $item->totalAmount }}</td>

<!-- DELETE ITEM -->
<td class="border p-2 text-center">
    <button 
        class="deleteItemBtn bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600"
        data-id="{{ $item->id }}">
        Delete
    </button>
</td>

</tr>
@endforeach

<tr>
<td colspan="8" class="border p-2 text-right font-bold">
    Total
</td>
<td class="border p-2 font-bold">
    Rs {{ number_format($inv->grandTotal,2) }}
</td>
</tr>

</tbody>
</table>

</td>
</tr>

@endforeach

@else

<tr>
    <td colspan="5" class="p-4 text-center">
        No invoices found
    </td>
</tr>

@endif


<!-- PAGINATION -->
<tr>
<td colspan="5" class="p-3">
    {{ $invoices->links() }}
</td>
</tr>