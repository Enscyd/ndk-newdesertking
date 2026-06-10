@php
$totalDriverAmount = 0;
$totalTripAmount = 0;
$totalOmaniAmount = 0;
@endphp


@foreach($trips as $trip)

@php
$totalDriverAmount += $trip->driverAmount ?? 0;
$totalTripAmount += $trip->tripAmount ?? 0;
$totalOmaniAmount += $trip->omaniAmount ?? 0;

$serialNumber = method_exists($trips, 'total')
    ? $trips->total() - (($trips->currentPage() - 1) * $trips->perPage()) - $loop->index
    : $trips->count() - $loop->index;
@endphp

<tr id="row-{{ $trip->id }}">

<td class="border p-2">{{ $serialNumber }}</td>

<td class="border p-2">{{ $trip->destination->name ?? '' }}</td>

<td class="border p-2">{{ $trip->employee->employeeName ?? '' }}</td>

<td class="border p-2">{{ $trip->truck->truckNumber ?? '' }}</td>

<td class="border p-2">{{ $trip->tripType }}</td>

<td class="border p-2">{{ $trip->driverAmount }}</td>

<td class="border p-2">
{{ \Carbon\Carbon::parse($trip->tripDate)->format('d-m-Y') }}
</td>

<td class="border p-2">{{ $trip->tripAmount }}</td>

<td class="border p-2">{{ $trip->isOmani }}</td>

<td class="border p-2">{{ $trip->omaniName }}</td>

<td class="border p-2">{{ $trip->omaniAmount }}</td>

<td class="border p-2">

@if($trip->image)

<img src="{{ asset('storage/'.$trip->image) }}"
class="w-12 h-12 object-cover rounded cursor-pointer tripImage"
data-src="{{ asset('storage/'.$trip->image) }}">

@endif

</td>

<td class="border p-2">{{ $trip->company->name ?? '' }}</td>

<td class="border p-2 space-x-1">

<button class="editBtn bg-blue-600 text-white px-2 py-1 rounded text-xs"
data-id="{{ $trip->id }}">
Edit
</button>

<button class="deleteBtn bg-red-600 text-white px-2 py-1 rounded text-xs"
data-id="{{ $trip->id }}">
Delete
</button>

</td>

</tr>

@endforeach



<!-- TOTAL ROW -->
<tr class="bg-gray-200 font-bold">

<td colspan="6" class="border p-2 text-right">
Totals
</td>

<td class="border p-2">
{{ number_format($totalDriverAmount,2) }}
</td>

<td class="border p-2"></td>

<td class="border p-2">
{{ number_format($totalTripAmount,2) }}
</td>

<td class="border p-2"></td>

<td class="border p-2"></td>

<td class="border p-2">
{{ number_format($totalOmaniAmount,2) }}
</td>

<td class="border p-2"></td>

<td class="border p-2"></td>

<td class="border p-2"></td>

</tr>



@if(method_exists($trips,'links'))

<tr>

<td colspan="14" class="p-3 text-center">

<div class="pagination">
    {{ $trips->links() }}
</div>

</td>

</tr>

@endif