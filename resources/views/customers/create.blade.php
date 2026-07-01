@extends('partials.app', ['title' => isset($customer) ? 'Edit Customer' : 'Create Customer'])

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isset($customer) ? 'Edit Customer' : 'Add New Customer' }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Manage profiles, regional routes, and account balances for retail or
                    hotel clients.</p>
            </div>
            <a href="{{ route('customers.index') }}"
                class="btn-secondary flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <form action="{{ route('customers.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf
                {{-- @if (isset($customer))
                    @method('PUT')
                @endif --}}

                <input type="hidden" name="update_id" value="{{ old('update_id') ?? @$customer->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700">
                            Customer Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" id="name" name="name"
                                value="{{ old('name') ?? @$customer->name }}" required
                                placeholder="e.g., John Doe or Grand Hyatt Hotel"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('name') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="phone_number" class="block text-sm font-semibold text-gray-700">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="text" id="phone_number" name="phone_number"
                                value="{{ old('phone_number') ?? @$customer->phone_number }}" required
                                placeholder="e.g., +1 555-0144"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('phone_number') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('phone_number')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="region_id" class="block text-sm font-semibold text-gray-700">
                            Assigned Region <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-map-pin"></i>
                            </span>
                            <select id="region_id" name="region_id" required
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border @error('region_id') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors appearance-none">

                                <option value="" disabled
                                    {{ old('region_id') === null && !isset($customer) ? 'selected' : '' }}>
                                    Select a region
                                </option>

                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}"
                                        {{ (string) old('region_id', @$customer->region_id) === (string) $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        @error('region_id')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="category" class="block text-sm font-semibold text-gray-700">
                            Customer Category <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-tags"></i>
                            </span>
                            <select id="category" name="category" required
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border @error('category') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors appearance-none">

                                <option value="" disabled
                                    {{ old('category') === null && !isset($customer) ? 'selected' : '' }}>
                                    Select a classification
                                </option>
                                <option value="customer"
                                    {{ old('category', @$customer->category) === 'customer' ? 'selected' : '' }}>
                                    Standard Customer / Retailer
                                </option>
                                <option value="hotel"
                                    {{ old('category', @$customer->category) === 'hotel' ? 'selected' : '' }}>
                                    Hotel / Commercial Client
                                </option>
                            </select>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        @error('category')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 ">
                        <label for="date" class="block text-sm font-semibold text-gray-700">
                            Date
                        </label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 font-medium text-sm">
                                <i class="fa-solid fa-calendar"></i>
                            </span>
                            <input type="date" id="date" name="date"
                                value="{{ old('date', isset($customer) ? date('Y-m-d', strtotime($customer->date)) : date('Y-m-d')) }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('date') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('date')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2 ">
                        <label for="opening_balance" class="block text-sm font-semibold text-gray-700">
                            Opening Balance
                        </label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 font-medium text-sm">
                                <i class="fa-solid fa-wallet"></i>
                            </span>
                            <input type="number" step="0.01" id="opening_balance" name="opening_balance"
                                value="{{ old('opening_balance') ?? (@$customer->opening_balance ?? '0.00') }}"
                                placeholder="0.00"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('opening_balance') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('opening_balance')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="space-y-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700">Description / Logistical
                        Notes</label>
                    <textarea id="description" name="description" rows="4"
                        placeholder="Add special delivery instructions, specific wholesale price tiers or credit limits..."
                        class="w-full px-4 py-2.5 bg-gray-50 border @error('description') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">{{ old('description') ?? @$customer->description }}</textarea>
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
                        <i class="fa-solid fa-cloud-arrow-up"></i> Save Customer Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
