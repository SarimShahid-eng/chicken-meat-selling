@extends('partials.app', ['title' => 'Products'])

@section('content')
    <div class="space-y-6">
        <!-- Header with Actions -->
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Products</h1>
                <p class="text-gray-600 mt-1">Manage your chicken meat products inventory</p>
            </div>
            <button class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Add New Product
            </button>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" placeholder="Search products..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <option>All Categories</option>
                        <option>Breast</option>
                        <option>Legs</option>
                        <option>Wings</option>
                        <option>Whole</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="btn-primary w-full">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-hover">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Product</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Category</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Price</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Stock</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Product Row 1 -->
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-drumstick-bite text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Chicken Breast</p>
                                        <p class="text-sm text-gray-500">Premium grade</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Breast</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-amber-600">₨450/KG</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-800">245</p>
                                    <p class="text-xs text-gray-500">Available</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-success">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 2 -->
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-drumstick-bite text-blue-600 transform -rotate-45"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Chicken Legs</p>
                                        <p class="text-sm text-gray-500">With thighs</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Legs</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-amber-600">₨380/KG</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-800">189</p>
                                    <p class="text-xs text-gray-500">Available</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-success">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 3 -->
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-feather text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Chicken Wings</p>
                                        <p class="text-sm text-gray-500">Fresh cut</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Wings</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-amber-600">₨320/KG</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-800">45</p>
                                    <p class="text-xs text-yellow-600">Low stock</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-warning">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 4 -->
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-egg text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Whole Chicken</p>
                                        <p class="text-sm text-gray-500">Dressed & cleaned</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Whole</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-amber-600">₨550/KG</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-800">78</p>
                                    <p class="text-xs text-gray-500">Available</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-success">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 5 -->
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-bone text-pink-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Chicken Bones</p>
                                        <p class="text-sm text-gray-500">For broth</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">Bones</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-amber-600">₨120/KG</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-800">0</p>
                                    <p class="text-xs text-red-600">Out of stock</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-danger">Inactive</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-600">Showing 1 to 5 of 12 products</p>
                <div class="flex gap-2">
                    <button class="btn-secondary">Previous</button>
                    <button class="btn-primary">Next</button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Products</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">12</h3>
                        <p class="text-blue-600 text-sm mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span>1 inactive</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Low Stock Items</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">3</h3>
                        <p class="text-yellow-600 text-sm mt-2">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>Need reorder</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Average Price</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">₨384</h3>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-dollar-sign mr-1"></i>
                            <span>Per KG</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
