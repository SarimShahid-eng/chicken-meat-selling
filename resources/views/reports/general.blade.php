@extends('partials.app', ['title' => 'Executive Overview'])

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 animate-fade-in p-4">

        <!-- Top Branding Header -->
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Executive Accounting Dashboard</h1>
                <p class="text-sm text-gray-500 mt-0.5">Consolidated exposure metrics, receivables tracking, and ledger
                    system validation.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('ledger.customer') }}"
                    class="px-4 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-user-tie"></i> Customer Ledgers
                </a>
                <a href="{{ route('ledger.supplier') }}"
                    class="px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-truck-field"></i> Supplier Ledgers
                </a>
            </div>
        </div>

        <!-- Main System Performance KPI Scorecard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Gross Sales Invoiced</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">Rs.
                        {{ number_format($totalSalesVolume, 2) }}</span>
                    <span class="text-[11px] text-emerald-600 font-medium"><i class="fa-solid fa-arrow-trend-up"></i>
                        Customer Debits</span>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-base">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Customer Inbound
                        Cash</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">Rs.
                        {{ number_format($totalCustomerCollections, 2) }}</span>
                    <span class="text-[11px] text-blue-600 font-medium"><i class="fa-solid fa-piggy-bank"></i> Total
                        Credits</span>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-base">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Gross Purchase
                        Volume</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">Rs.
                        {{ number_format($totalPurchasesVolume, 2) }}</span>
                    <span class="text-[11px] text-purple-600 font-medium"><i class="fa-solid fa-boxes-stacked"></i> Supplier
                        Credits</span>
                </div>
                <div class="p-3 bg-purple-50 text-purple-600 rounded-lg text-base">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Supplier Outbound
                        Cash</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">Rs.
                        {{ number_format($totalSupplierDisbursements, 2) }}</span>
                    <span class="text-[11px] text-rose-600 font-medium"><i class="fa-solid fa-money-bill-transfer"></i>
                        Total Debits</span>
                </div>
                <div class="p-3 bg-rose-50 text-rose-600 rounded-lg text-base">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>

        <!-- Balance Sheet Executive Summaries -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
                class="bg-gradient-to-br from-amber-500 to-amber-600 p-6 rounded-xl shadow-sm text-white flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider opacity-85 block">Total Net Accounts
                        Receivable</span>
                    <span class="text-2xl font-black mt-1 block">Rs. {{ number_format($totalReceivables, 2) }}</span>
                    <p class="text-xs opacity-75 mt-1.5 font-medium">Liquid capital waiting to be collected from active
                        client accounts.</p>
                </div>
                <div class="text-3xl opacity-20"><i class="fa-solid fa-users-viewfinder"></i></div>
            </div>

            <div
                class="bg-gradient-to-br from-slate-700 to-slate-800 p-6 rounded-xl shadow-sm text-white flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider opacity-85 block">Total Net Accounts
                        Payable</span>
                    <span class="text-2xl font-black mt-1 block">Rs. {{ number_format($totalPayables, 2) }}</span>
                    <p class="text-xs opacity-75 mt-1.5 font-medium">Outstanding financial liabilities currently owed to
                        credit vendors.</p>
                </div>
                <div class="text-3xl opacity-20"><i class="fa-solid fa-receipt"></i></div>
            </div>
        </div>

        <!-- Active Exposure Subledger Tables Breakdowns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Customer Exposure Block -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Active Customers Balance Matrix
                    </h3>
                    <span class="text-xs font-bold text-gray-500 bg-white border px-2 py-0.5 rounded shadow-sm">
                        {{ count($customerSummaries) }} Active
                    </span>
                </div>
                <div class="overflow-y-auto max-h-96">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr
                                class="bg-white border-b text-gray-400 font-bold uppercase tracking-wider sticky top-0 shadow-sm">
                                <th class="px-5 py-3">Customer Profile</th>
                                <th class="px-5 py-3 text-right">Outstanding Position</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-gray-700">
                            @forelse($customerSummaries as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $item->name }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-amber-700">
                                        Rs. {{ number_format($item->net_balance, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-10 text-center text-gray-400 italic">
                                        All customer statement balances are settled perfectly clear.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Supplier Exposure Block -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-700"></span> Active Suppliers Liabilities Matrix
                    </h3>
                    <span class="text-xs font-bold text-gray-500 bg-white border px-2 py-0.5 rounded shadow-sm">
                        {{ count($supplierSummaries) }} Active
                    </span>
                </div>
                <div class="overflow-y-auto max-h-96">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr
                                class="bg-white border-b text-gray-400 font-bold uppercase tracking-wider sticky top-0 shadow-sm">
                                <th class="px-5 py-3">Vendor / Supply House</th>
                                <th class="px-5 py-3 text-right">Payable Balance Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-gray-700">
                            @forelse($supplierSummaries as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $item->name }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-slate-800">
                                        Rs. {{ number_format($item->net_balance, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-10 text-center text-gray-400 italic">
                                        No credit liabilities logged outstanding with any trade suppliers.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Real-Time Profitability Performance Monitoring Matrix -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="border-b border-gray-100 pb-4 mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Net Gross Profitability Analysis</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Calculated as: Total Sales Turnover minus Cost of Inventory
                            Purchases.</p>
                    </div>
                    <span
                        class="text-xs font-bold {{ $grossProfit >= 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' }} border px-3 py-1 rounded-md">
                        {{ $grossProfit >= 0 ? 'Surplus Operational State' : 'Deficit Asset State' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <!-- Visual Progress Gauge Block -->
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 text-center space-y-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Gross Profit Retention
                            Margin</span>
                        <div class="relative inline-flex items-center justify-center">
                            <span
                                class="text-3xl font-black text-gray-900">{{ number_format($profitMarginPercent, 1) }}%</span>
                        </div>
                        <p class="text-[11px] text-gray-500 max-w-xs mx-auto">This represents the percentage of net revenue
                            kept as business profit before operational overhead costs.</p>
                    </div>

                    <!-- Metric Details Readouts -->
                    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="border border-gray-100 p-4 rounded-xl shadow-xs bg-white">
                            <span class="text-xs font-semibold text-gray-400 block">Total Sales Revenue (Inflow)</span>
                            <span class="text-lg font-bold text-gray-800 block mt-1">Rs.
                                {{ number_format($grossRevenue, 2) }}</span>
                        </div>

                        <div class="border border-gray-100 p-4 rounded-xl shadow-xs bg-white">
                            <span class="text-xs font-semibold text-gray-400 block">Inventory Costs (Outflow)</span>
                            <span class="text-lg font-bold text-gray-800 block mt-1 text-gray-600">Rs.
                                {{ number_format($costOfGoods, 2) }}</span>
                        </div>

                        <div
                            class="sm:col-span-2 border p-4 rounded-xl shadow-xs {{ $grossProfit >= 0 ? 'bg-emerald-50/20 border-emerald-100' : 'bg-rose-50/20 border-rose-100' }}">
                            <span
                                class="text-xs font-bold {{ $grossProfit >= 0 ? 'text-emerald-800' : 'text-rose-800' }} uppercase tracking-wider block">Net
                                Business Gross Profit</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span
                                    class="text-2xl font-black {{ $grossProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rs. {{ number_format($grossProfit, 2) }}
                                </span>
                                <span class="text-xs font-medium text-gray-500">
                                    Net Operating Performance
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
