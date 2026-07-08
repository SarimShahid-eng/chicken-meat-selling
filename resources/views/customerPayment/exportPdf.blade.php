<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Payments Report</title>
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
                content: "Customer Payments Ledger";
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
            vertical-align: top; /* Standard alignment to cleanly support wrapped descriptions */
        }

        /* Typography & Helper Classes */
        .font-medium {
            font-weight: 500;
            color: #1a202c;
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

        /* Type Status Badges */
        .badge-type {
            font-size: 8.5pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            background-color: #edf2f7;
            color: #2d3748;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="report-title">Customer Payments Ledger</h1>
        <p class="report-meta">
            Generated on: {{ now()->format('Y-m-d H:i') }} | Total Records: {{ count($data) }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Customer Name</th>
                    <th style="width: 15%; text-align: right;">Amount</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 15%;">Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $customerPayment)
                    <tr>
                        <td class="font-medium">
                            {{ $customerPayment->customer->name }}
                        </td>
                        <td class="text-right font-medium">
                            {{ number_format($customerPayment->amount, 2) }}
                        </td>
                        <td style="word-wrap: break-word; max-width: 250px;">
                            {{ $customerPayment->description ?? 'No description available.' }}
                        </td>
                        <td>
                            {{ $customerPayment->date ? $customerPayment->date->format('Y-m-d') : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-type">
                                {{ ucfirst($customerPayment->type) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #718096; margin: 0; font-size: 11pt;">No payments found</p>
                            <p class="text-muted" style="margin: 5px 0 0 0; font-size: 9pt;">There are no recorded customer payment items matching this criteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
