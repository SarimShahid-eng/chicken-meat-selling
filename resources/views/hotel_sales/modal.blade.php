{{-- Hotel Sale Details Modal --}}
<div id="saleDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm js-close-modal"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-xl overflow-hidden animate-fade-in">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Hotel Sale Details</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Voucher <span id="modalVoucherNo" class="font-semibold text-amber-600">—</span>
                    </p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors js-close-modal">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4 max-h-[75vh] overflow-y-auto">

                {{-- Loading state --}}
                <div id="modalLoading" class="hidden py-10 text-center">
                    <i class="fas fa-spinner fa-spin text-2xl text-amber-500"></i>
                    <p class="text-sm text-gray-400 mt-2">Loading sale details...</p>
                </div>

                {{-- Error state --}}
                <div id="modalError" class="hidden py-10 text-center">
                    <i class="fas fa-triangle-exclamation text-2xl text-red-400"></i>
                    <p class="text-sm text-red-500 mt-2">Failed to load sale details. Please try again.</p>
                </div>

                {{-- Content --}}
                <div id="modalContent" class="hidden space-y-5">

                    {{-- Primary Info --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer / Hotel</p>
                            <p id="modalCustomer" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Region</p>
                            <p id="modalRegion" class="text-sm font-medium text-gray-900 mt-0.5">—</p>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Multi-Product Items Table --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider my-2">Purchased Items</p>
                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead
                                    class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider">
                                    <tr>
                                        <th class="py-2.5 px-3">Product</th>
                                        <th class="py-2.5 px-3">Weight</th>
                                        <th class="py-2.5 px-3">Rate</th>
                                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItemsTableBody" class="divide-y divide-gray-100 text-gray-700">
                                    {{-- Dynamically populated via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Amount and Payment Summaries --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-amber-50/60 border border-amber-100 rounded-lg p-3">
                            <p class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Total Amount
                            </p>
                            <p class="text-base font-bold text-amber-700 mt-0.5"><span id="modalTotalAmount">—</span>
                            </p>
                        </div>

                        <div class="bg-emerald-50/60 border border-emerald-100 rounded-lg p-3">
                            <p class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Amount Paid
                            </p>
                            <p class="text-base font-bold text-emerald-700 mt-0.5"><span id="modalPaidAmount">—</span>
                            </p>
                        </div>

                        <div class="bg-rose-50/60 border border-rose-100 rounded-lg p-3">
                            <p class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Remaining
                                Balance</p>
                            <p class="text-base font-bold text-rose-700 mt-0.5"><span id="modalBalanceAmount">—</span>
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 pt-1">
                        Created: <span id="modalCreatedAt">—</span>
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                <button type="button"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors js-close-modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
