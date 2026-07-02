<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Chicken Meat POS' }} - Chicken POS</title>
    <!-- 4. Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- 1. Tailwind First -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- 2. Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- 3. jQuery (MUST be before Select2 JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!-- 5. Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- 6. Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('partials.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-auto bg-gradient-to-br from-gray-50 to-gray-100">
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
           <x-toast />

            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }

        // Toggle submenu with chevron rotation
        function toggleSubmenu(menuId) {
            const menu = document.getElementById(menuId);
            const chevron = document.getElementById(menuId.replace('menu', 'chevron'));

            menu.classList.toggle('hidden');

            // Rotate chevron icon
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        // Initialize chevron rotation for active submenus on page load
        document.addEventListener('DOMContentLoaded', function() {
            const activeMenus = ['sales-menu', 'inventory-menu', 'reports-menu'];

            activeMenus.forEach(menuId => {
                const menu = document.getElementById(menuId);
                const chevron = document.getElementById(menuId.replace('menu', 'chevron'));

                // If menu is not hidden (active), rotate the chevron
                if (menu && !menu.classList.contains('hidden') && chevron) {
                    chevron.classList.add('rotate-180');
                }
            });
        });

        // Highlight active nav link (fallback for older browsers)
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
