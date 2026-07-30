@extends('partials.app', ['title' => isset($sale) ? 'Edit Hotel Sale' : 'Create Hotel Sale'])

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isset($sale) ? 'Edit Hotel Sale' : 'Add New Hotel Sale' }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Record direct weight-based sales for hotels and restaurants.</p>
            </div>
            <a href="{{ route('sales.index') }}"
                class="btn-secondary flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
        <x-toast-fetch-error />
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <form id="hotelSaleForm" action="{{ route('hotel_sales.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf

                <input type="hidden" name="update_id" value="{{ old('update_id') ?? @$sale->id }}">

                {{-- Header Details --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Voucher No --}}
                    <div>
                        <label for="voucher_no" class="block text-sm font-semibold text-gray-700 mb-1">
                            Voucher No
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-hashtag"></i>
                            </span>
                            <input type="text" id="voucher_no" name="voucher_no" readonly
                                value="{{ old('voucher_no') ?? (@$sale->voucher_no ?? ($voucher_no ?? '')) }}"
                                placeholder="Auto-generated"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-gray-700 font-semibold focus:outline-none cursor-not-allowed">
                        </div>
                        @error('voucher_no')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Customer (Hotel/Restaurant) --}}
                    <div>
                        <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Customer / Hotel <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            {{-- <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 z-10">
                                <i class="fa-solid fa-hotel"></i>
                            </span> --}}
                            <select id="customer_id" name="customer_id" required class="select2-element w-full pl-10">
                                <option value="" disabled
                                    {{ old('customer_id') === null && !isset($sale) ? 'selected' : '' }}>
                                    Select a hotel customer
                                </option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ (string) old('customer_id', @$sale->customer_id) === (string) $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} -- {{ $customer->region->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('customer_id')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-calendar"></i>
                            </span>
                            <input type="date" id="date" name="date"
                                value="{{ old('date', isset($sale) ? date('Y-m-d', strtotime($sale->date)) : date('Y-m-d')) }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('date') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors">
                        </div>
                        @error('date')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <hr class="border-gray-100">

                {{-- Dynamic Products Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Products Breakdown</h3>
                        <button type="button" id="addProductRow"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-2 rounded-lg transition-colors border border-amber-200">
                            <i class="fa-solid fa-plus"></i> Add Product
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="productsTable">
                            <thead>
                                <tr
                                    class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                                    <th class="py-3 px-3 w-5/12">Product <span class="text-red-500">*</span></th>
                                    <th class="py-3 px-3 w-2/12">Weight (kg) <span class="text-red-500">*</span></th>
                                    <th class="py-3 px-3 w-2/12">Rate (per kg) <span class="text-red-500">*</span></th>
                                    <th class="py-3 px-3 w-2/12">Subtotal</th>
                                    <th class="py-3 px-3 w-1/12 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="productRowsContainer" class="divide-y divide-gray-100">
                                {{-- Rows populated dynamically via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Financial Summary Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-100">

                    {{-- Total Amount --}}
                    <div class="space-y-1">
                        <label for="total_amount" class="block text-xs font-semibold uppercase text-gray-500">
                            Total Amount
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-wallet"></i>
                            </span>
                            <input type="number" step="0.01" id="total_amount" name="total_amount" readonly
                                value="{{ old('total_amount', @$sale->total_amount ?? '0.00') }}"
                                class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-800 font-bold focus:outline-none cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Amount Received --}}
                    <div class="space-y-1">
                        <label for="amount_received" class="block text-xs font-semibold uppercase text-gray-500">
                            Amount Received
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </span>
                            <input type="number" step="0.01" min="0" id="amount_received" name="amount_received"
                                value="{{ old('amount_received', @$sale->customerPayment->amount ?? '0.00') }}"
                                placeholder="0.00"
                                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-900 font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                    </div>

                    {{-- Remaining Balance --}}
                    <div class="space-y-1">
                        <label for="due_amount" class="block text-xs font-semibold uppercase text-gray-500">
                            Remaining Balance
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-scale-unbalanced"></i>
                            </span>
                            <input type="number" step="0.01" id="due_amount" readonly value="0.00"
                                class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-800 font-bold focus:outline-none cursor-not-allowed">
                        </div>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="reset"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        Reset Form
                    </button>
                    <button type="submit"
                        class="btn-primary inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm shadow-md hover:shadow-amber-500/20 transition-all">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Save Sale
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Template for new Product Rows --}}
    <template id="productRowTemplate">
        <tr class="product-row">
            <td class="py-2 px-3">
                <select name="items[{INDEX}][product_id]" class="select2-element dynamic-product w-full" required>
                    <option value="" disabled selected>Select a product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="py-2 px-3">
                <input type="number" step="0.01" min="0" name="items[{INDEX}][weight]"
                    class="row-weight w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:border-amber-500 text-sm"
                    placeholder="0.00" required>
            </td>
            <td class="py-2 px-3">
                <input type="number" step="0.01" min="0" name="items[{INDEX}][rate]"
                    class="row-rate w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:border-amber-500 text-sm"
                    placeholder="0.00" required>
            </td>
            <td class="py-2 px-3">
                <input type="number" step="0.01" name="items[{INDEX}][amount]" readonly
                    class="row-subtotal w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-700 font-semibold text-sm cursor-not-allowed"
                    value="0.00">
            </td>
            <td class="py-2 px-3 text-center">
                <button type="button"
                    class="remove-row-btn text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        $(document).ready(function() {
            let rowIndex = 0;

            /**
             * Reusable Select2 Initializer Function
             * Safely initializes Select2 on target elements or scoped containers
             */
            function initSelect2(context = document) {
                $(context).find('.select2-element').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                    }
                });
            }

            // Helper to add a new product row
            function addProductRow(data = null) {
                const template = document.getElementById('productRowTemplate').innerHTML;
                const rowHtml = template.replace(/{INDEX}/g, rowIndex);
                const $newRow = $(rowHtml);

                $('#productRowsContainer').append($newRow);

                // Initialize Select2 specifically for the newly added row
                initSelect2($newRow);

                // Pre-fill data if editing existing records
                if (data) {
                    $newRow.find('.dynamic-product').val(data.product_id).trigger('change');
                    $newRow.find('.row-weight').val(data.weight);
                    $newRow.find('.row-rate').val(data.rate);
                }

                rowIndex++;
                calculateGrandTotals();
            }

            // Global recalculation function
            function calculateGrandTotals() {
                let grandTotal = 0;

                $('.product-row').each(function() {
                    const weight = parseFloat($(this).find('.row-weight').value) || parseFloat($(this).find(
                        '.row-weight').val()) || 0;
                    const rate = parseFloat($(this).find('.row-rate').val()) || 0;
                    const subtotal = weight * rate;

                    $(this).find('.row-subtotal').val(subtotal.toFixed(2));
                    grandTotal += subtotal;
                });

                $('#total_amount').val(grandTotal.toFixed(2));

                const amountReceived = parseFloat($('#amount_received').val()) || 0;
                const dueAmount = grandTotal - amountReceived;
                $('#due_amount').val(dueAmount.toFixed(2));
            }

            // Attach event listeners for dynamic row inputs
            $('#productRowsContainer').on('input', '.row-weight, .row-rate', function() {
                calculateGrandTotals();
            });

            $('#amount_received').on('input', function() {
                calculateGrandTotals();
            });

            // Add product button click
            $('#addProductRow').on('click', function() {
                addProductRow();
            });

            // Remove product row
            $('#productRowsContainer').on('click', '.remove-row-btn', function() {
                if ($('.product-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateGrandTotals();
                } else {
                    alert('At least one product item is required.');
                }
            });

            // Initial load setup
            initSelect2();

            // Populate existing records if editing or validation re-population exists
            @if (isset($sale) && $sale->items && $sale->items->count() > 0)
                const existingItems = @json($sale->items);
                existingItems.forEach(item => addProductRow(item));
            @else
                // Add initial empty row for new records
                addProductRow();
            @endif
        });
    </script>
@endsection
