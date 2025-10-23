<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hadir.in')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-transition fixed left-0 top-0 flex h-screen w-64 flex-shrink-0 flex-col overflow-hidden">
            <!-- Background Image -->
            <img src="{{ asset('images/background-sidebar.png') }}" alt="Sidebar Background"
                class="absolute inset-0 z-0 h-full w-full object-cover">

            <!-- Top Section -->
            <div class="relative z-10 bg-transparent p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300">
                            <img src="" alt="">
                        </div>
                        <h1 class="sidebar-label text-xl font-bold text-gray-800">Hadir.in</h1>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="relative z-10 flex-1 space-y-2 p-4">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'bg-[#60B5FF] text-white' : 'text-gray-600 hover:bg-blue-100' }} flex items-center space-x-3 rounded-lg p-3 font-medium transition-colors">
                    <img src="{{ asset(request()->routeIs('dashboard') ? 'images/dashboard_white.png' : 'images/dashboard_black.png') }}"
                        alt="Dashboard" class="h-6 w-6">
                    <span class="sidebar-label">Dashboard</span>
                </a>

                <!-- Data Karyawan -->
                <a href="{{ route('employees') }}"
                    class="{{ request()->routeIs('employees', 'employee.details') ? 'bg-[#60B5FF] text-white' : 'text-gray-600 hover:bg-blue-100' }} flex items-center space-x-3 rounded-lg p-3 font-medium transition-colors">
                    <img src="{{ asset(request()->routeIs('employees', 'employee.details') ? 'images/employee_white.png' : 'images/employee_black.png') }}"
                        alt="Data Karyawan" class="h-6 w-6">
                    <span class="sidebar-label">Data Karyawan</span>
                </a>

                <!-- Data Absensi -->
                <a href="{{ route('attendance') }}"
                    class="{{ request()->routeIs('attendance') ? 'bg-[#60B5FF] text-white' : 'text-gray-600 hover:bg-blue-100' }} flex items-center space-x-3 rounded-lg p-3 font-medium transition-colors">
                    <img src="{{ asset(request()->routeIs('attendance') ? 'images/attandance_data.png' : 'images/attandance_black.png') }}"
                        alt="Data Absensi" class="h-6 w-6">
                    <span class="sidebar-label">Data Absensi</span>
                </a>

                <!-- Manajemen Akun -->
                <a href="{{ route('account.management') }}"
                    class="{{ request()->routeIs('account.management') ? 'bg-[#60B5FF] text-white' : 'text-gray-600 hover:bg-blue-100' }} flex items-center space-x-3 rounded-lg p-3 font-medium transition-colors">
                    <img src="{{ asset(request()->routeIs('account.management') ? 'images/account_management.png' : 'images/account_manajement.png') }}"
                        alt="Manajemen Akun" class="h-6 w-6">
                    <span class="sidebar-label">Manajemen Akun</span>
                </a>
            </nav>


            <!-- Bottom Section -->
            <div class="relative z-10 mb-8 p-4">
                <button
                    class="flex w-full items-center justify-start space-x-2 rounded-lg bg-white px-4 py-3 text-red-500 shadow-sm transition-colors hover:bg-red-50">
                    <i class="fas fa-sign-out-alt text-red-500"></i>
                    <span class="sidebar-label">Keluar</span>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex min-w-0 flex-1 flex-col ml-64 h-screen overflow-y-auto">
            <!-- Top Header/Navbar -->
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Collapse Button di Navbar -->
                        <button id="sidebarToggle"
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#60B5FF] shadow-lg transition-colors hover:bg-blue-400">
                            <i class="fas fa-angle-double-left text-white" id="toggleIcon"></i>
                        </button>
                        <button id="mobileSidebarToggle" class="text-gray-500 hover:text-gray-700 lg:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notification Bell -->
                        <a href="#" class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 transition-colors hover:bg-gray-300">
                                <i class="fas fa-bell text-lg"></i>
                            </div>
                        </a>
                        <!-- User Profile -->
                        <a href="#" class="flex items-center space-x-3 focus:outline-none">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300 transition-colors hover:bg-gray-400">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <span class="font-medium text-gray-700">Admin user</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="min-w-0 flex-1 bg-blue-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Token management script -->
    <script>
        // Ensure token is available in localStorage from cookie
        document.addEventListener('DOMContentLoaded', function() {
            // Try to get token from cookie and save to localStorage as backup
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'auth_token') {
                    localStorage.setItem('auth_token', value);
                    sessionStorage.setItem('auth_token', value);
                    console.log('Token synced from cookie to localStorage');
                    break;
                }
            }
        });
    </script>

    <!-- Sidebar toggle script -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.flex.min-w-0.flex-1.flex-col');
            const labels = document.querySelectorAll('.sidebar-label');
            const toggleIcon = document.getElementById('toggleIcon');

            const isCollapsed = sidebar.classList.contains('w-64');

            if (isCollapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-20');
                labels.forEach(label => label.classList.add('hidden'));
                toggleIcon.classList.remove('fa-angle-double-left');
                toggleIcon.classList.add('fa-angle-double-right');
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('ml-20');
                mainContent.classList.add('ml-64');
                labels.forEach(label => label.classList.remove('hidden'));
                toggleIcon.classList.remove('fa-angle-double-right');
                toggleIcon.classList.add('fa-angle-double-left');
            }
        });

        document.getElementById('mobileSidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>

</html>
