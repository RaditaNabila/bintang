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

<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-5">
    <div>
        <span class="inline-block px-2.5 py-1 bg-blue-100 text-blue-800 font-bold rounded-lg text-[10px] uppercase">
            Panel Kontrol Manual
        </span>
        <h3 class="text-base font-bold text-gray-800 mt-2 flex items-center gap-2">
            <i class="fa-solid fa-people-arrows text-blue-600"></i>
            Pilih & Pindahkan Siswa Rombel
        </h3>
        <!-- Highlighted Notice Box -->
        <div class="mt-3 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-xl shadow-sm flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg mt-0.5 animate-pulse"></i>
            <div class="text-xs text-amber-900 leading-relaxed">
                <span class="font-bold uppercase tracking-wider block mb-0.5 text-amber-950">⚠️ Perhatian Langkah Kerja (Wajib Dibaca):</span>
                Harap lakukan proses secara berurutan: Mulai dari <strong class="underline font-bold text-amber-950">Kelas 6</strong> terlebih dahulu untuk diluluskan/diarsipkan, lalu lanjutkan dari <strong class="underline font-bold text-amber-950">Kelas 5 ke Kelas 6</strong>, <strong class="underline font-bold text-amber-950">Kelas 4 ke Kelas 5</strong>, dan seterusnya agar data tidak bertumpuk!
            </div>
        </div>
    </div>

    <div class="space-y-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- KELAS ASAL --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    1. Pilih Tingkat Kelas Asal
                </label>
                <select id="filterTingkat" onchange="onTingkatChange()" class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-800 focus:outline-none focus:border-blue-500">
                    <option value="1">Kelas 1</option>
                    <option value="2">Kelas 2</option>
                    <option value="3">Kelas 3</option>
                    <option value="4">Kelas 4</option>
                    <option value="5">Kelas 5</option>
                    <option value="6" selected>Kelas 6</option>
                </select>
            </div>
            {{-- ROMBEL ASAL --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    2. Pilih Rombel / Kelas Spesifik
                </label>
                <select id="filterRombel" onchange="renderSiswaList()" class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-800 focus:outline-none focus:border-blue-500">
                </select>
            </div>
            {{-- GENDER --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    3. Filter Jenis Kelamin
                </label>
                <select id="filterGender" onchange="renderSiswaList()" class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                    <option value="all">Semua Siswa</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            {{-- KELAS TUJUAN --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    4. Rombel Tujuan
                </label>
                <select id="targetClass" class="w-full p-2.5 bg-white border border-blue-300 font-bold text-blue-700 rounded-lg text-xs focus:outline-none focus:border-blue-500">
                    <option value="">-- Pilih Rombel Tujuan --</option>
                </select>
            </div>
        </div>
        {{-- SEARCH --}}
        <div>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchInput" onkeyup="renderSiswaList()" placeholder="Cari berdasarkan nama atau NIS siswa..." class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500 transition">
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('guru.naik-kelas.pindahkan') }}" method="POST" id="formPemindahan">
        @csrf
        <input type="hidden" name="kelas_tujuan" id="kelasTujuanInput">
        <input type="hidden" name="reset_poin" id="resetPoinInput" value="1">
        <input type="hidden" name="aksi" id="aksiInput" value="pindah">
        <div id="selectedStudents"></div>

        {{-- TABEL --}}
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
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BAWAH --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-4">
            <label class="text-xs text-gray-600 flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="resetPoin" checked class="w-4 h-4 text-blue-600 rounded">
                <span>Reset poin siswa terpilih menjadi <strong>250 Poin</strong></span>
            </label>
            <button type="button" id="btnSubmit" onclick="simpanPemindahan()" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right"></i>
                <span id="btnText">Proses Pindahkan Siswa Terpilih</span>
            </button>
        </div>
    </form>
</div>

<!-- Modal Kustom Pengganti Alert/Confirm Bawaan Browser -->
<div id="customModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div id="customModalIcon" class="w-12 h-12 rounded-full flex items-center justify-center mx-auto text-xl"></div>
        <div>
            <h3 id="customModalTitle" class="font-bold text-base text-gray-800">Pemberitahuan</h3>
            <p id="customModalMessage" class="text-xs text-gray-500 mt-1 leading-relaxed"></p>
        </div>
        <div id="customModalButtons" class="flex items-center justify-center gap-2 pt-2"></div>
    </div>
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

$daftarKelasRombel = $kelas->map(function ($k) {
    preg_match('/\d+/', $k->nama_kelas, $matches);
    $tingkat = $matches[0] ?? '';
    return [
        'id_kelas' => $k->id,
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

function showModal(title, message, type = 'warning', callback = null) {
    const modal = document.getElementById('customModal');
    const iconContainer = document.getElementById('customModalIcon');
    const titleEl = document.getElementById('customModalTitle');
    const msgEl = document.getElementById('customModalMessage');
    const btnContainer = document.getElementById('customModalButtons');

    if (!modal) return;

    titleEl.textContent = title;
    msgEl.textContent = message;

    if (type === 'warning') {
        iconContainer.className = 'w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
    } else if (type === 'danger') {
        iconContainer.className = 'w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
    } else if (type === 'success') {
        iconContainer.className = 'w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
    } else {
        iconContainer.className = 'w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-circle-info"></i>';
    }

    if (callback) {
        btnContainer.innerHTML = `
            <button type="button" onclick="closeCustomModal()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <button type="button" id="customModalConfirmBtn" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Lanjutkan</button>
        `;
        document.getElementById('customModalConfirmBtn').onclick = function() {
            closeCustomModal();
            callback();
        };
    } else {
        btnContainer.innerHTML = `
            <button type="button" onclick="closeCustomModal()" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition">Mengerti</button>
        `;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCustomModal() {
    const modal = document.getElementById('customModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function onTingkatChange() {
    const filterTingkat = document.getElementById('filterTingkat');
    const filterRombel = document.getElementById('filterRombel');
    const targetClass = document.getElementById('targetClass');

    if (!filterTingkat || !filterRombel || !targetClass) return;

    const selectedTingkat = filterTingkat.value;
    filterRombel.innerHTML = '';
    targetClass.innerHTML = '';

    const matchedRombels = kelasRombelData.filter(item => String(item.tingkat) === String(selectedTingkat));

    if (matchedRombels.length > 0) {
        matchedRombels.forEach((item, index) => {
            const option = document.createElement('option');
            option.value = item.nama_kelas;
            option.textContent = item.nama_kelas;
            if (index === 0) option.selected = true;
            filterRombel.appendChild(option);
        });
    } else {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Tidak ada rombel';
        filterRombel.appendChild(option);
    }

    if (selectedTingkat === '6') {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Kelas 6 → Lulus / Arsip Alumni';
        option.selected = true;
        targetClass.appendChild(option);

        document.getElementById('btnText').textContent = 'Luluskan & Arsipkan Siswa Terpilih';
        document.getElementById('btnSubmit').className = 'w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2';

        renderSiswaList();
        return;
    }

    const tingkatTujuan = parseInt(selectedTingkat) + 1;
    const kelasTujuan = kelasRombelData.filter(item => parseInt(item.tingkat) === tingkatTujuan);

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = '-- Pilih Rombel Tujuan --';
    defaultOption.selected = true;
    defaultOption.disabled = true;
    targetClass.appendChild(defaultOption);

    kelasTujuan.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id_kelas;
        option.textContent = item.nama_kelas;
        targetClass.appendChild(option);
    });

    document.getElementById('btnText').textContent = 'Proses Pindahkan Siswa Terpilih';
    document.getElementById('btnSubmit').className = 'w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2';

    renderSiswaList();
}

function renderSiswaList() {
    const searchInput = document.getElementById('searchInput');
    const filterRombel = document.getElementById('filterRombel');
    const filterTingkat = document.getElementById('filterTingkat');
    const filterGender = document.getElementById('filterGender');
    const tbody = document.getElementById('listSiswaTbody');

    if (!searchInput || !filterRombel || !filterTingkat || !filterGender || !tbody) return;

    const searchValue = searchInput.value.toLowerCase().trim();
    const selectedRombel = filterRombel.value;
    const gender = filterGender.value;
    tbody.innerHTML = '';

    const filtered = siswaDatabase.filter(siswa => {
        const matchesGrade = String(siswa.kelas).trim().toLowerCase() === String(selectedRombel).trim().toLowerCase();
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
                    Tidak ada siswa yang ditemukan.
                </td>
            </tr>
        `;
        const checkAll = document.getElementById('checkAll');
        if (checkAll) checkAll.checked = false;
        return;
    }

    filtered.forEach(siswa => {
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
    document.querySelectorAll('.cb-siswa').forEach(checkbox => {
        checkbox.checked = master.checked;
    });
}

function simpanPemindahan() {
    const checkedBoxes = document.querySelectorAll('.cb-siswa:checked');

    if (checkedBoxes.length === 0) {
        showModal('Perhatian', 'Silakan centang minimal satu siswa terlebih dahulu.', 'warning');
        return;
    }

    const tingkat = document.getElementById('filterTingkat').value;
    const targetClass = document.getElementById('targetClass');

    if (tingkat === '6') {
        showModal(
            'Konfirmasi Kelulusan',
            `Yakin ingin meluluskan ${checkedBoxes.length} siswa terpilih dan memasukkannya ke Arsip Alumni?`,
            'danger',
            () => executeSubmitAction('lulus', '')
        );
        return;
    }

    const targetId = targetClass ? targetClass.value : '';
    if (!targetId) {
        showModal('Perhatian', 'Silakan pilih rombel tujuan terlebih dahulu.', 'warning');
        return;
    }

    const selectedOption = targetClass.options[targetClass.selectedIndex];
    const targetName = selectedOption ? selectedOption.text.trim() : '';

    showModal(
        'Konfirmasi Pemindahan',
        `Yakin ingin memindahkan ${checkedBoxes.length} siswa terpilih ke ${targetName}?`,
        'warning',
        () => executeSubmitAction('pindah', targetId)
    );
}

function executeSubmitAction(aksi, targetId) {
    const checkedBoxes = document.querySelectorAll('.cb-siswa:checked');
    const selectedStudents = document.getElementById('selectedStudents');
    selectedStudents.innerHTML = '';

    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'siswa[]';
        input.value = checkbox.value;
        selectedStudents.appendChild(input);
    });

    document.getElementById('aksiInput').value = aksi;
    document.getElementById('kelasTujuanInput').value = targetId;
    document.getElementById('resetPoinInput').value = document.getElementById('resetPoin').checked ? '1' : '0';

    document.getElementById('formPemindahan').submit();
}
</script>
@endpush
