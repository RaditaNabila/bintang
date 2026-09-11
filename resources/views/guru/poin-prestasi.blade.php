@extends('layouts.app')
@section('title', 'Poin Prestasi - Bintang Poin')
@section('page_title', 'Apresiasi & Prestasi Siswa')
@section('page_description', 'Catat kebaikan, kedisiplinan, dan capaian siswa')

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
    <!-- Card Peringkat / Rank -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($ranking as $index => $top)
        <div class="@if($index==0) bg-gradient-to-br from-amber-500 via-amber-400 to-orange-400 text-white @elseif($index==1) bg-gradient-to-br from-slate-100 to-slate-200/60 text-slate-800 border border-slate-200/80 @else bg-gradient-to-br from-orange-50 to-amber-100/60 text-amber-950 border border-amber-200/70 @endif p-5 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl pointer-events-none"><i class="fa-solid fa-trophy"></i></div>
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="@if($index==0) bg-white/20 border-white/30 text-white @elseif($index==1) bg-white/80 border-slate-300 text-slate-700 @else bg-white/80 border-amber-300 text-amber-900 @endif backdrop-blur-sm text-[11px] font-bold px-3 py-1 rounded-full border flex items-center gap-1.5">
                        <i class="fa-solid @if($index==0) fa-crown text-yellow-200 @elseif($index==1) fa-medal text-slate-400 @else fa-award text-amber-700 @endif"></i>
                        Rank {{ $index+1 }} @if($index==0)(Tertinggi)@endif
                    </span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold tracking-tight">{{ $top->poin_saat_ini ?? 0 }}</span>
                        <span class="@if($index==0) text-amber-100 @else text-slate-500 @endif text-xs font-medium block -mt-1">Poin</span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-lg font-bold leading-snug">{{ $top->nama_lengkap ?? '-' }}</h3>
                    <p class="@if($index==0) text-amber-100 @else text-slate-500 @endif text-xs font-medium">Kelas {{ $top->kelas->nama_kelas ?? '-' }} &bull; NIS: {{ $top->nis ?? '-' }}</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t @if($index==0) border-white/20 @else border-slate-300/50 @endif">
                <p class="@if($index==0) text-amber-50 @else text-slate-600 @endif text-xs leading-relaxed"><i class="fa-solid fa-trophy opacity-60 mr-1"></i>Siswa dengan perolehan poin apresiasi tertinggi.</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Toolbar Pencarian & Tombol Aksi -->
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
            <button type="button" onclick="openModal('add')" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-plus text-sm"></i><span>Catat Poin Prestasi</span>
            </button>
        </div>
    </div>

    <!-- Filter Bulan -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-800">Riwayat Catatan Prestasi</h3>
            <p class="text-xs text-gray-500">Log penambahan poin siswa pada periode yang dipilih</p>
        </div>
        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
            <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>
            <select id="filterBulan" onchange="filterData()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer">
                <option value="">Semua Bulan</option>
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $value=>$nama)
                <option value="{{ $value }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabel Riwayat Prestasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="achievementTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Jenis Prestasi</th>
                        <th class="py-3 px-4">Keterangan Prestasi</th>
                        <th class="py-3 px-4 text-center">Poin (+)</th>
                        <th class="py-3 px-4 text-center">Total Poin</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $item)
                    <tr class="hover:bg-gray-50/50" data-bulan="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m') }}">
                        <td class="py-3.5 px-4 text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">{{ $item->siswa->nis ?? '-' }}</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-500">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">{{ $item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? '-' }}</span></td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">+{{ $item->poin }}</td>
                        <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">{{ $item->siswa->poin_saat_ini ?? 0 }}</span></td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="confirmDeleteTransaction('{{ route('guru.poin-prestasi.destroy', $item->id) }}', '{{ addslashes($item->siswa->nama_lengkap ?? 'Siswa') }}', '{{ addslashes($item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? 'Prestasi') }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-gray-400 text-sm">Belum ada catatan poin prestasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($transaksi, 'hasPages') && $transaksi->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 w-full bg-white">
                <div class="text-xs text-gray-500">
                    Menampilkan <span class="font-bold text-gray-700">{{ $transaksi->firstItem() }}</span>
                    hingga <span class="font-bold text-gray-700">{{ $transaksi->lastItem() }}</span>
                    dari <span class="font-bold text-gray-700">{{ $transaksi->total() }}</span> catatan
                </div>

                <div class="inline-flex items-center gap-1.5">
                    @if ($transaksi->onFirstPage())
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold cursor-not-allowed select-none">
                            <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Prev
                        </span>
                    @else
                        <a href="{{ $transaksi->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-amber-500 hover:text-white rounded-xl text-xs font-semibold transition">
                            <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Prev
                        </a>
                    @endif

                    <span class="px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 rounded-xl border border-amber-200/60">
                        {{ $transaksi->currentPage() }} / {{ $transaksi->lastPage() }}
                    </span>

                    @if ($transaksi->hasMorePages())
                        <a href="{{ $transaksi->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-amber-500 hover:text-white rounded-xl text-xs font-semibold transition">
                            Next <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                        </a>
                    @else
                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold cursor-not-allowed select-none">
                            Next <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form (Tambah & Edit Catatan) -->
<div id="achievementModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-base">Catat Poin Prestasi</h3>
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="achievementForm" action="{{ route('guru.poin-prestasi.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="tanggal_transaksi" id="inputTanggal" value="{{ date('Y-m-d') }}">

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Siswa</label>
                <select name="siswa_id" id="selectSiswa" required placeholder="Cari nama atau NIS siswa...">
                    <option value="">-- Cari / Pilih Siswa --</option>
                    @foreach($siswa as $s)
                    <option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama_lengkap }} @if($s->kelas) ({{ $s->kelas->nama_kelas }}) @endif</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-semibold text-gray-700">Jenis Prestasi</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openManageCategoryModal()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
                            <i class="fa-solid fa-list text-[10px]"></i> Kelola
                        </button>
                        <button type="button" onclick="openAddCategoryModal()" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px]"></i> Jenis Baru
                        </button>
                    </div>
                </div>
                <select name="aturan_poin_id" id="selectAturanPoin" required placeholder="Cari jenis prestasi...">
                    <option value="">-- Cari / Pilih Prestasi --</option>
                    @foreach($aturanPrestasi as $aturan)
                        <option value="{{ $aturan->id }}" data-poin="{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }}">
                            {{ $aturan->judul ?? $aturan->nama_aturan }} (+{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }} Poin)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Prestasi / Apresiasi</label>
                <textarea name="keterangan" id="inputKeterangan" required rows="3" placeholder="Contoh: Menjawab pertanyaan di depan kelas dengan sangat baik" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></textarea>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1"><label class="block text-xs font-semibold text-gray-700">Jumlah Poin (+)</label><span class="text-[10px] text-amber-600 font-medium">*Otomatis berdasarkan jenis</span></div>
                <input type="number" name="poin" id="inputPoin" required min="1" max="100" readonly class="w-full px-3.5 py-2 border border-amber-300 bg-amber-50/50 font-bold text-amber-900 rounded-xl text-xs focus:outline-none focus:border-amber-500 cursor-not-allowed">          
            </div>
            <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:outline-none focus:border-amber-500 focus:bg-white transition">
                    </div>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600">Simpan Poin</button>
            </div>
            
        </form>
    </div>
