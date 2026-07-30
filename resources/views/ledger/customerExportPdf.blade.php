<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Account Ledger Statement</title>
    <style>
        /* PDF Document Layout Configuration */
        @page {
            size: A4 portrait;
            margin: 15mm 10mm;
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

        *, *::before, *::after {
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
            color: #d97706; /* Amber accent */
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

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .text-debit {
            color: #dc2626;
            font-weight: 600;
        }

        .text-credit {
            color: #16a34a;
            font-weight: 600;
        }

        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .italic { font-style: italic; }

        .sub-text {
            font-size: 7.5pt;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="company-name">Rajput Chicken Centre</h1>
        <h2 class="report-title">Customer Account Ledger Statement</h2>
        <p class="report-meta">
            Statement Period: {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }} | Generated: {{ now()->format('d-M-Y H:i') }}
        </p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if(isset($entry->customer_name) || isset($ledgerEntries->first()->customer_name))
                        <th style="width: 18%;">Customer</th>
                    @endif
                    <th style="width: 32%;">Description / Reference</th>
                    <th style="width: 13%; text-align: right;">Debit (Sales)</th>
                    <th style="width: 13%; text-align: right;">Credit (Payments)</th>
                    <th style="width: 12%; text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>

                <!-- Opening Balance Row Entry -->
                <tr class="opening-balance-row">
                    <td>{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                    @if(isset($entry->customer_name) || isset($ledgerEntries->first()->customer_name))
                        <td class="font-bold">All Customers</td>
                    @endif
                    <td class="italic">Opening Balance Carriage</td>
                    <td class="text-right">—</td>
                    <td class="text-right">—</td>
                    <td class="text-right font-bold">Rs. {{ number_format($openingBalance, 2) }}</td>
                </tr>

                @php $running = $openingBalance; @endphp

                @forelse($ledgerEntries as $entry)
                    @php
                        $running += ($entry->debit ?? 0) - ($entry->credit ?? 0);
                    @endphp
                    <tr>
                        <td style="color: #6b7280;">
                            {{ date('d-M-Y', strtotime($entry->date)) }}
                        </td>

                        {{-- Customer Name column if region export --}}
                        @if(isset($entry->customer_name))
                            <td class="font-semibold" style="color: #1f2937;">
                                {{ $entry->customer_name }}
                            </td>
                        @endif

                        <td>
                            {{-- 1. Regular Sale Badge --}}
                            @if($entry->type === 'sale')
                                <span class="badge badge-sale">Regular Sale</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>

                            {{-- 2. Hotel Sale Badge --}}
                            @elseif($entry->type === 'hotel_sale')
                                <span class="badge badge-hotel">Hotel Sale</span>
                                <span class="sub-text">Ref: #{{ $entry->reference_id }}</span>

                            {{-- 3. Payments Logic --}}
                            @elseif($entry->type === 'payment')
                                @if(!empty($entry->sale_id))
                                    @if($entry->reference === 'hotel_sale')
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
                @empty
                    <tr>
                        <td colspan="{{ isset($ledgerEntries->first()->customer_name) ? 6 : 5 }}" class="text-center" style="padding: 40px 0;">
                            <p style="color: #6b7280; font-weight: 500; font-size: 10pt; margin: 0;">No ledger records found</p>
                            <p style="color: #9ca3af; font-size: 8.5pt; margin: 4px 0 0 0;">No transactions logged within the selected date range.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>