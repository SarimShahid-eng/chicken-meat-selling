<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Account Ledger Statement</title>
    <style>
        .summary-container {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .summary-card-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 2px solid #1e293b;
            border-radius: 4px;
        }

        .summary-card-table td {
            padding: 12px 15px;
            vertical-align: middle;
            border: none;
        }

        .summary-label {
            font-size: 10pt;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 10pt;
            font-weight: 700;
            white-space: nowrap;
        }

        /* PDF Document Layout Configuration */
        @page {
            size: A4 portrait;
            margin: 15mm 10mm 20mm 10mm;
            /* Added bottom margin space for page footer */

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }

            @bottom-left {
                content: "Rajput Chicken Centre — Customer Statement of Account";
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

        /* Structural Table Styles */
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

        tr {
            page-break-inside: avoid;
        }

        .opening-balance-row {
            background-color: #fef3c7 !important;
            font-weight: 500;
            color: #78350f;
        }

        tbody tr:nth-child(even):not(.opening-balance-row) {
            background-color: #f9fafb;
        }

        td {
            padding: 8px 6px;
            font-size: 8.5pt;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        tfoot tr td {
            border-top: 2px solid #1e293b;
            border-bottom: 2px solid #1e293b;
            background-color: #f8fafc;
            padding: 10px 6px;
        }

        /* Inline Badges Optimized for Dompdf */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: 600;
            border-radius: 4px;
            text-transform: uppercase;
            margin-right: 4px;
        }

        .badge-sale {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-hotel {
            background-color: #f3e8ff;
            color: #6b21a8;
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

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        .italic {
            font-style: italic;
        }

        .sub-text {
            font-size: 7.5pt;
            color: #6b7280;
        }

        .details-text {
            font-size: 7pt;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }

        /* Statement Summary Footer Styles */
        .summary-wrapper {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 25px;
        }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }

        .summary-card-title {
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .summary-card-value {
            font-size: 11pt;
            font-weight: 700;
        }

        /* Footer Signatures */
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .signature-cell {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 15px;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 45px;
            padding-top: 5px;
            font-size: 8pt;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <h1 class="company-name">Rajput Chicken Centre</h1>
        <h2 class="report-title">Customer Account Ledger Statement</h2>
        <div style="font-size: 10.5pt; font-weight: 700; color: #2563eb; margin-bottom: 4px;">
            Customer: {{ $customer->name ?? 'All Account Holders' }}
        </div>
        <p class="report-meta">
            Statement Period: {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }} |
            Generated: {{ now()->format('d-M-Y H:i') }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if (isset($ledgerEntries) && $ledgerEntries->first() && isset($ledgerEntries->first()->customer_name))
                        <th style="width: 18%;">Customer</th>
                        <th style="width: 24%;">Description / Reference</th>
                    @else
                        <th style="width: 42%;">Description / Reference</th>
                    @endif
                    <th style="width: 15%; text-align: right;">Debit (Sales)</th>
                    <th style="width: 15%; text-align: right;">Credit (Payments)</th>
                    <th style="width: 16%; text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>

                <!-- Opening Balance Row Entry -->
                <tr class="opening-balance-row">
                    <td>{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                    @if (isset($ledgerEntries) && $ledgerEntries->first() && isset($ledgerEntries->first()->customer_name))
                        <td>—</td>
                    @endif
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

                        $running += $debitVal - $creditVal;
                        $hasItems = $entry->type === 'hotel_sale' && isset($entry->items) && count($entry->items) > 0;
                    @endphp
                    <tr>
                        <td style="color: #6b7280;">
                            {{ date('d-M-Y', strtotime($entry->date)) }}
                        </td>

                        @if (isset($entry->customer_name))
                            <td class="font-semibold" style="color: #1f2937;">
                                {{ $entry->customer_name }}
                            </td>
                        @endif

                        <td>
                            @if ($entry->type === 'sale')
                                <span class="badge badge-sale">Regular Sale</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>
                                @if ($entry->product_name)
                                    <span class="product-text">{{ $entry->product_name }}</span>
                                @endif
                                <span class="details-text">
                                    Crates: {{ $entry->sale_crate_qty ?? 0 }}
                                    &nbsp;|&nbsp; Weight: {{ number_format($entry->sale_total_weight ?? 0, 2) }} kg
                                    &nbsp;|&nbsp; Cut: {{ number_format($entry->sale_weight_cut ?? 0, 2) }} kg
                                    &nbsp;|&nbsp; Net: {{ number_format($entry->sale_netweight ?? 0, 2) }} kg
                                    &nbsp;|&nbsp; Rate:
                                    {{ $entry->sale_rate ? 'Rs. ' . number_format($entry->sale_rate, 2) : '—' }}
                                </span>
                            @elseif($entry->type === 'hotel_sale')
                                <span class="badge badge-hotel">Hotel Sale</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>
                                @if ($hasItems)
                                    <span class="product-text">{{ count($entry->items) }} item(s)</span>
                                @endif
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

                    @if ($hasItems)
                        <tr class="items-subrow">
                            <td colspan="{{ isset($entry->customer_name) ? 6 : 5 }}">
                                <span class="items-label">Items Sold</span>
                                <table class="items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Product</th>
                                            <th style="width: 20%; text-align: right;">Weight</th>
                                            <th style="width: 20%; text-align: right;">Rate</th>
                                            <th style="width: 20%; text-align: right;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($entry->items as $item)
                                            <tr>
                                                <td class="font-semibold">{{ $item->product->name ?? '—' }}</td>
                                                <td class="text-right">{{ number_format($item->weight, 2) }}</td>
                                                <td class="text-right">
                                                    {{ $item->rate ? number_format($item->rate, 2) : '—' }}</td>
                                                <td class="text-right font-semibold">Rs.
                                                    {{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="{{ isset($ledgerEntries->first()->customer_name) ? 6 : 5 }}" class="text-center"
                            style="padding: 40px 0;">
                            <p style="color: #6b7280; font-weight: 500; font-size: 10pt; margin: 0;">No ledger records
                                found</p>
                            <p style="color: #9ca3af; font-size: 8.5pt; margin: 4px 0 0 0;">No transactions logged
                                within the selected date range.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
    <div class="summary-container">
        <table class="summary-card-table">
            <tr>
                <td style="width: 20%;" class="summary-label">
                    Statement Summary
                </td>
                <td style="width: 20%; text-align: right;">
                    <span style="font-size: 7.5pt; color: #64748b; display: block; text-transform: uppercase;">Total
                        Paid (Debit)</span>
                    <span class="summary-value text-debit">Rs. {{ number_format($debitSum, 2) }}</span>
                </td>
                <td style="width: 20%; text-align: right;">
                    <span style="font-size: 7.5pt; color: #64748b; display: block; text-transform: uppercase;">Total
                        Purchases (Credit)</span>
                    <span class="summary-value text-credit">Rs. {{ number_format($creditSum, 2) }}</span>
                </td>
                 <td style="width: 20%; text-align: right;">
                    <span style="font-size: 7.5pt; color: #64748b; display: block; text-transform: uppercase;">Monthly
                        Net Change</span>
                    <span class="summary-value" style="color: #0f172a;">Rs. {{ number_format($debitSum - $creditSum, 2) }}</span>
                </td>
                <td style="width: 20%; text-align: right;">
                    <span style="font-size: 7.5pt; color: #64748b; display: block; text-transform: uppercase;">Closing
                        Balance</span>
                    <span class="summary-value" style="color: #0f172a;">Rs. {{ number_format($running, 2) }}</span>
                </td>
            </tr>
        </table>
    </div>


</body>

</html>
