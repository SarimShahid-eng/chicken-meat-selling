@extends('partials.app', ['title' => 'Purchases'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Purchases</h1>
                <p class="text-gray-500 mt-1 text-sm">Manage your chicken meat purchases inventory and stock levels</p>
            </div>
            <a href="{{ route('purchases.create') }}"
                class="btn-primary cursor-pointer inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition-colors text-sm">
                <i class="fas fa-plus mr-2 text-xs"></i>
                Add New Purchase
            </a>
        </div>

        <div class="flex justify-start">
            <div class="w-full max-w-[1200px] bg-white rounded-xl p-4 shadow-sm border border-gray-100">

                <div class="flex items-center gap-2">
                    <form action="{{ route('purchases.index') }}" method="GET" class="flex items-center gap-2 w-full">
                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Search Inventory
                            </label>
                            <input type="text" placeholder="Search purchases..." name="search"
                                class="w-full pl-3 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                            <div
                                class="absolute inset-y-0 right-0 top-5 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                        </div>
                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                From
                            </label>
                            <input type="date" name="from_date"
                                class="w-full pl-3 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                        </div>
                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                To
                            </label>
                            <input type="date" name="to_date"
                                class="w-full pl-3 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">

                        </div>

                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Supplier
                            </label>
                            <select name="supplier_id" class="main" id="supplier">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option @selected(request('supplier_id') == $supplier->id) value="{{ $supplier->id }}">
                                        {{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Product
                            </label>
                            <select name="product_id" class="main" id="product">
                                <option value="">Select Product</option>

                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(request('product') == $product->id)>
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>

                        </div>

                </div>
                <button type="submit"
                    class="mt-5 btn-xs btn-primary bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                    <i class="fa fa-search text-xs mr-2"></i>Search
                </button>
                <a href="{{ route('sales.index') }}"
                    class="mt-5 btn-sm cursor-pointer bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                    <i class="text-xs fa-solid fa-arrow-rotate-left mx-2"></i>Reset
                </a>
                <button type="submit" name="export" value="pdf"
                    class="mx-2 btn-sm btn-danger bg-red-700 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                    Export
                </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm text-gray-500">
                    <thead class="bg-gray-50/70 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Voucher</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Supplier</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Date</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Product</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Crate</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Net.Weight</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Rate</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Total</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        @forelse ($purchases as $purchase)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $purchase->voucher_no }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="text-xs">
                                        {{ $purchase->supplier->name }} /
                                        {{ $purchase->supplier->region->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="text-xs">
                                        {{ $purchase->date->format('m-d-Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="text-xs">
                                        {{ $purchase->product->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <div class="max-w-xs truncate" title="{{ $purchase->crate_qty }}">
                                        {{ $purchase->crate_qty }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-gray-500">
                                    <div class="max-w-xs truncate" title="{{ $purchase->crate_qty }}">
                                        {{ $purchase->netweight }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <div class="max-w-xs truncate" title="{{ $purchase->crate_qty }}">
                                        <span @class([
                                            'text-red-400' => is_null($purchase->rate),
                                            'font-bold' => is_null($purchase->rate),
                                        ])>
                                            {{ $purchase->rate ? $purchase->rate : 'Not final yet' }}

                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <div class="max-w-xs truncate" title="{{ $purchase->total_amount }}">
                                        {{ $purchase->total_amount }}
                                    </div>
                                </td>


                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center gap-3">
                                        <button type="button"
                                            class="cursor-pointer text-gray-400 hover:text-amber-600 transition-colors js-view-purchase"
                                            title="View Details"
                                            data-url="{{ route('purchases.show', ['purchase' => $purchase->id]) }}">
                                            <i class="fas fa-eye text-base"></i>
                                        </button>
                                        <a href="{{ route('purchases.edit', ['purchase' => $purchase->id]) }}"
                                            class="cursor-pointer text-gray-400 hover:text-blue-600 transition-colors"
                                            title="Edit Purchase">
                                            <i class="fas fa-edit text-base"></i>
                                        </a>
                                        @if (is_null($purchase->rate))
                                            <button type="button"
                                                class="cursor-pointer text-gray-400 hover:text-amber-600 transition-colors js-rate-confirm"
                                                title="Set Rate"
                                                data-update-url="{{ route('purchases.update_rate', ['purchase' => $purchase->id]) }}">
                                                <i class="fas fa-receipt text-base"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-box-open text-4xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No purchases found</p>
                                    <p class="text-gray-400 text-xs mt-1">Try refining your search terms or add a new item.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>

    @include('purchases.modal')
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.main').select2();
            });

            (function() {
                // Details Modal Elements
                const viewModal = document.getElementById('purchaseDetailsModal');
                const loadingEl = document.getElementById('modalLoading');
                const errorEl = document.getElementById('modalError');
                const contentEl = document.getElementById('modalContent');

                // Rate Modal Elements
                const rateModal = document.getElementById('rateConfirmModal');
                const rateForm = document.getElementById('rateConfirmForm');
                const rateInput = document.getElementById('modalInputRate');

                // --- Details Modal Management ---
                function setViewState(state) {
                    loadingEl.classList.toggle('hidden', state !== 'loading');
                    errorEl.classList.toggle('hidden', state !== 'error');
                    contentEl.classList.toggle('hidden', state !== 'content');
                }

                function populateView(data) {
                    document.getElementById('modalVoucherNo').textContent = data.voucher_no ?? '—';
                    document.getElementById('modalProduct').textContent = data.product?.name ?? '—';
                    document.getElementById('modalSupplier').textContent = data.supplier?.name ?? '—';
                    document.getElementById('modalRegion').textContent = data.supplier?.region?.name ?? '—';
                    document.getElementById('modalVehicleNo').textContent = data.vehicle_no ?? '—';
                    document.getElementById('modalCrateQty').textContent = data.crate_qty ?? '—';
                    document.getElementById('modalTotalWeight').textContent = data.total_weight ?? '—';
                    document.getElementById('modalWeightCut').textContent = data.weight_cut ?? '—';
                    document.getElementById('modalNetWeight').textContent = data.netweight ?? '—';
                    document.getElementById('modalRate').textContent = data.rate ?? 'Not set';
                    document.getElementById('modalRateDate').textContent = data.rate_date_formatted ?? data.rate_date ??
                        '—';
                    document.getElementById('modalTotalAmount').textContent = data.total_amount ?? '—';
                    document.getElementById('modalCreatedAt').textContent = data.created_at_formatted ?? data.created_at ??
                        '—';
                }

                function openViewModal() {
                    viewModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                function closeViewModal() {
                    viewModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }

                function loadAndShow(url) {
                    openViewModal();
                    setViewState('loading');

                    fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Request failed');
                            return response.json();
                        })
                        .then(data => {
                            const purchase = data.purchase ?? data;
                            populateView(purchase);
                            setViewState('content');
                        })
                        .catch(() => setViewState('error'));
                }

                // --- Rate Modal Management ---
                function openRateModal(updateUrl) {
                    rateForm.action = updateUrl; // Dynamically binds the update URL
                    rateInput.value = ''; // Resets the input field
                    rateModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                    setTimeout(() => rateInput.focus(), 100);
                }

                function closeRateModal() {
                    rateModal.classList.add('hidden');
                    if (viewModal.classList.contains('hidden')) {
                        document.body.classList.remove('overflow-hidden');
                    }
                }

                // --- Event Listeners ---

                // View purchases click events
                document.querySelectorAll('.js-view-purchase').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const url = btn.dataset.url;
                        if (url) loadAndShow(url);
                    });
                });

                document.querySelectorAll('.js-close-modal').forEach(el => {
                    el.addEventListener('click', closeViewModal);
                });

                // Rate conversion click events
                document.querySelectorAll('.js-rate-confirm').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const updateUrl = btn.dataset.updateUrl;
                        if (updateUrl) openRateModal(updateUrl);
                    });
                });

                document.querySelectorAll('.js-close-rate-modal').forEach(el => {
                    el.addEventListener('click', closeRateModal);
                });

                // Master Escape Key Handler for both Modals
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (!rateModal.classList.contains('hidden')) {
                            closeRateModal();
                        } else if (!viewModal.classList.contains('hidden')) {
                            closeViewModal();
                        }
                    }
                });
            })();
        </script>
    @endpush
@endsection
