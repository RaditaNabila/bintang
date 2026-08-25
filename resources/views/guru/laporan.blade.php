@extends('layouts.app')

@section('title', 'Laporan Poin - Bintang Poin')
@section('page_title', 'Laporan Akhir Poin Siswa')
@section('page_description', 'Unduh rekapitulasi sisa poin serta predikat karakter siswa per kelas')

@section('content')

{{-- FILTER --}}
<div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center gap-3">
    {{-- SEARCH --}}
    <div class="relative flex-1 min-w-[200px]">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari NIS atau Nama Siswa..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
    </div>

    {{-- FILTER KELAS --}}
    <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 border border-gray-200 rounded-xl">
        <i class="fa-solid fa-filter text-amber-500 text-xs"></i>
        <select id="filterKelas" onchange="filterTable()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none cursor-pointer">
            <option value="all">Semua Kelas</option>
            <option value="3-B">Kelas 3-B</option>
            <option value="4-A">Kelas 4-A</option>
            <option value="5-A">Kelas 5-A</option>
            <option value="5-B">Kelas 5-B</option>
            <option value="6-A">Kelas 6-A</option>
            <option value="6-B">Kelas 6-B</option>
        </select>
    </div>

    {{-- FILTER PERIODE --}}
    <select id="filterPeriode" class="px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:outline-none focus:border-amber-500 cursor-pointer">
        <option value="08-2026">Agustus 2026</option>
        <option value="sem-1">Semester Ganjil 2026/2027</option>
    </select>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
    {{-- TABLE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">
                Daftar Sisa Poin Siswa
            </h3>
            <p class="text-xs text-gray-500">
                Rekap saldo akhir poin kedisiplinan dan karakter siswa
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span id="totalSiswaBadge" class="text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-full border border-amber-200">
                Menampilkan 5 Siswa
            </span>

            <button type="button" onclick="exportExcel()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fa-solid fa-file-excel text-sm"></i>
                Export Excel
            </button>
        </div>
    </div>

    {{-- TABLE CONTENT --}}
    <div class="overflow-x-auto mt-4">
        <table class="w-full text-left border-collapse text-sm" id="reportTable">
            <thead>
                <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                    <th class="py-3.5 px-4 w-12">No</th>
                    <th class="py-3.5 px-4">NIS</th>
                    <th class="py-3.5 px-4">Nama Siswa</th>
                    <th class="py-3.5 px-4">Kelas</th>
                    <th class="py-3.5 px-4 text-center">Sisa Poin Aktif</th>
                    <th class="py-3.5 px-4 text-center">Predikat Karakter</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50">
                {{-- DATA 1 --}}
                <tr class="hover:bg-gray-50/50" data-kelas="6-A">
                    <td class="py-3.5 px-4 text-xs font-bold text-gray-400">1</td>
                    <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260103</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800">Fatimah Az-Zahra</td>
                    <td class="py-3.5 px-4 text-gray-500 font-semibold">6-A</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-xl font-extrabold text-base border border-amber-200/60">
                            <i class="fa-solid fa-star text-xs"></i>
                            275
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full text-[11px] font-bold">
                            Sangat Baik
                        </span>
                    </td>
                </tr>

                {{-- DATA 2 --}}
                <tr class="hover:bg-gray-50/50" data-kelas="4-A">
                    <td class="py-3.5 px-4 text-xs font-bold text-gray-400">2</td>
                    <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260101</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800">Muhammad Raihan</td>
                    <td class="py-3.5 px-4 text-gray-500 font-semibold">4-A</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-xl font-extrabold text-base border border-amber-200/60">
                            <i class="fa-solid fa-star text-xs"></i>
                            260
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full text-[11px] font-bold">
                            Sangat Baik
                        </span>
                    </td>
                </tr>

                {{-- DATA 3 --}}
                <tr class="hover:bg-gray-50/50" data-kelas="5-B">
                    <td class="py-3.5 px-4 text-xs font-bold text-gray-400">3</td>
                    <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260102</td>
                    <td class="py-3.5 px-4 font-bold text-gray-800">Aisyah Azzahra</td>
                    <td class="py-3.5 px-4 text-gray-500 font-semibold">5-B</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-xl font-extrabold text-base border border-amber-200/60">
                            <i class="fa-solid fa-star text-xs"></i>
                            255
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full text-[11px] font-bold">
                            Sangat Baik
                        </span>
                    </td>
                </tr>

                {{-- DATA 4 --}}
                <tr class="hover:bg-gray-50/50" data-kelas="3-B">
                    <td class="py-3.5 px-4 text-xs font-bold text-gray-400">4</td>
                    <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260105</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-700">Fikri Zulkarnain</td>
                    <td class="py-3.5 px-4 text-gray-500 font-semibold">3-B</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 rounded-xl font-bold text-base border border-slate-200">
                            40
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200/80 rounded-full text-[11px] font-semibold">
                            Baik
                        </span>
                    </td>
                </tr>

                {{-- DATA 5 --}}
                <tr class="hover:bg-gray-50/50" data-kelas="6-B">
                    <td class="py-3.5 px-4 text-xs font-bold text-gray-400">5</td>
                    <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260112</td>
                    <td class="py-3.5 px-4 font-semibold text-gray-700">Davin Rizky</td>
                    <td class="py-3.5 px-4 text-gray-500 font-semibold">6-B</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-600 rounded-xl font-bold text-base border border-rose-200">
                            -15
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200/80 rounded-full text-[11px] font-semibold">
                            Cukup
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    filterTable();
});

function filterTable() {
    const searchInput = document.getElementById('searchInput');
    const filterKelas = document.getElementById('filterKelas');

    if (!searchInput || !filterKelas) {
        return;
    }

    const search = searchInput.value.toLowerCase().trim();
    const selectedKelas = filterKelas.value;
    const rows = document.querySelectorAll('#reportTable tbody tr');

    let visibleCount = 0;

    rows.forEach(function(row) {
        const nis = row.cells[1].textContent.toLowerCase();
        const nama = row.cells[2].textContent.toLowerCase();
        const rowKelas = row.getAttribute('data-kelas');

        const matchesSearch = nis.includes(search) || nama.includes(search);
        const matchesKelas = selectedKelas === 'all' || rowKelas === selectedKelas;

        if (matchesSearch && matchesKelas) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const badge = document.getElementById('totalSiswaBadge');
    if (badge) {
        badge.textContent = `Menampilkan ${visibleCount} Siswa`;
    }
}

function exportExcel() {
    alert('Fitur Export Excel akan dihubungkan ke Laravel setelah bagian database selesai.');
}
</script>
@endpush
