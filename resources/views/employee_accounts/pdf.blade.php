<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">
<title>Salary Slip</title>

<style>
    body {
        font-family: DejaVu Sans;
        font-size: 12px;
        margin: 0;
        padding: 20px;
        position: relative;
    }

    /* WATERMARK */
    .watermark {
        position: fixed;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        font-size: 120px;
        color: #000;
        opacity: 0.05;
        z-index: -1;
        white-space: nowrap;
    }

    /* HEADER */
    .header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }

    .company {
        font-size: 22px;
        font-weight: bold;
    }

    .title {
        font-size: 15px;
        margin-top: 3px;
    }

    /* INFO TABLE */
    .info-table {
        width: 100%;
        margin-bottom: 15px;
        border-collapse: collapse;
    }

    .info-table td {
        border: none;
        padding: 4px 0;
    }

    .left {
        text-align: left;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
        font-weight: bold;
        padding-top: 5px;
    }

    /* DATA TABLE */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .data-table th {
        background: #f2f2f2;
    }

    .data-table th,
    .data-table td {
        border: 1px solid #000;
        padding: 7px;
        text-align: center;
    }

    /* SUMMARY */
    .summary-table {
        width: 45%;
        margin-top: 20px;
        margin-left: auto;
        border-collapse: collapse;
    }

    .summary-table td {
        padding: 6px;
    }

    .summary-table .label {
        text-align: left;
    }

    .summary-table .value {
        text-align: right;
    }

    .net-row td {
        border-top: 2px solid #000;
        font-weight: bold;
        font-size: 13px;
    }

    /* FOOTER */
    .footer-table {
        width: 100%;
        margin-top: 70px;
        border-collapse: collapse;
    }

    .footer-table td {
        border: none;
        text-align: center;
    }

    .signature-line {
        margin-top: 40px;
        border-top: 1px solid #000;
        width: 160px;
        margin-left: auto;
        margin-right: auto;
    }

</style>

</head>

<body>

<!-- WATERMARK -->

<div class="watermark">NDK</div>

<!-- HEADER -->

<div class="header">
    <div class="company">NDK Company</div>
    <div class="title">Salary Slip</div>
</div>

<!-- INFO -->

<table class="info-table">
    <tr>
        <td class="left">
            <strong>Employee:</strong> {{ $employee->employeeName }}
        </td>
        <td class="right">
            <strong>Date:</strong> {{ date('d/m/Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="2" class="center">
            <strong>Month:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
        </td>
    </tr>
</table>

<!-- DATA TABLE -->

<table class="data-table">
    <thead>
        <tr>
            <th style="width:20%">Date</th>
            <th style="width:20%">Type</th>
            <th style="width:20%">Amount</th>
            <th style="width:40%">Remarks</th>
        </tr>
    </thead>

```
<tbody>
    @foreach($entries as $e)
    <tr>
        <td>{{ \Carbon\Carbon::parse($e->date)->format('d/m/Y') }}</td>
        <td>{{ $e->type }}</td>
        <td>{{ number_format($e->amount,2) }}</td>
        <td>{{ $e->remarks }}</td>
    </tr>
    @endforeach
</tbody>
```

</table>

<!-- SUMMARY -->

<table class="summary-table">
    <tr>
        <td class="label"><strong>Total Credit:</strong></td>
        <td class="value">{{ number_format($credits,2) }}</td>
    </tr>
    <tr>
        <td class="label"><strong>Total Debit:</strong></td>
        <td class="value">{{ number_format($debits,2) }}</td>
    </tr>
    <tr class="net-row">
        <td class="label"><strong>Net Salary:</strong></td>
        <td class="value">{{ number_format($net,2) }}</td>
    </tr>
</table>

<!-- FOOTER -->

<table class="footer-table">
    <tr>
        <td>
            <div class="signature-line"></div>
            <p>Employee Signature</p>
        </td>
        <td>
            <div class="signature-line"></div>
            <p>Authorized Signature</p>
        </td>
    </tr>
</table>

</body>
</html>
