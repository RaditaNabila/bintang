@extends('layouts.app')
@section('title', 'Pelanggaran Siswa - Bintang Poin')
@section('page_title', 'Pencatatan Pelanggaran Siswa')
@section('page_description', 'Pantau dan evaluasi kedisiplinan serta ketertiban siswa')

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
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
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
    <!-- Toolbar Pencarian & Tombol Aksi -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" onkeyup="filterData()" placeholder="Cari NIS, nama, atau keterangan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 focus:bg-white transition">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="openManageCategoryModal()" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-list-check"></i>
                <span>Daftar Jenis Pelanggaran</span>
            </button>
            <button type="button" onclick="openModal('add')" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-plus text-sm"></i><span>Catat Pelanggaran</span>
            </button>
        </div>
    </div>

    <!-- Filter Bulan -->
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

    <!-- Tabel Riwayat Pelanggaran -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="violationTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Jenis Pelanggaran</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-center">Poin (-)</th>
                        <th class="py-3 px-4 text-center">Sisa Poin</th>
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
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-xs font-medium">{{ $item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? '-' }}</span></td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                        <td class="py-3.5 px-4 font-bold text-rose-600 text-center">-{{ $item->poin }}</td>
                        <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">{{ $item->siswa->poin_saat_ini ?? 0 }}</span></td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="confirmDeleteTransaction('{{ route('guru.pelanggaran.destroy', $item->id) }}', '{{ addslashes($item->siswa->nama_lengkap ?? 'Siswa') }}', '{{ addslashes($item->aturanPoin->judul ?? $item->aturanPoin->nama_aturan ?? 'Pelanggaran') }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-gray-400 text-sm">Belum ada catatan pelanggaran siswa.</td></tr>
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
                        <a href="{{ $transaksi->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-semibold transition">
                            <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Prev
                        </a>
                    @endif

                    <span class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 rounded-xl border border-rose-200/60">
                        {{ $transaksi->currentPage() }} / {{ $transaksi->lastPage() }}
                    </span>

                    @if ($transaksi->hasMorePages())
                        <a href="{{ $transaksi->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-semibold transition">
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

<!-- Modal Form (Tambah Catatan Pelanggaran) -->
<div id="violationModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-rose-500 to-red-600 px-6 py-4 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-base">Catat Pelanggaran Siswa</h3>
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="violationForm" action="{{ route('guru.pelanggaran.store') }}" method="POST" class="p-6 space-y-4">
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
                    <label class="block text-xs font-semibold text-gray-700">Jenis Pelanggaran</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openManageCategoryModal()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
                            <i class="fa-solid fa-list text-[10px]"></i> Kelola
                        </button>
                        <button type="button" onclick="openAddCategoryModal()" class="text-[11px] text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px]"></i> Jenis Baru
                        </button>
                    </div>
                </div>
                <select name="aturan_poin_id" id="selectAturanPoin" required placeholder="Cari jenis pelanggaran...">
                    <option value="">-- Cari / Pilih Pelanggaran --</option>
                    @foreach($aturanPelanggaran as $aturan)
                        <option value="{{ $aturan->id }}" data-poin="{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }}">
                            {{ $aturan->judul ?? $aturan->nama_aturan }} (-{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }} Poin)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Pelanggaran</label>
                <textarea name="keterangan" id="inputKeterangan" required rows="3" placeholder="Contoh: Terlambat hadir ke sekolah lebih dari 15 menit" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"></textarea>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1"><label class="block text-xs font-semibold text-gray-700">Jumlah Poin Pengurangan (-)</label><span class="text-[10px] text-rose-600 font-medium">*Otomatis berdasarkan kategori</span></div>
                <input type="number" name="poin" id="inputPoin" required min="1" max="100" class="w-full px-3.5 py-2 border border-rose-300 bg-rose-50/50 font-bold text-rose-900 rounded-xl text-xs focus:outline-none focus:border-rose-500">
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-medium hover:bg-rose-700">Simpan Pelanggaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Kategori Pelanggaran -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-rose-600 px-5 py-3.5 text-white flex justify-between items-center">
            <h4 class="font-bold text-sm">Tambah Kategori Pelanggaran</h4>
            <button type="button" onclick="closeAddCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('guru.pelanggaran.storeKategori') }}" method="POST" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="jenis" value="pelanggaran">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori Pelanggaran</label>
                <input type="text" name="nama_kategori" id="newCategoryName" required placeholder="Misal: Atribut Tidak Lengkap" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Default Poin (-)</label>
                <input type="number" name="poin" id="newCategoryPoints" required min="1" max="100" placeholder="10" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddCategoryModal()" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-xs">Batal</button>
                <button type="submit" class="px-3.5 py-1.5 bg-rose-600 text-white rounded-xl text-xs">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Daftar & Kelola Kategori Pelanggaran -->
<div id="manageCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 text-white flex justify-between items-center flex-shrink-0">
            <h3 class="font-bold text-base">Daftar Kategori Pelanggaran</h3>
            <button type="button" onclick="closeManageCategoryModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4 flex-1">
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-500">Kelola dan hapus aturan kategori pelanggaran yang tersimpan.</p>
            </div>

            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase font-semibold">
                            <th class="py-2.5 px-3">Nama Kategori Pelanggaran</th>
                            <th class="py-2.5 px-3 text-center">Poin (-)</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($aturanPelanggaran as $aturan)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-2.5 px-3 font-semibold text-gray-700">{{ $aturan->judul ?? $aturan->nama_aturan }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-rose-600">-{{ $aturan->nilai_poin ?? $aturan->poin ?? 0 }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" onclick="confirmDeleteCategory('{{ route('guru.pelanggaran.destroyKategori', $aturan->id) }}', '{{ addslashes($aturan->judul ?? $aturan->nama_aturan) }}')" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus Kategori">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-400">Belum ada kategori pelanggaran tersimpan.</td>
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

<!-- Modal Pop-Up Konfirmasi Hapus Catatan Riwayat Transaksi Pelanggaran -->
<div id="deleteTransactionModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <div>
            <h3 class="font-bold text-base text-gray-800">Hapus Catatan Pelanggaran?</h3>
            <p class="text-xs text-gray-500 mt-1">Anda akan menghapus catatan pelanggaran siswa <span id="deleteTransactionSiswaLabel" class="font-bold text-gray-700"></span> untuk kasus <span id="deleteTransactionKategoriLabel" class="font-bold text-gray-700"></span>. Poin siswa akan dikembalikan semula.</p>
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
    const m = document.getElementById('violationModal');
    const f = document.getElementById('violationForm');

    document.getElementById('modalTitle').textContent = mode === 'edit' ? 'Edit Catatan Pelanggaran' : 'Catat Pelanggaran Siswa';

    if(mode === 'add'){
        f.action = "{{ route('guru.pelanggaran.store') }}";
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
    const m = document.getElementById('violationModal');
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

    document.querySelectorAll('#violationTable tbody tr').forEach(row => {
        if(row.cells.length < 9) return;
        const cocokSearch = row.innerText.toLowerCase().includes(search);
        const cocokBulan = !bulan || row.dataset.bulan === bulan;
        row.style.display = cocokSearch && cocokBulan ? '' : 'none';
    });
}

document.addEventListener('click', function(e) {
    if (e.target === document.getElementById('violationModal')) closeModal();
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