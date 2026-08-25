@extends('layouts.app')

@section('title', 'Proses Kenaikan Kelas - Bintang Poin')
@section('page_title', 'Manajemen Kenaikan Kelas')
@section('page_description', 'Kelola perpindahan siswa untuk Tahun Ajaran Baru')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
    <a href="{{ route('guru.data-siswa') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali ke Data Siswa
    </a>
</div>

<div id="successAlert" class="hidden mb-6 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl items-center gap-3">
    <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
    <div>
        <p id="alertTitle" class="font-bold text-xs">Proses Berhasil!</p>
        <p id="alertMessage" class="text-xs">Kenaikan kelas telah berhasil dijalankan.</p>
    </div>
</div>

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
                Sistem memproses: <strong>Kelas 1 → 2</strong>, <strong>2 → 3</strong>, <strong>3 → 4</strong>, <strong>5 → 6</strong>, dan <strong>6 → Lulus</strong>.
                <br>
                <span class="text-amber-700 font-semibold">*Kelas 4 ditahan di Kelas 4 untuk pemindahan khusus ke Kelas 5 pada Langkah 2.</span>
            </p>
        </div>

        <button onclick="prosesAutoNaik()" class="shrink-0 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-play"></i>
            Jalankan Naik Kelas Otomatis
        </button>
    </div>
