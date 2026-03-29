<!DOCTYPE html>
<html>
<head>
    <title>Ledger PDF</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

<h3>NDK Accounts Report ({{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }})</h3>

<p>
    Opening: {{ number_format($opening,2) }} |
    Income: {{ number_format($income,2) }} |
    Expense: {{ number_format($expense,2) }} |
    Net: {{ number_format($net,2) }}
</p>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th>Balance</th>
        </tr>
    </thead>

    <tbody>

        <tr>
            <td colspan="5">Opening</td>
            <td class="text-right">{{ number_format($opening,2) }}</td>
        </tr>

        @foreach($accounts as $acc)
        <tr>
            <td>{{ \Carbon\Carbon::parse($acc->date)->format('d-m-Y') }}</td>
            <td>{{ $acc->type }}</td>
            <td>{{ $acc->description }}</td>
            <td>{{ $acc->purpose->name ?? '-' }}</td>
            <td class="text-right">
                {{ $acc->type == 'INCOME' ? '+' : '-' }}
                {{ number_format($acc->amount,2) }}
            </td>
            <td class="text-right">
                {{ number_format($acc->running_balance,2) }}
            </td>
        </tr>
        @endforeach

    </tbody>
</table>

</body>
</html>