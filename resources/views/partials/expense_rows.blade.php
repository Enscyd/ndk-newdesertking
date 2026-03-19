@foreach($expenses as $expense)

<tr>

<td class="border p-2">
{{ $expense->employee->employeeName ?? '-' }}
</td>

<td class="border p-2">
{{ $expense->truck->truckNumber ?? '-' }}
</td>

<td class="border p-2">
{{ \Carbon\Carbon::parse($expense->expenseDate)->format('d-m-Y') }}
</td>

<td class="border p-2">
{{ $expense->category }}
</td>

<td class="border p-2">
{{ $expense->details }}
</td>

<td class="border p-2 font-semibold">
{{ number_format($expense->amount,2) }}
</td>

<td class="border p-2">

@if($expense->image)

<img src="{{ asset('storage/'.$expense->image) }}"
class="w-10 h-10 object-cover cursor-pointer rounded expenseImage">

@endif

</td>

<td class="border p-2 flex gap-1">

<button
class="editBtn bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs"
data-id="{{ $expense->id }}">

Edit

</button>

<button


</td>

</tr>

@endforeach