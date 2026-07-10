<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Inventory Report</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4 landscape; /* Landscape format ensures your 7 core metrics columns align smoothly without overlapping */
            margin: 15mm 15mm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }
            @bottom-left {
                content: "Chicken Meat Sales Inventory Log";
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #2d3748;
            background-color: #ffffff;
            font-size: 9.5pt;
            line-height: 1.4;
        }

        /* Document Header Styling */
        .header-container {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 22pt;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 4px 0;
            letter-spacing: -0.025em;
        }

        .report-meta {
            font-size: 9pt;
            color: #4a5568;
            margin: 0;
        }

        /* Print-Safe Structural Table Styles */
        .table-container {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background-color: #f8fafc;
        }

        th {
            padding: 10px 12px;
            font-weight: 600;
            font-size: 8.5pt;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        tr {
            page-break-inside: avoid;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        td {
            padding: 10px 12px;
            font-size: 9pt;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        /* Typography & Helper Classes */
        .font-medium {
            font-weight: 500;
            color: #111827;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-red {
            color: #ef4444;
        }

        .font-bold {
            font-weight: 700;
        }

        .empty-state-padding {
            padding: 60px 0;
        }

        .sub-text {
            font-size: 8pt;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="report-title">Sales Ledger</h1>
        <p class="report-meta">
            Generated on: {{ now()->format('Y-m-d H:i') }} | Total Dynamic Records: {{ count($data) }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Voucher</th>
                    <th style="width: 22%;">Customer / Region</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 16%;">Product</th>
                    <th style="width: 11%; text-align: right;">Crate Qty</th>
                    <th style="width: 13%; text-align: right;">Net Weight</th>
                    <th style="width: 14%; text-align: right;">Rate</th>
                    <th style="width: 13%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $sale)
                    <tr>
                        <td class="font-medium">
                            {{ $sale->voucher_no }}
                        </td>
                        <td class="font-medium">
                            {{ $sale->customer->name }}
                            <span class="sub-text">{{ $sale->customer->region->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            {{ $sale->date ? $sale->date->format('m-d-Y') : '—' }}
                        </td>
                        <td>
                            {{ $sale->product->name ?? '—' }}
                        </td>
                        <td class="text-right">
                            {{ number_format($sale->crate_qty ?? 0) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($sale->netweight ?? 0, 2) }} kg
                        </td>
                        <td class="text-right">
                            @if(is_null($sale->rate))
                                <span class="text-red font-bold">Not final yet</span>
                            @else
                                {{ number_format($sale->rate, 2) }}
                            @endif
                        </td>
                        <td class="text-right font-medium">
                            {{ is_null($sale->rate) ? '—' : number_format($sale->total_amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #4b5563; margin: 0; font-size: 11pt;">No data found</p>
                            <p style="color: #9ca3af; margin: 4px 0 0 0; font-size: 9pt;">There are no recorded active data matching this ledger query.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
