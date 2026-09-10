@extends('layouts.app')

@section('title', 'Poin Prestasi - Pengajar')
@section('page_title', 'Apresiasi & Prestasi Siswa')
@section('page_description', 'Daftar dan perolehan poin prestasi siswa')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 0.75rem !important;
        border-color: #e5e7eb !important;
        padding: 0.5rem 0.875rem !important;
        font-size: 0.75rem !important;
        background-color: #ffffff !important;
        box-shadow: none !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
    }
    .ts-dropdown {
        border-radius: 0.75rem !important;
        font-size: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        z-index: 60 !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- ============================= -->
    <!-- CARD PERINGKAT / RANK -->
    <!-- ============================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($ranking as $index => $top)
        <div class="
            @if($index == 0)
                bg-gradient-to-br from-amber-500 via-amber-400 to-orange-400 text-white
            @elseif($index == 1)
                bg-gradient-to-br from-slate-100 to-slate-200/60 text-slate-800 border border-slate-200/80
            @else
                bg-gradient-to-br from-orange-50 to-amber-100/60 text-amber-950 border border-amber-200/70
            @endif
            p-5 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between
        ">
            <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl pointer-events-none">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="
                        @if($index == 0)
                            bg-white/20 border-white/30 text-white
                        @elseif($index == 1)
                            bg-white/80 border-slate-300 text-slate-700
                        @else
                            bg-white/80 border-amber-300 text-amber-900
                        @endif
                        backdrop-blur-sm text-[11px] font-bold px-3 py-1 rounded-full border flex items-center gap-1.5
                    ">
                        <i class="
                            fa-solid
                            @if($index == 0)
                                fa-crown text-yellow-200
                            @elseif($index == 1)
                                fa-medal text-slate-400
                            @else
                                fa-award text-amber-700
                            @endif
                        "></i>
                        Rank {{ $index + 1 }}
                        @if($index == 0)
                            (Tertinggi)
                        @endif
                    </span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold tracking-tight">
                            {{ $top->poin_saat_ini ?? 0 }}
                        </span>
                        <span class="
                            @if($index == 0)
                                text-amber-100
                            @else
                                text-slate-500
                            @endif
                            text-xs font-medium block -mt-1
                        ">
                            Poin
                        </span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-lg font-bold leading-snug">
                        {{ $top->nama_lengkap ?? '-' }}
                    </h3>
                    <p class="
                        @if($index == 0)
                            text-amber-100
                        @else
                            text-slate-500
                        @endif
                        text-xs font-medium
                    ">
                        Kelas {{ $top->kelas->nama_kelas ?? '-' }}
                        &bull;
                        NIS: {{ $top->nis ?? '-' }}
                    </p>
                </div>
            </div>
            <div class="
                mt-4 pt-3 border-t
                @if($index == 0)
                    border-white/20
                @else
                    border-slate-300/50
                @endif
            ">
                <p class="
                    @if($index == 0)
                        text-amber-50
                    @else
                        text-slate-600
                    @endif
                    text-xs leading-relaxed
                ">
                    <i class="fa-solid fa-trophy opacity-60 mr-1"></i>
                    Siswa dengan perolehan poin apresiasi tertinggi.
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ============================= -->
    <!-- ALERT NOTIFIKASI -->
    <!-- ============================= -->
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- ============================= -->
    <!-- TOOLBAR PENCARIAN & AKSI -->
    <!-- ============================= -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" onkeyup="filterData()" placeholder="Cari NIS, nama, atau keterangan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="openManageCategoryModal()" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-list-check"></i>
                <span>Daftar Jenis Prestasi</span>
            </button>
            <button type="button" onclick="openTambahPoinModal()" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Poin</span>
            </button>
        </div>
    </div>

    <!-- ============================= -->
    <!-- FILTER BULAN -->
    <!-- ============================= -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-800">
                Riwayat Catatan Prestasi
            </h3>
            <p class="text-xs text-gray-500">
                Daftar transaksi poin prestasi siswa
            </p>
        </div>
        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
            <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>
            <select id="filterBulan" onchange="filterData()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer">
                <option value="">Semua Bulan</option>
                @foreach([
                    '01'=>'Januari',
                    '02'=>'Februari',
                    '03'=>'Maret',
                    '04'=>'April',
                    '05'=>'Mei',
                    '06'=>'Juni',
                    '07'=>'Juli',
                    '08'=>'Agustus',
                    '09'=>'September',
                    '10'=>'Oktober',
                    '11'=>'November',
                    '12'=>'Desember'
                ] as $value => $nama)
                    <option value="{{ $value }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- ============================= -->
    <!-- TABEL RIWAYAT PRESTASI -->
    <!-- ============================= -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="achievementTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4 text-center">No</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Prestasi</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Poin</th>
                        <th class="py-3 px-4 text-center">Total Poin</th>
                        <th class="py-3 px-4">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $item)
                    <tr class="hover:bg-gray-50/50 transition" data-bulan="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m') }}">
                        <td class="py-3.5 px-4 text-center font-mono text-xs text-gray-400">
                            {{ $transaksi->firstItem() + $loop->index }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">
                            {{ $item->siswa->nis ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">
                            {{ $item->siswa->nama_lengkap ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            @php
                                $namaKelas = $item->siswa->kelas->nama_kelas ?? null;
                                if ($namaKelas && !\Illuminate\Support\Str::startsWith(strtolower($namaKelas), 'kelas')) {
                                    $namaKelas = 'Kelas ' . $namaKelas;
                                }
                            @endphp
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">
                                {{ $namaKelas ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-700 font-medium">
                            {{ $item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-500 text-xs max-w-xs">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold">
                                +{{ $item->poin }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600">
                            {{ $item->siswa->poin_saat_ini ?? 0 }}
                        </td>
                        <td class="py-3.5 px-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-10 text-center text-xs text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-trophy text-2xl text-gray-300"></i>
                                <span>Belum ada riwayat poin prestasi.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ============================= -->
        <!-- PAGINASI -->
        <!-- ============================= -->
        @if($transaksi->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH POIN -->
<!-- ========================================== -->
<div id="modalTambahPoin" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeTambahPoinModal()"></div>
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <!-- Tambahkan max-h-[90vh] flex flex-col agar kontainernya responsif dan bisa di-scroll -->
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Header (Tetap di atas) -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">
                        Tambah Poin Prestasi
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Tambahkan poin prestasi untuk siswa
                    </p>
                </div>
                <button type="button" onclick="closeTambahPoinModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-400 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form Body (Bisa di-scroll ke bawah) -->
            <form action="{{ route('pengajar.poin-prestasi.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Siswa <span class="text-red-500">*</span>
                        </label>
                        <select name="siswa_id" id="selectSiswa" required placeholder="Cari nama atau NIS siswa...">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->nis }} - {{ $s->nama_lengkap }} @if($s->kelas) ({{ $s->kelas->nama_kelas }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-xs font-semibold text-gray-700">
                                Jenis Prestasi <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="openManageCategoryModal()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                                <i class="fa-solid fa-list text-[10px]"></i> Kelola Kategori
                            </button>
                        </div>
                        <select name="aturan_poin_id" id="selectAturanPoin" required placeholder="Cari jenis prestasi...">
                            <option value="">-- Pilih Jenis Prestasi --</option>
                            @forelse($aturanPrestasi as $aturan)
                                <option value="{{ $aturan->id }}" data-poin="{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }}">
                                    {{ $aturan->judul ?? $aturan->nama_aturan }} (+{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }} poin)
                                </option>
                            @empty
                                <option value="" disabled>Belum ada aturan prestasi</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-xs font-semibold text-gray-700">
                                Jumlah Poin (+) <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] text-amber-600 font-medium">*Otomatis berdasarkan kategori</span>
                        </div>
                        <input type="number" name="poin" id="inputPoin" required min="1" max="100" placeholder="Poin otomatis dari jenis prestasi" class="w-full px-3.5 py-2.5 bg-amber-50/50 border border-amber-200 rounded-xl text-xs text-amber-900 font-bold focus:outline-none focus:border-amber-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="keterangan" rows="3" required placeholder="Contoh: Juara 1 lomba kebersihan kelas" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 placeholder-gray-400 focus:outline-none focus:border-amber-500 focus:bg-white transition"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:outline-none focus:border-amber-500 focus:bg-white transition">
                    </div>
                </div>

                <!-- Footer / Tombol Aksi (Tetap di bawah dan tidak ikut tertutup) -->
                <div class="flex items-center justify-end gap-2 px-6 py-4 bg-gray-50 border-t border-gray-100 flex-shrink-0">
                    <button type="button" onclick="closeTambahPoinModal()" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-100 text-gray-600 rounded-xl text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold transition">
                        <i class="fa-solid fa-plus mr-1"></i> Tambahkan Poin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DAFTAR & KELOLA KATEGORI -->
<!-- ========================================== -->
<div id="manageCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 text-white flex justify-between items-center flex-shrink-0">
            <h3 class="font-bold text-base">
                Daftar Kategori Prestasi
            </h3>
            <button type="button" onclick="closeManageCategoryModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4 flex-1">
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-500">
                    Daftar aturan kategori prestasi yang tersedia.
                </p>
            </div>

            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase font-semibold">
                            <th class="py-2.5 px-3">Nama Kategori Prestasi</th>
                            <th class="py-2.5 px-3 text-center">Poin (+)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($aturanPrestasi as $aturan)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 px-3 font-semibold text-gray-700">
                                {{ $aturan->judul ?? $aturan->nama_aturan }}
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-emerald-600">
                                +{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-6 text-center text-gray-400">
                                Belum ada kategori prestasi tersimpan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end flex-shrink-0">
            <button type="button" onclick="closeManageCategoryModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-xs font-medium hover:bg-gray-300 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
let selectSiswaTs = null;
let selectAturanPoinTs = null;

document.addEventListener('DOMContentLoaded', () => {
    selectSiswaTs = new TomSelect("#selectSiswa", {
        create: false,
        sortField: { field: "text", direction: "asc" }
    });

    selectAturanPoinTs = new TomSelect("#selectAturanPoin", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        onChange: function(value) {
            updatePointFromAturan(value);
        }
    });
});

function updatePointFromAturan(value) {
    const inputPoin = document.getElementById('inputPoin');
    if (!value) {
        inputPoin.value = '';
        return;
    }
    const opt = document.querySelector(`#selectAturanPoin option[value="${value}"]`);
    if (opt) {
        inputPoin.value = opt.getAttribute('data-poin') || '';
    }
}

function openTambahPoinModal() {
    const modal = document.getElementById('modalTambahPoin');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function closeTambahPoinModal() {
    const modal = document.getElementById('modalTambahPoin');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

function openManageCategoryModal() {
    const modal = document.getElementById('manageCategoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeManageCategoryModal() {
    const modal = document.getElementById('manageCategoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function filterData() {
    const search = document.getElementById('searchInput').value.toLowerCase().trim();
    const bulan = document.getElementById('filterBulan').value;

    document.querySelectorAll('#achievementTable tbody tr').forEach(row => {
        if (row.cells.length < 9) {
            return;
        }

        const cocokSearch = row.innerText.toLowerCase().includes(search);
        const cocokBulan = !bulan || row.dataset.bulan === bulan;

        row.style.display = cocokSearch && cocokBulan ? '' : 'none';
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeTambahPoinModal();
        closeManageCategoryModal();
    }
});
</script>
@endpush