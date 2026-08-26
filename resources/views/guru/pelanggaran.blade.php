@extends('layouts.app')

@section('title', 'Pelanggaran Siswa - Bintang Poin')
@section('page_title', 'Pencatatan Pelanggaran Siswa')
@section('page_description', 'Pantau dan evaluasi kedisiplinan serta ketertiban siswa')

@section('content')

    {{-- =========================================================
        ALERT SUCCESS
    ========================================================== --}}
    @if(session('success'))
        <div class="mb-5 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    {{-- =========================================================
        VALIDATION ERROR
    ========================================================== --}}
    @if($errors->any())
        <div class="mb-5 bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-xl text-xs">
            <div class="font-semibold mb-1">
                Terjadi kesalahan:
            </div>

            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =========================================================
        STAT CARDS
    ========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- TOTAL POIN --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">
                    Poin Pelanggaran Bulan Ini
                </p>

                <h3
                    id="statTotalPoin"
                    class="text-2xl font-bold text-rose-600 mt-1"
                >
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
                <p class="text-xs text-gray-400 font-medium">
                    Pelanggaran Terbanyak
                </p>

                <h3
                    class="text-sm font-bold text-gray-800 mt-1 truncate"
                    title="{{ $namaPelanggaranTerbanyak ?? '-' }}"
                >
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
                <p class="text-xs text-gray-400 font-medium">
                    Total Catatan Pelanggaran
                </p>

                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    <span id="statTotalKasus">
                        {{ $totalKasus ?? 0 }}
                    </span>

                    <span class="text-xs font-normal text-gray-400">
                        Kasus
                    </span>
                </h3>
            </div>

            <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>

    </div>


    {{-- =========================================================
        SEARCH & ACTION
    ========================================================== --}}
    <div class="mt-6 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">

        {{-- SEARCH --}}
        <div class="relative w-full sm:w-80">

            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

            <input
                type="text"
                id="searchInput"
                onkeyup="searchViolations()"
                placeholder="Cari NIS, nama, atau jenis pelanggaran..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 focus:bg-white transition"
            >

        </div>


        {{-- BUTTON TAMBAH --}}
        <button
            type="button"
            onclick="openModal()"
            class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition"
        >
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Catat Pelanggaran</span>
        </button>

    </div>


    {{-- =========================================================
        TABLE SECTION
    ========================================================== --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">

        {{-- HEADER TABLE --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-gray-100 pb-4">

            <div>
                <h3 class="text-base font-bold text-gray-800">
                    Riwayat Catatan Pelanggaran
                </h3>

                <p class="text-xs text-gray-500">
                    Log pengurangan poin siswa berdasarkan periode yang dipilih
                </p>
            </div>


            {{-- FILTER BULAN --}}
            <form
                method="GET"
                action="{{ route('guru.pelanggaran') }}"
                class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200"
            >

                <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>

                <select
                    name="bulan"
                    onchange="this.form.submit()"
                    class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer"
                >

                    @php
                        $bulanSekarang = now();
                    @endphp

                    @for($i = 0; $i < 12; $i++)

                        @php
                            $tanggalBulan = now()->copy()->subMonths($i);
                            $valueBulan = $tanggalBulan->format('m-Y');
                        @endphp

                        <option
                            value="{{ $valueBulan }}"
                            {{ ($bulan ?? now()->format('m-Y')) == $valueBulan ? 'selected' : '' }}
                        >
                            {{ $tanggalBulan->translatedFormat('F Y') }}

                            @if($i === 0)
                                (Bulan Ini)
                            @endif
                        </option>

                    @endfor

                </select>

            </form>

        </div>


        {{-- =====================================================
            TABLE
        ====================================================== --}}
        <div class="overflow-x-auto">

            <table
                id="violationTable"
                class="w-full text-left border-collapse text-sm"
            >

                <thead>

                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">

                        <th class="py-3 px-4 whitespace-nowrap">
                            Tanggal
                        </th>

                        <th class="py-3 px-4 whitespace-nowrap">
                            NIS
                        </th>

                        <th class="py-3 px-4 whitespace-nowrap">
                            Nama Siswa
                        </th>

                        <th class="py-3 px-4 whitespace-nowrap">
                            Kelas
                        </th>

                        <th class="py-3 px-4 whitespace-nowrap">
                            Bentuk Pelanggaran
                        </th>

                        <th class="py-3 px-4 text-center whitespace-nowrap">
                            Poin (-)
                        </th>

                        <th class="py-3 px-4 whitespace-nowrap">
                            Tindakan / Sanksi
                        </th>

                        <th class="py-3 px-4 text-center whitespace-nowrap">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="violationTableBody"
                    class="divide-y divide-gray-50"
                >

                    @forelse($transaksi as $item)

                        <tr class="hover:bg-gray-50/50">

                            {{-- TANGGAL --}}
                            <td class="py-3.5 px-4 text-xs text-gray-400 whitespace-nowrap">
                                {{ $item->tanggal_transaksi->format('d M Y') }}
                            </td>


                            {{-- NIS --}}
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500 whitespace-nowrap">
                                {{ $item->siswa->nis ?? '-' }}
                            </td>


                            {{-- NAMA --}}
                            <td class="py-3.5 px-4 font-semibold text-gray-700 whitespace-nowrap">
                                {{ $item->siswa->nama_lengkap ?? '-' }}
                            </td>


                            {{-- KELAS --}}
                            <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                            </td>


                            {{-- BENTUK PELANGGARAN --}}
                            <td class="py-3.5 px-4 text-gray-600 min-w-[220px]">

                                <div class="font-semibold text-gray-700">
                                    {{ $item->aturanPoin->judul ?? '-' }}
                                </div>

                                @if($item->keterangan)
                                    <div class="text-[11px] text-gray-400 mt-1">
                                        {{ $item->keterangan }}
                                    </div>
                                @endif

                            </td>


                            {{-- POIN --}}
                            <td class="py-3.5 px-4 font-bold text-rose-600 text-center whitespace-nowrap">
                                -{{ abs($item->poin) }}
                            </td>


                            {{-- SANKSI --}}
                            <td class="py-3.5 px-4 text-xs text-gray-500 min-w-[180px]">
                                {{ $item->sanksi ?? '-' }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">

                                <form
                                    action="{{ route('guru.pelanggaran.destroy', $item->id) }}"
                                    method="POST"
                                    style="display:inline;"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus catatan pelanggaran ini? Poin siswa akan dikembalikan.')"
                                    >
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="py-10 px-4 text-center text-gray-500 text-xs"
                            >

                                <i class="fa-solid fa-inbox text-gray-300 text-3xl mb-2 block"></i>

                                <div class="font-medium text-gray-500">
                                    Belum ada data pelanggaran
                                </div>

                                <div class="text-gray-400 mt-1">
                                    Tidak ada catatan pelanggaran pada periode ini.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
        MODAL CATAT PELANGGARAN
    ========================================================== --}}
    <div
        id="violationModal"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4"
    >

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden max-h-[90vh] overflow-y-auto">

            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-6 py-4 text-white flex justify-between items-center">

                <h3 class="font-bold text-base">
                    Catat Pelanggaran Siswa
                </h3>

                <button
                    type="button"
                    onclick="closeModal()"
                    class="text-white/80 hover:text-white text-lg"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>


            {{-- FORM --}}
            <form
                id="violationForm"
                action="{{ route('guru.pelanggaran.store') }}"
                method="POST"
                class="p-6 space-y-4"
            >

                @csrf


                {{-- SISWA --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Pilih Siswa
                    </label>

                    <select
                        id="selectSiswa"
                        name="siswa_id"
                        required
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"
                    >

                        <option
                            value=""
                            disabled
                            selected
                        >
                            -- Pilih Siswa --
                        </option>

                        @foreach($siswa as $s)

                            <option value="{{ $s->id }}">
                                {{ $s->nis ?? 'N/A' }}
                                -
                                {{ $s->nama_lengkap }}
                                ({{ $s->kelas->nama_kelas ?? '-' }})
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- JENIS PELANGGARAN --}}
                <div>

                    <div class="flex justify-between items-center mb-1">

                        <label class="block text-xs font-semibold text-gray-700">
                            Pilih Jenis Pelanggaran
                        </label>

                        <button
                            type="button"
                            onclick="openCategoryModal()"
                            class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1"
                        >
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            Jenis Baru
                        </button>

                    </div>


                    <select
                        id="selectKategori"
                        name="aturan_poin_id"
                        onchange="autoFillViolationData()"
                        required
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 bg-amber-50/50"
                    >

                        <option
                            value=""
                            disabled
                            selected
                        >
                            -- Pilih Jenis Pelanggaran --
                        </option>

                        @foreach($aturanPelanggaran as $aturan)

                            <option
                                value="{{ $aturan->id }}"
                                data-poin="{{ $aturan->nilai_poin }}"
                            >
                                {{ $aturan->judul }}
                                -
                                {{ $aturan->nilai_poin }}
                                Poin
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- POIN --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Poin Pengurangan (-)
                    </label>

                    <input
                        type="number"
                        id="inputPoin"
                        name="poin"
                        required
                        min="1"
                        max="100"
                        readonly
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-rose-600 bg-gray-50 focus:outline-none focus:border-rose-500"
                    >

                    <p class="text-[10px] text-gray-400 mt-1">
                        Poin ditentukan otomatis berdasarkan jenis pelanggaran.
                    </p>

                </div>


                {{-- KETERANGAN --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Bentuk / Rincian Pelanggaran
                    </label>

                    <textarea
                        id="inputKeterangan"
                        name="keterangan"
                        required
                        rows="3"
                        placeholder="Contoh: Siswa datang terlambat 20 menit..."
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"
                    ></textarea>

                </div>


                {{-- TANGGAL --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Transaksi
                    </label>

                    <input
                        type="date"
                        id="inputTanggal"
                        name="tanggal_transaksi"
                        required
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"
                        value="{{ date('Y-m-d') }}"
                    >

                </div>


                {{-- SANKSI --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Tindakan Pembinaan / Sanksi
                    </label>

                    <input
                        type="text"
                        id="inputSanksi"
                        name="sanksi"
                        placeholder="Contoh: Nasihat dan tugas tambahan"
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"
                    >

                </div>


                {{-- BUTTON --}}
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">

                    <button
                        type="button"
                        onclick="closeModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-medium hover:bg-rose-600 transition"
                    >
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        Simpan Pelanggaran
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        MODAL TAMBAH JENIS PELANGGARAN
    ========================================================== --}}
    <div
        id="categoryModal"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4"
    >

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">

            {{-- HEADER --}}
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">

                <h3 class="font-bold text-base">
                    Tambah Jenis Pelanggaran
                </h3>

                <button
                    type="button"
                    onclick="closeCategoryModal()"
                    class="text-white/80 hover:text-white text-lg"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>


            {{-- FORM --}}
            <form
                id="categoryForm"
                action="{{ route('guru.pelanggaran.kategori.store') }}"
                method="POST"
                class="p-6 space-y-4"
            >

                @csrf


                {{-- NAMA --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Nama Jenis Pelanggaran
                    </label>

                    <input
                        type="text"
                        name="nama_kategori"
                        required
                        placeholder="Contoh: Bermain Game Saat Jam Belajar"
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"
                    >

                </div>


                {{-- POIN --}}
                <div>

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Poin Pengurangan (-)
                    </label>

                    <input
                        type="number"
                        name="poin"
                        value="5"
                        required
                        min="1"
                        max="100"
                        class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"
                    >

                </div>


                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">

                    <div class="flex gap-2">

                        <i class="fa-solid fa-circle-info text-amber-500 text-xs mt-0.5"></i>

                        <p class="text-[10px] text-amber-700 leading-relaxed">
                            Jenis pelanggaran dan poin akan disimpan sebagai aturan.
                            Tindakan atau sanksi dicatat saat guru membuat catatan pelanggaran.
                        </p>

                    </div>

                </div>


                {{-- BUTTON --}}
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">

                    <button
                        type="button"
                        onclick="closeCategoryModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition"
                    >
                        <i class="fa-solid fa-plus mr-1"></i>
                        Simpan Jenis
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        JAVASCRIPT
    ========================================================== --}}
    <script>

        // =====================================================
        // AUTO ISI POIN
        // =====================================================

        function autoFillViolationData() {

            const selectElement =
                document.getElementById('selectKategori');

            const selectedOption =
                selectElement.options[
                    selectElement.selectedIndex
                ];

            const inputPoin =
                document.getElementById('inputPoin');


            if (!selectedOption || !selectedOption.value) {

                inputPoin.value = '';

                return;
            }


            const poin =
                selectedOption.getAttribute('data-poin');


            inputPoin.value = poin || '';
        }


        // =====================================================
        // MODAL PELANGGARAN
        // =====================================================

        function openModal() {

            const modal =
                document.getElementById('violationModal');

            modal.classList.remove('hidden');

            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        }


        function closeModal() {

            const modal =
                document.getElementById('violationModal');

            modal.classList.add('hidden');

            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');

            document.getElementById('violationForm').reset();

            document.getElementById('inputPoin').value = '';

            document.getElementById('inputTanggal').value =
                new Date().toISOString().split('T')[0];
        }


        // =====================================================
        // MODAL KATEGORI
        // =====================================================

        function openCategoryModal() {

            const modal =
                document.getElementById('categoryModal');

            modal.classList.remove('hidden');

            modal.classList.add('flex');
        }


        function closeCategoryModal() {

            const modal =
                document.getElementById('categoryModal');

            modal.classList.add('hidden');

            modal.classList.remove('flex');

            document.getElementById('categoryForm').reset();
        }


        // =====================================================
        // SEARCH
        // =====================================================

        function searchViolations() {

            const searchInput =
                document.getElementById('searchInput');

            const search =
                searchInput.value.toLowerCase().trim();

            const rows =
                document.querySelectorAll(
                    '#violationTableBody tr'
                );


            rows.forEach(row => {

                if (row.cells.length < 8) {
                    return;
                }


                const nis =
                    row.cells[1]?.textContent.toLowerCase() || '';

                const nama =
                    row.cells[2]?.textContent.toLowerCase() || '';

                const kelas =
                    row.cells[3]?.textContent.toLowerCase() || '';

                const bentuk =
                    row.cells[4]?.textContent.toLowerCase() || '';

                const sanksi =
                    row.cells[6]?.textContent.toLowerCase() || '';


                const cocok =
                    nis.includes(search) ||
                    nama.includes(search) ||
                    kelas.includes(search) ||
                    bentuk.includes(search) ||
                    sanksi.includes(search);


                row.style.display =
                    cocok ? '' : 'none';

            });
        }


        // =====================================================
        // TUTUP MODAL KETIKA KLIK AREA LUAR
        // =====================================================

        document.addEventListener('click', function(event) {

            const violationModal =
                document.getElementById('violationModal');

            const categoryModal =
                document.getElementById('categoryModal');


            if (
                event.target === violationModal
            ) {
                closeModal();
            }


            if (
                event.target === categoryModal
            ) {
                closeCategoryModal();
            }

        });


        // =====================================================
        // ESC UNTUK TUTUP MODAL
        // =====================================================

        document.addEventListener('keydown', function(event) {

            if (event.key !== 'Escape') {
                return;
            }


            const violationModal =
                document.getElementById('violationModal');

            const categoryModal =
                document.getElementById('categoryModal');


            if (
                !violationModal.classList.contains('hidden')
            ) {
                closeModal();
            }


            if (
                !categoryModal.classList.contains('hidden')
            ) {
                closeCategoryModal();
            }

        });


        // =====================================================
        // INITIALIZATION
        // =====================================================

        document.addEventListener('DOMContentLoaded', function() {

            const tanggalInput =
                document.getElementById('inputTanggal');


            if (!tanggalInput.value) {

                tanggalInput.value =
                    new Date().toISOString().split('T')[0];

            }

        });

    </script>

@endsection