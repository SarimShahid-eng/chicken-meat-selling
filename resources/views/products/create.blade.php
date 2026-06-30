@extends('partials.app', ['title' => 'Create Product'])

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
                <p class="text-sm text-gray-500 mt-1">Manage and expand your Chicken POS menu items.</p>
            </div>
            <a href="{{ route('products.index') }}"
                class="btn-secondary flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <form action="{{ route('products.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-utensils"></i>
                            </span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                placeholder="e.g., Crispy Chicken Breast"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border @error('name') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="soldCreateWise" class="block text-sm font-semibold text-gray-700">
                            Sold Create Wise <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </span>
                            <select id="soldCreateWise" name="soldCreateWise" required
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border @error('soldCreateWise') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors appearance-none">

                                <option value="" disabled {{ old('soldCreateWise') === null ? 'selected' : '' }}>
                                    Select an option</option>

                                <option value="0" {{ old('soldCreateWise') === '0' ? 'selected' : '' }}>Yes</option>

                                <option value="1" {{ old('soldCreateWise') === '1' ? 'selected' : '' }}>No, Sold By
                                    Weight (Per KG)</option>
                            </select>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        @error('soldCreateWise')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4"
                        placeholder="Describe the item parameters, processing method, or packaging details..."
                        class="w-full px-4 py-2.5 bg-gray-50 border @error('description') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @enderror rounded-lg text-gray-900 focus:outline-none transition-colors placeholder:text-gray-400">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-gray-100">

                {{-- <div class="flex items-center justify-between bg-amber-50/50 p-4 rounded-lg border border-amber-100/50">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-amber-900">Product Availability Status</span>
                        <span class="text-xs text-amber-700/80">Active products appear instantly in the POS checkout
                            screen.</span>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500">
                        </div>
                    </label>
                </div> --}}

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="reset"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                        Reset Form
                    </button>
                    <button type="submit"
                        class="btn-primary inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm shadow-md hover:shadow-amber-500/20 transition-all">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
