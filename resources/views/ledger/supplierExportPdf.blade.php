<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Account Ledger Statement</title>
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

        /* Inline vehicle number badge (used directly in the main ledger rows) */
        .veh-no {
            display: inline-block;
            width: 13px;
            height: 13px;
            line-height: 13px;
            font-size: 6.5pt;
            font-weight: 700;
            text-align: center;
            color: #ffffff;
            background-color: #d97706;
            border-radius: 50%;
            margin-right: 3px;
        }

        /* PDF Document Layout Configuration */
        @page {
            size: A4 portrait;
            /* Five structural columns fit neatly in a portrait orientation */
            margin: 20mm 15mm;

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #718096;
            }

            @bottom-left {
                content: "Rajput Chicken Centre — Supplier Statement of Account";
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
            background-color: #fef3c7 !important;
            /* Soft distinct gold accent */
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
            color: #dc2626;
            /* Crimson text indicating funds outgoing/paid out */
            font-weight: 500;
        }

        .text-credit {
            color: #16a34a;
            /* Distinct deep green representing received stock liability volume */
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

        .product-text {
            font-size: 8pt;
            color: #b45309;
            display: block;
            margin-top: 2px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <h1 class="company-name">Rajput Chicken Centre</h1>
        <h2 class="report-title">Supplier Account Ledger Statement</h2>
        <div style="font-size: 10.5pt; font-weight: 700; color: #2563eb; margin-bottom: 4px;">
            Supplier: {{ $supplier->name ?? ($supplier->name ?? 'All Account Holders') }}
        </div>
        <p class="report-meta">
            Statement Period: From {{ date('d-M-Y', strtotime($fromDate)) }} onwards | Generated:
            {{ now()->format('Y-m-d H:i') }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 31%;">Description</th>
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
                        $running += $creditVal - $debitVal;
                        $hasVehicles =
                            $entry->sort_order == 1 && isset($entry->vehicles) && count($entry->vehicles) > 0;
                    @endphp
                    @if ($hasVehicles)
                        @php $vehicleCount = count($entry->vehicles); @endphp
                        @foreach ($entry->vehicles as $vehicle)
                            @php
                                $vehicleAmount = $vehicle->netweight * ($entry->rate ?? 0);
                                $vehicleDebit = $vehicle->debit ?? 0;
                                $vehicleCredit = $vehicle->credit ?? $vehicleAmount;
                            @endphp
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $vehicleCount }}" style="color: #6b7280; vertical-align: middle;">
                                        {{ date('d-M-Y', strtotime($entry->date)) }}
                                    </td>
                                @endif
                                <td class="font-medium" style="color: #111827;">
                                    <span class="veh-no">{{ $loop->iteration }}</span>
                                    {{ $vehicle->name }}
                                    (<span class="font-medium">Product:
                                        {{ $entry->product_name }}
                                    </span>)
                                    <span class="product-text">
                                        {{ number_format($vehicle->netweight, 2) }} Kg
                                        @if ($entry->rate)
                                            &nbsp;@&nbsp;Rs. {{ number_format($entry->rate, 2) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-right text-debit">
                                    {{ $vehicleDebit ? 'Rs. ' . number_format($vehicleDebit, 2) : '—' }}
                                </td>
                                <td class="text-right text-credit">
                                    {{ $vehicleCredit ? 'Rs. ' . number_format($vehicleCredit, 2) : '—' }}
                                </td>
                                @if ($loop->last)
                                    <td class="text-right font-semibold" style="color: #111827;">
                                        Rs. {{ number_format($running, 2) }}
                                    </td>
                                @else
                                    <td class="text-right" style="color: #d1d5db;">—</td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="color: #6b7280;">
                                {{ date('d-M-Y', strtotime($entry->date)) }}
                            </td>
                            <td class="font-medium" style="color: #111827;">
                                {{ $entry->description }}
                                @if (@$entry->product_name)
                                    <span class="product-text">{{ $entry->product_name }}</span>
                                @endif
                                @if ($entry->rate)
                                    <span class="product-text">Rate: {{ $entry->rate }}</span>
                                @endif
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
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="text-center empty-state-padding">
                            <p class="font-medium" style="color: #6b7280; margin: 0; font-size: 11pt;">No supplier
                                activity found</p>
                            <p style="color: #9ca3af; margin: 4px 0 0 0; font-size: 9pt;">No ledger activity
                                transactions logged within selected parameters.</p>
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
                    <span class="summary-value" style="color: #0f172a;">Rs. {{ number_format($creditSum - $debitSum, 2) }}</span>
                </td>
                <td style="width: 20%; text-align: right;">
                    <span style="font-size: 7.5pt; color: #64748b; display: block; text-transform: uppercase;">Closing Balance</span>
                    <span class="summary-value" style="color: #0f172a;">Rs. {{ number_format($running, 2) }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
