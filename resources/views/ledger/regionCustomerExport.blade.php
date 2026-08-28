<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Region-wise Customer Ledger Statement</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4 portrait;
            margin: 15mm 10mm;

            @bottom-right {
                content: "Page " counter(page);
            }

            @bottom-left {
                content: "Rajput Chicken Centre — Region-wise Customer Ledger";
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }
        }

        .row-alt {
            background-color: #f9fafb;
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
            font-size: 8.5pt;
            line-height: 1.3;
        }

        /* Document Header Styling */
        .header-container {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .company-name {
            font-size: 20pt;
            font-weight: 800;
            color: #d97706;
            /* Amber accent */
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-meta {
            font-size: 8.5pt;
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
            padding: 8px 6px;
            font-weight: 700;
            font-size: 8pt;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #e5e7eb;
        }


        /* Highlight wrapper for the custom opening row balance */
        .opening-balance-row {
            background-color: #fef3c7 !important;
            /* Soft distinct gold accent */
            font-weight: 500;
            color: #78350f;
        }


        td {
            padding: 8px 6px;
            font-size: 8.5pt;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        /* Inline Badges Optimized for Dompdf */
        .badge {
            font-size: 7.5pt;
            font-weight: 600;
            margin-right: 4px;
        }

        .badge-sale {
            color: #1e40af;
        }

        .badge-hotel {
            color: #6b21a8;
        }

        /* Dompdf-Safe Summary Table Card Styles */
        .summary-box {
            margin-top: 15px;
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

        /* Typography & Alignment Helpers */
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
            font-weight: 600;
        }

        .text-credit {
            color: #16a34a;
            font-weight: 600;
        }

        .sub-text {
            font-size: 7.5pt;
            color: #6b7280;
        }

        .italic {
            font-style: italic;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <h1 class="company-name">Rajput Chicken Centre</h1>
        <h2 class="report-title">Region-Wise Customer Ledger Statement</h2>
        <p class="report-meta">
            @if (isset($regionName))
                <strong>Region: {{ $regionName }}</strong> |
            @endif
            Statement Period: {{ date('d-M-Y', strtotime($fromDate)) }} to
            {{ date('d-M-Y', strtotime($toDate ?? now())) }} | Generated: {{ now()->format('d-M-Y H:i') }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 11%;">Date</th>
                    <th style="width: 20%;">Customer</th>
                    <th style="width: 28%;">Description / Reference</th>
                    <th style="width: 14%; text-align: right;">Debit (Sales)</th>
                    <th style="width: 14%; text-align: right;">Credit (Paid)</th>
                    <th style="width: 13%; text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>

                <!-- Opening Balance Carriage Row -->
                <tr class="opening-balance-row">
                    <td>{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                    <td class="font-bold">All Regional Customers</td>
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

                @forelse($ledgerEntries as $index=> $entry)
                    @php
                        $debitVal = $entry->debit ?? 0;
                        $creditVal = $entry->credit ?? 0;

                        $debitSum += $debitVal;
                        $creditSum += $creditVal;

                        // Customer Accounting: Debits (Sales) increase balance, Credits (Payments) decrease it.
                        $running += $debitVal - $creditVal;
                    @endphp
                    <tr class="{{ $index % 2 === 1 ? 'row-alt' : '' }}">
                        <td style="color: #6b7280;">
                            {{ $entry->date_formatted }}
                        </td>
                        <td class="font-semibold" style="color: #1f2937;">
                            {{ $entry->customer_name }}
                        </td>
                        <td>
                            {{-- 1. Regular Sale Badge --}}
                            @if ($entry->type === 'sale')
                                <span class="badge badge-sale">Regular Sale</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>

                                {{-- 2. Hotel Sale Badge --}}
                            @elseif($entry->type === 'hotel_sale')
                                <span class="badge badge-hotel">Hotel Sale</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>

                                {{-- 3. Payments Logic --}}
                            @elseif($entry->type === 'payment')
                                @if (!empty($entry->sale_id))
                                    @if ($entry->reference === 'hotel_sale')
                                        <span class="badge badge-hotel">Hotel Sale Payment</span>
                                    @else
                                        <span class="badge badge-sale">Sale Payment</span>
                                    @endif
                                    <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>
                                @else
                                    <span style="color: #374151; font-weight: 500;">Payment</span>
                                    <span class="sub-text">(Ref: #{{ $entry->reference_id }})</span>
                                @endif
                            @else
                                {{-- Fallback Description --}}
                                <span class="font-medium" style="color: #111827;">{{ $entry->description }}</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>
                            @endif
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
                        <td colspan="6" class="text-center" style="padding: 40px 0;">
                            <p style="color: #6b7280; font-weight: 500; font-size: 10pt; margin: 0;">No customer
                                activity found</p>
                            <p style="color: #9ca3af; font-size: 8.5pt; margin: 4px 0 0 0;">No ledger activity
                                transactions logged for this region within selected parameters.</p>
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
                <td style="width: 20%; padding: 0; border: none;">
                    <div class="summary-card card-gray" style="text-align: left;">
                        <span class="summary-label" style="color: #94a3b8;">Statement Summary</span>
                        <span class="summary-value" style="color: #1e293b; font-size: 9pt;">Regional Ledger</span>
                    </div>
                </td>
                <td style="width: 20%; padding: 0; border: none;">
                    <div class="summary-card card-gray">
                        <span class="summary-label" style="color: #64748b;">Total Sales (Debit)</span>
                        <span class="summary-value text-debit">Rs. {{ number_format($debitSum, 2) }}</span>
                    </div>
                </td>
                <td style="width: 20%; padding: 0; border: none;">
                    <div class="summary-card card-gray">
                        <span class="summary-label" style="color: #64748b;">Total Paid (Credit)</span>
                        <span class="summary-value text-credit">Rs. {{ number_format($creditSum, 2) }}</span>
                    </div>
                </td>
                <td style="width: 20%; text-align: right;">
                    <span style="font-size: 7.5pt; color: #64748b; display: block; text-transform: uppercase;">
                        Net Change</span>
                    <span class="summary-value" style="color: #0f172a;">Rs.
                        {{ number_format($debitSum - $creditSum, 2) }}</span>
                </td>
                <td style="width: 20%; padding: 0; border: none;">
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
