<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bintang Poin')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-amber-50/40 text-gray-800 min-h-screen">
    <!-- Layout Sidebar + Content -->
    <div class="flex min-h-screen">
        {{-- SIDEBAR BERDASARKAN ROLE --}}
        @if(Auth::user()->peran === 'guru')
            {{-- SIDEBAR PENGAJAR --}}
            @include('layouts.sidebar-pengajar')
        @else
            {{-- SIDEBAR ADMIN --}}
            @include('layouts.sidebar')
        @endif

        <!-- Main Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="bg-white border-b px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <!-- Mobile Burger -->
                <button type="button" onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Page Title -->
                <div class="ml-3 lg:ml-0 min-w-0">
                    <h2 class="text-base sm:text-xl font-bold text-gray-800 truncate">
                        @yield('page_title', 'Dashboard Utama')
                    </h2>
                    <p class="text-[10px] sm:text-xs text-gray-500 truncate">
                        @yield('page_description', 'Ringkasan statistik dan aktivitas poin siswa')
                    </p>
                </div>

                <!-- Date -->
                <div class="hidden sm:flex items-center gap-2 px-3 sm:px-4 py-2 bg-amber-50 border border-amber-200/60 rounded-xl text-amber-900 text-xs sm:text-sm font-medium shadow-sm ml-3">
                    <i class="fa-regular fa-calendar-days text-amber-600"></i>
                    <span id="currentDate">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </span>
                </div>
            </header>

            <!-- Dynamic Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script -->
    <script>
        // ==========================================
        // SIDEBAR MOBILE
        // ==========================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!sidebar || !backdrop) {
                return;
            }

            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!sidebar || !backdrop) {
                return;
            }

            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        // ==========================================
        // TANGGAL OTOMATIS
        // ==========================================
        const dateElement = document.getElementById('currentDate');

        if (dateElement) {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            const today = new Date().toLocaleDateString(
                'id-ID',
                options
            );

            dateElement.textContent = today;
        }
    </script>

    @stack('scripts')
</body>
</html>
