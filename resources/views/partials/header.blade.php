<!-- Header -->
<header class="bg-white shadow-md border-b border-gray-200">
    <div class="flex items-center justify-between px-8 py-4">
        <!-- Left Section: Title and Search -->
        <div class="flex items-center gap-6 flex-1">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
                <p class="text-sm text-gray-500">{{ date('l, F j, Y') }}</p>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-md">
                <div class="relative w-full">
                    <input type="text" placeholder="Search products, customers..."
                        class="w-full px-4 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Right Section: Notifications and User Menu -->
        <div class="flex items-center gap-6">
            <!-- Quick Stats -->
            <div class="hidden lg:flex items-center gap-4 pr-4 border-r border-gray-200">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Today's Sales</p>
                    <p class="text-lg font-bold text-green-600">₨45,230</p>
                </div>
                <div class="w-px h-8 bg-gray-300"></div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Stock Status</p>
                    <p class="text-lg font-bold text-amber-600">125 items</p>
                </div>
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button onclick="toggleNotifications()" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1 -translate-y-1 bg-red-600 rounded-full">3</span>
                </button>

                <!-- Notifications Dropdown -->
                <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-800">Notifications</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="font-medium text-sm text-gray-800">Low Stock Alert</p>
                                    <p class="text-xs text-gray-500">Chicken Breast - Only 5 items left</p>
                                    <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="font-medium text-sm text-gray-800">Pending Order</p>
                                    <p class="text-xs text-gray-500">Order #1024 waiting for approval</p>
                                    <p class="text-xs text-gray-400 mt-1">15 minutes ago</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <p class="font-medium text-sm text-gray-800">Sale Completed</p>
                                    <p class="text-xs text-gray-500">Sale #1023 completed successfully</p>
                                    <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200 text-center">
                        <a href="#" class="text-sm text-amber-600 font-medium hover:text-amber-700">View All Notifications</a>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative">
                <button onclick="toggleUserMenu()" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-100 rounded-lg transition">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="User" class="w-8 h-8 rounded-full">
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-600"></i>
                </button>

                <!-- User Dropdown Menu -->
                <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50">
                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                        <i class="fas fa-user-circle mr-2"></i> My Profile
                    </a>
                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                        <i class="fas fa-sliders-h mr-2"></i> Preferences
                    </a>
                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notifications-dropdown');
        dropdown.classList.toggle('hidden');
        document.getElementById('user-dropdown').classList.add('hidden');
    }

    function toggleUserMenu() {
        const dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('hidden');
        document.getElementById('notifications-dropdown').classList.add('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationsBtn = event.target.closest('[onclick="toggleNotifications()"]');
        const userBtn = event.target.closest('[onclick="toggleUserMenu()"]');

        if (!notificationsBtn && !document.getElementById('notifications-dropdown').contains(event.target)) {
            document.getElementById('notifications-dropdown').classList.add('hidden');
        }
        if (!userBtn && !document.getElementById('user-dropdown').contains(event.target)) {
            document.getElementById('user-dropdown').classList.add('hidden');
        }
    });
</script>
