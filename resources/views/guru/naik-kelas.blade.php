@extends('layouts.app')

@section('title', 'Proses Kenaikan Kelas - Bintang Poin')
@section('page_title', 'Manajemen Kenaikan Kelas')
@section('page_description', 'Kelola perpindahan siswa untuk Tahun Ajaran Baru')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
    <div>
        <p class="font-bold text-xs">Proses Berhasil!</p>
        <p class="text-xs">{{ session('success') }}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-2xl flex items-center gap-3">
    <i class="fa-solid fa-circle-xmark text-rose-600 text-xl"></i>
    <div>
        <p class="font-bold text-xs">Proses Gagal!</p>
        <p class="text-xs">{{ session('error') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-2xl">
    <p class="font-bold text-xs mb-2">Terjadi kesalahan:</p>
    <ul class="text-xs list-disc ml-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
    <a href="{{ route('guru.data-siswa') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali ke Data Siswa
    </a>
</div>

{{-- LANGKAH 1 --}}
<div class="bg-white p-6 rounded-2xl border border-emerald-200 shadow-sm space-y-4 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
        <div>
            <span class="inline-block px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold rounded-lg text-[10px] uppercase">
                Langkah 1
            </span>

            <h3 class="text-base font-bold text-gray-800 mt-2 flex items-center gap-2">
                <i class="fa-solid fa-bolt text-emerald-600"></i>
                Jalankan Naik Kelas Otomatis
            </h3>

            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                Sistem akan memproses:
                <strong>Kelas 1 → 2</strong>,
                <strong>2 → 3</strong>,
                <strong>3 → 4</strong>,
                <strong>5 → 6</strong>,
                dan <strong>6 → Lulus</strong>.
                <br>
                <span class="text-amber-700 font-semibold">
                    *Kelas 4 tidak diproses otomatis dan dapat diatur pada Langkah 2.
                </span>
            </p>
        </div>

        <form action="{{ route('guru.naik-kelas.proses') }}" method="POST" onsubmit="return confirm('Yakin ingin menjalankan proses kenaikan kelas otomatis?\\n\\nKelas 1-3 akan naik, Kelas 5 naik ke Kelas 6, dan Kelas 6 menjadi Lulus.\\n\\nKelas 4 tidak akan diubah.')">
            @csrf

            <button type="submit" class="shrink-0 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-play"></i>
                Jalankan Naik Kelas Otomatis
            </button>
        </form>
    </div>
</div>

{{-- LANGKAH 2 --}}
<div class="bg-white p-6 rounded-2xl border border-orange-200 shadow-sm space-y-5">

    <div>
        <span class="inline-block px-2.5 py-1 bg-orange-100 text-orange-800 font-bold rounded-lg text-[10px] uppercase">
            Langkah 2
        </span>

        <h3 class="text-base font-bold text-gray-800 mt-2 flex items-center gap-2">
            <i class="fa-solid fa-sliders text-orange-600"></i>
            Plotting & Penyesuaian Kelas Tujuan
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Cari nama siswa atau gunakan filter untuk memilih siswa yang akan dipindahkan ke rombel tujuan.
        </p>
    </div>

    {{-- FILTER --}}
    <div class="space-y-4 bg-amber-50/60 p-4 rounded-xl border border-amber-200">

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                Cari Nama / NIS Siswa
            </label>

            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>

                <input
                    type="text"
                    id="searchInput"
                    onkeyup="renderSiswaList()"
                    placeholder="Ketik nama atau NIS siswa..."
                    class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-amber-500 transition">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- FILTER KELAS --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    1. Tampilkan Siswa Kelas
                </label>

                <select
                    id="filterFromGrade"
                    onchange="renderSiswaList()"
                    class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-amber-900">

                    <option value="1">Siswa Kelas 1</option>
                    <option value="2">Siswa Kelas 2</option>
                    <option value="3">Siswa Kelas 3</option>
                    <option value="4" selected>Siswa Kelas 4</option>
                    <option value="5">Siswa Kelas 5</option>
                    <option value="6">Siswa Kelas 6</option>
                    <option value="Lulus">Siswa Lulus</option>

                </select>
            </div>

            {{-- FILTER GENDER --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    2. Filter Jenis Kelamin
                </label>

                <select
                    id="filterGender"
                    onchange="renderSiswaList()"
                    class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-medium">

                    <option value="all">Semua Siswa</option>
                    <option value="Laki-laki">Khusus Laki-laki (Putra)</option>
                    <option value="Perempuan">Khusus Perempuan (Putri)</option>

                </select>
            </div>

            {{-- KELAS TUJUAN --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    3. Pindahkan Terpilih Ke Rombel
                </label>

                <select
                    id="targetClass"
                    class="w-full p-2.5 bg-white border border-orange-300 font-bold text-orange-700 rounded-lg text-xs">

                    @foreach($kelas as $k)
                        @php
                            $tingkat = (int) preg_replace('/[^0-9]/', '', $k->nama_kelas);
                        @endphp

                        @if($tingkat >= 2 && $tingkat <= 6)
                            <option value="{{ $k->id_kelas }}" data-nama="{{ $k->nama_kelas }}">
                                Kelas {{ $k->nama_kelas }}
                            </option>
                        @endif
                    @endforeach

                </select>
            </div>

        </div>
    </div>

    {{-- FORM PEMINDAHAN --}}
    <form
        action="{{ route('guru.naik-kelas.pindahkan') }}"
        method="POST"
        id="formPemindahan">

        @csrf

        <input type="hidden" name="kelas_tujuan" id="kelasTujuanInput">
        <input type="hidden" name="reset_poin" id="resetPoinInput" value="1">

        <div id="selectedStudents"></div>

        {{-- TABLE --}}
        <div class="border rounded-xl overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full min-w-[650px] text-left text-xs border-collapse">

                    <thead class="bg-gray-100 border-b text-gray-600 font-bold">

                        <tr>

                            <th class="p-3 w-10 text-center">
                                <input
                                    type="checkbox"
                                    id="checkAll"
                                    onclick="selectAll(this)"
                                    class="rounded">
                            </th>

                            <th class="p-3">
                                NIS
                            </th>

                            <th class="p-3">
                                Nama Lengkap
                            </th>

                            <th class="p-3">
                                Kelas Saat Ini
                            </th>

                            <th class="p-3">
                                Gender
                            </th>

                        </tr>

                    </thead>

                    <tbody
                        id="listSiswaTbody"
                        class="divide-y divide-gray-100">
                    </tbody>

                </table>

            </div>

        </div>

        {{-- BOTTOM --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4">

            <label class="text-xs text-gray-600 flex items-center gap-2 cursor-pointer">

                <input
                    type="checkbox"
                    id="resetPoin"
                    checked
                    class="w-4 h-4 text-orange-600 rounded">

                <span>
                    Reset Poin Siswa Terpilih kembali ke
                    <strong>250 Poin</strong>
                </span>

            </label>

            <button
                type="button"
                onclick="simpanPemindahan()"
                class="w-full sm:w-auto px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">

                <i class="fa-solid fa-check"></i>

                Simpan Pemindahan Rombel

            </button>

        </div>

    </form>

</div>

@endsection

@push('scripts')

@php
    $siswaDatabase = $siswa->map(function ($item) {
        return [
            'id' => $item->id,
            'nisn' => $item->nisn ?? '',
            'nis' => $item->nis ?? '',
            'nama' => $item->nama_lengkap ?? '',
            'kelas_id' => $item->kelas_id,
            'kelas' => $item->kelas?->nama_kelas ?? '-',
            'tingkat' => $item->kelas?->tingkat ?? '',
            'gender' => $item->jenis_kelamin ?? '-',
            'poin' => $item->poin_saat_ini ?? 250,
            'status' => $item->status ?? 'aktif',
        ];
    })->values();
@endphp

<script>
    const siswaDatabase = @json($siswaDatabase);

    document.addEventListener('DOMContentLoaded', function () {
        renderSiswaList();
    });

    function getTingkat(kelas) {
        if (!kelas || kelas === '-') {
            return '';
        }

        const match = String(kelas).match(/\d+/);

        return match ? match[0] : '';
    }

    function renderSiswaList() {
        const searchInput = document.getElementById('searchInput');
        const filterFromGrade = document.getElementById('filterFromGrade');
        const filterGender = document.getElementById('filterGender');
        const tbody = document.getElementById('listSiswaTbody');

        if (!searchInput || !filterFromGrade || !filterGender || !tbody) {
            return;
        }

        const searchValue = searchInput.value.toLowerCase().trim();
        const fromGrade = filterFromGrade.value;
        const gender = filterGender.value;

        tbody.innerHTML = '';

        const filtered = siswaDatabase.filter(function (siswa) {

            const tingkat = getTingkat(siswa.kelas);

            const matchesGrade =
                String(tingkat) === String(fromGrade);

            const matchesGender =
                gender === 'all' ||
                siswa.gender === gender;

            const nama = String(siswa.nama || '').toLowerCase();
            const nis = String(siswa.nis || '').toLowerCase();
            const nisn = String(siswa.nisn || '').toLowerCase();

            const matchesSearch =
                nama.includes(searchValue) ||
                nis.includes(searchValue) ||
                nisn.includes(searchValue);

            return matchesGrade &&
                   matchesGender &&
                   matchesSearch;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5"
                        class="p-6 text-center text-gray-400 italic bg-gray-50/50">

                        <i class="fa-solid fa-user-slash
                                  text-gray-300 text-2xl mb-2 block"></i>

                        Siswa tidak ditemukan pada pencarian/filter ini.

                    </td>
                </tr>
            `;

            const checkAll = document.getElementById('checkAll');

            if (checkAll) {
                checkAll.checked = false;
            }

            return;
        }

        filtered.forEach(function (siswa) {

            const tr = document.createElement('tr');

            tr.className =
                'hover:bg-amber-50/40 transition';

            // CHECKBOX
            const checkbox = document.createElement('input');

            checkbox.type = 'checkbox';
            checkbox.className = 'cb-siswa rounded';
            checkbox.value = siswa.id;
            checkbox.dataset.id = siswa.id;

            const tdCheck = document.createElement('td');

            tdCheck.className =
                'p-3 text-center';

            tdCheck.appendChild(checkbox);

            // NIS
            const tdNis = document.createElement('td');

            tdNis.className =
                'p-3 font-mono text-gray-500';

            tdNis.textContent =
                siswa.nis || siswa.nisn || '-';

            // NAMA
            const tdNama = document.createElement('td');

            tdNama.className =
                'p-3 font-semibold text-gray-800';

            tdNama.textContent =
                siswa.nama || '-';

            // KELAS
            const tdKelas = document.createElement('td');

            tdKelas.className =
                'p-3';

            const badge =
                document.createElement('span');

            badge.className =
                'px-2.5 py-1 bg-amber-100 text-amber-800 font-bold rounded-lg text-xs';

            badge.textContent =
                siswa.kelas || '-';

            tdKelas.appendChild(badge);

            // GENDER
            const tdGender =
                document.createElement('td');

            tdGender.className =
                'p-3 text-gray-600 font-medium';

            tdGender.textContent =
                siswa.gender || '-';

            // APPEND
            tr.appendChild(tdCheck);
            tr.appendChild(tdNis);
            tr.appendChild(tdNama);
            tr.appendChild(tdKelas);
            tr.appendChild(tdGender);

            tbody.appendChild(tr);
        });

        const checkAll =
            document.getElementById('checkAll');

        if (checkAll) {
            checkAll.checked = false;
        }
    }

    function selectAll(master) {

        document.querySelectorAll('.cb-siswa').forEach(function (checkbox) {

            checkbox.checked =
                master.checked;

        });
    }

    function simpanPemindahan() {

        const checkedBoxes =
            document.querySelectorAll('.cb-siswa:checked');

        if (checkedBoxes.length === 0) {

            alert(
                'Centang siswa di tabel yang ingin dipindahkan!'
            );

            return;
        }

        const targetClass =
            document.getElementById('targetClass');

        if (!targetClass) {

            alert(
                'Kelas tujuan tidak ditemukan.'
            );

            return;
        }

        const targetId =
            targetClass.value;

        if (!targetId) {

            alert(
                'Pilih kelas tujuan terlebih dahulu.'
            );

            return;
        }

        const selectedOption =
            targetClass.options[
                targetClass.selectedIndex
            ];

        const targetName =
            selectedOption
                ? selectedOption.getAttribute('data-nama')
                : '';

        if (!confirm(
            `Pindahkan ${checkedBoxes.length} siswa terpilih ke Kelas ${targetName}?`
        )) {
            return;
        }

        const selectedStudents =
            document.getElementById('selectedStudents');

        if (!selectedStudents) {

            alert(
                'Form siswa tidak ditemukan.'
            );

            return;
        }

        selectedStudents.innerHTML = '';

        checkedBoxes.forEach(function (checkbox) {

            const input =
                document.createElement('input');

            input.type = 'hidden';

            input.name =
                'siswa[]';

            input.value =
                checkbox.value;

            selectedStudents.appendChild(input);
        });

        const kelasTujuanInput =
            document.getElementById('kelasTujuanInput');

        const resetPoinInput =
            document.getElementById('resetPoinInput');

        const resetPoin =
            document.getElementById('resetPoin');

        if (kelasTujuanInput) {
            kelasTujuanInput.value =
                targetId;
        }

        if (resetPoinInput) {
            resetPoinInput.value =
                resetPoin && resetPoin.checked
                    ? '1'
                    : '0';
        }

        const form =
            document.getElementById('formPemindahan');

        if (form) {
            form.submit();
        }
    }
</script>

@endpush