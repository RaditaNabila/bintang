@extends('layouts.app')

@section('title', 'Pelanggaran Siswa - Bintang Poin')
@section('page_title', 'Pencatatan Pelanggaran Siswa')
@section('page_description', 'Pantau dan evaluasi kedisiplinan serta ketertiban siswa')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
.ts-control{border-radius:.75rem!important;border-color:#e5e7eb!important;padding:.5rem .875rem!important;font-size:.75rem!important;background:#fff!important;box-shadow:none!important}.ts-wrapper.focus .ts-control{border-color:#ef4444!important;box-shadow:0 0 0 2px rgba(239,68,68,.2)!important}.ts-dropdown{border-radius:.75rem!important;font-size:.75rem!important;box-shadow:0 10px 15px -3px rgba(0,0,0,.1)!important;z-index:100!important}
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- TOOLBAR --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" onkeyup="filterData()" placeholder="Cari NIS, nama, atau keterangan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 focus:bg-white transition">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="openManageCategoryJenisModal()" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-list-check"></i><span>Kelola Kategori & Jenis</span>
            </button>
            <button type="button" onclick="openModal('add')" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-plus text-sm"></i><span>Catat Pelanggaran</span>
            </button>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-800">Riwayat Catatan Pelanggaran</h3>
            <p class="text-xs text-gray-500">Log pengurangan poin disiplin siswa pada periode yang dipilih</p>
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

    {{-- TABEL TRANSAKSI --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="violationTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Tanggal</th><th class="py-3 px-4">NIS</th><th class="py-3 px-4">Nama Siswa</th><th class="py-3 px-4">Kelas</th><th class="py-3 px-4">Jenis Pelanggaran</th><th class="py-3 px-4">Keterangan</th><th class="py-3 px-4">Sanksi</th><th class="py-3 px-4 text-center">Poin (-)</th><th class="py-3 px-4 text-center">Sisa Poin</th><th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $item)
                        <tr class="hover:bg-gray-50/50" data-bulan="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m') }}">
                            <td class="py-3.5 px-4 text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">{{ $item->siswa->nis ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-gray-500">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-xs font-medium">{{ $item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? '-' }}</span></td>
                            <td class="py-3.5 px-4 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-gray-600">{{ $item->sanksi ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-bold text-rose-600 text-center">-{{ $item->poin }}</td>
                            <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">{{ $item->siswa->poin_saat_ini ?? 0 }}</span></td>
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" onclick="confirmDeleteTransaction('{{ route('guru.pelanggaran.destroy',$item->id) }}','{{ addslashes($item->siswa->nama_lengkap ?? 'Siswa') }}','{{ addslashes($item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? 'Pelanggaran') }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition"><i class="fa-solid fa-trash text-xs"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-10 text-center text-gray-400 text-sm">Belum ada catatan pelanggaran siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($transaksi,'hasPages') && $transaksi->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-500">Menampilkan <span class="font-bold text-gray-700">{{ $transaksi->firstItem() }}</span> hingga <span class="font-bold text-gray-700">{{ $transaksi->lastItem() }}</span> dari <span class="font-bold text-gray-700">{{ $transaksi->total() }}</span> catatan</div>
                <div class="inline-flex items-center gap-1.5">
                    @if($transaksi->onFirstPage())<span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold">Prev</span>@else<a href="{{ $transaksi->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-semibold transition">Prev</a>@endif
                    <span class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 rounded-xl border border-rose-200/60">{{ $transaksi->currentPage() }} / {{ $transaksi->lastPage() }}</span>
                    @if($transaksi->hasMorePages())<a href="{{ $transaksi->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-semibold transition">Next</a>@else<span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold">Next</span>@endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL CATAT PELANGGARAN --}}
<div id="violationModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden max-h-[90vh]">
        <div class="bg-gradient-to-r from-rose-500 to-red-600 px-6 py-4 text-white flex justify-between items-center">
            <div><h3 id="modalTitle" class="font-bold text-base">Catat Pelanggaran Siswa</h3><p class="text-xs text-white/80">Catat pelanggaran siswa</p></div>
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="violationForm" action="{{ route('guru.pelanggaran.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Nama Siswa <span class="text-red-500">*</span></label>
                    <select name="siswa_id" id="inputSiswa" required><option value="">Pilih siswa</option>@foreach($siswa as $item)<option value="{{ $item->id }}" data-kelas="{{ $item->kelas->nama_kelas ?? '-' }}">{{ $item->nama_lengkap }} ({{ $item->nis ?? '-' }})</option>@endforeach</select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_transaksi" id="inputTanggal" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-xl text-xs py-2.5 px-3">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Kelas</label>
                    <input type="text" id="inputKelas" readonly placeholder="Kelas siswa otomatis" class="w-full border border-gray-200 rounded-xl bg-gray-100 text-gray-700 text-xs py-2.5 px-3">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-gray-700">Kategori Pelanggaran <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <button type="button" onclick="openManageCategoryJenisModal()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700"><i class="fa-solid fa-list text-[10px]"></i> Kelola</button>
                            <button type="button" onclick="openAddCategoryModal()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700"><i class="fa-solid fa-plus text-[10px]"></i> Kategori Baru</button>
                        </div>
                    </div>
                    <select name="kategori_id" id="inputKategori" required placeholder="Cari kategori pelanggaran..."><option value="">-- Pilih Kategori --</option>@foreach($kategoriPelanggaran as $kategori)<option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>@endforeach</select>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-gray-700">Jenis Pelanggaran <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <button type="button" onclick="openManageCategoryJenisModal()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700"><i class="fa-solid fa-list text-[10px]"></i> Kelola</button>
                            <button type="button" onclick="openAddJenisModal()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700"><i class="fa-solid fa-plus text-[10px]"></i> Jenis Baru</button>
                        </div>
                    </div>
                    <select name="aturan_poin_id" id="inputAturan" required><option value="">Pilih kategori terlebih dahulu</option></select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Poin (-) <span class="text-red-500">*</span></label>
                    <input type="number" id="inputPoin" readonly placeholder="Otomatis terisi" class="w-full border border-gray-200 rounded-xl bg-gray-100 font-bold text-rose-600 text-xs py-2.5 px-3">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Keterangan <span class="text-red-500">*</span></label>
                    <textarea name="keterangan" id="inputKeterangan" rows="3" required placeholder="Masukkan keterangan pelanggaran..." class="w-full border border-gray-200 rounded-xl text-xs p-3"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Sanksi</label>
                    <textarea name="sanksi" id="inputSanksi" rows="3" placeholder="Masukkan sanksi yang diberikan..." class="w-full border border-gray-200 rounded-xl text-xs p-3"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-6 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-medium hover:bg-rose-600">Simpan Pelanggaran</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TAMBAH KATEGORI --}}
<div id="addCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-rose-600 px-5 py-3.5 text-white flex justify-between items-center"><h4 class="font-bold text-sm">Tambah Kategori Pelanggaran</h4><button type="button" onclick="closeAddCategoryModal()"><i class="fa-solid fa-xmark"></i></button></div>
        <form action="{{ route('guru.pelanggaran.storeKategori') }}" method="POST" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="jenis" value="pelanggaran">
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori</label><input type="text" name="nama_kategori" id="newCategoryName" required placeholder="Misal: Kedisiplinan" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs"></div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Default Poin (-)</label><input type="number" name="poin" id="newCategoryPoints" required min="1" max="100" placeholder="10" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs"></div>
            <div class="flex justify-end gap-2 pt-3 border-t"><button type="button" onclick="closeAddCategoryModal()" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-xs">Batal</button><button type="submit" class="px-3.5 py-1.5 bg-rose-600 text-white rounded-xl text-xs">Tambah</button></div>
        </form>
    </div>
</div>

{{-- MODAL TAMBAH JENIS --}}
<div id="addJenisModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[80] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-rose-600 px-5 py-3.5 text-white flex justify-between items-center"><h4 class="font-bold text-sm">Tambah Jenis Pelanggaran</h4><button type="button" onclick="closeAddJenisModal()"><i class="fa-solid fa-xmark"></i></button></div>
        <form action="{{ route('guru.pelanggaran.storeJenis') }}" method="POST" class="p-5 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori_id" id="newJenisKategoriId" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoriPelanggaran as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jenis Pelanggaran <span class="text-red-500">*</span></label><input type="text" name="nama_jenis" id="newJenisName" required placeholder="Misal: Tidak memakai atribut lengkap" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs"></div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Poin (-) <span class="text-red-500">*</span></label><input type="number" name="poin" id="newJenisPoints" required min="1" max="100" placeholder="5" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs"></div>
            <div class="flex justify-end gap-2 pt-3 border-t"><button type="button" onclick="closeAddJenisModal()" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-xs">Batal</button><button type="submit" class="px-3.5 py-1.5 bg-rose-600 text-white rounded-xl text-xs">Tambah</button></div>
        </form>
    </div>
</div>

{{-- MODAL KELOLA KATEGORI & JENIS --}}
<div id="manageCategoryJenisModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-base">Kelola Kategori & Jenis Pelanggaran</h3>
                <p class="text-xs text-white/70 mt-0.5">Kelola kategori dan jenis pelanggaran siswa</p>
            </div>
            <button type="button" onclick="closeManageCategoryJenisModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-8">
            {{-- KATEGORI --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <div><h4 class="text-sm font-bold text-gray-800">Kategori Pelanggaran</h4><p class="text-[11px] text-gray-400">Daftar kategori pelanggaran yang tersedia</p></div>
                    <button type="button" onclick="closeManageCategoryJenisModal();openAddCategoryModal()" class="px-3.5 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[11px] font-semibold flex items-center gap-1.5 transition"><i class="fa-solid fa-plus text-[10px]"></i>Kategori Baru</button>
                </div>
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead><tr class="bg-gray-50 border-b border-gray-100"><th class="py-3 px-4 text-[10px] uppercase font-bold text-gray-400">Nama Kategori</th><th class="py-3 px-4 text-center text-[10px] uppercase font-bold text-gray-400">Default Poin</th><th class="py-3 px-4 text-center text-[10px] uppercase font-bold text-gray-400">Jumlah Jenis</th><th class="py-3 px-4 text-center text-[10px] uppercase font-bold text-gray-400">Aksi</th></tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($kategoriPelanggaran as $kategori)
                                    @php $jumlahJenis=$aturanPelanggaran->where('kategori_id',$kategori->id)->count(); @endphp
                                    <tr class="hover:bg-gray-50/60 transition">
                                        <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i class="fa-solid fa-folder-open text-xs"></i></div><div><div class="font-semibold text-xs text-gray-700">{{ $kategori->nama_kategori }}</div><div class="text-[10px] text-gray-400">Kategori pelanggaran</div></div></div></td>
                                        <td class="py-3 px-4 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold">-{{ abs($kategori->poin ?? 0) }}</span></td>
                                        <td class="py-3 px-4 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-[11px] font-semibold">{{ $jumlahJenis }} Jenis</span></td>
                                        <td class="py-3 px-4 text-center"><button type="button" onclick="confirmDeleteCategory('{{ route('guru.pelanggaran.destroyKategori',$kategori->id) }}','{{ addslashes($kategori->nama_kategori) }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition"><i class="fa-solid fa-trash text-[11px]"></i></button></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-gray-400 text-xs">Belum ada kategori pelanggaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <div class="border-t border-gray-100"></div>

            {{-- JENIS --}}
            <section>
                <div class="flex items-center justify-between mb-3">
                    <div><h4 class="text-sm font-bold text-gray-800">Jenis Pelanggaran</h4><p class="text-[11px] text-gray-400">Daftar jenis pelanggaran berdasarkan kategori</p></div>
                    <button type="button" onclick="closeManageCategoryJenisModal();openAddJenisModal()" class="px-3.5 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[11px] font-semibold flex items-center gap-1.5 transition"><i class="fa-solid fa-plus text-[10px]"></i>Jenis Baru</button>
                </div>
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead><tr class="bg-gray-50 border-b border-gray-100"><th class="py-3 px-4 text-[10px] uppercase font-bold text-gray-400">Kategori</th><th class="py-3 px-4 text-[10px] uppercase font-bold text-gray-400">Jenis Pelanggaran</th><th class="py-3 px-4 text-center text-[10px] uppercase font-bold text-gray-400">Poin</th><th class="py-3 px-4 text-center text-[10px] uppercase font-bold text-gray-400">Aksi</th></tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($aturanPelanggaran as $aturan)
                                    <tr class="hover:bg-gray-50/60 transition">
                                        <td class="py-3 px-4"><span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-[11px] font-medium">{{ $aturan->kategori->nama_kategori ?? '-' }}</span></td>
                                        <td class="py-3 px-4"><div class="flex items-center gap-2"><div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation text-[10px]"></i></div><span class="font-semibold text-xs text-gray-700">{{ $aturan->judul }}</span></div></td>
                                        <td class="py-3 px-4 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold">-{{ abs($aturan->nilai_poin ?? 0) }}</span></td>
                                        <td class="py-3 px-4 text-center"><button type="button" onclick="confirmDeleteJenis('{{ route('guru.pelanggaran.destroyJenis',$aturan->id) }}','{{ addslashes($aturan->judul) }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition"><i class="fa-solid fa-trash text-[11px]"></i></button></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-gray-400 text-xs">Belum ada jenis pelanggaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeManageCategoryJenisModal()" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-semibold transition">Tutup</button>
        </div>
    </div>
</div>

{{-- HAPUS KATEGORI --}}
<div id="deleteCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[90] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3 class="font-bold text-base text-gray-800 mt-4">Hapus Kategori Ini?</h3>
        <p class="text-xs text-gray-500 mt-1">Kategori <span id="deleteCategoryNameLabel" class="font-bold text-gray-700"></span> akan dihapus.</p>
        <div class="flex gap-2 mt-5"><button type="button" onclick="closeDeleteCategoryModal()" class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs">Batal</button><form id="deleteCategoryForm" method="POST" class="w-full">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold">Ya, Hapus</button></form></div>
    </div>
</div>

{{-- HAPUS JENIS --}}
<div id="deleteJenisModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[90] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3 class="font-bold text-base text-gray-800 mt-4">Hapus Jenis Pelanggaran?</h3>
        <p class="text-xs text-gray-500 mt-1">Jenis <span id="deleteJenisNameLabel" class="font-bold text-gray-700"></span> akan dihapus.</p>
        <div class="flex gap-2 mt-5"><button type="button" onclick="closeDeleteJenisModal()" class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs">Batal</button><form id="deleteJenisForm" method="POST" class="w-full">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold">Ya, Hapus</button></form></div>
    </div>
</div>

{{-- HAPUS TRANSAKSI --}}
<div id="deleteTransactionModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[90] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl"><i class="fa-solid fa-trash-can"></i></div>
        <h3 class="font-bold text-base text-gray-800 mt-4">Hapus Catatan Pelanggaran?</h3>
        <p class="text-xs text-gray-500 mt-1">Catatan pelanggaran <span id="deleteTransactionSiswaLabel" class="font-bold text-gray-700"></span> untuk <span id="deleteTransactionKategoriLabel" class="font-bold text-gray-700"></span> akan dihapus.</p>
        <div class="flex gap-2 mt-5"><button type="button" onclick="closeDeleteTransactionModal()" class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs">Batal</button><form id="deleteTransactionForm" method="POST" class="w-full">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold">Ya, Hapus</button></form></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
let selectSiswaTs,selectKategoriTs,selectAturanPoinTs;

document.addEventListener('DOMContentLoaded',()=>{
    selectSiswaTs=new TomSelect('#inputSiswa',{create:false,sortField:{field:'text',direction:'asc'},onChange:value=>updateKelasFromSiswa(value)});
    selectKategoriTs=new TomSelect('#inputKategori',{create:false,sortField:{field:'text',direction:'asc'},onChange:value=>filterJenisPelanggaran(value)});
    selectAturanPoinTs=new TomSelect('#inputAturan',{create:false,sortField:{field:'text',direction:'asc'},onChange:value=>updatePointFromAturan(value)});
    selectAturanPoinTs.disable();
});

function updateKelasFromSiswa(value){
    const input=document.getElementById('inputKelas');
    if(!value){input.value='';return}
    const option=document.querySelector(`#inputSiswa option[value="${value}"]`);
    input.value=option?.dataset.kelas||'-';
}

function filterJenisPelanggaran(kategoriId){
    const poin=document.getElementById('inputPoin');
    if(!selectAturanPoinTs)return;
    selectAturanPoinTs.clear(true);
    selectAturanPoinTs.clearOptions();
    poin.value='';
    if(!kategoriId){
        selectAturanPoinTs.addOption({value:'',text:'Pilih kategori terlebih dahulu'});
        selectAturanPoinTs.setValue('',true);
        selectAturanPoinTs.disable();
        return;
    }
    const aturan=@json($aturanPelanggaran);
    const filtered=aturan.filter(item=>String(item.kategori_id)===String(kategoriId));
    filtered.forEach(item=>{
        const nilai=Math.abs(Number(item.nilai_poin||0));
        selectAturanPoinTs.addOption({value:item.id,text:`${item.judul} (-${nilai} Poin)`,poin:nilai});
    });
    if(!filtered.length)selectAturanPoinTs.addOption({value:'',text:'Belum ada jenis untuk kategori ini'});
    selectAturanPoinTs.enable();
    selectAturanPoinTs.refreshOptions(false);
}

function updatePointFromAturan(value){
    const input=document.getElementById('inputPoin');
    input.value=value&&selectAturanPoinTs.options[value]?selectAturanPoinTs.options[value].poin||'':'';
}

function openModal(mode){
    if(mode!=='add')return;
    document.getElementById('modalTitle').textContent='Catat Pelanggaran Siswa';
    document.getElementById('violationForm').action="{{ route('guru.pelanggaran.store') }}";
    selectSiswaTs?.clear(true);
    selectKategoriTs?.clear(true);
    selectAturanPoinTs?.clear(true);
    selectAturanPoinTs?.clearOptions();
    selectAturanPoinTs?.addOption({value:'',text:'Pilih kategori terlebih dahulu'});
    selectAturanPoinTs?.setValue('',true);
    selectAturanPoinTs?.disable();
    document.getElementById('inputKelas').value='';
    document.getElementById('inputTanggal').value="{{ date('Y-m-d') }}";
    document.getElementById('inputKeterangan').value='';
    document.getElementById('inputSanksi').value='';
    document.getElementById('inputPoin').value='';
    showModal('violationModal');
}

function closeModal(){hideModal('violationModal')}
function openAddCategoryModal(){showModal('addCategoryModal')}
function closeAddCategoryModal(){hideModal('addCategoryModal');document.getElementById('newCategoryName').value='';document.getElementById('newCategoryPoints').value=''}
function openAddJenisModal(){
    const mainKategori=selectKategoriTs?.getValue();
    const jenisKategori=document.getElementById('newJenisKategoriId');
    if(mainKategori)jenisKategori.value=mainKategori;
    else jenisKategori.value='';
    document.getElementById('newJenisName').value='';
    document.getElementById('newJenisPoints').value='';
    showModal('addJenisModal');
}
function closeAddJenisModal(){hideModal('addJenisModal');document.getElementById('newJenisName').value='';document.getElementById('newJenisPoints').value=''}
function openManageCategoryJenisModal(){showModal('manageCategoryJenisModal')}
function closeManageCategoryJenisModal(){hideModal('manageCategoryJenisModal')}

function confirmDeleteCategory(url,name){
    document.getElementById('deleteCategoryForm').action=url;
    document.getElementById('deleteCategoryNameLabel').textContent=`"${name}"`;
    showModal('deleteCategoryModal');
}
function closeDeleteCategoryModal(){hideModal('deleteCategoryModal')}

function confirmDeleteJenis(url,name){
    document.getElementById('deleteJenisForm').action=url;
    document.getElementById('deleteJenisNameLabel').textContent=`"${name}"`;
    showModal('deleteJenisModal');
}
function closeDeleteJenisModal(){hideModal('deleteJenisModal')}

function confirmDeleteTransaction(url,siswa,jenis){
    document.getElementById('deleteTransactionForm').action=url;
    document.getElementById('deleteTransactionSiswaLabel').textContent=siswa;
    document.getElementById('deleteTransactionKategoriLabel').textContent=`"${jenis}"`;
    showModal('deleteTransactionModal');
}
function closeDeleteTransactionModal(){hideModal('deleteTransactionModal')}

function showModal(id){
    const modal=document.getElementById(id);
    if(!modal)return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function hideModal(id){
    const modal=document.getElementById(id);
    if(!modal)return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function filterData(){
    const search=document.getElementById('searchInput').value.toLowerCase().trim();
    const bulan=document.getElementById('filterBulan').value;
    document.querySelectorAll('#violationTable tbody tr').forEach(row=>{
        if(row.cells.length<2)return;
        const cocokSearch=row.innerText.toLowerCase().includes(search);
        const cocokBulan=!bulan||row.dataset.bulan===bulan;
        row.style.display=cocokSearch&&cocokBulan?'':'none';
    });
}

document.addEventListener('click',e=>{
    const modals=[
        ['violationModal',closeModal],
        ['addCategoryModal',closeAddCategoryModal],
        ['addJenisModal',closeAddJenisModal],
        ['manageCategoryJenisModal',closeManageCategoryJenisModal],
        ['deleteCategoryModal',closeDeleteCategoryModal],
        ['deleteJenisModal',closeDeleteJenisModal],
        ['deleteTransactionModal',closeDeleteTransactionModal]
    ];
    modals.forEach(([id,fn])=>{
        if(e.target===document.getElementById(id))fn();
    });
});

document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){
        closeModal();
        closeAddCategoryModal();
        closeAddJenisModal();
        closeManageCategoryJenisModal();
        closeDeleteCategoryModal();
        closeDeleteJenisModal();
        closeDeleteTransactionModal();
    }
});
</script>
@endpush