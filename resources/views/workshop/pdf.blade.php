<!DOCTYPE html>
<html>
<head>
    <title>NDK Workshop Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: -1;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            padding: 120px 45px 70px 45px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .top-row,
        .customer-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .top-row td,
        .customer-row td {
            border: none;
            padding: 3px 0;
            font-size: 12px;
            font-weight: bold;
        }

        .table-area {
            margin-top: 10px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 11px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 0.7px solid #000;
            padding: 5px;
            text-align: center;
        }

        .invoice-table th {
            background: #e6e6e6;
            font-weight: bold;
        }

        .left {
            text-align: left !important;
        }

        .blank-row td {
            border-top: none;
            border-bottom: none;
        }

        .total-row td {
            font-weight: bold;
            height: 28px;
            background: #f0f0f0;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <img src="{{ public_path('storage/workshop.jpg') }}" class="bg" alt="Letterhead">

    <div class="wrapper">

        <h2>NDK WORKSHOP INVOICE</h2>

        <table class="top-row">
            <tr>
                <td><strong>Invoice No:</strong> {{ $bill->bill_no ?? '' }}</td>
                <td style="text-align:right;">
                    <strong>Date:</strong>
                    {{ isset($bill->date) ? \Carbon\Carbon::parse($bill->date)->format('d-m-Y') : '' }}
                </td>
            </tr>
        </table>

        <table class="customer-row">
            <tr>
                <td><strong>Vehicle:</strong> {{ $bill->vehicle_no ?? '' }}</td>
                <td><strong>Customer:</strong> {{ $bill->name ?? 'Walk-in' }}</td>
                <td ><strong>Status:</strong> {{ $bill->payment_status ?? '' }}</td>
            </tr>
        </table>

        <div class="table-area">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width:10%">#</th>
                        <th style="width:65%">Description</th>
                        <th style="width:25%">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $items = $bill->items ?? [];
                        $currentRows = count($items);

                        /*
                         * fewer items = larger blank area
                         * more items = smaller blank area
                         */
                        $blankHeight = max(120, 580 - ($currentRows * 32));
                    @endphp

                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="left">{{ $item->description ?? '' }}</td>
                        <td>{{ number_format($item->price ?? 0, 2) }}</td>
                    </tr>
                    @endforeach

                    <tr class="blank-row">
                        <td style="height: {{ $blankHeight }}px;">&nbsp;</td>
                        <td style="height: {{ $blankHeight }}px;"></td>
                        <td style="height: {{ $blankHeight }}px;"></td>
                    </tr>

                    <tr class="total-row">
                        <td colspan="2">TOTAL</td>
                        <td>Rs {{ number_format($bill->total_amount ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>