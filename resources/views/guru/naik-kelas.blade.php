@extends('layouts.app')

@section('title', 'Manajemen Kenaikan Kelas - Bintang Poin')
@section('page_title', 'Manajemen Kenaikan Kelas')
@section('page_description', 'Kelola perpindahan rombel dan kenaikan kelas siswa secara manual')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
    <div>
        <p class="font-bold text-xs">Berhasil!</p>
        <p class="text-xs">{{ session('success') }}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-2xl flex items-center gap-3">
    <i class="fa-solid fa-circle-xmark text-rose-600 text-xl"></i>
    <div>
        <p class="font-bold text-xs">Gagal!</p>
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

{{-- KONTROL UTAMA MANUAL --}}
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-5">

    <div>
        <span class="inline-block px-2.5 py-1 bg-blue-100 text-blue-800 font-bold rounded-lg text-[10px] uppercase">
            Panel Kontrol Manual
        </span>

        <h3 class="text-base font-bold text-gray-800 mt-2 flex items-center gap-2">
            <i class="fa-solid fa-people-arrows text-blue-600"></i>
            Pilih & Pindahkan Siswa Rombel
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Pilih tingkat kelas dan rombel asal di bawah ini, centang siswa yang ingin dipindahkan, tentukan kelas tujuan, lalu simpan.
        </p>
    </div>

    {{-- FILTER & TARGET BOX --}}
    <div class="space-y-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- 1A. PILIH TINGKAT KELAS ASAL --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    1. Pilih Tingkat Kelas Asal
                </label>
                <select
                    id="filterTingkat"
                    onchange="onTingkatChange()"
                    class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-800 focus:outline-none focus:border-blue-500">
                    <option value="1">Kelas 1</option>
                    <option value="2">Kelas 2</option>
                    <option value="3">Kelas 3</option>
                    <option value="4" selected>Kelas 4</option>
                    <option value="5">Kelas 5</option>
                    <option value="6">Kelas 6</option>
                    <option value="Lulus">Lulus / Alumni</option>
                </select>
            </div>

            {{-- 1B. PILIH ROMBEL / KELAS SPESIFIK --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    2. Pilih Rombel / Kelas Spesifik
                </label>
                <select
                    id="filterRombel"
                    onchange="renderSiswaList()"
                    class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-800 focus:outline-none focus:border-blue-500">
                    {{-- Diisi otomatis oleh JS --}}
                </select>
            </div>

            {{-- 2. FILTER GENDER --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    3. Filter Jenis Kelamin
                </label>
                <select
                    id="filterGender"
                    onchange="renderSiswaList()"
                    class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="all">Semua Siswa</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            {{-- 3. KELAS TUJUAN (Menggunakan id_kelas agar bernilai integer) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    4. Pindahkan Ke Rombel Tujuan
                </label>
                <select
                    id="targetClass"
                    class="w-full p-2.5 bg-white border border-blue-300 font-bold text-blue-700 rounded-lg text-xs focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>-- Pilih Rombel Tujuan --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}">
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- PENCARIAN KATA KUNCI --}}
        <div>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input
                    type="text"
                    id="searchInput"
                    onkeyup="renderSiswaList()"
                    placeholder="Cari cepat berdasarkan nama atau NIS siswa..."
                    class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500 transition">
            </div>
        </div>
    </div>

    {{-- FORM EKSEKUSI PEMINDAHAN --}}
    <form action="{{ route('guru.naik-kelas.pindahkan') }}" method="POST" id="formPemindahan">
        @csrf

        <input type="hidden" name="kelas_tujuan" id="kelasTujuanInput">
        <input type="hidden" name="reset_poin" id="resetPoinInput" value="1">
        <div id="selectedStudents"></div>

        {{-- TABEL SISWA --}}
        <div class="border rounded-xl overflow-hidden bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-left text-xs border-collapse">
                    <thead class="bg-gray-100 border-b text-gray-600 font-bold">
                        <tr>
                            <th class="p-3 w-10 text-center">
                                <input type="checkbox" id="checkAll" onclick="selectAll(this)" class="rounded">
                            </th>
                            <th class="p-3">NIS / NISN</th>
                            <th class="p-3">Nama Lengkap</th>
                            <th class="p-3">Kelas Saat Ini</th>
                            <th class="p-3">Gender</th>
                        </tr>
                    </thead>
                    <tbody id="listSiswaTbody" class="divide-y divide-gray-100">
                        {{-- Diisi secara dinamis via JavaScript --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BAGIAN BAWAH (OPSI & TOMBOL SUBMIT) --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4">
            <label class="text-xs text-gray-600 flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="resetPoin" checked class="w-4 h-4 text-blue-600 rounded">
                <span>
                    Reset Poin Siswa Terpilih kembali ke <strong>250 Poin</strong> saat dipindah
                </span>
            </label>

            <button
                type="button"
                onclick="simpanPemindahan()"
                class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Proses Pindahkan Siswa Terpilih
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
            'gender' => $item->jenis_kelamin ?? '-',
            'poin' => $item->poin_saat_ini ?? 250,
            'status' => $item->status ?? 'aktif',
        ];
    })->values();

    $daftarKelasRombel = $kelas->map(function($k) {
        preg_match('/\d+/', $k->nama_kelas, $matches);
        $tingkat = $matches[0] ?? '';
        return [
            'tingkat' => $tingkat,
            'nama_kelas' => $k->nama_kelas
        ];
    })->values();
@endphp

<script>
    const siswaDatabase = @json($siswaDatabase);
    const kelasRombelData = @json($daftarKelasRombel);

    document.addEventListener('DOMContentLoaded', function () {
        onTingkatChange();
    });

    function onTingkatChange() {
        const filterTingkat = document.getElementById('filterTingkat');
        const filterRombel = document.getElementById('filterRombel');
        if (!filterTingkat || !filterRombel) return;

        const selectedTingkat = filterTingkat.value;
        filterRombel.innerHTML = '';

        if (selectedTingkat === 'Lulus') {
            filterRombel.innerHTML = `<option value="Lulus">Status: Lulus</option>`;
        } else {
            const matchedRombels = kelasRombelData.filter(item => item.tingkat === selectedTingkat);

            if (matchedRombels.length > 0) {
                matchedRombels.forEach((item, index) => {
                    const isSelected = index === 0 ? 'selected' : '';
                    filterRombel.innerHTML += `<option value="${item.nama_kelas}" ${isSelected}>${item.nama_kelas}</option>`;
                });
            } else {
                filterRombel.innerHTML = `<option value="">Tidak ada rombel</option>`;
            }
        }

        renderSiswaList();
    }

    function renderSiswaList() {
        const searchInput = document.getElementById('searchInput');
        const filterRombel = document.getElementById('filterRombel');
        const filterTingkat = document.getElementById('filterTingkat');
        const filterGender = document.getElementById('filterGender');
        const tbody = document.getElementById('listSiswaTbody');

        if (!searchInput || !filterRombel || !filterGender || !tbody) return;

        const searchValue = searchInput.value.toLowerCase().trim();
        const selectedRombel = filterRombel.value;
        const selectedTingkat = filterTingkat.value;
        const gender = filterGender.value;

        tbody.innerHTML = '';

        const filtered = siswaDatabase.filter(function (siswa) {
            let matchesGrade = false;

            if (selectedTingkat === 'Lulus') {
                matchesGrade = String(siswa.status).toLowerCase() === 'lulus' || String(siswa.kelas).toLowerCase().includes('lulus');
            } else {
                matchesGrade = String(siswa.kelas).trim().toLowerCase() === String(selectedRombel).trim().toLowerCase();
            }

            const matchesGender = gender === 'all' || siswa.gender === gender;

            const nama = String(siswa.nama || '').toLowerCase();
            const nis = String(siswa.nis || '').toLowerCase();
            const nisn = String(siswa.nisn || '').toLowerCase();

            const matchesSearch = nama.includes(searchValue) || nis.includes(searchValue) || nisn.includes(searchValue);

            return matchesGrade && matchesGender && matchesSearch;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-400 italic bg-gray-50/50">
                        <i class="fa-solid fa-user-slash text-gray-300 text-2xl mb-2 block"></i>
                        Tidak ada siswa yang ditemukan pada rombel/pencarian ini.
                    </td>
                </tr>
            `;
            const checkAll = document.getElementById('checkAll');
            if (checkAll) checkAll.checked = false;
            return;
        }

        filtered.forEach(function (siswa) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-blue-50/30 transition';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'cb-siswa rounded';
            checkbox.value = siswa.id;

            const tdCheck = document.createElement('td');
            tdCheck.className = 'p-3 text-center';
            tdCheck.appendChild(checkbox);

            const tdNis = document.createElement('td');
            tdNis.className = 'p-3 font-mono text-gray-500';
            tdNis.textContent = siswa.nis || siswa.nisn || '-';

            const tdNama = document.createElement('td');
            tdNama.className = 'p-3 font-semibold text-gray-800';
            tdNama.textContent = siswa.nama || '-';

            const tdKelas = document.createElement('td');
            tdKelas.className = 'p-3';
            const badge = document.createElement('span');
            badge.className = 'px-2.5 py-1 bg-gray-100 text-gray-700 font-bold rounded-lg text-xs';
            badge.textContent = siswa.kelas || '-';
            tdKelas.appendChild(badge);

            const tdGender = document.createElement('td');
            tdGender.className = 'p-3 text-gray-600 font-medium';
            tdGender.textContent = siswa.gender || '-';

            tr.appendChild(tdCheck);
            tr.appendChild(tdNis);
            tr.appendChild(tdNama);
            tr.appendChild(tdKelas);
            tr.appendChild(tdGender);

            tbody.appendChild(tr);
        });

        const checkAll = document.getElementById('checkAll');
        if (checkAll) checkAll.checked = false;
    }

    function selectAll(master) {
        document.querySelectorAll('.cb-siswa').forEach(function (checkbox) {
            checkbox.checked = master.checked;
        });
    }

    function simpanPemindahan() {
        const checkedBoxes = document.querySelectorAll('.cb-siswa:checked');
        if (checkedBoxes.length === 0) {
            alert('Silakan centang minimal satu siswa di tabel yang ingin dipindahkan!');
            return;
        }

        const targetClassSelect = document.getElementById('targetClass');
        const targetId = targetClassSelect ? targetClassSelect.value : '';

        if (!targetId) {
            alert('Silakan pilih rombel tujuan terlebih dahulu.');
            return;
        }

        const selectedOption = targetClassSelect.options[targetClassSelect.selectedIndex];
        const targetName = selectedOption ? selectedOption.text.trim() : '';

        if (!confirm(`Yakin ingin memindahkan ${checkedBoxes.length} siswa terpilih ke tujuan: ${targetName}?`)) {
            return;
        }

        const selectedStudents = document.getElementById('selectedStudents');
        selectedStudents.innerHTML = '';

        checkedBoxes.forEach(function (checkbox) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'siswa[]';
            input.value = checkbox.value;
            selectedStudents.appendChild(input);
        });

        document.getElementById('kelasTujuanInput').value = targetId;
        document.getElementById('resetPoinInput').value = document.getElementById('resetPoin').checked ? '1' : '0';

        document.getElementById('formPemindahan').submit();
    }
</script>
@endpush
