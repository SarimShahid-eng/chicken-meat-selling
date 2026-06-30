@extends('partials.app', ['title' => 'Dashboard'])

@section('content')

    <div class="space-y-6">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Sales Card -->
            <div class="stat-card">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Sales</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">₨245,320</h3>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>+12.5% from last month</span>
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">1,234</h3>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>+8.2% from last month</span>
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Stock Status Card -->
            <div class="stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Stock Items</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">1,847</h3>
                        <p class="text-yellow-600 text-sm mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <span>12 items low</span>
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Customers Card -->
            <div class="stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Customers</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">456</h3>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>+15 new this month</span>
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sales Chart -->
            <div class="lg:col-span-2 stat-card">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Sales Overview</h3>
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Top Products -->
            <div class="stat-card">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Top Products</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Chicken Breast</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full"
                                    style="width: 85%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 ml-3">85%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Chicken Legs</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full" style="width: 72%">
                                </div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 ml-3">72%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Chicken Wings</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full"
                                    style="width: 68%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 ml-3">68%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Whole Chicken</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-gradient-to-r from-red-400 to-red-600 h-2 rounded-full" style="width: 55%">
                                </div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800 ml-3">55%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Sales & Inventory -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Sales -->
            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Recent Sales</h3>
                    <a href="#" class="text-amber-600 font-medium text-sm hover:text-amber-700">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm table-hover">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Order ID</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Customer</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Amount</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-800">#1024</td>
                                <td class="px-4 py-3 text-gray-600">Ahmed Hassan</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">₨2,450</td>
                                <td class="px-4 py-3"><span class="badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-800">#1023</td>
                                <td class="px-4 py-3 text-gray-600">Fatima Khan</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">₨1,850</td>
                                <td class="px-4 py-3"><span class="badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-800">#1022</td>
                                <td class="px-4 py-3 text-gray-600">Ali Muhammad</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">₨3,200</td>
                                <td class="px-4 py-3"><span class="badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-800">#1021</td>
                                <td class="px-4 py-3 text-gray-600">Sara Ahmed</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">₨2,100</td>
                                <td class="px-4 py-3"><span class="badge-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="stat-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Stock Alerts</h3>
                    <a href="#" class="text-amber-600 font-medium text-sm hover:text-amber-700">Manage Stock</a>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-4 p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Chicken Breast</p>
                            <p class="text-sm text-gray-600">Only 5 items left in stock</p>
                        </div>
                        <button class="btn-primary text-sm whitespace-nowrap">Reorder</button>
                    </div>

                    <div class="flex items-center gap-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Chicken Wings</p>
                            <p class="text-sm text-gray-600">Only 12 items left in stock</p>
                        </div>
                        <button class="btn-primary text-sm whitespace-nowrap">Reorder</button>
                    </div>

                    <div class="flex items-center gap-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">Chicken Legs</p>
                            <p class="text-sm text-gray-600">Stock level: 245 items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="stat-card">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button
                    class="flex items-center gap-3 p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:shadow-lg transition border border-green-200">
                    <i class="fas fa-plus-circle text-2xl text-green-600"></i>
                    <span class="font-medium text-gray-800">New Sale</span>
                </button>
                <button
                    class="flex items-center gap-3 p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:shadow-lg transition border border-blue-200">
                    <i class="fas fa-plus-square text-2xl text-blue-600"></i>
                    <span class="font-medium text-gray-800">Add Inventory</span>
                </button>
                <button
                    class="flex items-center gap-3 p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:shadow-lg transition border border-purple-200">
                    <i class="fas fa-user-plus text-2xl text-purple-600"></i>
                    <span class="font-medium text-gray-800">New Customer</span>
                </button>
                <button
                    class="flex items-center gap-3 p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg hover:shadow-lg transition border border-orange-200">
                    <i class="fas fa-print text-2xl text-orange-600"></i>
                    <span class="font-medium text-gray-800">Print Report</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales',
                    data: [12000, 19000, 15000, 25000, 22000, 28000, 32000],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
