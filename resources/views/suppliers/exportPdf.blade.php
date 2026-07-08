<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suppliers Report</title>
    <style>
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
                content: "Suppliers Directory Report";
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

        /* Header block styling */
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

        /* Table structural styling for PDF */
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

        /* Simple clean alternating rows */
        tbody tr:nth-child(even) {
            background-color: #fcfdfd;
        }

        td {
            padding: 12px;
            font-size: 9.5pt;
            color: #4a5568;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
        }

        /* Typography accents and alignment */
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
            color: #a0aec0;
        }

        .empty-state-padding {
            padding: 50px 0;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="report-title">Suppliers Directory</h1>
        <p class="report-meta">Generated on: {{ now()->format('Y-m-d H:i') }} | Total Records: {{ count($data) }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Name / Region</th>
                    <th style="width: 25%;">Phone Number</th>
                    <th style="width: 20%; text-align: right;">Opening Balance</th>
                    <th style="width: 20%; text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $supplier)
                    <tr>
                        <td class="font-medium">
                            {{ $supplier->name }}
                            @if($supplier->region)
                                <br><span style="font-size: 8.5pt; color: #718096; font-weight: normal;">{{ $supplier->region->name }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $supplier->phone_number ?? 'No phone number available' }}
                        </td>
                        <td class="text-right font-medium">
                            {{ number_format($supplier->opening_balance, 2) }}
                        </td>
                        <td class="text-right font-medium" style="color: {{ $supplier->current_balance < 0 ? '#b7791f' : '#2d3748' }}">
                            {{ number_format($supplier->current_balance, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #718096; margin: 0; font-size: 11pt;">No suppliers found</p>
                            <p class="text-muted" style="margin: 5px 0 0 0; font-size: 9pt;">There are no supplier records available to generate this print report.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
