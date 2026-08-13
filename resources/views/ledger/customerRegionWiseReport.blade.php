@extends('partials.app', ['title' => 'Region-wise Customer Ledger'])

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">

        <!-- Top Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Region-wise Customer Statement Ledger</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Track consolidated sales histories, customer payments, and outstanding party balances region-wise.
                </p>
            </div>
        </div>

        <!-- Filter Control Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <form method="POST" action="{{ route('ledger.regionCustomerReport') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf

                <!-- Region Selector -->
                <div class="space-y-2">
                    <label for="region_id" class="block text-sm font-semibold text-gray-700">Select Region</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </span>
                        <select id="region_id" name="region_id" required
                            class="w-full pl-10 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none appearance-none text-sm">
                            <option value="" disabled {{ !request('region_id') ? 'selected' : '' }}>
                                Choose a region...
                            </option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}"
                                    {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- From Date -->
                <div class="space-y-2">
                    <label for="from_date" class="block text-sm font-semibold text-gray-700">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="{{ @$fromDate }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none">
                </div>

                <!-- To Date -->
                <div class="space-y-2">
                    <label for="to_date" class="block text-sm font-semibold text-gray-700">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="{{ @$toDate }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none">
                </div>

                <!-- Submit / Reset / Export Buttons -->
                <div class="flex">
                    <button type="submit"
                        class="text-xs bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>Search
                    </button>
                    <a href="{{ route('ledger.regionCustomerReport') }}"
                        class="text-xs ml-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-rotate-left"></i>Reset
                    </a>
                    <button type="submit" name="export" value="pdf"
                        class="text-xs ml-2 btn-sm btn-danger bg-red-700 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                        PDF
                    </button>
                </div>
            </form>
        </div>

        @if (request('region_id'))
            <!-- Ledger Output Table -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Description / Reference</th>
                                <th class="px-6 py-4 text-right">Debit (Sales / Charges)</th>
                                <th class="px-6 py-4 text-right">Credit (Payments Received)</th>
                                <th class="px-6 py-4 text-right">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                            <!-- Opening Balance Carriage Row -->
                            <tr class="bg-amber-50/40 font-medium text-amber-900">
                                <td class="px-6 py-3.5">{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                                <td class="px-6 py-3.5 font-bold">All Regional Customers</td>
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

                                    // Customer Accounting: Debits (Sales) increase balance, Credits (Payments) decrease it.
                                    $running += $debitVal - $creditVal;
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-6 py-3.5 text-gray-500">{{ date('d-M-Y', strtotime($entry->date)) }}</td>
                                    <td class="px-6 py-3.5 font-semibold text-gray-800">{{ $entry->customer_name }}</td>
                                    <td class="px-6 py-3.5 font-medium">
                                        <div class="flex items-center gap-2">
                                            {{-- 1. Regular Sale Badge --}}
                                            @if ($entry->type === 'sale')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Regular Sale
                                                </span>
                                                <span>Ref: #{{ $entry->reference_id }}</span>

                                                {{-- 2. Hotel Sale Badge --}}
                                            @elseif($entry->type === 'hotel_sale')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    Hotel Sale
                                                </span>
                                                <span>Ref: #{{ $entry->reference_id }}</span>

                                                {{-- 3. Payments Logic --}}
                                            @elseif($entry->type === 'payment')
                                                @if (!empty($entry->sale_id))
                                                    {{-- Payment linked to a sale --}}
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
                                                    {{-- Payment made separately (No sale_id present) --}}
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
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                        <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i>
                                        No ledger activity transactions logged for this region within selected parameters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Statement Summary Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-center">

                    <!-- Label Section -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Statement Summary</span>
                            <span class="text-sm font-bold text-gray-800">Regional Overview</span>
                        </div>
                    </div>

                    <!-- Total Debit Sum -->
                    <div class="bg-gray-50/80 rounded-lg p-3 border border-gray-100 text-right">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Total Sales (Debit)</span>
                        <span class="text-base font-bold text-red-600">Rs. {{ number_format($debitSum, 2) }}</span>
                    </div>

                    <!-- Total Credit Sum -->
                    <div class="bg-gray-50/80 rounded-lg p-3 border border-gray-100 text-right">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Total Paid (Credit)</span>
                        <span class="text-base font-bold text-green-600">Rs. {{ number_format($creditSum, 2) }}</span>
                    </div>

                    <!-- Closing Balance -->
                    <div class="bg-slate-900 rounded-lg p-3 text-right">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide block">Closing Balance</span>
                        <span class="text-base font-bold text-gray-500">Rs. {{ number_format($running, 2) }}</span>
                    </div>

                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#region_id').select2();
        });
    </script>
@endpush
