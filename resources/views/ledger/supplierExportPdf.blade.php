<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Account Ledger Statement</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4 portrait; /* Five structural columns fit neatly in a portrait orientation */
            margin: 20mm 15mm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }
            @bottom-left {
                content: "Supplier Statement of Account — Internal Procurement Log";
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
            font-size: 20pt;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 4px 0;
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
            background-color: #f8fafc;
        }

        th {
            padding: 12px 10px;
            font-weight: 700;
            font-size: 8.5pt;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Highlight wrapper for the custom opening row balance */
        .opening-balance-row {
            background-color: #fef3c7 !important; /* Soft distinct gold accent */
            font-weight: 500;
            color: #78350f;
        }

        tbody tr:nth-child(even):not(.opening-balance-row) {
            background-color: #f9fafb;
        }

        td {
            padding: 11px 10px;
            font-size: 9pt;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        /* Typography & Ledger Alignment Helpers */
        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-debit {
            color: #dc2626; /* Crimson text indicating funds outgoing/paid out */
            font-weight: 500;
        }

        .text-credit {
            color: #16a34a; /* Distinct deep green representing received stock liability volume */
            font-weight: 500;
        }

        .empty-state-padding {
            padding: 50px 0;
        }

        .sub-text {
            font-size: 8pt;
            color: #9ca3af;
            display: block;
            margin-top: 2px;
        }

        .italic {
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="report-title">Supplier Ledger Statement</h1>
        <p class="report-meta">
            Statement Period: From {{ date('d-M-Y', strtotime($fromDate)) }} onwards | Generated: {{ now()->format('Y-m-d H:i') }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 31%;">Description / Reference</th>
                    <th style="width: 18%; text-align: right;">Debit (Amount Paid)</th>
                    <th style="width: 18%; text-align: right;">Credit (Purchase Vol)</th>
                    <th style="width: 18%; text-align: right;">Running Balance</th>
                </tr>
            </thead>
            <tbody>

                <tr class="opening-balance-row">
                    <td>{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                    <td class="italic">Opening Balance Carriage</td>
                    <td class="text-right">—</td>
                    <td class="text-right">—</td>
                    <td class="text-right font-bold">Rs. {{ number_format($openingBalance, 2) }}</td>
                </tr>

                @php $running = $openingBalance; @endphp

                @forelse($ledgerEntries as $entry)
                    @php
                        // Supplier Account Logic adjustment loop
                        $running += ($entry->credit ?? 0) - ($entry->debit ?? 0);
                    @endphp
                    <tr>
                        <td style="color: #6b7280;">
                            {{ date('d-M-Y', strtotime($entry->date)) }}
                        </td>
                        <td class="font-medium" style="color: #111827;">
                            {{ $entry->description }}
                            <span class="sub-text">Ref ID: #{{ $entry->reference_id }}</span>
                        </td>
                        <td class="text-right text-debit">
                            {{ $entry->debit ? 'Rs. ' . number_format($entry->debit, 2) : '—' }}
                        </td>
                        <td class="text-right text-credit">
                            {{ $entry->credit ? 'Rs. ' . number_format($entry->credit, 2) : '—' }}
                        </td>
                        <td class="text-right font-semibold" style="color: #111827;">
                            Rs. {{ number_format($running, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #6b7280; margin: 0; font-size: 11pt;">No supplier activity found</p>
                            <p style="color: #9ca3af; margin: 4px 0 0 0; font-size: 9pt;">No ledger activity transactions logged within selected parameters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
