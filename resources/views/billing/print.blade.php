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

/* LETTERHEAD */
.bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 1122px;
    z-index: -1;
}

/* PAGE */
.page {
    position: relative;
    width: 100%;
    height: 1122px;
    page-break-after: always;
}

/* CONTENT */
.wrapper {
    position: absolute;
    top: 130px;
    bottom: 190px;
    left: 35px;
    right: 35px;
}

/* HEADER */
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
}

.customer {
    text-align: center;
    font-size: 13px;
    margin-bottom: 8px;
}

/* TABLE */
.table-box {
    position: absolute;
    top: 120px;
    bottom: 0;
    left: 0;
    right: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: 11px;
}

th, td {
    border: 0.1px solid #000;
    padding: 3px;
    text-align: center;
}

th {
    background: #d9d9d9;
}

.left {
    text-align: left;
}

/* DATA ROWS */
.data-row td {
    height: 28px;
}

/* 🔥 EMPTY ROWS (NO HORIZONTAL LINES) */
.empty-row td {
    height: 28px;
    border-top: none;
    border-bottom: none;
    border-left: 0.1px solid #000;
    border-right: 0.1px solid #000;
}

/* TOTAL */
.total-row td {
    height: 40px;
    font-weight: bold;
}

/* AMOUNT IN WORDS */
.words-row td {
    border: none;
    border-top: 1px solid #000;
    padding-top: 6px;
    text-align: center;
    font-size: 12px;
}

/* PRINT */
@media print {
    button { display: none; }
}
</style>
</head>

<body onload="window.print()">

@php
    $rowsPerPage = 20;
    $lastPageRows = 17;

    $items = $invoice->items;
    $totalItems = $items->count();

    $pages = [];
    $index = 0;

    while ($index < $totalItems) {
        $remaining = $totalItems - $index;

        if ($remaining <= $lastPageRows) {
            $pages[] = $items->slice($index, $lastPageRows);
            break;
        }

        $pages[] = $items->slice($index, $rowsPerPage);
        $index += $rowsPerPage;
    }

    $totalQty = 0;
    $totalRent = 0;
    $totalTaxable = 0;
    $totalVat = 0;
    $grandTotal = 0;
@endphp

@foreach($pages as $pageIndex => $pageItems)

<div class="page">

    <img src="{{ asset('storage/letterhead.jpg') }}" class="bg">

    <div class="wrapper">

        <h2>Summary Tax Invoice</h2>

        <div class="vat">
            VAT Registration Certificate No:10241591 , VATIN No:OM1100183557
        </div>

        <div class="top-row">
            <div><strong>Invoice No:</strong> {{ $invoice->invoiceNo }}</div>
            <div><strong>Date:</strong> {{ now()->format('d-m-Y') }}</div>
        </div>

        @php
            $companyName = $invoice->company->name ?? null;
            $companyAddress = $companyName
                ? \App\Models\Company::where('name', $companyName)->value('address')
                : null;
        @endphp

        <div class="customer">
            <strong>{{ $companyName }}</strong><br>
            {{ $companyAddress }}
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th style="width:4%">S.No</th>
                        <th style="width:10%">Date</th>
                        <th style="width:30%">Description</th>
                        <th style="width:14%">Vehicle</th>
                        <th style="width:5%">Qty</th>
                        <th style="width:9%">Rent</th>
                        <th style="width:10%">Taxable</th>
                        <th style="width:7%">VAT</th>
                        <th style="width:11%">Amount</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($pageItems as $i => $item)
                @php
                    $totalQty += $item->quantity;
                    $totalRent += $item->rent;
                    $totalTaxable += $item->taxableAmount;
                    $totalVat += $item->vat;
                    $grandTotal += $item->totalAmount;
                @endphp

                <tr class="data-row">
                    <td>{{ ($pageIndex * $rowsPerPage) + $i + 1 }}</td>
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

                @php
                    $fillRows = ($loop->last ? $lastPageRows : $rowsPerPage) - count($pageItems);
                @endphp

                @for($i = 0; $i < $fillRows; $i++)
                <tr class="empty-row">
                    <td>&nbsp;</td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td><td></td>
                </tr>
                @endfor

                @if($loop->last)
                <tr class="total-row">
                    <td colspan="4">TOTAL</td>
                    <td>{{ $totalQty }}</td>
                    <td>{{ number_format($totalRent,3) }}</td>
                    <td>{{ number_format($totalTaxable,3) }}</td>
                    <td>{{ number_format($totalVat,3) }}</td>
                    <td>{{ number_format($grandTotal,3) }}</td>
                </tr>

                <tr class="words-row">
                    <td colspan="9">
                        <strong>Amount in Words:</strong>
                        {{ \App\Helpers\CurrencyHelper::omrToWords($grandTotal) }}
                    </td>
                </tr>
                @endif

                </tbody>
            </table>
        </div>

    </div>
</div>

@endforeach

<button onclick="window.print()">Print</button>

</body>
</html>