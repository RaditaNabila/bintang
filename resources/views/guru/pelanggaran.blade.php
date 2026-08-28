@extends('layouts.app')

@section('title', 'Pelanggaran Siswa - Bintang Poin')
@section('page_title', 'Pencatatan Pelanggaran Siswa')
@section('page_description', 'Pantau dan evaluasi kedisiplinan serta ketertiban siswa')

@push('styles')
<!-- Tom Select CSS untuk Dropdown Searchable -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Styling disesuaikan dengan tema Rose/Red Tailwind */
    .ts-control {
        border-radius: 0.75rem !important; /* rounded-xl */
        border-color: #e5e7eb !important; /* border-gray-200 */
        padding: 0.5rem 0.875rem !important;
        font-size: 0.75rem !important; /* text-xs */
        background-color: #ffffff !important;
        box-shadow: none !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #f43f5e !important; /* border-rose-500 */
        box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2) !important;
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

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="mb-5 bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-xl text-xs">
            <div class="font-semibold mb-1">Terjadi kesalahan:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- TOTAL POIN --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">Poin Pelanggaran Bulan Ini</p>
                <h3 id="statTotalPoin" class="text-2xl font-bold text-rose-600 mt-1">
                    -{{ $totalPoinBulanIni ?? 0 }}
                </h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 text-lg">
                <i class="fa-solid fa-circle-minus"></i>
            </div>
        </div>

        {{-- PELANGGARAN TERBANYAK --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-xs text-gray-400 font-medium">Pelanggaran Terbanyak</p>
                <h3 class="text-sm font-bold text-gray-800 mt-1 truncate" title="{{ $namaPelanggaranTerbanyak ?? '-' }}">
                    {{ $namaPelanggaranTerbanyak ?? '-' }}
                </h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 text-lg flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        {{-- TOTAL KASUS --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">Total Catatan Pelanggaran</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    <span id="statTotalKasus">{{ $totalKasus ?? 0 }}</span>
                    <span class="text-xs font-normal text-gray-400">Kasus</span>
                </h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>
    </div>

    {{-- SEARCH & ACTION --}}
    <div class="mt-6 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" onkeyup="searchViolations()" placeholder="Cari NIS, nama, atau jenis pelanggaran..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 focus:bg-white transition">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="openManageCategoryModal()" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-xs rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-list-check"></i>
                <span>Daftar Jenis Pelanggaran</span>
            </button>
            <button type="button" onclick="openModal()" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Catat Pelanggaran</span>
            </button>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Riwayat Catatan Pelanggaran</h3>
                <p class="text-xs text-gray-500">Log pengurangan poin siswa berdasarkan periode yang dipilih</p>
            </div>
            <form method="GET" action="{{ route('guru.pelanggaran') }}" class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
                <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>
                <select name="bulan" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer">
                    @for($i = 0; $i < 12; $i++)
                        @php
                            $tanggalBulan = now()->copy()->subMonths($i);
                            $valueBulan = $tanggalBulan->format('m-Y');
                        @endphp
                        <option value="{{ $valueBulan }}" {{ ($bulan ?? now()->format('m-Y')) == $valueBulan ? 'selected' : '' }}>
                            {{ $tanggalBulan->translatedFormat('F Y') }}
                            @if($i === 0) (Bulan Ini) @endif
                        </option>
                    @endfor
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table id="violationTable" class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4 whitespace-nowrap">Tanggal</th>
                        <th class="py-3 px-4 whitespace-nowrap">NIS</th>
                        <th class="py-3 px-4 whitespace-nowrap">Nama Siswa</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kelas</th>
                        <th class="py-3 px-4 whitespace-nowrap">Bentuk Pelanggaran</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Poin (-)</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tindakan / Sanksi</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="violationTableBody" class="divide-y divide-gray-50">
                    @forelse($transaksi as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $item->tanggal_transaksi->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $item->siswa->nis ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700 whitespace-nowrap">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-gray-600 min-w-[220px]">
                                <div class="font-semibold text-gray-700">{{ $item->aturanPoin->judul ?? '-' }}</div>
                                @if($item->keterangan)
                                    <div class="text-[11px] text-gray-400 mt-1">{{ $item->keterangan }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-rose-600 text-center whitespace-nowrap">-{{ abs($item->poin) }}</td>
                            <td class="py-3.5 px-4 text-xs text-gray-500 min-w-[180px]">{{ $item->sanksi ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('guru.pelanggaran.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus" onclick="return confirm('Yakin ingin menghapus catatan pelanggaran ini? Poin siswa akan dikembalikan.')">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 px-4 text-center text-gray-500 text-xs">
                                <i class="fa-solid fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                                <div class="font-medium text-gray-500">Belum ada data pelanggaran</div>
                                <div class="text-gray-400 mt-1">Tidak ada catatan pelanggaran pada periode ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CATAT PELANGGARAN --}}
    <div id="violationModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-6 py-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-base">Catat Pelanggaran Siswa</h3>
                <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form id="violationForm" action="{{ route('guru.pelanggaran.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                {{-- SISWA (SEARCHABLE) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Siswa</label>
                    <select id="selectSiswa" name="siswa_id" required placeholder="Cari NIS atau nama siswa...">
                        <option value="">-- Cari / Pilih Siswa --</option>
                        @foreach($siswa as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->nis ?? 'N/A' }} - {{ $s->nama_lengkap }} ({{ $s->kelas->nama_kelas ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TIPE PELANGGARAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe Pelanggaran</label>
                    <select id="selectTipePelanggaran" onchange="filterKategoriByTipe()" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 bg-gray-50 font-medium">
                        <option value="semua">-- Semua Tipe Pelanggaran --</option>
                        <option value="ringan">Pengurangan Kegiatan Harian</option>
                        <option value="berat">Pelanggaran Khusus</option>
                    </select>
                </div>

                {{-- JENIS PELANGGARAN (SEARCHABLE) --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-gray-700">Pilih Jenis Pelanggaran</label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openManageCategoryModal()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
                                <i class="fa-solid fa-list text-[10px]"></i> Kelola
                            </button>
                            <button type="button" onclick="openCategoryModal()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Jenis Baru
                            </button>
                        </div>
                    </div>

                    <select id="selectKategori" name="aturan_poin_id" required placeholder="Cari jenis pelanggaran...">
                        <option value="">-- Cari / Pilih Jenis Pelanggaran --</option>
                        @foreach($aturanPelanggaran as $aturan)
                            <option value="{{ $aturan->id }}" data-poin="{{ $aturan->nilai_poin }}" data-tipe="{{ $aturan->tipe_pelanggaran ?? 'ringan' }}">
                                {{ $aturan->judul }} (-{{ $aturan->nilai_poin }} Poin)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- POIN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Poin Pengurangan (-)</label>
                    <input type="number" id="inputPoin" name="poin" required min="1" max="100" readonly class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-rose-600 bg-gray-50 focus:outline-none focus:border-rose-500">
                    <p class="text-[10px] text-gray-400 mt-1">Poin ditentukan otomatis berdasarkan jenis pelanggaran.</p>
                </div>

                {{-- KETERANGAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bentuk / Rincian Pelanggaran</label>
                    <textarea id="inputKeterangan" name="keterangan" required rows="3" placeholder="Contoh: Siswa datang terlambat 20 menit..." class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"></textarea>
                </div>

                {{-- TANGGAL --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Transaksi</label>
                    <input type="date" id="inputTanggal" name="tanggal_transaksi" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500" value="{{ date('Y-m-d') }}">
                </div>

                {{-- SANKSI --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tindakan Pembinaan / Sanksi</label>
                    <input type="text" id="inputSanksi" name="sanksi" placeholder="Contoh: Nasihat dan pemanggilan orang tua" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500">
                </div>

                {{-- BUTTON --}}
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-medium hover:bg-rose-600 transition">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH JENIS PELANGGARAN --}}
    <div id="categoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-base">Tambah Jenis Pelanggaran</h3>
                <button type="button" onclick="closeCategoryModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form id="categoryForm" action="#" onsubmit="alert('Fitur ini hanya tampilan frontend. Backend belum dihubungkan.'); return false;" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jenis Pelanggaran</label>
                    <input type="text" name="nama_kategori" required placeholder="Contoh: Berkelahi / Perundungan" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori Tipe Pelanggaran</label>
                    <select id="newCategoryTipe" name="tipe_pelanggaran" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 bg-gray-50 font-medium">
                        <option value="ringan">Pengurangan Kegiatan Harian</option>
                        <option value="berat">Pelanggaran Khusus</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Poin Pengurangan (-)</label>
                    <input type="number" id="newCategoryPoin" name="poin" value="5" required min="1" max="100" placeholder="Masukkan jumlah poin..." class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                    <div class="flex gap-2">
                        <i class="fa-solid fa-circle-info text-amber-500 text-xs mt-0.5"></i>
                        <p class="text-[10px] text-amber-700 leading-relaxed">
                            Bebas menentukan jumlah poin pengurangan tanpa batasan minimal untuk kedua tipe pelanggaran.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">
                        <i class="fa-solid fa-plus mr-1"></i> Simpan Jenis
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DAFTAR & KELOLA JENIS PELANGGARAN --}}
    <div id="manageCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 text-white flex justify-between items-center flex-shrink-0">
                <h3 class="font-bold text-base">Daftar Jenis Pelanggaran</h3>
                <button type="button" onclick="closeManageCategoryModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4 flex-1">
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500">Kelola dan hapus aturan jenis pelanggaran yang telah dibuat.</p>
                </div>

                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase font-semibold">
                                <th class="py-2.5 px-3">Nama Jenis Pelanggaran</th>
                                <th class="py-2.5 px-3">Tipe</th>
                                <th class="py-2.5 px-3 text-center">Poin (-)</th>
                                <th class="py-2.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($aturanPelanggaran as $aturan)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-2.5 px-3 font-semibold text-gray-700">{{ $aturan->judul }}</td>
                                    <td class="py-2.5 px-3 text-gray-500">
                                        @if(($aturan->tipe_pelanggaran ?? 'ringan') === 'berat')
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold bg-rose-50 text-rose-600 rounded-md">Pelanggaran Khusus</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-600 rounded-md">Kegiatan Harian</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold text-rose-600">-{{ $aturan->nilai_poin }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        {{-- Tampilan Hapus (Frontend Only) --}}
                                        <button type="button" onclick="alert('Ini adalah sampel UI hapus. Backend belum terhubung.')" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus Jenis Pelanggaran">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-400">Belum ada jenis pelanggaran tersimpan.</td>
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

@endsection

@push('scripts')
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    let selectSiswaTs, selectKategoriTs;

    let rawKategoriOptions = [
        @foreach($aturanPelanggaran as $aturan)
            {
                value: "{{ $aturan->id }}",
                text: "{{ $aturan->judul }} (-{{ $aturan->nilai_poin }} Poin)",
                poin: {{ $aturan->nilai_poin }},
                tipe: "{{ $aturan->tipe_pelanggaran ?? 'ringan' }}"
            },
        @endforeach
    ];

    document.addEventListener('DOMContentLoaded', function() {
        selectSiswaTs = new TomSelect("#selectSiswa", {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });

        selectKategoriTs = new TomSelect("#selectKategori", {
            create: false,
            sortField: { field: "text", direction: "asc" },
            onChange: function(value) {
                updatePoinValue(value);
            }
        });

        filterKategoriByTipe();

        const tanggalInput = document.getElementById('inputTanggal');
        if (!tanggalInput.value) {
            tanggalInput.value = new Date().toISOString().split('T')[0];
        }
    });

    function filterKategoriByTipe() {
        const tipe = document.getElementById('selectTipePelanggaran').value;

        selectKategoriTs.clear();
        selectKategoriTs.clearOptions();

        const filtered = rawKategoriOptions.filter(item => {
            if (tipe === 'semua') return true;
            return item.tipe === tipe;
        });

        selectKategoriTs.addOptions(filtered);
        selectKategoriTs.refreshOptions(false);
        document.getElementById('inputPoin').value = '';
    }

    function updatePoinValue(value) {
        const inputPoin = document.getElementById('inputPoin');
        if (!value) {
            inputPoin.value = '';
            return;
        }
        const item = rawKategoriOptions.find(k => k.value == value);
        inputPoin.value = item ? item.poin : '';
    }

    function openModal() {
        const modal = document.getElementById('violationModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        const modal = document.getElementById('violationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');

        document.getElementById('violationForm').reset();
        selectSiswaTs.clear();
        document.getElementById('selectTipePelanggaran').value = 'semua';
        filterKategoriByTipe();

        document.getElementById('inputPoin').value = '';
        document.getElementById('inputTanggal').value = new Date().toISOString().split('T')[0];
    }

    function openCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('categoryForm').reset();
        document.getElementById('newCategoryTipe').value = 'ringan';
        document.getElementById('newCategoryPoin').value = 5;
    }

    function openManageCategoryModal() {
        const modal = document.getElementById('manageCategoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeManageCategoryModal() {
        const modal = document.getElementById('manageCategoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function searchViolations() {
        const searchInput = document.getElementById('searchInput');
        const search = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#violationTableBody tr');

        rows.forEach(row => {
            if (row.cells.length < 8) return;

            const nis = row.cells[1]?.textContent.toLowerCase() || '';
            const nama = row.cells[2]?.textContent.toLowerCase() || '';
            const kelas = row.cells[3]?.textContent.toLowerCase() || '';
            const bentuk = row.cells[4]?.textContent.toLowerCase() || '';
            const sanksi = row.cells[6]?.textContent.toLowerCase() || '';

            const cocok = nis.includes(search) || nama.includes(search) || kelas.includes(search) || bentuk.includes(search) || sanksi.includes(search);
            row.style.display = cocok ? '' : 'none';
        });
    }

    document.addEventListener('click', function(event) {
        const violationModal = document.getElementById('violationModal');
        const categoryModal = document.getElementById('categoryModal');
        const manageCategoryModal = document.getElementById('manageCategoryModal');

        if (event.target === violationModal) closeModal();
        if (event.target === categoryModal) closeCategoryModal();
        if (event.target === manageCategoryModal) closeManageCategoryModal();
    });

    document.addEventListener('keydown', function(event) {
        if (event.key !== 'Escape') return;
        const violationModal = document.getElementById('violationModal');
        const categoryModal = document.getElementById('categoryModal');
        const manageCategoryModal = document.getElementById('manageCategoryModal');

        if (!violationModal.classList.contains('hidden')) closeModal();
        if (!categoryModal.classList.contains('hidden')) closeCategoryModal();
        if (!manageCategoryModal.classList.contains('hidden')) closeManageCategoryModal();
    });
</script>
@endpush