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

                        @foreach($anak as $item)

                            <option
                                value="{{ $item->id }}"
                                class="text-gray-800"
                                {{ $siswa && $siswa->id == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->nama_lengkap }}
                                @if($item->kelas)
                                    ({{ $item->kelas->nama_kelas }})
                                @endif
                            </option>

                        @endforeach

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

                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mt-1">
                        {{ $siswa->nama_lengkap }}
                    </h2>

                    <p class="text-xs text-gray-500">
                        NIS:
                        <span class="font-mono">
                            {{ $siswa->nis ?? '-' }}
                        </span>
                        |
                        Kelas:
                        <span class="font-semibold text-gray-700">
                            {{ $siswa->kelas->nama_kelas ?? '-' }}
                        </span>
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
                            {{ $totalScore }}
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
                            +{{ $totalPrestasi }} Poin
                        </h3>
                        <span id="countPrestasi" class="text-xs text-gray-500 font-medium">
                            ({{ $jumlahPrestasi }} Kejadian)
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
                            -{{ $totalPelanggaran }} Poin
                        </h3>
                        <span id="countPelanggaran" class="text-xs text-gray-500 font-medium">
                            ({{ $jumlahPelanggaran }} Kejadian)
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

                @forelse($riwayatPoin as $transaksi)

                    @php
                        $isPrestasi = $transaksi->jenis === 'apresiasi';
                    @endphp

                    <div class="log-item {{ $isPrestasi ? 'log-prestasi' : 'log-pelanggaran' }}
                                p-4 rounded-2xl
                                {{ $isPrestasi
                                    ? 'bg-emerald-50/40 border border-emerald-100'
                                    : 'bg-rose-50/40 border border-rose-100' }}
                                flex items-start justify-between gap-4">

                        <div class="flex items-start gap-3">

                            {{-- ICON --}}
                            <div class="w-9 h-9 rounded-xl
                                        {{ $isPrestasi
                                            ? 'bg-emerald-500'
                                            : 'bg-rose-500' }}
                                        text-white flex items-center justify-center
                                        text-sm shrink-0 mt-0.5 shadow-sm">

                                <i class="fa-solid
                                    {{ $isPrestasi
                                        ? 'fa-award'
                                        : 'fa-triangle-exclamation' }}">
                                </i>

                            </div>

                            {{-- INFORMASI --}}
                            <div>

                                <div class="flex items-center gap-2 flex-wrap">

                                    <span class="px-2 py-0.5
                                        {{ $isPrestasi
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : 'bg-rose-100 text-rose-800' }}
                                        rounded-md text-[10px] font-bold">

                                        {{ $isPrestasi ? 'PRESTASI' : 'PELANGGARAN' }}

                                    </span>

                                    <span class="text-xs text-gray-400">

                                        {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d M Y') }}

                                    </span>

                                </div>
                
                                {{-- JENIS PRESTASI / PELANGGARAN --}}
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="font-medium text-gray-600">
                                        {{ $isPrestasi ? 'Jenis Prestasi:' : 'Jenis Pelanggaran:' }}
                                    </span>
                                    <span class="text-gray-700 font-semibold">
                                        {{ $transaksi->aturanPoin->judul ?? $transaksi->aturanPoin->nama_aturan ?? '-' }}
                                    </span>
                                </p>

                                {{-- KETERANGAN TAMBAHAN --}}
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $isPrestasi ? 'Poin tambahan' : 'Poin pengurangan' }}
                                </p>

                            </div>

                        </div>

                        {{-- JUMLAH POIN --}}
                        <div class="text-right shrink-0">

                            <span class="text-base font-bold
                                {{ $isPrestasi
                                    ? 'text-emerald-600'
                                    : 'text-rose-600' }}">

                                {{ $isPrestasi ? '+' : '-' }}{{ $transaksi->poin }}

                            </span>

                            <p class="text-[10px] text-gray-400">

                                {{ $isPrestasi
                                    ? 'Poin Tambahan'
                                    : 'Poin Pengurangan' }}

                            </p>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-10">

                        <div class="w-12 h-12 bg-gray-100 rounded-2xl
                                    flex items-center justify-center
                                    mx-auto mb-3 text-gray-400">

                            <i class="fa-solid fa-clipboard-list"></i>

                        </div>

                        <p class="text-sm font-medium text-gray-500">
                            Belum ada riwayat poin
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Belum terdapat transaksi poin untuk siswa ini.
                        </p>

                    </div>

                @endforelse

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
        // Modal Logout Control
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Ganti data anak
        function switchChild() {

            const selectedId = document.getElementById('childSelector').value;

            const url = new URL(window.location.href);

            url.searchParams.set('siswa_id', selectedId);

            window.location.href = url.toString();
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
