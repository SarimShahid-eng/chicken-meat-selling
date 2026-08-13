<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Region-wise Supplier Ledger Statement</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4 portrait;
            margin: 20mm 15mm;

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }

            @bottom-left {
                content: "Rajput Chicken Centre — Region-wise Supplier Ledger";
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
            /* Amber accent matching UI theme */
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
        }

        thead {
            background-color: #f8fafc;
        }

        th {
            padding: 10px 8px;
            font-weight: 700;
            font-size: 8pt;
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
            background-color: #fef3c7 !important;
            /* Soft distinct gold accent */
            font-weight: 500;
            color: #78350f;
        }

        tbody tr:nth-child(even):not(.opening-balance-row) {
            background-color: #f9fafb;
        }

        td {
            padding: 9px 8px;
            font-size: 8.5pt;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        /* Dompdf-Safe Summary Table Card Styles */
        .summary-box {
            margin-top: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            background-color: #ffffff;
            page-break-inside: avoid;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .summary-card {
            padding: 8px 10px;
            border-radius: 5px;
            text-align: right;
        }

        .card-gray {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
        }

        .card-dark {
            background-color: #e3e8f3;
            color: #ffffff;
        }

        .summary-label {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .summary-value {
            font-size: 10pt;
            font-weight: 800;
            display: block;
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
            color: #dc2626;
            /* Crimson text indicating funds outgoing/paid out */
            font-weight: 600;
        }

        .text-credit {
            color: #16a34a;
            /* Distinct deep green representing received stock liability volume */
            font-weight: 600;
        }

        .empty-state-padding {
            padding: 50px 0;
        }

        .sub-text {
            font-size: 7.5pt;
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
        <h1 class="company-name">Rajput Chicken Centre</h1>
        <h2 class="report-title">Region-Wise Supplier Ledger Statement</h2>
        <p class="report-meta">
            Statement Period: From {{ date('d-M-Y', strtotime($fromDate)) }} onwards | Generated:
            {{ now()->format('Y-m-d H:i') }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 13%;">Date</th>
                    <th style="width: 22%;">Supplier</th>
                    <th style="width: 23%;">Description / Reference</th>
                    <th style="width: 14%; text-align: right;">Debit (Paid)</th>
                    <th style="width: 14%; text-align: right;">Credit (Purchase)</th>
                    <th style="width: 14%; text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>

                <!-- Opening Balance Row -->
                <tr class="opening-balance-row">
                    <td>{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                    <td class="font-bold">All Regional Suppliers</td>
                    <td class="italic">Opening Balance Carriage</td>
                    <td class="text-right">—</td>
                    <td class="text-right">—</td>
                    <td class="text-right font-bold">Rs. {{ number_format($openingBalance, 2) }}</td>
                </tr>

                @php
                    $running = $openingBalance;
                    $debitSum = 0;
                    $creditSum = 0;
                @endphp

                @forelse($ledgerEntries as $entry)
                    @php
                        $debitVal = $entry->debit ?? 0;
                        $creditVal = $entry->credit ?? 0;

                        $debitSum += $debitVal;
                        $creditSum += $creditVal;

                        // Supplier Account Logic: Credits (Purchases) increase liability balance, Debits (Payments) reduce it.
                        $running += $creditVal - $debitVal;
                    @endphp
                    <tr>
                        <td style="color: #6b7280;">
                            {{ date('d-M-Y', strtotime($entry->date)) }}
                        </td>
                        <td class="font-semibold" style="color: #1f2937;">
                            {{ $entry->supplier_name }}
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
                        <td colspan="6" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #6b7280; margin: 0; font-size: 11pt;">No supplier
                                activity
                                found</p>
                            <p style="color: #9ca3af; margin: 4px 0 0 0; font-size: 9pt;">No ledger activity
                                transactions
                                logged for this region within the selected date range.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Statement Summary Block -->
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td style="width: 25%; padding: 0; border: none;">
                    <div class="summary-card card-gray" style="text-align: left;">
                        <span class="summary-label" style="color: #94a3b8;">Statement Summary</span>
                        <span class="summary-value" style="color: #1e293b; font-size: 9pt;">Regional Ledger</span>
                    </div>
                </td>
                <td style="width: 25%; padding: 0; border: none;">
                    <div class="summary-card card-gray">
                        <span class="summary-label" style="color: #64748b;">Total Sales (Debit)</span>
                        <span class="summary-value text-debit">Rs. {{ number_format($debitSum, 2) }}</span>
                    </div>
                </td>
                <td style="width: 25%; padding: 0; border: none;">
                    <div class="summary-card card-gray">
                        <span class="summary-label" style="color: #64748b;">Total Paid (Credit)</span>
                        <span class="summary-value text-credit">Rs. {{ number_format($creditSum, 2) }}</span>
                    </div>
                </td>
                <td style="width: 25%; padding: 0; border: none;">
                    <div class="summary-card card-dark">
                        <span class="summary-label" style="color: #121314;">Closing Balance</span>
                        <span class="summary-value" style="color: #181515;">Rs. {{ number_format($running, 2) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
