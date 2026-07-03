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
            <form method="POST" action="{{ route('ledger.supplierReport') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="space-y-2">
                    <label for="supplier_id" class="block text-sm font-semibold text-gray-700">Select Supplier</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-truck-field"></i>
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

                <div>
                    <button type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Generate Ledger
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
                                <td class="px-6 py-3.5 italic">Opening Balance Carriage</td>
                                <td class="px-6 py-3.5 text-right">-</td>
                                <td class="px-6 py-3.5 text-right">-</td>
                                <td class="px-6 py-3.5 text-right font-bold">Rs. {{ number_format($openingBalance, 2) }}
                                </td>
                            </tr>

                            @php $running = $openingBalance; @endphp

                            @forelse($ledgerEntries as $entry)
                                @php
                                    // Supplier running calculation formula adjustments
                                    $running += ($entry->credit ?? 0) - ($entry->debit ?? 0);
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-6 py-3.5 text-gray-500">{{ date('d-M-Y', strtotime($entry->date)) }}</td>
                                    <td class="px-6 py-3.5 font-medium">
                                        {{ $entry->description }}
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
        @endif
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#supplier_id').select2()
            })
        </script>
    @endpush
@endsection
