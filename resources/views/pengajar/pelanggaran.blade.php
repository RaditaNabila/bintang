@extends('layouts.app')

@section('title', 'Pelanggaran Siswa - Pengajar')
@section('page_title', 'Pelanggaran Siswa')
@section('page_description', 'Daftar dan pencatatan pelanggaran siswa')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<style>
.ts-control {
    border-radius: .75rem !important;
    border-color: #e5e7eb !important;
    padding: .5rem .875rem !important;
    font-size: .75rem !important;
    background: #fff !important;
    box-shadow: none !important;
}

.ts-wrapper.focus .ts-control {
    border-color: #f43f5e !important;
    box-shadow: 0 0 0 2px rgba(244, 63, 94, .15) !important;
}

.ts-dropdown {
    border-radius: .75rem !important;
    font-size: .75rem !important;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,.1) !important;
    z-index: 100 !important;
}
</style>
@endpush

@section('content')

<div class="space-y-6">

    {{-- TOOLBAR --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">

        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

            <input
                type="text"
                id="searchInput"
                onkeyup="filterData()"
                placeholder="Cari NIS, nama, atau keterangan..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 focus:bg-white transition"
            >
        </div>

        <button
            type="button"
            onclick="openModal()"
            class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition"
        >
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Catat Pelanggaran</span>
        </button>

    </div>


    {{-- FILTER / JUDUL --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">

        <div>
            <h3 class="text-base font-bold text-gray-800">
                Riwayat Catatan Pelanggaran
            </h3>

            <p class="text-xs text-gray-500">
                Riwayat pengurangan poin disiplin siswa
            </p>
        </div>

        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
            <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>

            <select
                id="filterBulan"
                onchange="filterData()"
                class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer"
            >
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

                    <option value="{{ $value }}">
                        {{ $nama }}
                    </option>

                @endforeach

            </select>
        </div>

    </div>


    {{-- TABEL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <div class="overflow-x-auto">

            <table
                class="w-full text-left border-collapse text-sm"
                id="violationTable"
            >

                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">

                        <th class="py-3 px-4">Tanggal</th>

                        <th class="py-3 px-4">NIS</th>

                        <th class="py-3 px-4">Nama Siswa</th>

                        <th class="py-3 px-4">Kelas</th>

                        <th class="py-3 px-4">
                            Jenis Pelanggaran
                        </th>

                        <th class="py-3 px-4">
                            Keterangan
                        </th>

                        <th class="py-3 px-4">
                            Sanksi
                        </th>

                        <th class="py-3 px-4 text-center">
                            Poin (-)
                        </th>

                        <th class="py-3 px-4 text-center">
                            Sisa Poin
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-50">

                    @forelse($transaksi as $item)

                        <tr
                            class="hover:bg-gray-50/50"
                            data-bulan="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m') }}"
                        >

                            {{-- TANGGAL --}}
                            <td class="py-3.5 px-4 text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}
                            </td>


                            {{-- NIS --}}
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">
                                {{ $item->siswa->nis ?? '-' }}
                            </td>


                            {{-- NAMA --}}
                            <td class="py-3.5 px-4 font-semibold text-gray-700">
                                {{ $item->siswa->nama_lengkap ?? '-' }}
                            </td>


                            {{-- KELAS --}}
                            <td class="py-3.5 px-4 text-gray-500">
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                            </td>


                            {{-- JENIS --}}
                            <td class="py-3.5 px-4">

                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-xs font-medium">

                                    {{
                                        $item->aturanPoin->judul
                                        ?? $item->aturanPoin->nama_aturan
                                        ?? '-'
                                    }}

                                </span>

                            </td>


                            {{-- KETERANGAN --}}
                            <td class="py-3.5 px-4 text-gray-600">
                                {{ $item->keterangan ?? '-' }}
                            </td>


                            {{-- SANKSI --}}
                            <td class="py-3.5 px-4 text-gray-600">
                                {{ $item->sanksi ?? '-' }}
                            </td>


                            {{-- POIN --}}
                            <td class="py-3.5 px-4 font-bold text-rose-600 text-center">
                                -{{ abs($item->poin ?? 0) }}
                            </td>


                            {{-- SISA POIN --}}
                            <td class="py-3.5 px-4 text-center">

                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">
                                    {{ $item->siswa->poin_saat_ini ?? 0 }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="py-10 text-center text-gray-400 text-sm"
                            >
                                Belum ada catatan pelanggaran siswa.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if(method_exists($transaksi,'hasPages') && $transaksi->hasPages())

            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">

                <div class="text-xs text-gray-500">

                    Menampilkan

                    <span class="font-bold text-gray-700">
                        {{ $transaksi->firstItem() }}
                    </span>

                    hingga

                    <span class="font-bold text-gray-700">
                        {{ $transaksi->lastItem() }}
                    </span>

                    dari

                    <span class="font-bold text-gray-700">
                        {{ $transaksi->total() }}
                    </span>

                    catatan

                </div>


                <div class="inline-flex items-center gap-1.5">

                    @if($transaksi->onFirstPage())

                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold">
                            Prev
                        </span>

                    @else

                        <a
                            href="{{ $transaksi->previousPageUrl() }}"
                            class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-semibold transition"
                        >
                            Prev
                        </a>

                    @endif


                    <span class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 rounded-xl border border-rose-200/60">

                        {{ $transaksi->currentPage() }}
                        /
                        {{ $transaksi->lastPage() }}

                    </span>


                    @if($transaksi->hasMorePages())

                        <a
                            href="{{ $transaksi->nextPageUrl() }}"
                            class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 hover:bg-rose-500 hover:text-white rounded-xl text-xs font-semibold transition"
                        >
                            Next
                        </a>

                    @else

                        <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold">
                            Next
                        </span>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>



{{-- ========================================================= --}}
{{-- MODAL CATAT PELANGGARAN --}}
{{-- ========================================================= --}}

<div
    id="violationModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4"
>

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden max-h-[90vh]">


        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-rose-500 to-red-600 px-6 py-4 text-white flex justify-between items-center">

            <div>

                <h3 class="font-bold text-base">
                    Catat Pelanggaran Siswa
                </h3>

                <p class="text-xs text-white/80">
                    Catat pelanggaran siswa
                </p>

            </div>


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
            action="{{ route('pengajar.pelanggaran.store') }}"
            method="POST"
        >

            @csrf

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">


                {{-- SISWA --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">
                        Nama Siswa
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="siswa_id"
                        id="inputSiswa"
                        required
                    >

                        <option value="">
                            Pilih siswa
                        </option>

                        @foreach($siswa as $item)

                            <option
                                value="{{ $item->id }}"
                                data-kelas="{{ $item->kelas->nama_kelas ?? '-' }}"
                            >

                                {{ $item->nama_lengkap }}
                                ({{ $item->nis ?? '-' }})

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TANGGAL --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">

                        Tanggal
                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="date"
                        name="tanggal_transaksi"
                        id="inputTanggal"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full border border-gray-200 rounded-xl text-xs py-2.5 px-3"
                    >

                </div>


                {{-- KELAS --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">
                        Kelas
                    </label>

                    <input
                        type="text"
                        id="inputKelas"
                        readonly
                        placeholder="Kelas siswa otomatis"
                        class="w-full border border-gray-200 rounded-xl bg-gray-100 text-gray-700 text-xs py-2.5 px-3"
                    >

                </div>


                {{-- KATEGORI --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">

                        Kategori Pelanggaran
                        <span class="text-red-500">*</span>

                    </label>

                    <select
                        name="kategori_id"
                        id="inputKategori"
                        required
                    >

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        @foreach($kategoriPelanggaran as $kategori)

                            <option value="{{ $kategori->id }}">
                                {{ $kategori->nama_kategori }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- JENIS --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">

                        Jenis Pelanggaran
                        <span class="text-red-500">*</span>

                    </label>

                    <select
                        name="aturan_poin_id"
                        id="inputAturan"
                        required
                    >

                        <option value="">
                            Pilih kategori terlebih dahulu
                        </option>

                    </select>

                </div>


                {{-- POIN --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">

                        Poin (-)
                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="number"
                        id="inputPoin"
                        readonly
                        placeholder="Otomatis terisi"
                        class="w-full border border-gray-200 rounded-xl bg-gray-100 font-bold text-rose-600 text-xs py-2.5 px-3"
                    >

                </div>


                {{-- KETERANGAN --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">

                        Keterangan
                        <span class="text-red-500">*</span>

                    </label>

                    <textarea
                        name="keterangan"
                        id="inputKeterangan"
                        rows="3"
                        required
                        placeholder="Masukkan keterangan pelanggaran..."
                        class="w-full border border-gray-200 rounded-xl text-xs p-3"
                    ></textarea>

                </div>


                {{-- SANKSI --}}
                <div>

                    <label class="mb-1 block text-xs font-semibold text-gray-700">
                        Sanksi
                    </label>

                    <textarea
                        name="sanksi"
                        id="inputSanksi"
                        rows="3"
                        placeholder="Masukkan sanksi yang diberikan..."
                        class="w-full border border-gray-200 rounded-xl text-xs p-3"
                    ></textarea>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="flex justify-end gap-2 p-6 pt-4 border-t">

                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-medium hover:bg-rose-600"
                >
                    Simpan Pelanggaran
                </button>

            </div>

        </form>

    </div>

</div>

@endsection



@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>

let selectSiswaTs;
let selectKategoriTs;
let selectAturanPoinTs;


/* =========================================================
   INIT TOM SELECT
========================================================= */

document.addEventListener('DOMContentLoaded', () => {

    selectSiswaTs = new TomSelect('#inputSiswa', {

        create: false,

        sortField: {
            field: 'text',
            direction: 'asc'
        },

        onChange: value => {
            updateKelasFromSiswa(value);
        }

    });


    selectKategoriTs = new TomSelect('#inputKategori', {

        create: false,

        sortField: {
            field: 'text',
            direction: 'asc'
        },

        onChange: value => {
            filterJenisPelanggaran(value);
        }

    });


    selectAturanPoinTs = new TomSelect('#inputAturan', {

        create: false,

        sortField: {
            field: 'text',
            direction: 'asc'
        },

        onChange: value => {
            updatePointFromAturan(value);
        }

    });


    selectAturanPoinTs.disable();

});


/* =========================================================
   KELAS OTOMATIS
========================================================= */

function updateKelasFromSiswa(value) {

    const input = document.getElementById('inputKelas');

    if (!value) {

        input.value = '';

        return;

    }

    const option = document.querySelector(
        `#inputSiswa option[value="${value}"]`
    );

    input.value = option?.dataset.kelas || '-';

}


/* =========================================================
   FILTER JENIS BERDASARKAN KATEGORI
========================================================= */

function filterJenisPelanggaran(kategoriId) {

    const poin = document.getElementById('inputPoin');

    if (!selectAturanPoinTs) {
        return;
    }


    selectAturanPoinTs.clear(true);

    selectAturanPoinTs.clearOptions();

    poin.value = '';


    if (!kategoriId) {

        selectAturanPoinTs.addOption({
            value: '',
            text: 'Pilih kategori terlebih dahulu'
        });

        selectAturanPoinTs.setValue('', true);

        selectAturanPoinTs.disable();

        return;

    }


    const aturan = @json($aturanPelanggaran);


    const filtered = aturan.filter(item =>
        String(item.kategori_id) === String(kategoriId)
    );


    filtered.forEach(item => {

        const nilai = Math.abs(
            Number(item.nilai_poin || 0)
        );


        selectAturanPoinTs.addOption({

            value: item.id,

            text: `${item.judul} (-${nilai} Poin)`,

            poin: nilai

        });

    });


    if (!filtered.length) {

        selectAturanPoinTs.addOption({

            value: '',

            text: 'Belum ada jenis untuk kategori ini'

        });

    }


    selectAturanPoinTs.enable();

    selectAturanPoinTs.refreshOptions(false);

}


/* =========================================================
   POIN OTOMATIS
========================================================= */

function updatePointFromAturan(value) {

    const input = document.getElementById('inputPoin');

    input.value =
        value &&
        selectAturanPoinTs.options[value]
            ? selectAturanPoinTs.options[value].poin || ''
            : '';

}


/* =========================================================
   MODAL
========================================================= */

function openModal() {

    selectSiswaTs?.clear(true);

    selectKategoriTs?.clear(true);

    selectAturanPoinTs?.clear(true);

    selectAturanPoinTs?.clearOptions();


    selectAturanPoinTs?.addOption({

        value: '',

        text: 'Pilih kategori terlebih dahulu'

    });


    selectAturanPoinTs?.setValue('', true);

    selectAturanPoinTs?.disable();


    document.getElementById('inputKelas').value = '';

    document.getElementById('inputTanggal').value =
        "{{ date('Y-m-d') }}";

    document.getElementById('inputKeterangan').value = '';

    document.getElementById('inputSanksi').value = '';

    document.getElementById('inputPoin').value = '';


    showModal('violationModal');

}


function closeModal() {

    hideModal('violationModal');

}


/* =========================================================
   SHOW / HIDE MODAL
========================================================= */

function showModal(id) {

    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.remove('hidden');

    modal.classList.add('flex');

}


function hideModal(id) {

    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.add('hidden');

    modal.classList.remove('flex');

}


/* =========================================================
   SEARCH + FILTER BULAN
========================================================= */

function filterData() {

    const search =
        document
            .getElementById('searchInput')
            .value
            .toLowerCase()
            .trim();


    const bulan =
        document
            .getElementById('filterBulan')
            .value;


    document
        .querySelectorAll('#violationTable tbody tr')
        .forEach(row => {

            if (row.cells.length < 2) {
                return;
            }


            const cocokSearch =
                row.innerText
                    .toLowerCase()
                    .includes(search);


            const cocokBulan =
                !bulan ||
                row.dataset.bulan === bulan;


            row.style.display =
                cocokSearch && cocokBulan
                    ? ''
                    : 'none';

        });

}


/* =========================================================
   ESC
========================================================= */

document.addEventListener('keydown', e => {

    if (e.key === 'Escape') {

        closeModal();

    }

});


/* =========================================================
   KLIK LUAR MODAL
========================================================= */

document.addEventListener('click', e => {

    const modal =
        document.getElementById('violationModal');


    if (e.target === modal) {

        closeModal();

    }

});

</script>

@endpush