<!DOCTYPE html>

<html>
<head>
    <title>Invoice</title>
<style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* BACKGROUND LETTERHEAD */
    .bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    /* MAIN CONTENT AREA */
    .wrapper {
        position: relative;
        z-index: 1;
        padding: 95px 35px 70px 35px;
        height: 1122px; /* 🔥 A4 fixed height */
        box-sizing: border-box;
    }

    h2 {
        text-align: center;
        margin: 0;
        font-size: 15px;
    }

    .vat {
        text-align: center;
        font-size: 11px;
        margin: 5px 0 10px;
    }

    .top-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .customer {
        text-align: center;
        font-size: 13px;
        margin-bottom: 10px;
    }

    /* TABLE AREA */
    .table-container {
        height: 700px; /* 🔥 LOCKED TABLE AREA */
    }

    table {
        width: 100%;
        height: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 11px;
    }

    thead {
        display: table-header-group;
    }

    table th, table td {
        border: 0.1px solid #000;
        padding: 3px;
        text-align: center;
    }

    table th {
        background: #d9d9d9;
        color: #000;
    }

    .left {
        text-align: left;
        white-space: pre-line;
    }

    /* NORMAL ROW */
    .data-row td {
        height: 24px;
    }

    /* 🔥 FLEXIBLE EMPTY AREA */
    .empty-row {
        height: 100%;
    }

    .empty-row td {
        height: 100%;
    }

    /* TOTAL ROW */
    .total-row td {
        font-weight: bold;
        height: 35px;
    }

    /* WORDS */
.words {
    text-align: center;              /* 🔥 move to right */
    font-size: 12px;
    margin-top: 5px;
    padding-bottom: 5px;
    border-bottom: 1px solid #000;  /* 🔥 horizontal line */
}

    /* FOOTER */
    .footer {
        position: fixed;
        bottom: 0;
        left: 35px;
        right: 35px;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
    }

    tr {
        page-break-inside: avoid;
    }

    @media print {
        button {
            display: none;
        }
    }
</style>
</head>

<body onload="window.print()">

<!-- LETTERHEAD -->

<img src="{{ asset('storage/letterhead.jpg') }}" class="bg">

<div class="wrapper">


<!-- TITLE -->
 <br>
<h2>TAX INVOICE</h2>

<!-- VAT -->
<div class="vat">
    VAT Registration Certificate No:10241591 , VATIN No:OM1100183557
</div>

<!-- TOP -->
<div class="top-row">
    <div><strong>Invoice No:</strong> {{ $invoice->invoiceNo }}</div>
    <div><strong>Invoice Date:</strong> {{ now()->format('d-m-Y') }}</div>
</div>

<!-- CUSTOMER -->
<div class="customer">
    @php
        $companyName = $invoice->company->name ?? null;

        $companyAddress = $companyName
            ? \App\Models\Company::where('name', $companyName)->value('address')
            : null;
    @endphp

    <strong>{{ $companyName ?? '' }}</strong><br>
    {{ $companyAddress ?? '' }}
</div>

<!-- 🔥 TABLE LOCK AREA -->
<div class="table-container">

    <table>

        <thead>
            <tr>
                <th style="width:4%">S.No</th>
                <th style="width:10%">Date</th>
                <th style="width:30%">Description</th>
                <th style="width:14%">Vehicle No</th>
                <th style="width:4%">QTY Trips</th>
                <th style="width:9%">Rent</th>
                <th style="width:10%">Taxable Amount</th>
                <th style="width:7%">VAT %5</th>
                <th style="width:12%">Amount</th>
            </tr>
        </thead>

        <tbody>

        @php
            $totalQty = 0;
            $totalRent = 0;
            $totalTaxable = 0;
            $totalVat = 0;
            $grandTotal = 0;
        @endphp

        <!-- DATA -->
        @foreach($invoice->items as $i => $item)

        @php
            $totalQty += $item->quantity;
            $totalRent += $item->rent;
            $totalTaxable += $item->taxableAmount;
            $totalVat += $item->vat;
            $grandTotal += $item->totalAmount;
        @endphp

        <tr class="data-row">
            <td>{{ $i+1 }}</td>
            <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</td>
            <td class="left">{{ $item->description }}</td>
            <td>{{ $item->vehicleNo }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->rent,3) }}</td>
            <td>{{ number_format($item->taxableAmount,3) }}</td>
            <td>{{ number_format($item->vat,3) }}</td>
            <td>{{ number_format($item->totalAmount,3) }}</td>
        </tr>

        @endforeach

        <!-- 🔥 AUTO FILL SPACE -->
        <tr class="empty-row">
            <td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td>
        </tr>

        <!-- TOTAL -->
        <tr class="total-row">
            <td colspan="4">TOTAL</td>
            <td>{{ $totalQty }}</td>
            <td>{{ number_format($totalRent,3) }}</td>
            <td>{{ number_format($totalTaxable,3) }}</td>
            <td>{{ number_format($totalVat,3) }}</td>
            <td>{{ number_format($grandTotal,3) }}</td>
        </tr>

        </tbody>
    </table>

</div>

<!-- WORDS -->
<div class="words">
    <strong>Amount in Words:</strong>
    {{ \App\Helpers\CurrencyHelper::omrToWords($grandTotal) }}
</div>


</div>



<button onclick="window.print()">Print</button>

</body>
</html>
