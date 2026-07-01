@extends('partials.app', ['title' => isset($payment) ? 'Edit Payment' : 'Record Payment'])

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isset($payment) ? 'Edit Customer Payment' : 'Record New Customer Payment' }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Log ledger settlements, cash distributions, or wire transfers to customer balances.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <form action="{{ route('customersPayment.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf
                {{-- @if(isset($payment))
                    @method('PUT')
                @endif --}}

                <input type="hidden" name="update_id" value="{{ old('update_id') ?? @$payment->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="customer_id" class="block text-sm font-semibold text-gray-700">
                            Select Customer <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-truck-field"></i>
                            </span>
                            <select id="customer_id" name="customer_id" required
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border @error('customer_id') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors appearance-none">

                                <option value="" disabled {{ (old('customer_id') === null && !isset($payment)) ? 'selected' : '' }}>
                                    Select a customer account
                                </option>

                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ (string) old('customer_id', @$payment->customer_id) === (string) $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        @error('customer_id')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- <div class="space-y-2">
                        <label for="payment_type" class="block text-sm font-semibold text-gray-700">
                            Payment Type <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-right-left"></i>
                            </span>
                            <select id="payment_type" name="payment_type" required
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border @error('payment_type') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors appearance-none">

                                <option value="" disabled {{ (old('payment_type') === null && !isset($payment)) ? 'selected' : '' }}>
                                    Select adjustment type
                                </option>
                                <option value="debit" {{ old('payment_type', @$payment->payment_type) === 'debit' ? 'selected' : '' }}>
                                    Debit (Giving / Outgoing)
                                </option>
                                <option value="credit" {{ old('payment_type', @$payment->payment_type) === 'credit' ? 'selected' : '' }}>
                                    Credit (Receiving / Return)
                                </option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        @error('payment_type')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    <div class="space-y-2">
                        <label for="type" class="block text-sm font-semibold text-gray-700">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-credit-card"></i>
                            </span>
                            <select id="type" name="type" required
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border @error('type') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors appearance-none">

                                <option value="" disabled {{ (old('type') === null && !isset($payment)) ? 'selected' : '' }}>
                                    Select transaction channel
                                </option>
                                <option value="cash" {{ old('type', @$payment->type) === 'cash' ? 'selected' : '' }}>
                                    Cash Payment
                                </option>
                                <option value="bank" {{ old('type', @$payment->type) === 'bank' ? 'selected' : '' }}>
                                    Bank Transfer / Cheque
                                </option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="date" class="block text-sm font-semibold text-gray-700">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 font-medium text-sm">
                                <i class="fa-solid fa-calendar"></i>
                            </span>
                            <input type="date" id="date" name="date" required
                                value="{{ old('date', isset($payment) ? date('Y-m-d', strtotime($payment->date)) : date('Y-m-d')) }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('date') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('date')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 ">
                        <label for="amount" class="block text-sm font-semibold text-gray-700">
                            Amount Paid ($) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 font-medium text-sm">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </span>
                            <input type="number" step="0.01" id="amount" name="amount" required
                                value="{{ old('amount') ?? @$payment->amount }}"
                                placeholder="0.00"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('amount') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700">Description / Memo Details</label>
                    <textarea id="description" name="description" rows="4"
                        placeholder="Add voucher receipt numbers, bank trace IDs, check serial details, or allocation balances notes..."
                        class="w-full px-4 py-2.5 bg-gray-50 border @error('description') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">{{ old('description') ?? @$payment->description }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-gray-100">

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="reset"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        Reset Form
                    </button>
                    <button type="submit"
                        class="btn-primary inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm shadow-md hover:shadow-amber-500/20 transition-all">
                        <i class="fa-solid fa-receipt"></i> Save Payment Record
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
