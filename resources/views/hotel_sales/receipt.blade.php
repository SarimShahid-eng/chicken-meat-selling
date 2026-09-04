<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hotel Sales Report - {{ $hotelSale->voucher_no }}</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4 landscape;
            margin: 15mm 15mm;

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }

            @bottom-left {
                content: "Rajput Chicken Centre — Chicken Meat Sales Inventory Log";
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }
        }

        *,
        *::before,
        *::after {
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
            text-align: center;
        }

        .company-name {
            font-size: 22pt;
            font-weight: 800;
            color: #d97706;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            table-layout: fixed;
            /* Ensures strict adherence to column width percentages */
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

        /* Footer / Summary Row Styles */
        tfoot tr td {
            padding: 8px 12px;
            font-size: 9pt;
            border-bottom: 1px solid #e5e7eb;
        }

        tfoot tr.summary-header td {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #1a365d;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.5px;
            border-top: 2px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }

        tfoot tr.grand-total td {
            background-color: #fef3c7;
            font-weight: 700;
            color: #92400e;
            border-top: 2px solid #f59e0b;
            border-bottom: 2px solid #f59e0b;
            font-size: 9.5pt;
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
        <h1 class="company-name">Rajput Chicken Centre</h1>
        <h2 class="report-title">Sales Invoice — Voucher #{{ $hotelSale->voucher_no }}</h2>
        <p class="report-meta">
            Generated on: {{ now()->format('Y-m-d H:i') }}
        </p>
        <div style="font-size: 10.5pt; font-weight: 700; color: #2563eb; margin-top: 6px;">
            Customer: {{ $hotelSale->customer->name ?? '—' }}
            {{ isset($hotelSale->customer->region->name) ? '/ ' . $hotelSale->customer->region->name : '' }}
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Voucher</th>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 28%;">Product</th>
                    <th style="width: 14%; text-align: right;">Net Weight</th>
                    <th style="width: 13%; text-align: right;">Rate</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hotelSale->items as $hotelSaleItem)
                    <tr>
                        <td class="font-medium">
                            {{ $hotelSale->voucher_no }}
                        </td>
                        <td>
                            {{ $hotelSale->date ? $hotelSale->date->format('m-d-Y') : '—' }}
                        </td>
                        <td>
                            {{ $hotelSaleItem->product->name ?? '—' }}
                        </td>
                        <td class="text-right">
                            {{ number_format($hotelSaleItem->weight ?? 0, 2) }} kg
                        </td>
                        <td class="text-right">
                            @if (is_null($hotelSaleItem->rate))
                                <span class="text-red font-bold">Not final yet</span>
                            @else
                                {{ number_format($hotelSaleItem->rate, 2) }}
                            @endif
                        </td>
                        <td class="text-right font-medium">
                            {{ number_format($hotelSaleItem->amount ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <!-- Financial Summary Rows matching 6 total columns -->
            <tfoot>
                <tr class="summary-header">
                    <td colspan="6" class="text-center">
                        Financial Summary Breakdown
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-medium">
                        Total Sale Amount:
                    </td>
                    <td class="text-right font-bold">
                        {{ number_format($hotelSale->total_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-medium">
                        Amount Received in this Sale:
                    </td>
                    <td class="text-right">
                        {{ number_format(@$hotelSale->customerPayment->amount ?? 0, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-medium">
                        Remaining Balance Out of This Sale:
                    </td>
                    <td class="text-right text-red">
                        -{{ number_format(($hotelSale->total_amount ?? 0) - (@$hotelSale->customerPayment->amount ?? 0), 2) }}
                    </td>
                </tr>
                <tr class="grand-total">
                    <td colspan="5" class="text-right">
                        Previous Customer Balance:
                    </td>
                    <td class="text-right">
                        {{ number_format(@$previousBalance ?? 0, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</body>

</html>
