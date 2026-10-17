<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Accounts Ledger</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px;
            color: #111827;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* ACTIONS BAR (HIDDEN IN PRINT) */
        .no-print {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }

        /* HEADER */
        .report-header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .report-subtitle {
            font-size: 12px;
            color: #4b5563;
            margin-top: 4px;
        }

        .print-timestamp {
            font-size: 11px;
            color: #6b7280;
            text-align: right;
        }

        /* SUMMARY BAR */
        .summary-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .summary-card {
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .summary-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-val {
            font-size: 13px;
            font-weight: 700;
        }

        .val-blue { color: #1d4ed8; }
        .val-green { color: #059669; }
        .val-red { color: #dc2626; }
        .val-net { color: #111827; }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th {
            background: #374151;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #374151;
        }

        td {
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge-income {
            background: #d1fae5;
            color: #065f46;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            display: inline-block;
        }

        .badge-expense {
            background: #fee2e2;
            color: #991b1b;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            display: inline-block;
        }

        .row-opening {
            background: #fef08a !important;
            font-weight: 700;
        }
        .row-opening td {
            border-color: #fde047;
            background: #fef08a !important;
        }

        .row-summary {
            background: #1f2937 !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 11px;
        }
        .row-summary td {
            background: #1f2937 !important;
            color: #ffffff !important;
            border-color: #1f2937;
            padding: 8px 10px;
        }

        /* PRINT MEDIA OVERRIDES */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .print-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            th {
                background: #374151 !important;
                color: #ffffff !important;
            }

            .row-opening td {
                background: #fef08a !important;
            }

            .row-summary td {
                background: #1f2937 !important;
                color: #ffffff !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- NO-PRINT ACTIONS -->
    <div class="print-container">
        <div class="no-print">
            <button type="button" class="btn btn-secondary" onclick="window.close()">
                ✕ Close
            </button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                🖨️ Print
            </button>
        </div>

        <!-- REPORT HEADER -->
        <div class="report-header">
            <div>
                <h1 class="report-title">NDK Accounts Ledger Report</h1>
                <div class="report-subtitle">
                    Period: 
                    @if(!empty($month) && !empty($year))
                        <strong>{{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}</strong>
                    @elseif(!empty($year))
                        <strong>Year {{ $year }}</strong>
                    @else
                        <strong>All Records</strong>
                    @endif
                </div>
            </div>
            <div class="print-timestamp">
                <div>Printed: {{ now()->format('d M Y, h:i A') }}</div>
                <div>Currency: <strong>OMR</strong></div>
            </div>
        </div>

        <!-- KPI SUMMARY BAR -->
        <div class="summary-bar">
            <div class="summary-card">
                <div class="summary-label">Opening Balance</div>
                <div class="summary-val val-blue">{{ number_format($opening, 3) }} OMR</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Income</div>
                <div class="summary-val val-green">+{{ number_format($income, 3) }} OMR</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Expense</div>
                <div class="summary-val val-red">-{{ number_format($expense, 3) }} OMR</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Net Balance</div>
                <div class="summary-val val-net">{{ number_format($net, 3) }} OMR</div>
            </div>
        </div>

        <!-- TABLE -->
        <table>
            <thead>
                <tr>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 10%;" class="text-center">Type</th>
                    <th style="width: 32%;">Description</th>
                    <th style="width: 18%;">Purpose</th>
                    <th style="width: 13%;" class="text-right">Amount (OMR)</th>
                    <th style="width: 13%;" class="text-right">Balance (OMR)</th>
                </tr>
            </thead>
            <tbody>
                <!-- OPENING ROW -->
                <tr class="row-opening">
                    <td colspan="5">
                        OPENING BALANCE
                    </td>
                    <td class="text-right">
                        {{ number_format($opening, 3) }}
                    </td>
                </tr>

                @if($accounts->count())
                    @foreach($accounts as $acc)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($acc->date)->format('d M Y') }}</td>
                        <td class="text-center">
                            <span class="{{ $acc->type == 'INCOME' ? 'badge-income' : 'badge-expense' }}">
                                {{ $acc->type }}
                            </span>
                        </td>
                        <td>{{ $acc->description ?? '-' }}</td>
                        <td>{{ $acc->purpose->name ?? '-' }}</td>
                        <td class="text-right font-semibold {{ $acc->type == 'INCOME' ? 'val-green' : 'val-red' }}">
                            {{ $acc->type == 'INCOME' ? '+' : '-' }}{{ number_format($acc->amount, 3) }}
                        </td>
                        <td class="text-right val-blue font-bold">
                            {{ number_format($acc->running_balance ?? 0, 3) }}
                        </td>
                    </tr>
                    @endforeach

                    <!-- FOOTER SUMMARY ROW -->
                    <tr class="row-summary">
                        <td colspan="2">INCOME: {{ number_format($income, 3) }}</td>
                        <td colspan="2">EXPENSE: {{ number_format($expense, 3) }}</td>
                        <td class="text-right">NET</td>
                        <td class="text-right">{{ number_format($net, 3) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px; color: #6b7280;">
                            No records found for this period
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

</body>
</html>
