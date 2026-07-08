@extends('partials.app', ['title' => 'Customers'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Customers</h1>
                <p class="text-gray-500 mt-1 text-sm">Manage your chicken meat inventory and stock levels</p>
            </div>
            <a href="{{ route('customers.create') }}"
                class="btn-primary cursor-pointer inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition-colors text-sm">
                <i class="fas fa-plus mr-2 text-xs"></i>
                Add New Customer
            </a>
        </div>

        <div class="flex justify-start">
            <div class="w-full max-w-md bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Search Inventory
                </label>
                <div class="flex items-center gap-2">
                    <form action="{{ route('customers.index') }}" method="GET" class="flex items-center gap-2 w-full">
                        <div class="relative flex-1">
                            <input type="text" placeholder="Search customers..." name="search"
                                value="{{ request('search') }}"
                                class="w-full pl-3 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                        </div>
                        <button type="submit"
                            class="btn-sm btn-primary bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                            Search
                        </button>
                        <a href="{{ route('customers.index') }}"
                            class="btn-sm cursor-pointer bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                            Reset
                        </a>
                        <button type="submit" name="export" value="pdf"
                            class="btn-sm btn-danger bg-red-700 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                            Export
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm text-gray-500">
                    <thead class="bg-gray-50/70 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Name</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Phone number</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Category</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Opening Balance</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700">Balance</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-gray-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="text-sm">
                                        {{ $customer->name }}/ {{ $customer->region->name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-gray-500">
                                    <div class="max-w-xs truncate" title="{{ $customer->phone_number }}">
                                        {{ $customer->phone_number ?? 'No phone.no available.' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ ucfirst($customer->category) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $customer->opening_balance }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $customer->current_balance }}
                                </td>

                                {{-- <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $customer->region->name }}
                                </td> --}}

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('customers.edit', ['customer' => $customer->id]) }}"
                                            class="cursor-pointer text-gray-400 hover:text-blue-600 transition-colors"
                                            title="Edit Customer">
                                            <i class="fas fa-edit text-base"></i>
                                        </a>
                                        {{-- <button class="text-gray-400 hover:text-red-600 transition-colors"
                                            title="Delete Customer">
                                            <i class="fas fa-trash text-base"></i>
                                        </button> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-box-open text-4xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No customers found</p>
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
            {{ $customers->links() }}
        </div>
    </div>
@endsection
