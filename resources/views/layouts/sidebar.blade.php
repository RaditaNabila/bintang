<div id="sidebarBackdrop"
     onclick="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
</div>

<aside id="sidebar"
    class="fixed lg:sticky top-0 left-0 z-50 w-56 h-screen
           bg-gradient-to-b from-amber-500 to-orange-500
           text-white flex flex-col justify-between shadow-lg shrink-0
           transform -translate-x-full lg:translate-x-0
           transition-transform duration-300 ease-in-out">

    {{-- BAGIAN ATAS --}}
    <div>

        {{-- LOGO --}}
        <div class="p-3.5 flex items-center gap-2.5 border-b border-amber-400/40">
            <div class="bg-white p-1.5 rounded-lg shadow-md flex items-center justify-center">
                <i class="fa-solid fa-star text-amber-500 text-base"></i>
            </div>

            <div>
                <h1 class="font-bold text-sm leading-tight">
                    Bintang Poin
                </h1>

                <p class="text-[10px] text-amber-100">
                    SDIT Nurul Fikri
                </p>
            </div>
        </div>


        {{-- NAVIGATION --}}
        <nav class="p-2.5 space-y-0.5 text-sm font-medium">

            {{-- DASHBOARD --}}
            <a href="{{ route('guru.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.dashboard')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-chart-pie w-4 text-center text-xs"></i>
                <span>Dashboard</span>
            </a>


            {{-- KELOLA AKUN --}}
            <a href="{{ route('guru.kelola') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.kelola')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-user-gear w-4 text-center text-xs"></i>
                <span>Kelola Akun</span>
            </a>


            {{-- DATA SISWA --}}
            <a href="{{ route('guru.data-siswa') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.data-siswa', 'guru.naik-kelas')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-user-graduate w-4 text-center text-xs"></i>
                <span>Data Siswa</span>
            </a>


            {{-- RELASI --}}
            <a href="{{ route('guru.relasi-wali-siswa.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.relasi-wali-siswa.index')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-link w-4 text-center text-xs"></i>
                <span>Relasi Wali-Siswa</span>
            </a>


            {{-- POIN PRESTASI --}}
            <a href="{{ route('guru.poin-prestasi') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.poin-prestasi')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-award w-4 text-center text-xs"></i>
                <span>Poin Prestasi</span>
            </a>


            {{-- PELANGGARAN --}}
            <a href="{{ route('guru.pelanggaran') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.pelanggaran')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-triangle-exclamation w-4 text-center text-xs"></i>
                <span>Pelanggaran</span>
            </a>


            {{-- LAPORAN --}}
            <a href="{{ route('guru.laporan') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('guru.laporan')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}">

                <i class="fa-solid fa-file-invoice w-4 text-center text-xs"></i>
                <span>Laporan</span>
            </a>

            {{-- INFORMASI --}}
            <a
                href="{{ route('guru.informasi') }}"
                onclick="closeSidebar()"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('guru.informasi')
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'hover:bg-white/10 text-amber-50' }}"
            >
                <i class="fa-solid fa-circle-info w-4 text-center text-xs"></i>
                <span>Informasi</span>
            </a>

            {{-- ARSIP --}}
            <a
                href="{{ route('guru.arsip') }}"
                onclick="closeSidebar()"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('guru.arsip') ? 'bg-white/20 text-white shadow-sm' : 'hover:bg-white/10 text-amber-50' }}"
            >
                <i class="fa-solid fa-box-archive w-4 text-center text-xs"></i>
                <span>Arsip</span>
            </a>

        </nav>
    </div>


    {{-- USER DINAMIS DARI DATABASE --}}
    <div class="p-3 border-t border-amber-400/40">

        <div class="flex items-center justify-between">

            <div class="flex items-center gap-2.5 min-w-0">

                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs shrink-0">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                </div>

                <div class="overflow-hidden">

                    <p class="text-xs font-semibold leading-none truncate">
                        {{ Auth::user()->nama ?? 'Pengguna' }}
                    </p>

                    <span class="text-[10px] text-amber-200 capitalize">
                        {{ Auth::user()->peran ?? 'Guru' }}
                    </span>

                </div>
            </div>


            {{-- LOGOUT --}}
            <button type="button"
                    onclick="openLogoutModal()"
                    class="text-amber-100 hover:text-white p-1 transition"
                    title="Keluar">

                <i class="fa-solid fa-right-from-bracket text-xs"></i>

            </button>

        </div>

    </div>

</aside>

{{-- MODAL KONFIRMASI LOGOUT --}}
<div id="logoutModal"
     class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">

    <div id="logoutModalContent"
         class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-200">

        {{-- ICON --}}
        <div class="flex justify-center pt-6">
            <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                <i class="fa-solid fa-right-from-bracket text-xl"></i>
            </div>
        </div>

        {{-- ISI --}}
        <div class="px-6 pt-4 pb-5 text-center">

            <h3 class="text-base font-bold text-gray-800">
                Konfirmasi Keluar
            </h3>

            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                Apakah Anda yakin ingin keluar dari sistem
                <span class="font-semibold text-gray-700">Bintang Poin</span>?
            </p>

        </div>

        {{-- BUTTON --}}
        <div class="px-6 pb-6 flex gap-3">

            <button type="button"
                    onclick="closeLogoutModal()"
                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
                Batal
            </button>

            <!-- Form POST untuk Logout -->
            <form action="{{ route('logout') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl text-xs font-semibold text-center transition shadow-sm">
                    Ya, Keluar
                </button>
            </form>

        </div>

    </div>
</div>


<script>
    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const content = document.getElementById('logoutModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const content = document.getElementById('logoutModalContent');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    document.getElementById('logoutModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeLogoutModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeLogoutModal();
        }
    });
</script>
