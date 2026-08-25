<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Wali Murid - Bintang Poin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50/60 text-gray-800 min-h-screen flex flex-col">

    {{-- HEADER --}}
    <header class="bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md sticky top-0 z-20">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex items-center justify-between">
            {{-- Logo & Back --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('pilih-peran') }}"
                   class="bg-white/20 hover:bg-white/30 p-2 rounded-xl transition text-white"
                   title="Kembali ke Pilihan Peran">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>

                <div class="flex items-center gap-2">
                    <div class="bg-white p-1.5 rounded-lg text-amber-500 text-base font-bold">
                        <i class="fa-solid fa-star"></i>
                    </div>

                    <div>
                        <h1 class="font-bold text-sm leading-tight">
                            Bintang Poin
                        </h1>
                        <p class="text-[10px] text-amber-100">
                            Portal Wali Murid
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Selector Anak --}}
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/20">
                    <i class="fa-solid fa-user-graduate text-xs text-amber-100"></i>

                    <select id="childSelector"
                            onchange="switchChild()"
                            class="bg-transparent text-xs font-semibold text-white focus:outline-none cursor-pointer">
                        <option value="fikri" class="text-gray-800" selected>
                            Fikri Zulkarnain (3-B)
                        </option>
                        <option value="aisyah" class="text-gray-800">
                            Aisyah Humaira (1-A)
                        </option>
                    </select>
                </div>

                {{-- Tombol Pemicu Modal Logout --}}
                <button type="button"
                        onclick="openLogoutModal()"
                        class="flex items-center gap-1.5 bg-rose-500/80 hover:bg-rose-600 px-3 py-1.5 rounded-xl text-xs font-semibold border border-rose-400/40 transition shadow-sm"
                        title="Keluar dari Akun">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </div>
        </div>
    </header>


    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-5xl w-full mx-auto p-4 sm:p-6 space-y-6">

        {{-- PROFILE SISWA & POIN --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
            {{-- Informasi Siswa --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-2xl shrink-0 shadow-inner border-2 border-amber-200">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>

                <div>
                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-full text-[10px] font-semibold">
                        Siswa Aktif
                    </span>

                    <h2 id="studentName" class="text-lg sm:text-xl font-bold text-gray-800 mt-1">
                        Fikri Zulkarnain
                    </h2>

                    <p class="text-xs text-gray-500">
                        NIS: <span id="studentNis" class="font-mono">20260105</span> |
                        Kelas: <span id="studentClass" class="font-semibold text-gray-700">3-B</span>
                    </p>

                    <p class="text-[11px] text-gray-400 mt-0.5">
                        Wali Kelas: <span id="teacherName" class="text-gray-600 font-medium">Ustadz Ahmad</span>
                    </p>
                </div>
            </div>

            {{-- Skor Poin --}}
            <div class="w-full md:w-auto bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/60 p-5 rounded-2xl flex items-center justify-between gap-6 shrink-0">
                <div>
                    <p class="text-xs text-amber-800 font-semibold uppercase tracking-wider">
                        Sisa Poin Akhir
                    </p>

                    <div class="flex items-baseline gap-1 mt-0.5">
                        <span id="totalScore" class="text-3xl font-extrabold text-amber-600">
                            260
                        </span>
                        <span class="text-xs text-gray-500 font-medium">
                            / 250 Poin Awal
                        </span>
                    </div>

                    <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i>
                        Status: Sangat Baik
                    </p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shadow-md shadow-amber-500/30">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>


        {{-- STATISTIK PRESTASI & PELANGGARAN --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Total Prestasi --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-medium">
                        Total Bonus Prestasi
                    </p>

                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 id="statPrestasi" class="text-2xl font-bold text-emerald-600">
                            +25 Poin
                        </h3>
                        <span id="countPrestasi" class="text-xs text-gray-500 font-medium">
                            (2 Kejadian)
                        </span>
                    </div>

                    <p class="text-[10px] text-gray-400 mt-1">
                        Poin tambahan yang diperoleh anak
                    </p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-award"></i>
                </div>
            </div>

            {{-- Total Pelanggaran --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-medium">
                        Total Pengurangan Poin
                    </p>

                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 id="statPelanggaran" class="text-2xl font-bold text-rose-600">
                            -15 Poin
                        </h3>
                        <span id="countPelanggaran" class="text-xs text-gray-500 font-medium">
                            (2 Kejadian)
                        </span>
                    </div>

                    <p class="text-[10px] text-gray-400 mt-1">
                        Poin dipotong karena pelanggaran
                    </p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>


        {{-- RIWAYAT POIN --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 space-y-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800">
                        Catatan &amp; Riwayat Poin Siswa
                    </h3>
                    <p class="text-xs text-gray-500">
                        Rincian riwayat penambahan dan pemotongan poin anak
                    </p>
                </div>

                {{-- Filter Buttons --}}
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl text-xs font-medium">
                    <button onclick="filterLogs('all')"
                            id="btnAll"
                            class="px-3 py-1.5 rounded-lg bg-white shadow-sm text-gray-800 font-semibold transition">
                        Semua
                    </button>

                    <button onclick="filterLogs('prestasi')"
                            id="btnPrestasi"
                            class="px-3 py-1.5 rounded-lg text-gray-500 hover:text-gray-800 transition">
                        Prestasi
                    </button>

                    <button onclick="filterLogs('pelanggaran')"
                            id="btnPelanggaran"
                            class="px-3 py-1.5 rounded-lg text-gray-500 hover:text-gray-800 transition">
                        Pelanggaran
                    </button>
                </div>
            </div>

            {{-- Log Items --}}
            <div class="space-y-3" id="logContainer">
                {{-- Prestasi 1 --}}
                <div class="log-item log-prestasi p-4 rounded-2xl bg-emerald-50/40 border border-emerald-100 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-award"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-[10px] font-bold">
                                    PRESTASI
                                </span>
                                <span class="text-xs text-gray-400">22 Aug 2026</span>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-800 mt-1">
                                Juara 1 Lomba Hafalan Surah Pendek
                            </h4>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Diberikan oleh: <span class="font-medium text-gray-700">Ustadzah Siti</span>
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-base font-bold text-emerald-600">+15</span>
                        <p class="text-[10px] text-gray-400">Poin Tambahan</p>
                    </div>
                </div>

                {{-- Pelanggaran 1 --}}
                <div class="log-item log-pelanggaran p-4 rounded-2xl bg-rose-50/40 border border-rose-100 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-sm shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-md text-[10px] font-bold">
                                    PELANGGARAN
                                </span>
                                <span class="text-xs text-gray-400">20 Aug 2026</span>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-800 mt-1">
                                Terlambat Masuk Sekolah (&gt;15 Menit)
                            </h4>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Sanksi: <span class="font-medium text-gray-700">Teguran lisan &amp; piket kebersihan</span>
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-base font-bold text-rose-600">-5</span>
                        <p class="text-[10px] text-gray-400">Poin Pengurangan</p>
                    </div>
                </div>

                {{-- Prestasi 2 --}}
                <div class="log-item log-prestasi p-4 rounded-2xl bg-emerald-50/40 border border-emerald-100 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-award"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-[10px] font-bold">
                                    PRESTASI
                                </span>
                                <span class="text-xs text-gray-400">15 Aug 2026</span>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-800 mt-1">
                                Membantu Kebersihan Musholla Sekolah
                            </h4>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Diberikan oleh: <span class="font-medium text-gray-700">Ustadz Ahmad</span>
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-base font-bold text-emerald-600">+10</span>
                        <p class="text-[10px] text-gray-400">Poin Tambahan</p>
                    </div>
                </div>

                {{-- Pelanggaran 2 --}}
                <div class="log-item log-pelanggaran p-4 rounded-2xl bg-rose-50/40 border border-rose-100 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-sm shrink-0 mt-0.5 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-md text-[10px] font-bold">
                                    PELANGGARAN
                                </span>
                                <span class="text-xs text-gray-400">10 Aug 2026</span>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-800 mt-1">
                                Tidak Mengerjakan Tugas &amp; Seragam Tidak Lengkap
                            </h4>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Sanksi: <span class="font-medium text-gray-700">Catatan buku penghubung ortu</span>
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-base font-bold text-rose-600">-10</span>
                        <p class="text-[10px] text-gray-400">Poin Pengurangan</p>
                    </div>
                </div>
            </div>
        </div>

    </main>


    {{-- MODAL POPUP LOGOUT --}}
    <div id="logoutModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl text-center space-y-5 border border-gray-100 transform transition-all">
            {{-- Icon Modal --}}
            <div class="w-16 h-16 bg-rose-100 text-rose-500 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>

            {{-- Text Modal --}}
            <div class="space-y-1.5">
                <h3 class="font-bold text-lg text-gray-800">
                    Konfirmasi Keluar
                </h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Apakah Anda yakin ingin keluar dari portal Wali Murid Bintang Poin?
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button"
                        onclick="closeLogoutModal()"
                        class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-semibold shadow-md transition">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>


    {{-- FOOTER --}}
    <footer class="bg-white border-t py-4 text-center text-xs text-gray-400 mt-auto">
        © 2026 Bintang Poin | SDIT Nurul Fikri
    </footer>


    {{-- JAVASCRIPT --}}
    <script>
        const childrenData = {
            fikri: {
                name: "Fikri Zulkarnain",
                nis: "20260105",
                class: "3-B",
                teacher: "Ustadz Ahmad",
                totalScore: 260,
                prestasi: "+25 Poin",
                countPrestasi: "(2 Kejadian)",
                pelanggaran: "-15 Poin",
                countPelanggaran: "(2 Kejadian)"
            },
            aisyah: {
                name: "Aisyah Humaira",
                nis: "20260119",
                class: "1-A",
                teacher: "Ustadzah Fatimah",
                totalScore: 268,
                prestasi: "+18 Poin",
                countPrestasi: "(2 Kejadian)",
                pelanggaran: "-0 Poin",
                countPelanggaran: "(0 Kejadian)"
            }
        };

        // Modal Logout Control
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Ganti data anak
        function switchChild() {
            const selected = document.getElementById('childSelector').value;
            const data = childrenData[selected];

            document.getElementById('studentName').textContent = data.name;
            document.getElementById('studentNis').textContent = data.nis;
            document.getElementById('studentClass').textContent = data.class;
            document.getElementById('teacherName').textContent = data.teacher;
            document.getElementById('totalScore').textContent = data.totalScore;
            document.getElementById('statPrestasi').textContent = data.prestasi;
            document.getElementById('countPrestasi').textContent = data.countPrestasi;
            document.getElementById('statPelanggaran').textContent = data.pelanggaran;
            document.getElementById('countPelanggaran').textContent = data.countPelanggaran;
        }

        // Filter riwayat (All, Prestasi, Pelanggaran)
        function filterLogs(type) {
            const logs = document.querySelectorAll('.log-item');
            const btnAll = document.getElementById('btnAll');
            const btnPrestasi = document.getElementById('btnPrestasi');
            const btnPelanggaran = document.getElementById('btnPelanggaran');

            [btnAll, btnPrestasi, btnPelanggaran].forEach(btn => {
                btn.className = "px-3 py-1.5 rounded-lg text-gray-500 hover:text-gray-800 transition";
            });

            if (type === 'all') {
                btnAll.className = "px-3 py-1.5 rounded-lg bg-white shadow-sm text-gray-800 font-semibold transition";
                logs.forEach(log => log.style.display = 'flex');
            } else if (type === 'prestasi') {
                btnPrestasi.className = "px-3 py-1.5 rounded-lg bg-white shadow-sm text-gray-800 font-semibold transition";
                logs.forEach(log => {
                    log.style.display = log.classList.contains('log-prestasi') ? 'flex' : 'none';
                });
            } else if (type === 'pelanggaran') {
                btnPelanggaran.className = "px-3 py-1.5 rounded-lg bg-white shadow-sm text-gray-800 font-semibold transition";
                logs.forEach(log => {
                    log.style.display = log.classList.contains('log-pelanggaran') ? 'flex' : 'none';
                });
            }
        }
    </script>

</body>

</html>
