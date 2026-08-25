<header class="bg-white border-b px-4 md:px-8 py-4 flex justify-between items-center sticky top-0 z-10">
  <div class="flex items-center gap-3">
    <!-- Tombol Burger Menu (Hanya muncul di HP/Layar Kecil) -->
    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-600 hover:text-amber-600 focus:outline-none p-2 rounded-lg border border-gray-200">
      <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <div>
      <h2 class="text-lg md:text-xl font-bold text-gray-800">@yield('page_title', 'Dashboard Utama')</h2>
      <p class="text-xs text-gray-500 hidden sm:block">@yield('page_description', 'Ringkasan statistik dan aktivitas poin siswa')</p>
    </div>
  </div>

  <div class="flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-amber-50 border border-amber-200/60 rounded-xl text-amber-900 text-xs md:text-sm font-medium shadow-sm">
    <i class="fa-regular fa-calendar-days text-amber-600"></i>
    <span id="currentDate"></span>
  </div>
</header>
