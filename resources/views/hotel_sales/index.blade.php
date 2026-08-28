@extends('partials.app', ['title' => 'Hotel Sales'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Hotel Sales</h1>
                <p class="text-gray-500 mt-1 text-sm">Manage hotel sales inventory, multi-item transactions, and payment
                    tracking</p>
            </div>
            <a href="{{ route('hotel_sales.create') }}"
                class="btn-primary cursor-pointer inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition-colors text-sm">
                <i class="fas fa-plus mr-2 text-xs"></i>
                Add New Hotel Sale
            </a>
        </div>

        <div class="flex justify-start">
            <div class="w-full max-w-[1200px] bg-white rounded-xl p-4 shadow-sm border border-gray-100">

                <div class="flex items-center gap-2">
                    <form action="{{ route('hotel_sales.index') }}" method="GET" class="flex items-center gap-2 w-full">
                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Search Inventory
                            </label>
                            <input type="text" placeholder="Search voucher..." name="search"
                                value="{{ request('search') }}"
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
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                class="w-full pl-3 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                        </div>

                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                To
                            </label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                class="w-full pl-3 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                        </div>

                        <div class="relative flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                Customer / Hotel
                            </label>
                            <select name="customer_id" class="main" id="customer">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option @selected(request('customer_id') == $customer->id) value="{{ $customer->id }}">
                                        {{ $customer->name }}
                                    </option>
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
                                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="mt-5 btn-xs btn-primary bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                        <i class="fa fa-search text-xs mr-2"></i>Search
                    </button>
                    <a href="{{ route('hotel_sales.index') }}"
                        class="mt-5 btn-sm cursor-pointer bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                        <i class="text-xs fa-solid fa-arrow-rotate-left mr-2"></i>Reset
                    </a>
                    <button type="submit" name="export" value="pdf"
                        class="mt-5 btn-sm btn-danger bg-red-700 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                        Export PDF
                    </button>
                </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm text-gray-500">
                    <thead class="bg-gray-50/70 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Voucher</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Customer / Region</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Date</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Items Count</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Total Amount</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Amount Received</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $sale->voucher_no }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="text-xs">
                                        {{ $sale->customer->name ?? 'N/A' }}
                                        @if ($sale->customer?->region)
                                            / {{ $sale->customer->region->name }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="text-xs">
                                        {{ \Carbon\Carbon::parse($sale->date)->format('m-d-Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        {{ $sale->items_count ?? $sale->items->count() }} items
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ number_format($sale->total_amount ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-emerald-600">
                                    {{ number_format($sale->customerPayment->amount ?? ($sale->customerPayment->amount ?? 0), 2) }}
                                </td>
                                {{-- <td class="px-6 py-4 font-semibold text-red-500">
                                    {{ number_format(($sale->total_amount ?? 0) - ($sale->paid_amount ?? ($sale->amount_paid ?? 0)), 2) }}
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center gap-3">
                                        <button type="button"
                                            class="cursor-pointer text-gray-400 hover:text-amber-600 transition-colors js-view-sale"
                                            title="View Details"
                                            data-url="{{ route('hotel_sales.show', ['hotel_sale' => $sale->id]) }}">
                                            <i class="fas fa-eye text-base"></i>
                                        </button>
                                        <a href="{{ route('hotel_sales.edit', $sale->id) }}"
                                            class="cursor-pointer text-gray-400 hover:text-blue-600 transition-colors"
                                            title="Edit Sale">
                                            <i class="fas fa-edit text-base"></i>
                                        </a>
                                        <a href="{{ route('hotel_sales.receipt', ['hotel_sale' => $sale->id]) }}"
                                            target="_blank"
                                            class="cursor-pointer text-gray-400 hover:text-blue-600 transition-colors"
                                            title="Receipt">
                                            <i class="fa-solid fa-money-bill-1-wave text-base"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-box-open text-4xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No hotel sales found</p>
                                    <p class="text-gray-400 text-xs mt-1">Try refining your search terms or create a new
                                        sale entry.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>

    @include('hotel_sales.modal')

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.main').select2();
            });

            (function() {
                // Details Modal Elements
                const viewModal = document.getElementById('saleDetailsModal');
                const loadingEl = document.getElementById('modalLoading');
                const errorEl = document.getElementById('modalError');
                const contentEl = document.getElementById('modalContent');

                function setViewState(state) {
                    if (loadingEl) loadingEl.classList.toggle('hidden', state !== 'loading');
                    if (errorEl) errorEl.classList.toggle('hidden', state !== 'error');
                    if (contentEl) contentEl.classList.toggle('hidden', state !== 'content');
                }

                function populateView(data) {
                    // Basic Information
                    document.getElementById('modalVoucherNo').textContent = data.voucher_no ?? '—';
                    document.getElementById('modalCustomer').textContent = data.customer?.name ?? '—';
                    document.getElementById('modalRegion').textContent = data.customer?.region?.name ?? '—';
                    document.getElementById('modalCreatedAt').textContent = data.formatted_date ?? data.formatted_date ??
                        '—';

                    // Payment Summaries
                    const totalAmt = parseFloat(data.total_amount ?? 0);
                    const paidAmt = parseFloat(data.customer_payment.amount ?? 0);
                    const balance = totalAmt - paidAmt;

                    document.getElementById('modalTotalAmount').textContent = totalAmt.toFixed(2);
                    document.getElementById('modalPaidAmount').textContent = paidAmt.toFixed(2);

                    const balanceEl = document.getElementById('modalBalanceAmount');
                    if (balanceEl) {
                        balanceEl.textContent = balance.toFixed(2);
                    }

                    // Render Products Table Inside Modal
                    const itemsContainer = document.getElementById('modalItemsTableBody');
                    if (itemsContainer) {
                        itemsContainer.innerHTML = '';
                        const items = data.items ?? data.hotel_sale_items ?? [];

                        if (items.length === 0) {
                            itemsContainer.innerHTML =
                                `<tr><td colspan="6" class="text-center py-4 text-gray-400">No items registered for this sale.</td></tr>`;
                        } else {
                            items.forEach(item => {
                                const row = document.createElement('tr');
                                row.className = 'border-b border-gray-100 text-sm';
                                row.innerHTML = `
                                    <td class="py-2.5 px-3 text-gray-900 font-medium">${item.product?.name ?? item.product_name ?? '—'}</td>
                                    <td class="py-2.5 px-3 text-gray-600">${item.weight ?? 0}</td>
                                    <td class="py-2.5 px-3 text-gray-600">${item.rate ? parseFloat(item.rate).toFixed(2) : 'Not final'}</td>
                                    <td class="py-2.5 px-3 text-right font-medium text-gray-900">${item.amount ? parseFloat(item.amount).toFixed(2) :0}</td>
                                `;
                                itemsContainer.appendChild(row);
                            });
                        }
                    }
                }

                function openViewModal() {
                    if (viewModal) {
                        viewModal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    }
                }

                function closeViewModal() {
                    if (viewModal) {
                        viewModal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
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
                            const sale = data.sale ?? data.hotel_sale ?? data;
                            populateView(sale);
                            setViewState('content');
                        })
                        .catch(() => setViewState('error'));
                }

                // Event Listeners
                document.querySelectorAll('.js-view-sale').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const url = btn.dataset.url;
                        if (url) loadAndShow(url);
                    });
                });

                document.querySelectorAll('.js-close-modal').forEach(el => {
                    el.addEventListener('click', closeViewModal);
                });
            })();
        </script>
    @endpush
@endsection