</div>

<!-- Modal Tambah Kategori Prestasi -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-amber-500 px-5 py-3.5 text-white flex justify-between items-center">
            <h4 class="font-bold text-sm">Tambah Kategori Prestasi</h4>
            <button type="button" onclick="closeAddCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('guru.poin-prestasi.kategori.store') }}" method="POST" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="jenis" value="apresiasi">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori Prestasi</label>
                <input type="text" name="nama_kategori" id="newCategoryName" required placeholder="Misal: Aktif Menjawab di Kelas" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Default Poin (+)</label>
                <input type="number" name="poin" id="newCategoryPoints" required min="1" max="100" placeholder="5" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddCategoryModal()" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-xs">Batal</button>
                <button type="submit" class="px-3.5 py-1.5 bg-amber-500 text-white rounded-xl text-xs">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Daftar & Kelola Kategori Prestasi -->
<div id="manageCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 text-white flex justify-between items-center flex-shrink-0">
            <h3 class="font-bold text-base">Daftar Kategori Prestasi</h3>
            <button type="button" onclick="closeManageCategoryModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4 flex-1">
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-500">Kelola dan hapus aturan kategori prestasi yang tersimpan.</p>
            </div>

            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase font-semibold">
                            <th class="py-2.5 px-3">Nama Kategori Prestasi</th>
                            <th class="py-2.5 px-3 text-center">Poin (+)</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($aturanPrestasi as $aturan)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-2.5 px-3 font-semibold text-gray-700">{{ $aturan->judul ?? $aturan->nama_aturan }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-emerald-600">+{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" onclick="confirmDeleteCategory('{{ route('guru.poin-prestasi.kategori.destroy', $aturan->id) }}', '{{ addslashes($aturan->judul ?? $aturan->nama_aturan) }}')" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus Kategori">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-400">Belum ada kategori prestasi tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end flex-shrink-0">
            <button type="button" onclick="closeManageCategoryModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-xs font-medium hover:bg-gray-300 transition">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Pop-Up Konfirmasi Hapus Kategori Kustom -->
<div id="deleteCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="font-bold text-base text-gray-800">Hapus Kategori Ini?</h3>
            <p class="text-xs text-gray-500 mt-1">Anda akan menghapus kategori <span id="deleteCategoryNameLabel" class="font-bold text-gray-700"></span>. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex items-center justify-center gap-2 pt-2">
            <button type="button" onclick="closeDeleteCategoryModal()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <form id="deleteCategoryForm" method="POST" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pop-Up Konfirmasi Hapus Catatan Riwayat Transaksi -->
<div id="deleteTransactionModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <div>
            <h3 class="font-bold text-base text-gray-800">Hapus Catatan Prestasi?</h3>
            <p class="text-xs text-gray-500 mt-1">Anda akan menghapus catatan prestasi siswa <span id="deleteTransactionSiswaLabel" class="font-bold text-gray-700"></span> untuk kategori <span id="deleteTransactionKategoriLabel" class="font-bold text-gray-700"></span>. Poin siswa akan disesuaikan kembali.</p>
        </div>
        <div class="flex items-center justify-center gap-2 pt-2">
            <button type="button" onclick="closeDeleteTransactionModal()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <form id="deleteTransactionForm" method="POST" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
let selectSiswaTs = null, selectAturanPoinTs = null;

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

function openModal(mode){
    const m = document.getElementById('achievementModal');
    const f = document.getElementById('achievementForm');

    document.getElementById('modalTitle').textContent = mode === 'edit' ? 'Edit Poin Prestasi' : 'Catat Poin Prestasi';

    if(mode === 'add'){
        f.action = "{{ route('guru.poin-prestasi.store') }}";
        document.getElementById('formMethod').value = 'POST';

        if (selectSiswaTs) selectSiswaTs.clear(true);
        if (selectAturanPoinTs) selectAturanPoinTs.clear(true);

        document.getElementById('inputKeterangan').value = '';
        document.getElementById('inputPoin').value = '';
        document.getElementById('inputTanggal').value = "{{ date('Y-m-d') }}";
    }

    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeModal(){
    const m = document.getElementById('achievementModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function openAddCategoryModal(){
    const m = document.getElementById('addCategoryModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeAddCategoryModal(){
    const m = document.getElementById('addCategoryModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.getElementById('newCategoryName').value = '';
    document.getElementById('newCategoryPoints').value = '';
}

function openManageCategoryModal(){
    const m = document.getElementById('manageCategoryModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeManageCategoryModal(){
    const m = document.getElementById('manageCategoryModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function confirmDeleteCategory(actionUrl, categoryName) {
    const modal = document.getElementById('deleteCategoryModal');
    document.getElementById('deleteCategoryForm').action = actionUrl;
    document.getElementById('deleteCategoryNameLabel').textContent = `"${categoryName}"`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteCategoryModal() {
    const modal = document.getElementById('deleteCategoryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmDeleteTransaction(actionUrl, namaSiswa, namaKategori) {
    const modal = document.getElementById('deleteTransactionModal');
    document.getElementById('deleteTransactionForm').action = actionUrl;
    document.getElementById('deleteTransactionSiswaLabel').textContent = namaSiswa;
    document.getElementById('deleteTransactionKategoriLabel').textContent = `"${namaKategori}"`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteTransactionModal() {
    const modal = document.getElementById('deleteTransactionModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function filterData(){
    const search = document.getElementById('searchInput').value.toLowerCase().trim();
    const bulan = document.getElementById('filterBulan').value;

    document.querySelectorAll('#achievementTable tbody tr').forEach(row => {
        if(row.cells.length < 9) return;
        const cocokSearch = row.innerText.toLowerCase().includes(search);
        const cocokBulan = !bulan || row.dataset.bulan === bulan;
        row.style.display = cocokSearch && cocokBulan ? '' : 'none';
    });
}

document.addEventListener('click', function(e) {
    if (e.target === document.getElementById('achievementModal')) closeModal();
    if (e.target === document.getElementById('addCategoryModal')) closeAddCategoryModal();
    if (e.target === document.getElementById('manageCategoryModal')) closeManageCategoryModal();
    if (e.target === document.getElementById('deleteCategoryModal')) closeDeleteCategoryModal();
    if (e.target === document.getElementById('deleteTransactionModal')) closeDeleteTransactionModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeAddCategoryModal();
        closeManageCategoryModal();
        closeDeleteCategoryModal();
        closeDeleteTransactionModal();
    }
});
</script>
@endpush