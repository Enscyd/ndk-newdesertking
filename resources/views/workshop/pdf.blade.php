<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .bill-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .bill-info td {
            padding: 5px;
        }

        .bill-info .right {
            text-align: right;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f3f4f6;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .total-box {
            margin-top: 20px;
            width: 100%;
        }

        .total-box td {
            padding: 10px;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 11px;
            color: white;
        }

        .paid { background: #16a34a; }
        .unpaid { background: #dc2626; }

    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h1>Workshop Invoice</h1>
        <p>NDK</p>
      
    </div>

    <!-- BILL INFO -->
    <table class="bill-info">
        <tr>
            <td>
                <strong>Bill No:</strong> {{ $bill->bill_no }}<br>
                <strong>Vehicle:</strong> {{ $bill->vehicle_no }}<br>
                <strong>Name:</strong> {{ $bill->name ?? '-' }}
            </td>

            <td class="right">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($bill->date)->format('d M Y') }}<br>

                <strong>Status:</strong>
                    @if($bill->payment_status == 'PAID')
                        <strong style="color:#16a34a;">PAID</strong>
                    @else
                        <strong style="color:#dc2626;">UNPAID</strong>
                    @endif
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
<table class="table">
    <thead>
        <tr>
            <th style="width:10%;">S.No</th>
            <th style="width:60%;">Description</th>
            <th style="width:30%;">Price</th>
        </tr>
    </thead>

    <tbody>
        @foreach($bill->items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ number_format($item->price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

    <!-- TOTAL -->
    <table class="total-box">
        <tr>
            <td class="total">
                Total: Rs {{ number_format($bill->total_amount, 2) }}
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Powered by npxsoft.com</p>
    </div>

</div>

</body>
</html>