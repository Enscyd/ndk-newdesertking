@if($accounts->count())

    <!-- OPENING ROW -->
    <tr class="bg-yellow-200 font-bold">
        <td colspan="6" class="px-2">
            OPENING BALANCE
        </td>
        <td class="text-right px-2">
            {{ number_format($opening, 2) }}
        </td>
    </tr>


    @foreach($accounts as $acc)
    <tr class="hover:bg-gray-50 transition">

        <!-- DATE -->
        <td class="border p-2 text-center">
            {{ \Carbon\Carbon::parse($acc->date)->format('d M Y') }}
        </td>

        <!-- TYPE -->
        <td class="border p-2 text-center">
            <span class="px-2 py-1 rounded text-xs font-semibold
                {{ $acc->type == 'INCOME' 
                    ? 'bg-green-100 text-green-700' 
                    : 'bg-red-100 text-red-700' }}">
                {{ $acc->type }}
            </span>
        </td>

        <!-- DESCRIPTION -->
        <td class="border p-2">
            {{ $acc->description ?? '-' }}
        </td>

        <!-- PURPOSE -->
        <td class="border p-2">
            {{ $acc->purpose->name ?? '-' }}
        </td>

        <!-- AMOUNT -->
        <td class="border p-2 text-right font-semibold
            {{ $acc->type == 'INCOME' ? 'text-green-600' : 'text-red-600' }}">
            
            {{ $acc->type == 'INCOME' ? '+' : '-' }}
            {{ number_format($acc->amount, 2) }}
            
        </td>

        <!-- RUNNING BALANCE -->
        <td class="border p-2 text-right font-bold text-blue-700">
            {{ number_format($acc->running_balance ?? 0, 2) }}
        </td>

        <!-- ACTION -->
        <td class="border p-2 text-center">
            <button 
                class="delete-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                data-id="{{ $acc->id }}">
                Delete
            </button>
        </td>

    </tr>
    @endforeach


    <!-- SUMMARY ROW -->
    <tr class="bg-gray-800 text-white font-bold text-sm">

        <td colspan="2" class="px-2">
            INCOME : {{ number_format($income, 2) }}
        </td>

        <td colspan="2" class="px-2">
            EXPENSE : {{ number_format($expense, 2) }}
        </td>

        <td class="text-right px-2">
            NET
        </td>

        <td class="text-right px-2">
            {{ number_format($net, 2) }}
        </td>

        <!-- EMPTY FOR ACTION COLUMN -->
        <td></td>

    </tr>

@else

<tr>
    <td colspan="7" class="text-center p-4 text-gray-500">
        No records found
    </td>
</tr>

@endif