</div>

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
            Cari nama siswa atau gunakan filter di bawah ini untuk memindahkan siswa ke rombel tujuan.
        </p>
    </div>

    <div class="space-y-4 bg-amber-50/60 p-4 rounded-xl border border-amber-200">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Nama / NISN Siswa</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchInput" onkeyup="renderSiswaList()" placeholder="Ketik nama atau NISN siswa..." class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-lg text-xs font-medium focus:outline-none focus:border-amber-500 transition">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">1. Tampilkan Siswa Kelas</label>
                <select id="filterFromGrade" onchange="renderSiswaList()" class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-amber-900">
                    <option value="1">Siswa Kelas 1</option>
                    <option value="2">Siswa Kelas 2</option>
                    <option value="3">Siswa Kelas 3</option>
                    <option value="4" selected>Siswa Kelas 4 (Siap Masuk Kelas 5)</option>
                    <option value="5">Siswa Kelas 5</option>
                    <option value="6">Siswa Kelas 6</option>
                    <option value="Lulus">Siswa Lulus (Alumni)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">2. Filter Jenis Kelamin</label>
                <select id="filterGender" onchange="renderSiswaList()" class="w-full p-2.5 bg-white border border-gray-200 rounded-lg text-xs font-medium">
                    <option value="all">Semua Siswa</option>
                    <option value="Laki-laki">Khusus Laki-laki (Putra)</option>
                    <option value="Perempuan">Khusus Perempuan (Putri)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">3. Pindahkan Terpilih Ke Rombel</label>
                <select id="targetClass" class="w-full p-2.5 bg-white border border-orange-300 font-bold text-orange-700 rounded-lg text-xs">
                    <option value="5-A">Kelas 5-A</option>
                    <option value="5-B">Kelas 5-B</option>
                    <option value="5-C">Kelas 5-C</option>
                    <option value="6-A">Kelas 6-A</option>
                    <option value="6-B">Kelas 6-B</option>
                </select>
            </div>
        </div>
    </div>

    <div class="border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-xs border-collapse">
                <thead class="bg-gray-100 border-b text-gray-600 font-bold">
                    <tr>
                        <th class="p-3 w-10 text-center">
                            <input type="checkbox" id="checkAll" onclick="selectAll(this)" class="rounded">
                        </th>
                        <th class="p-3">NISN</th>
                        <th class="p-3">Nama Lengkap</th>
                        <th class="p-3">Kelas Saat Ini</th>
                        <th class="p-3">Gender</th>
                    </tr>
                </thead>
                <tbody id="listSiswaTbody" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-2">
        <label class="text-xs text-gray-600 flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="resetPoin" checked class="w-4 h-4 text-orange-600 rounded">
            <span>Reset Poin Siswa Terpilih kembali ke <strong>250 Poin</strong></span>
        </label>

        <button onclick="simpanPemindahan()" class="w-full sm:w-auto px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-check"></i>
            Simpan Pemindahan Rombel
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const initialData = [
        { nisn: '0012345601', nama: 'Ahmad Ibrahim', kelas: '1-A', tingkat: '1', gender: 'Laki-laki' },
        { nisn: '0012345602', nama: 'Aisyah Humaira', kelas: '1-A', tingkat: '1', gender: 'Perempuan' },
        { nisn: '0012345603', nama: 'Bilal Bin Rabah', kelas: '2-A', tingkat: '2', gender: 'Laki-laki' },
        { nisn: '0012345604', nama: 'Khadijah Al-Kubra', kelas: '3-A', tingkat: '3', gender: 'Perempuan' },
        { nisn: '0012345605', nama: 'Siti Maryam', kelas: '4-A', tingkat: '4', gender: 'Perempuan' },
        { nisn: '0012345606', nama: 'Muhammad Hasan', kelas: '4-A', tingkat: '4', gender: 'Laki-laki' },
        { nisn: '0012345607', nama: 'Fatimah Az-Zahra', kelas: '4-B', tingkat: '4', gender: 'Perempuan' },
        { nisn: '0012345608', nama: 'Umar Bin Khattab', kelas: '4-B', tingkat: '4', gender: 'Laki-laki' },
        { nisn: '0012345609', nama: 'Ali Bin Abi Thalib', kelas: '5-A', tingkat: '5', gender: 'Laki-laki' },
        { nisn: '0012345610', nama: 'Zainab Binti Ali', kelas: '5-B', tingkat: '5', gender: 'Perempuan' },
        { nisn: '0012345611', nama: 'Usman Bin Affan', kelas: '6-A', tingkat: '6', gender: 'Laki-laki' }
    ];

    let dummySiswa = JSON.parse(JSON.stringify(initialData));

    document.addEventListener('DOMContentLoaded', function () {
        renderSiswaList();
    });

    function resetDataUjiCoba() {
        dummySiswa = JSON.parse(JSON.stringify(initialData));
        document.getElementById('searchInput').value = '';
        document.getElementById('filterFromGrade').value = '4';
        document.getElementById('filterGender').value = 'all';
        document.getElementById('successAlert').classList.add('hidden');
        renderSiswaList();
        alert('Data simulasi berhasil di-reset!');
    }

    function showNotification(title, message) {
        const alertBox = document.getElementById('successAlert');
        document.getElementById('alertTitle').textContent = title;
        document.getElementById('alertMessage').textContent = message;
        alertBox.classList.remove('hidden');
        alertBox.classList.add('flex');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prosesAutoNaik() {
        if (confirm('Jalankan Naik Kelas Otomatis?\n\nKelas 1-3 naik sejajar, Kelas 5 naik ke 6, Kelas 6 menjadi Lulus.\n\nKelas 4 TIDAK disentuh.')) {
            dummySiswa.forEach(function (siswa) {
                let t = parseInt(siswa.tingkat);
                if (t === 6) {
                    siswa.tingkat = 'Lulus';
                    siswa.kelas = 'Alumni';
                } else if (t === 5) {
                    let suffix = siswa.kelas.split('-')[1] || 'A';
                    siswa.tingkat = '6';
                    siswa.kelas = `6-${suffix}`;
                } else if (t >= 1 && t <= 3) {
                    let nextTingkat = t + 1;
                    let suffix = siswa.kelas.split('-')[1] || 'A';
                    siswa.tingkat = nextTingkat.toString();
                    siswa.kelas = `${nextTingkat}-${suffix}`;
                }
            });

            showNotification('Proses Otomatis Selesai!', 'Kelas 1, 2, 3, dan 5 telah dinaikkan. Silakan atur pemindahan siswa Kelas 4 pada Langkah 2.');
            renderSiswaList();
        }
    }

    function renderSiswaList() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
        const fromGrade = document.getElementById('filterFromGrade').value;
        const gender = document.getElementById('filterGender').value;
        const tbody = document.getElementById('listSiswaTbody');

        tbody.innerHTML = '';

        const filtered = dummySiswa.filter(function (siswa) {
            const matchesGrade = siswa.tingkat === fromGrade;
            const matchesGender = gender === 'all' || siswa.gender === gender;
            const matchesSearch = siswa.nama.toLowerCase().includes(searchValue) || siswa.nisn.includes(searchValue);
            return matchesGrade && matchesGender && matchesSearch;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-400 italic bg-gray-50/50">
                        Siswa tidak ditemukan pada pencarian/filter ini.
                    </td>
                </tr>
            `;
            return;
        }

        filtered.forEach(function (siswa) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-amber-50/40 transition';
            const badgeClass = siswa.kelas === 'Alumni' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800';

            tr.innerHTML = `
                <td class="p-3 text-center">
                    <input type="checkbox" class="cb-siswa rounded" data-nisn="${siswa.nisn}">
                </td>
                <td class="p-3 font-mono text-gray-500">${siswa.nisn}</td>
                <td class="p-3 font-semibold text-gray-800">${siswa.nama}</td>
                <td class="p-3">
                    <span class="px-2.5 py-1 ${badgeClass} font-bold rounded-lg text-xs">${siswa.kelas}</span>
                </td>
                <td class="p-3 text-gray-600 font-medium">${siswa.gender}</td>
            `;

            tbody.appendChild(tr);
        });

        document.getElementById('checkAll').checked = false;
    }

    function selectAll(master) {
        document.querySelectorAll('.cb-siswa').forEach(function (checkbox) {
            checkbox.checked = master.checked;
        });
    }

    function simpanPemindahan() {
        const checkedBoxes = document.querySelectorAll('.cb-siswa:checked');

        if (checkedBoxes.length === 0) {
            alert('Centang siswa di tabel yang ingin dipindahkan!');
            return;
        }

        const targetClass = document.getElementById('targetClass').value;
        const targetTingkat = targetClass.split('-')[0];

        if (confirm(`Pindahkan ${checkedBoxes.length} siswa terpilih ke Kelas ${targetClass}?`)) {
            checkedBoxes.forEach(function (checkbox) {
                const nisn = checkbox.getAttribute('data-nisn');
                const siswaObj = dummySiswa.find(function (siswa) {
                    return siswa.nisn === nisn;
                });

                if (siswaObj) {
                    siswaObj.kelas = targetClass;
                    siswaObj.tingkat = targetTingkat;
                }
            });

            showNotification('Pemindahan Rombel Berhasil!', `Sebanyak ${checkedBoxes.length} siswa telah dipindahkan ke Kelas ${targetClass}.`);
            renderSiswaList();
        }
    }
</script>
@endpush
