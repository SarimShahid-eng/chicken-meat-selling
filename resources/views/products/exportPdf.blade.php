<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products Inventory Report</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4;
            margin: 20mm 15mm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }
            @bottom-left {
                content: "Products Inventory Status Report";
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
            font-size: 10pt;
            line-height: 1.5;
        }

        /* Document Header Styling */
        .header-container {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .report-title {
            font-size: 20pt;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 5px 0;
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
        }

        thead {
            background-color: #f7fafc;
        }

        th {
            padding: 10px 12px;
            font-weight: 600;
            font-size: 9pt;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #edf2f7;
        }

        tr {
            page-break-inside: avoid;
        }

        tbody tr:nth-child(even) {
            background-color: #fcfdfd;
        }

        td {
            padding: 12px;
            font-size: 9.5pt;
            color: #4a5568;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }

        /* Typography & Helper Classes */
        .font-medium {
            font-weight: 500;
            color: #1a202c;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #718096;
        }

        .empty-state-padding {
            padding: 50px 0;
        }

        /* Inventory Summary Badges */
        .badge-purchases {
            color: #92400e;
            background-color: #fef3c7;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8.5pt;
            display: inline-block;
        }

        .badge-sold {
            color: #1e40af;
            background-color: #dbeafe;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8.5pt;
            display: inline-block;
        }

        .badge-stock-normal {
            color: #166534;
            background-color: #dcfce7;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8.5pt;
            display: inline-block;
        }

        .badge-stock-low {
            color: #991b1b;
            background-color: #fee2e2;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8.5pt;
            display: inline-block;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="report-title">Products Inventory</h1>
        <p class="report-meta">
            Generated on: {{ now()->format('Y-m-d H:i') }} | Total Dynamic Lines: {{ count($data) }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Product</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 16%; text-align: right;">Total Purchases</th>
                    <th style="width: 16%; text-align: right;">Total Sold</th>
                    <th style="width: 18%; text-align: right;">Current Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $product)
                    <tr>
                        <td class="font-medium">
                            {{ $product->name }}
                        </td>
                        <td style="word-wrap: break-word; max-width: 200px;">
                            {{ $product->description ?? 'No description available.' }}
                        </td>
                        <td class="text-right">
                            <span class="badge-purchases font-semibold">
                                {{ number_format($product->total_purchased_weight ?? 0, 2) }} KG
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="badge-sold font-semibold">
                                {{ number_format($product->total_sold_weight ?? 0, 2) }} KG
                            </span>
                        </td>
                        <td class="text-right">
                            @if (($product->current_stock_weight ?? 0) < 10)
                                <span class="badge-stock-low">
                                    {{ number_format($product->current_stock_weight, 2) }} kg (Low)
                                </span>
                            @else
                                <span class="badge-stock-normal">
                                    {{ number_format($product->current_stock_weight, 2) }} kg
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #718096; margin: 0; font-size: 11pt;">No products found</p>
                            <p class="text-muted" style="margin: 5px 0 0 0; font-size: 9pt;">There are no active inventory items matching this index query.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
