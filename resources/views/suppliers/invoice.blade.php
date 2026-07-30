<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Invoice - {{ $supplier->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8.5pt;
                color: #6b7280;
            }
        }

        /* --- SCREEN STYLES (Readable Medium Font Preview) --- */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #1f2937;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px 0;
        }

        .invoice-card {
            max-width: 850px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Action Bar / Download Button */
        .actions-bar {
            max-width: 850px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: flex-end;
        }

        .btn-download {
            background-color: #1e3a8a; /* Deep blue theme for purchases */
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-size: 10pt;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: background-color 0.2s;
        }

        .btn-download:hover {
            background-color: #1e40af;
        }

        /* --- INVOICE STRUCTURE --- */
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20pt;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 18px;
            font-size: 10pt;
        }

        .meta-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .invoice-title {
            font-size: 15pt;
            font-weight: 700;
            color: #d97706;
            text-transform: uppercase;
            text-align: right;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            padding: 8px 6px;
            font-size: 8.5pt;
            text-transform: uppercase;
            color: #475569;
            text-align: left;
        }

        table.data-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 6px;
            font-size: 9.5pt;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }

        /* Summary Section */
        .summary-wrapper {
            width: 100%;
        }

        .summary-table {
            width: 48%;
            float: right;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .summary-table td {
            padding: 6px;
        }

        .summary-table tr.total-row {
            background-color: #f1f5f9;
            font-weight: 700;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
        }

        .clear { clear: both; }

        /* --- PRINT STYLES (EXACT PDF OUTPUT) --- */
        @media print {
            body {
                background-color: transparent !important;
                padding: 0 !important;
                font-size: 8.5pt !important;
            }

            .invoice-card {
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }

            /* Hide Download Button when Printing/Generating PDF */
            .actions-bar {
                display: none !important;
            }

            .company-name { font-size: 18pt !important; }
            .invoice-title { font-size: 14pt !important; }
            .meta-table { font-size: 8.5pt !important; }
            table.data-table th { font-size: 7.5pt !important; padding: 5px !important; }
            table.data-table td { font-size: 8.5pt !important; padding: 5px !important; }
            .summary-table { font-size: 8.5pt !important; width: 45% !important; }
            .summary-table td { padding: 4px 6px !important; }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Hidden in Print/PDF) -->
    <div class="actions-bar">
        <button onclick="window.print()" class="btn-download">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
            </svg>
            Download PDF / Print
        </button>
    </div>

    <div class="invoice-card">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <h1 class="company-name">Rajput Chicken Centre</h1>
                        <span style="color: #4b5563;">Purchase & Procurement Invoice</span>
                    </td>
                    <td class="invoice-title">
                        Supplier Invoice
                        <div style="font-size: 9.5pt; color: #64748b; font-weight: normal; margin-top: 2px;">
                            Date: {{ date('d-M-Y', strtotime($from_date)) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Supplier Metadata -->
        <table class="meta-table">
            <tr>
                <td style="width: 60%;">
                    <strong>Supplier Name:</strong> {{ $supplier->name ?? 'N/A' }}<br>
                    <strong>Phone / Contact:</strong> {{ $supplier->phone ?? 'N/A' }}<br>
                    <strong>Address / City:</strong> {{ $supplier->address ?? 'N/A' }}
                </td>
                <td style="width: 40%; text-align: right;">
                    <strong>Invoice Date:</strong> {{ date('d-M-Y', strtotime($from_date)) }}<br>
                </td>
            </tr>
        </table>

        <!-- Purchase Items Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 12%;">Voucher</th>
                    <th style="width: 18%;">Product</th>
                    <th style="width: 12%;">Vehicle No</th>
                    <th style="width: 8%; text-align: center;">Crates</th>
                    <th style="width: 11%; text-align: right;">Total Wt</th>
                    <th style="width: 10%; text-align: right;">Cut Wt</th>
                    <th style="width: 11%; text-align: right;">Net Wt</th>
                    <th style="width: 13%; text-align: right;">Rate (Rs)</th>
                    <th style="width: 14%; text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $itemIndex = 1; @endphp

                @forelse($purchases as $purchase)
                    <tr>
                        <td class="text-center">{{ $itemIndex++ }}</td>
                        <td class="font-bold">{{ $purchase->voucher_no ?? '-' }}</td>
                        <td class="font-bold">{{ $purchase->product->name ?? 'Chicken / Live' }}</td>
                        <td>{{ $purchase->vehicle_no ?? '-' }}</td>
                        <td class="text-center">{{ $purchase->crate_qty ? number_format($purchase->crate_qty, 0) : '-' }}</td>
                        <td class="text-right">{{ number_format($purchase->total_weight, 2) }} kg</td>
                        <td class="text-right" style="color: #dc2626;">{{ number_format($purchase->weight_cut, 2) }} kg</td>
                        <td class="text-right font-bold">{{ number_format($purchase->netweight, 2) }} kg</td>
                        <td class="text-right">Rs. {{ number_format($purchase->rate, 2) }}</td>
                        <td class="text-right font-bold">Rs. {{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 15px; color: #9ca3af;">
                            No purchase entries registered for this date.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary / Payable Balance Section -->
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td><strong>Today's Purchases Total:</strong></td>
                    <td class="text-right font-bold">Rs. {{ number_format($currentPurchaseTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Previous Balance Payable:</td>
                    <td class="text-right">Rs. {{ number_format($previousBalance, 2) }}</td>
                </tr>
                <tr style="border-top: 1px solid #cbd5e1;">
                    <td><strong>Subtotal Payable:</strong></td>
                    <td class="text-right font-bold">Rs. {{ number_format($subtotal, 2) }}</td>
                </tr>
                @if(($paidToday ?? 0) > 0)
                <tr>
                    <td style="color: #16a34a;">Paid Today:</td>
                    <td class="text-right" style="color: #16a34a;">- Rs. {{ number_format($paidToday, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Net Payable Balance:</td>
                    <td class="text-right">
                        Rs. {{ number_format($remainingBalance, 2) }}
                    </td>
                </tr>
            </table>
            <div class="clear"></div>
        </div>
    </div>

</body>
</html>
