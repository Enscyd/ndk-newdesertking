<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:10px;
}

.header{
    text-align:center;
    margin-bottom:10px;
}

.header h2{
    margin:0;
}

.report-info{
    margin-bottom:8px;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    display:table-header-group;
}

th,td{
    border:1px solid #000;
    padding:3px;
    word-break:break-word;
}

th{
    background:#eeeeee;
    text-align:center;
    font-weight:bold;
}

.center{
    text-align:center;
}

.amount{
    text-align:right;
}

.total{
    font-weight:bold;
    background:#f3f3f3;
}

.footer{
    position:fixed;
    bottom:10px;
    left:0;
    right:0;
    text-align:center;
    font-size:10px;
}

</style>

</head>

<body>

<div class="header">
<h2>Trip Report</h2>
</div>

<div class="report-info">
Generated Date : {{ date('d-m-Y H:i') }}
</div>

<table>

<!-- COLUMN WIDTHS (Dompdf friendly) -->

<colgroup>
<col width="25">  <!-- sr.No -->
<col width="130"> <!-- Company -->
<col width="170"> <!-- Destination -->
<col width="140"> <!-- Driver -->
<col width="120"> <!-- Truck -->
<col width="80">  <!-- Trip Type -->
<col width="130"> <!-- Driver Amount -->
<col width="90"> <!-- Trip Date -->
<col width="90"> <!-- Trip Amount -->
<col width="35"> <!-- Omani -->
<col width="80"> <!-- Omani Name -->
<col width="80"> <!-- Omani Amount --> 

</colgroup>

<thead>

<tr>
<th>sr.No</th>
<th>Company</th>
<th>Destination</th>
<th>Driver</th>
<th>Truck</th>
<th>Trip<br>Type</th>
<th>Driver<br>Amount</th>
<th>Trip<br>Date</th>
<th>Trip<br>Amount</th>
<th>Omani</th>
<th>Omani Name</th>
<th>Omani<br>Amount</th>
</tr>

</thead>

<tbody>

@php
$sr = 1;
@endphp

@foreach($trips as $trip)

<tr>

<td class="center">{{ $sr++ }}</td>

<td>{{ $trip->company->name ?? '' }}</td>

<td>{{ $trip->destination->name ?? '' }}</td>

<td>{{ $trip->employee->employeeName ?? '' }}</td>

<td class="center">{{ $trip->truck->truckNumber ?? '' }}</td>

<td class="center">{{ $trip->tripType }}</td>

<td class="amount">{{ number_format($trip->driverAmount,2) }}</td>

<td class="center">
{{ \Carbon\Carbon::parse($trip->tripDate)->format('d-m-Y') }}
</td>

<td class="amount">{{ number_format($trip->tripAmount,2) }}</td>

<td class="center">{{ $trip->isOmani }}</td>

<td>{{ $trip->omaniName }}</td>

<td class="amount">{{ number_format($trip->omaniAmount,2) }}</td>

</tr>

@endforeach


<tr class="total">

<td colspan="6">Totals</td>

<td class="amount">{{ number_format($totalDriver,2) }}</td>

<td></td>

<td class="amount">{{ number_format($totalTrip,2) }}</td>

<td></td>

<td></td>

<td class="amount">{{ number_format($totalOmani,2) }}</td>

</tr>

</tbody>

</table>


<div class="footer">
Page <span class="pagenum"></span>
</div>


<script type="text/php">
if ( isset($pdf) ) {
    $x = 500;
    $y = 820;
    $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $size = 9;
    $pdf->page_text($x, $y, $text, $font, $size);
}
</script>


</body>
</html>