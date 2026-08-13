@extends('partials.app', ['title' => 'Customer Ledger'])

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">

        <!-- Top Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Statement Ledger</h1>
                <p class="text-sm text-gray-500 mt-1">Track comprehensive sales histories (Regular & Hotel), customer
                    payments, and outstanding balances.</p>
            </div>
        </div>

        <!-- Filter Configuration Control Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <form id="ledgerFilterForm" method="POST" action="{{ route('ledger.customerReport') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="space-y-2">
                    <label for="customer_id" class="block text-sm font-semibold text-gray-700">Select Customer</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <select id="customer_id" name="customer_id" required
                            class="w-full pl-10 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none appearance-none text-sm">
                            <option value="" disabled {{ !request('customer_id') ? 'selected' : '' }}>Choose a
                                customer...</option>
                            @foreach ($customers as $cust)
                                <option value="{{ $cust->id }}"
                                    {{ request('customer_id') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="from_date" class="block text-sm font-semibold text-gray-700">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="{{ @$fromDate }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none">
                </div>

                <div class="space-y-2">
                    <label for="to_date" class="block text-sm font-semibold text-gray-700">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="{{ @$toDate }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none">
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <button type="submit"
                        class="text-xs bg-amber-600 hover:bg-amber-700 text-white font-medium px-3 py-2 rounded-lg shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-magnifying-glass"></i>Search
                    </button>

                    <a href="{{ route('ledger.customer') }}"
                        class="text-xs bg-gray-500 hover:bg-gray-600 text-white font-medium px-3 py-2 rounded-lg shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrow-rotate-left"></i>Reset
                    </a>

                    <button type="submit" name="export" value="pdf"
                        class="text-xs bg-red-700 hover:bg-red-600 text-white font-medium px-3 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i>Export
                    </button>

                    {{-- INVOICE BUTTON --}}
                    <button type="button" onclick="submitInvoiceRoute()"
                        class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-3 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-invoice"></i>Invoice
                    </button>
                </div>
            </form>
        </div>

        @if (request('customer_id'))
            <!-- Statement Output View -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden space-y-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Description / Reference</th>
                                <th class="px-6 py-4 text-right">Debit (Sales / Charges)</th>
                                <th class="px-6 py-4 text-right">Credit (Payments Received)</th>
                                <th class="px-6 py-4 text-right">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                            <!-- Opening Balance Row Entry -->
                            <tr class="bg-amber-50/40 font-medium text-amber-900">
                                <td class="px-6 py-3.5">{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                                <td class="px-6 py-3.5 italic">Opening Balance Carriage</td>
                                <td class="px-6 py-3.5 text-right">-</td>
                                <td class="px-6 py-3.5 text-right">-</td>
                                <td class="px-6 py-3.5 text-right font-bold">Rs. {{ number_format($openingBalance, 2) }}
                                </td>
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

                                    // Customer Account Logic: Debits (Sales) increase receivable balance, Credits (Payments) reduce it.
                                    $running += $debitVal - $creditVal;

                                    $hasItems =
                                        $entry->type === 'hotel_sale' &&
                                        isset($entry->items) &&
                                        count($entry->items) > 0;
                                    $rowId = 'hs-' . $entry->source_id . '-' . $loop->index;
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors {{ $hasItems ? 'cursor-pointer' : '' }}"
                                    @if ($hasItems) onclick="toggleHotelSaleRow('{{ $rowId }}', this)" @endif>
                                    <td class="px-6 py-3.5 text-gray-500">
                                        @if ($hasItems)
                                            <i class="fa-solid fa-chevron-right text-xs text-amber-500 mr-2 transition-transform"
                                                id="chevron-{{ $rowId }}"></i>
                                        @endif
                                        {{ date('d-M-Y', strtotime($entry->date)) }}
                                    </td>
                                    <td class="px-6 py-3.5 font-medium">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if ($entry->type === 'sale')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Regular Sale
                                                </span>
                                                <span>Ref: #{{ $entry->reference_id }}</span>
                                                @if ($entry->product_name)
                                                    <span class="text-xs text-gray-500 flex items-center gap-1 w-full">
                                                        <i class="fa-solid fa-drumstick-bite text-amber-500"></i>
                                                        {{ $entry->product_name }}
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-400 w-full">
                                                    Crates: {{ $entry->sale_crate_qty ?? 0 }}
                                                    · Weight: {{ number_format($entry->sale_total_weight ?? 0, 2) }} kg
                                                    · Cut: {{ number_format($entry->sale_weight_cut ?? 0, 2) }} kg
                                                    · Net: {{ number_format($entry->sale_netweight ?? 0, 2) }} kg
                                                    · Rate:
                                                    {{ $entry->sale_rate ? 'Rs. ' . number_format($entry->sale_rate, 2) : '—' }}
                                                </span>
                                            @elseif($entry->type === 'hotel_sale')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    Hotel Sale
                                                </span>
                                                <span>Ref: #{{ $entry->reference_id }}</span>
                                                @if ($hasItems)
                                                    <span class="text-xs text-gray-400">({{ count($entry->items) }}
                                                        items)</span>
                                                @endif
                                            @elseif($entry->type === 'payment')
                                                @if (!empty($entry->sale_id))
                                                    @if ($entry->reference === 'hotel_sale')
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                            Hotel Sale Payment
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            Sale Payment
                                                        </span>
                                                    @endif
                                                    <span>Ref: #{{ $entry->reference_id }}</span>
                                                @else
                                                    <span class="text-gray-700">Payment</span>
                                                    <span class="text-xs text-gray-400">(Ref:
                                                        #{{ $entry->reference_id }})</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-red-600 font-medium">
                                        {{ $entry->debit ? 'Rs. ' . number_format($entry->debit, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-green-600 font-medium">
                                        {{ $entry->credit ? 'Rs. ' . number_format($entry->credit, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-semibold text-gray-900">
                                        Rs. {{ number_format($running, 2) }}
                                    </td>
                                </tr>

                                @if ($hasItems)
                                    <tr id="{{ $rowId }}" class="hidden bg-gray-50/50">
                                        <td colspan="5" class="px-6 py-3">
                                            <div class="ml-6 rounded-lg border border-gray-200 overflow-hidden">
                                                <table class="w-full text-xs">
                                                    <thead class="bg-gray-100 text-gray-500 uppercase tracking-wide">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left">Product</th>
                                                            <th class="px-4 py-2 text-right">Weight</th>
                                                            <th class="px-4 py-2 text-right">Rate</th>
                                                            <th class="px-4 py-2 text-right">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100">
                                                        @foreach ($entry->items as $item)
                                                            <tr>
                                                                <td class="px-4 py-2 font-medium text-gray-700">
                                                                    {{ $item->product->name ?? '—' }}</td>
                                                                <td class="px-4 py-2 text-right">
                                                                    {{ number_format($item->weight, 2) }}</td>
                                                                <td class="px-4 py-2 text-right">
                                                                    {{ $item->rate ? number_format($item->rate, 2) : '—' }}
                                                                </td>
                                                                <td class="px-4 py-2 text-right font-semibold">Rs.
                                                                    {{ number_format($item->amount, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                        <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i>
                                        No ledger activity transactions logged within selected parameters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Exact Statement Summary Card Component -->
                <div class="bg-white rounded-b-xl border-t border-gray-100 p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-center">

                        <!-- Label Section -->
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Statement
                                    Summary</span>
                                <span class="text-sm font-bold text-gray-800">Customer Overview</span>
                            </div>
                        </div>

                        <!-- Total Debit Sum -->
                        <div class="bg-gray-50/80 rounded-lg p-3 border border-gray-100 text-right">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Total Sales
                                (Debit)</span>
                            <span class="text-base font-bold text-red-600">Rs. {{ number_format($debitSum, 2) }}</span>
                        </div>

                        <!-- Total Credit Sum -->
                        <div class="bg-gray-50/80 rounded-lg p-3 border border-gray-100 text-right">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Total Paid
                                (Credit)</span>
                            <span class="text-base font-bold text-green-600">Rs. {{ number_format($creditSum, 2) }}</span>
                        </div>

                        <!-- Closing Balance -->
                        <div class="bg-slate-900 rounded-lg p-3 text-right">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide block">Closing
                                Balance</span>
                            <span class="text-base font-bold text-gray-500">Rs. {{ number_format($running, 2) }}</span>
                        </div>

                    </div>
                </div>

            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function toggleHotelSaleRow(rowId, triggerEl) {
                const row = document.getElementById(rowId);
                const chevron = document.getElementById('chevron-' + rowId);
                row.classList.toggle('hidden');
                if (chevron) chevron.classList.toggle('rotate-90');
            }
            $(document).ready(function() {
                $('#customer_id').select2();
            });

            function submitInvoiceRoute() {
                const customerId = $('#customer_id').val();
                const fromDate = $('#from_date').val();

                if (!customerId) {
                    alert('Please select a customer first.');
                    return;
                }

                if (!fromDate) {
                    alert('Please select a "From Date" first.');
                    return;
                }

                // 1. Generate base URL using Laravel's named route with placeholders
                let invoiceUrl =
                    "{{ route('ledger.customerInvoice', ['customer' => ':cust_id', 'date' => ':f_date']) }}";

                // 2. Replace placeholders with actual JavaScript values
                invoiceUrl = invoiceUrl.replace(':cust_id', encodeURIComponent(customerId))
                    .replace(':f_date', encodeURIComponent(fromDate));

                // 3. Redirect / Download PDF
                window.open(invoiceUrl, '_blank');
            }
        </script>
    @endpush
@endsection
