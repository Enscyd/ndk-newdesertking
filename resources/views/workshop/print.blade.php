<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $bill->bill_no ?? 'Workshop' }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            background: #e5e7eb;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: 0;
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
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.5px;
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
            background: #e6e6e6 !important;
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
            background: #f0f0f0 !important;
        }

        tr {
            page-break-inside: avoid;
        }

        @media print {
            body {
                background: #fff;
            }

            .page-container {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                height: 297mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- SCREEN-ONLY ACTION BAR -->
    <div class="no-print" style="position: fixed; top: 12px; right: 20px; z-index: 9999; display: flex; gap: 8px;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            🖨️ Print
        </button>
        <button onclick="window.close()" style="background: #4b5563; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer;">
            Close
        </button>
    </div>

    <div class="page-container">

        @if(file_exists(public_path('storage/workshop.jpg')))
            <img src="{{ asset('storage/workshop.jpg') }}" class="bg" alt="Letterhead">
        @endif

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
                    <td><strong>Status:</strong> {{ $bill->payment_status ?? '' }}</td>
                </tr>
            </table>

            <div class="table-area">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th style="width:8%">S.No</th>
                            <th style="width:47%">Description</th>
                            <th style="width:13%">Qty</th>
                            <th style="width:16%">Rate ({{ \App\Helpers\CurrencyHelper::symbol() }})</th>
                            <th style="width:16%">Amount ({{ \App\Helpers\CurrencyHelper::symbol() }})</th>
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
                            $calculatedTotal = 0;
                        @endphp

                        @foreach($items as $index => $item)
                        @php
                            $itemQty = isset($item->qty) && (float)$item->qty > 0 ? (float)$item->qty : 1;
                            $itemRate = isset($item->rate) && (float)$item->rate > 0 ? (float)$item->rate : (float)($item->price ?? 0);
                            $itemAmount = isset($item->price) ? (float)$item->price : ($itemQty * $itemRate);
                            $calculatedTotal += $itemAmount;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="left">{{ $item->description ?? '' }}</td>
                            <td>{{ $itemQty == (int)$itemQty ? (int)$itemQty : number_format($itemQty, 2) }}</td>
                            <td>{{ number_format($itemRate, 3) }}</td>
                            <td>{{ number_format($itemAmount, 3) }}</td>
                        </tr>
                        @endforeach

                        <tr class="blank-row">
                            <td style="height: {{ $blankHeight }}px;">&nbsp;</td>
                            <td style="height: {{ $blankHeight }}px;"></td>
                            <td style="height: {{ $blankHeight }}px;"></td>
                            <td style="height: {{ $blankHeight }}px;"></td>
                            <td style="height: {{ $blankHeight }}px;"></td>
                        </tr>

                        <tr class="total-row">
                            <td colspan="4">TOTAL</td>
                            <td>{{ \App\Helpers\CurrencyHelper::format($calculatedTotal, 3) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</body>
</html>
