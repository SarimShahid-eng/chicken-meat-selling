<!-- Sidebar Navigation -->
<div id="sidebar" class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white shadow-2xl flex flex-col h-screen">
    <!-- Logo Section -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div
                class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-drumstick-bite text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">ChickenPOS</h1>
                <p class="text-xs text-gray-400">Point of Sale</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto p-4">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') ?? '#' }}"
                class="nav-link      @routeis('dashboard')
                active
                @endrouteis">
                <i class="fas fa-chart-line w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <!-- Products -->
            <div class="space-y-2">
                <button onclick="toggleSubmenu('products-menu')"
                    class="w-full nav-link justify-between
                @routeis('products.*')
                    active
                    @else
                    hidden
                    @endrouteis">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cash-register w-5 text-center"></i>
                        <span>Products</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="products-menu"
                    class=" pl-8 space-y-2 @routeis('products.*')
                            active
                            @else
                            hidden
                            @endrouteis">
                    <a href="{{ route('products.create') ?? '#' }}"
                        class="nav-link text-sm  @routeis('products.create')
                            active
                            @endrouteis">
                        <i class="fas fa-plus w-4 text-center"></i>
                        <span>New Product</span>
                    </a>
                    <a href="{{ route('products.index') ?? '#' }}"
                        class="nav-link text-sm
                    @routeis('products.index')
                        active
                        @endrouteis
                    ">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Products</span>
                    </a>
                </div>
            </div>
            <!-- Suppliers -->
            <div class="space-y-2">
                <button onclick="toggleSubmenu('suppliers-menu')"
                    class="w-full nav-link justify-between
                    @routeis(['suppliers.*', 'suppliersPayment.*'])
                    active
                    @else
                    hidden
                    @endrouteis">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span>Suppliers</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="suppliers-menu"
                    class=" pl-8 space-y-2 @routeis(['suppliers.*', 'suppliersPayment.*'])
                            active
                            @else
                            hidden
                            @endrouteis">
                    <a href="{{ route('suppliers.create') ?? '#' }}"
                        class="nav-link text-sm  @routeis('suppliers.create')
                                active
                                @endrouteis">
                        <i class="fas fa-plus w-4 text-center"></i>
                        <span>New Supplier</span>
                    </a>
                    <a href="{{ route('suppliers.index') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis('suppliers.index')
                                    active
                                    @endrouteis
                                                                    ">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Suppliers</span>
                    </a>
                    <a href="{{ route('suppliersPayment.index') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis('suppliersPayment.*')
                                    active
                                    @endrouteis
                                ">
                        <i class="fas fa-receipt w-4 text-center"></i>
                        <span>Payment</span>
                    </a>
                </div>
            </div>
            <!-- Customers -->
            <div class="space-y-2">
                <button onclick="toggleSubmenu('customers-menu')"
                    class="w-full nav-link justify-between
                    @routeis(['customers.*', 'customersPayment.*'])
                            active
                            @else
                            hidden
                            @endrouteis">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user w-5 text-center"></i>
                        <span>Customers</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="customers-menu"
                    class=" pl-8 space-y-2 @routeis(['customers.*', 'customersPayment.*'])
                                    active
                                    @else
                                    hidden
                                    @endrouteis">
                    <a href="{{ route('customers.create') ?? '#' }}"
                        class="nav-link text-sm  @routeis('customers.create')
                                active
                                @endrouteis">
                        <i class="fas fa-plus w-4 text-center"></i>
                        <span>New Customer</span>
                    </a>
                    <a href="{{ route('customers.index') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis('customers.index')
                                    active
                                    @endrouteis
                                                                    ">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Customers</span>
                    </a>
                    <a href="{{ route('customersPayment.index') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis('customersPayment.*')
                                    active
                                    @endrouteis
                                ">
                        <i class="fas fa-receipt w-4 text-center"></i>
                        <span>Payment</span>
                    </a>
                </div>
            </div>
            <!-- Purchase -->
            <div class="space-y-2">
                <button onclick="toggleSubmenu('purchases-menu')"
                    class="w-full nav-link justify-between
                    @routeis('purchases.*')
                            active
                            @else
                            hidden
                            @endrouteis">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-bag-shopping text-center"></i>
                        <span>Purchase</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="purchases-menu"
                    class=" pl-8 space-y-2 @routeis('purchases.*')
                        active
                        @else
                        hidden
                        @endrouteis">
                    <a href="{{ route('purchases.create') ?? '#' }}"
                        class="nav-link text-sm  @routeis('purchases.create')
                        active
                        @endrouteis">
                        <i class="fas fa-plus w-4 text-center"></i>
                        <span>New Purchase</span>
                    </a>
                    <a href="{{ route('purchases.index') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis('purchases.index')
                                        active
                                        @endrouteis
                                ">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Purchases</span>
                    </a>
                </div>
            </div>
            <!-- Sale -->
            <div class="space-y-2">
                <button onclick="toggleSubmenu('sales-menu')"
                    class="w-full nav-link justify-between
                    @routeis('sales.*')
                            active
                            @else
                            hidden
                            @endrouteis">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-sellsy"></i>
                        <span>Sale</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="sales-menu"
                    class=" pl-8 space-y-2 @routeis('sales.*')
                        active
                        @else
                        hidden
                        @endrouteis">
                    <a href="{{ route('sales.create') ?? '#' }}"
                        class="nav-link text-sm  @routeis('sales.create')
                        active
                        @endrouteis">
                        <i class="fas fa-plus w-4 text-center"></i>
                        <span>New Sale</span>
                    </a>
                    <a href="{{ route('sales.index') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis('sales.index')
                                        active
                                        @endrouteis
                                ">
                        <i class="fas fa-list w-4 text-center"></i>
                        <span>All Sales</span>
                    </a>
                </div>
            </div>
            <!-- Ledger -->
            <div class="space-y-2">
                <button onclick="toggleSubmenu('ledger-menu')"
                    class="w-full nav-link justify-between
                    @routeis('ledger.*')
                            active
                            @else
                            hidden
                            @endrouteis">
                    <div class="flex items-center gap-3">
                      <i class="fa-solid fa-book"></i>
                        <span>Ledger</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="ledger-menu"
                    class=" pl-8 space-y-2 @routeis('ledger.*')
                        active
                        @else
                        hidden
                        @endrouteis">
                    <a href="{{ route('ledger.supplier') ?? '#' }}"
                        class="nav-link text-sm  @routeis(['ledger.supplier','ledger.supplierReport'])
                        active
                        @endrouteis">
                        <i class="fas fa-users w-4 text-center"></i>
                        <span>Supplier</span>
                    </a>
                    <a href="{{ route('ledger.customer') ?? '#' }}"
                        class="nav-link text-sm
                                @routeis(['ledger.customer','ledger.customerReport'])
                                        active
                                        @endrouteis
                                ">
                        <i class="fas fa-user w-4 text-center"></i>
                        <span>Customer</span>
                    </a>
                </div>
            </div>


        </div>
    </nav>

    <!-- User Profile Section -->
    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="User"
                    class="w-10 h-10 rounded-full">
                <div class="text-sm">
                    <p class="font-medium text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-400">Administrator</p>
                </div>
            </div>
            <button onclick="logout()" class="text-gray-400 hover:text-red-400 transition">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>
</div>

<script>
    function toggleSubmenu(menuId) {
        const menu = document.getElementById(menuId);
        menu.classList.toggle('hidden');
    }

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            // Add your logout route here
            window.location.href = "{{ route('logout') }}";
        }
    }
</script>
