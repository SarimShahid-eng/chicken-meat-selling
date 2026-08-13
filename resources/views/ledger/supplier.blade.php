@extends('partials.app', ['title' => 'Supplier Ledger'])

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">

        <!-- Top Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Supplier Statement Ledger</h1>
                <p class="text-sm text-gray-500 mt-1">Track comprehensive purchase histories, cash payables, and outstanding
                    party ledger balances.</p>
            </div>
        </div>
        <!-- Filter Configuration Control Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <form id="ledgerFilterForm" method="POST" action="{{ route('ledger.supplierReport') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="space-y-2">
                    <label for="supplier_id" class="block text-sm font-semibold text-gray-700">Select Supplier</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <select id="supplier_id" name="supplier_id" required
                            class="w-full pl-10 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none appearance-none text-sm">
                            <option value="" disabled {{ !request('supplier_id') ? 'selected' : '' }}>Choose a
                                supplier...</option>
                            @foreach ($suppliers as $sup)
                                <option value="{{ $sup->id }}"
                                    {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
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
                        class="text-xs bg-amber-600 hover:bg-amber-700 text-white font-medium px-3 py-2 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-magnifying-glass"></i>Search
                    </button>

                    <a href="{{ route('ledger.supplier') }}"
                        class="text-xs bg-gray-500 hover:bg-gray-600 text-white font-medium px-3 py-2 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrow-rotate-left"></i>Reset
                    </a>

                    <button type="submit" name="export" value="pdf"
                        class="text-xs bg-red-700 hover:bg-red-600 text-white font-medium px-3 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i>Export
                    </button>

                    {{-- NEW INVOICE BUTTON --}}
                    <button type="button" onclick="submitInvoiceRoute()"
                        class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-3 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-invoice"></i>Invoice
                    </button>
                </div>
            </form>
        </div>


        @if (request('supplier_id'))
            <!-- Statement Output View -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Description / Reference</th>
                                <th class="px-6 py-4 text-right">Debit (Amount Paid)</th>
                                <th class="px-6 py-4 text-right">Credit (Purchase Vol)</th>
                                <th class="px-6 py-4 text-right">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                            <!-- Opening Balance Row Entry -->
                            <tr class="bg-amber-50/40 font-medium text-amber-900">
                                <td class="px-6 py-3.5">{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                                <td class="px-6 py-3.5 italic">Weight</td>
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
                                    $running += $creditVal - $debitVal;
                                    $hasVehicles =
                                        $entry->sort_order == 1 &&
                                        isset($entry->vehicles) &&
                                        count($entry->vehicles) > 0;
                                    $rowId = 'veh-' . $entry->purchase_id . '-' . $loop->index;
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors {{ $hasVehicles ? 'cursor-pointer' : '' }}"
                                    @if ($hasVehicles) onclick="toggleVehicleRow('{{ $rowId }}', this)" @endif>
                                    <td class="px-6 py-3.5 text-gray-500">
                                        @if ($hasVehicles)
                                            <i class="fa-solid fa-chevron-right text-xs text-amber-500 mr-2 transition-transform"
                                                id="chevron-{{ $rowId }}"></i>
                                        @endif
                                        {{ date('d-M-Y', strtotime($entry->date)) }}
                                    </td>
                                    <td class="px-6 py-3.5 font-medium">
                                        {{ $entry->description }}
                                        @if ($entry->product_name)
                                            <span class="text-xs text-gray-500 block">
                                                <i class="fa-solid fa-drumstick-bite text-amber-500"></i>
                                                {{ $entry->product_name }}
                                            </span>
                                        @endif
                                        @if ($entry->rate)
                                            <span class="text-xs text-gray-500 block">
                                                Rate:
                                                {{ $entry->rate }}
                                            </span>
                                        @else
                                            <!--<span>rate not </span>-->
                                        @endif
                                        <span class="text-xs text-gray-400 block">Ref ID:
                                            #{{ $entry->reference_id }}</span>
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

                                @if ($hasVehicles)
                                    <tr id="{{ $rowId }}" class="hidden bg-gray-50/50">
                                        <td colspan="5" class="px-6 py-3">
                                            <div class="ml-6 rounded-lg border border-gray-200 overflow-hidden">
                                                <table class="w-full text-xs">
                                                    <thead class="bg-gray-100 text-gray-500 uppercase tracking-wide">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left">Vehicle</th>
                                                            <th class="px-4 py-2 text-right">Crates</th>
                                                            <th class="px-4 py-2 text-right">Weight</th>
                                                            <th class="px-4 py-2 text-right">Cut</th>
                                                            <th class="px-4 py-2 text-right">Net Weight</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100">
                                                        @foreach ($entry->vehicles as $vehicle)
                                                            @php $vehicleAmount = $vehicle->netweight * ($entry->rate ?? 0); @endphp
                                                            <tr>
                                                                <td class="px-4 py-2 font-medium text-gray-700">
                                                                    {{ $vehicle->name }}</td>
                                                                <td class="px-4 py-2 text-right">{{ $vehicle->crate_qty }}
                                                                </td>
                                                                <td class="px-4 py-2 text-right">
                                                                    {{ number_format($vehicle->total_weight, 2) }}</td>
                                                                <td class="px-4 py-2 text-right text-red-500">
                                                                    {{ number_format($vehicle->weight_cut, 2) }}</td>
                                                                <td
                                                                    class="px-4 py-2 text-right font-semibold text-amber-700">
                                                                    {{ number_format($vehicle->netweight, 2) }}</td>
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
                            <span class="text-sm font-bold text-gray-800">Supplier Overview</span>
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
        @endif
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#supplier_id').select2()
            })

            function toggleVehicleRow(rowId, triggerEl) {
                const row = document.getElementById(rowId);
                const chevron = document.getElementById('chevron-' + rowId);
                row.classList.toggle('hidden');
                if (chevron) chevron.classList.toggle('rotate-90');
            }

            function submitInvoiceRoute() {
                const supplierId = $('#supplier_id').val();
                const fromDate = $('#from_date').val();

                if (!supplierId) {
                    alert('Please select a supplier first.');
                    return;
                }

                if (!fromDate) {
                    alert('Please select a "From Date" first.');
                    return;
                }

                // 1. Generate base URL using Laravel's named route with placeholders
                let invoiceUrl =
                    "{{ route('ledger.supplierInvoice', ['supplier' => ':cust_id', 'date' => ':f_date']) }}";

                // 2. Replace placeholders with actual JavaScript values
                invoiceUrl = invoiceUrl.replace(':cust_id', encodeURIComponent(supplierId))
                    .replace(':f_date', encodeURIComponent(fromDate));

                // 3. Redirect / Download PDF
                window.open(invoiceUrl, '_blank');
            }
        </script>
    @endpush
@endsection
