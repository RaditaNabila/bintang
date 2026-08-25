@extends('layouts.app')

@section('title', 'Poin Prestasi - Bintang Poin')
@section('page_title', 'Apresiasi & Prestasi Siswa')
@section('page_description', 'Catat kebaikan, kedisiplinan, dan capaian siswa')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-gradient-to-br from-amber-500 via-amber-400 to-orange-400 p-5 rounded-2xl text-white shadow-md relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl text-white pointer-events-none">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="bg-white/20 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full border border-white/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-crown text-yellow-200"></i>
                        Rank 1 (Tertinggi)
                    </span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold tracking-tight">275</span>
                        <span class="text-xs text-amber-100 font-medium block -mt-1">Poin</span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-lg font-bold leading-snug">Fatimah Az-Zahra</h3>
                    <p class="text-xs text-amber-100 font-medium">Kelas 6-A &bull; NIS: 20260103</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20">
                <p class="text-xs leading-relaxed text-amber-50">
                    <i class="fa-solid fa-quote-left text-amber-200/60 mr-1"></i>
                    Konsisten meraih juara lomba Tahfidz & paling aktif memimpin kebaikan kelas.
                </p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-slate-100 to-slate-200/60 p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="bg-white/80 backdrop-blur-sm text-slate-700 text-[11px] font-bold px-3 py-1 rounded-full border border-slate-300 flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-medal text-slate-400"></i>
                        Rank 2
                    </span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold text-slate-800 tracking-tight">260</span>
                        <span class="text-xs text-slate-500 font-medium block -mt-1">Poin</span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-lg font-bold text-slate-800 leading-snug">Muhammad Raihan</h3>
                    <p class="text-xs text-slate-500 font-medium">Kelas 4-A &bull; NIS: 20260101</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-300/50">
                <p class="text-xs leading-relaxed text-slate-600">
                    <i class="fa-solid fa-quote-left text-slate-400 mr-1"></i>
                    Teladan dalam ketepatan waktu ibadah berjamaah dan aktif memimpin shalat.
                </p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-amber-100/60 p-5 rounded-2xl border border-amber-200/70 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="bg-white/80 backdrop-blur-sm text-amber-900 text-[11px] font-bold px-3 py-1 rounded-full border border-amber-300 flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-award text-amber-700"></i>
                        Rank 3
                    </span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold text-amber-950 tracking-tight">255</span>
                        <span class="text-xs text-amber-700/70 font-medium block -mt-1">Poin</span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-lg font-bold text-amber-950 leading-snug">Aisyah Azzahra</h3>
                    <p class="text-xs text-amber-700/80 font-medium">Kelas 5-B &bull; NIS: 20260102</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-amber-200/60">
                <p class="text-xs leading-relaxed text-amber-900/80">
                    <i class="fa-solid fa-quote-left text-amber-400 mr-1"></i>
                    Sangat sigap menolong sesama teman dan memiliki kedisiplinan sosial tinggi.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" onkeyup="searchPoints()" placeholder="Cari NIS, nama, atau keterangan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
        </div>
        <button onclick="openModal('add')" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Catat Poin Prestasi</span>
        </button>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-800">Riwayat Catatan Prestasi</h3>
            <p class="text-xs text-gray-500">Log penambahan poin siswa pada periode yang dipilih</p>
        </div>
        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
            <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>
            <select id="filterBulan" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer">
                <option value="08-2026" selected>Agustus 2026</option>
                <option value="07-2026">Juli 2026</option>
                <option value="06-2026">Juni 2026</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="achievementTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Keterangan Prestasi</th>
                        <th class="py-3 px-4 text-center">Poin (+)</th>
                        <th class="py-3 px-4 text-center">Total Poin</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 text-xs text-gray-400">22 Aug 2026</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260101</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">Muhammad Raihan</td>
                        <td class="py-3.5 px-4 text-gray-500">4-A</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">Ibadah & Akhlak</span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-600">Menjadi imam shalat Dzuhur berjamaah</td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">+10</td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">260</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('edit')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 text-xs text-gray-400">22 Aug 2026</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260102</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">Aisyah Azzahra</td>
                        <td class="py-3.5 px-4 text-gray-500">5-B</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-medium">Kedisiplinan</span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-600">Membantu membersihkan dan merapikan perpustakaan</td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">+5</td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">255</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('edit')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 text-xs text-gray-400">21 Aug 2026</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260103</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">Fatimah Az-Zahra</td>
                        <td class="py-3.5 px-4 text-gray-500">6-A</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-medium">Lomba / Ekskul</span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-600">Juara 1 Lomba MHQ Tingkat Kecamatan</td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">+25</td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">275</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('edit')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="achievementModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-base">Catat Poin Prestasi</h3>
            <button onclick="closeModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="achievementForm" onsubmit="saveAchievement(event)" class="p-6 space-y-4">
            <div>
                <label for="selectSiswa" class="block text-xs font-semibold text-gray-700 mb-1">Pilih Siswa</label>
                <select id="selectSiswa" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                    <option value="20260101">20260101 - Muhammad Raihan (4-A)</option>
                    <option value="20260102">20260102 - Aisyah Azzahra (5-B)</option>
                    <option value="20260103">20260103 - Fatimah Az-Zahra (6-A)</option>
                </select>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="selectKategori" class="block text-xs font-semibold text-gray-700">Kategori Prestasi</label>
                    <button type="button" onclick="openAddCategoryModal()" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Kategori Baru</span>
                    </button>
                </div>
                <select id="selectKategori" onchange="updatePointFromCategory()" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></select>
            </div>
            <div>
                <label for="inputKeterangan" class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Prestasi / Apresiasi</label>
                <textarea id="inputKeterangan" required rows="3" placeholder="Contoh: Menjawab pertanyaan di depan kelas dengan sangat baik" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></textarea>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="inputPoin" class="block text-xs font-semibold text-gray-700">Jumlah Poin (+)</label>
                    <span class="text-[10px] text-amber-600 font-medium">*Otomatis berdasarkan kategori</span>
                </div>
                <input type="number" id="inputPoin" required min="1" max="100" placeholder="5" class="w-full px-3.5 py-2 border border-amber-300 bg-amber-50/50 font-bold text-amber-900 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">Simpan Poin</button>
            </div>
        </form>
    </div>
</div>

<div id="addCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-amber-500 px-5 py-3.5 text-white flex justify-between items-center">
            <h4 class="font-bold text-sm">Tambah Kategori Prestasi</h4>
            <button onclick="closeAddCategoryModal()" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form onsubmit="saveNewCategory(event)" class="p-5 space-y-3">
            <div>
                <label for="newCategoryName" class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori Prestasi</label>
                <input type="text" id="newCategoryName" required placeholder="Misal: Aktif Menjawab di Kelas" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label for="newCategoryPoints" class="block text-xs font-semibold text-gray-700 mb-1">Default Poin (+)</label>
                <input type="number" id="newCategoryPoints" required min="1" max="100" placeholder="5" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddCategoryModal()" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-3.5 py-1.5 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">Tambah</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let categoriesList = [
        { name: 'Aktif Maju & Menjawab Pertanyaan', points: 5 },
        { name: 'Ibadah & Akhlak', points: 10 },
        { name: 'Kedisiplinan & Kebersihan', points: 5 },
        { name: 'Prestasi Akademik / Hafalan', points: 15 },
        { name: 'Menang Lomba / Ekstrakulikuler', points: 25 }
    ];

    document.addEventListener('DOMContentLoaded', function () {
        renderCategoryOptions();
    });

    function renderCategoryOptions(selectedName = '') {
        const select = document.getElementById('selectKategori');
        if (!select) return;

        select.innerHTML = '';
        categoriesList.forEach(function (cat) {
            const option = document.createElement('option');
            option.value = cat.name;
            option.textContent = `${cat.name} (+${cat.points} Poin)`;
            option.dataset.points = cat.points;

            if (selectedName && cat.name === selectedName) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        updatePointFromCategory();
    }

    function updatePointFromCategory() {
        const select = document.getElementById('selectKategori');
        const inputPoin = document.getElementById('inputPoin');
        if (!select || !inputPoin) return;

        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption) {
            inputPoin.value = selectedOption.dataset.points;
        }
    }

    function openModal(mode) {
        const modal = document.getElementById('achievementModal');
        const title = document.getElementById('modalTitle');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        title.textContent = mode === 'edit' ? 'Edit Poin Prestasi' : 'Catat Poin Prestasi';
        updatePointFromCategory();
    }

    function closeModal() {
        const modal = document.getElementById('achievementModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openAddCategoryModal() {
        const modal = document.getElementById('addCategoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAddCategoryModal() {
        const modal = document.getElementById('addCategoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('newCategoryName').value = '';
        document.getElementById('newCategoryPoints').value = '';
    }

    function saveNewCategory(event) {
        event.preventDefault();
        const name = document.getElementById('newCategoryName').value.trim();
        const points = parseInt(document.getElementById('newCategoryPoints').value);

        if (!name || isNaN(points)) return;

        categoriesList.push({ name: name, points: points });
        renderCategoryOptions(name);
        closeAddCategoryModal();
        alert(`Kategori "${name}" (+${points} Poin) berhasil ditambahkan!`);
    }

    function searchPoints() {
        const input = document.getElementById('searchInput');
        const search = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#achievementTable tbody tr');

        rows.forEach(function (row) {
            const nis = row.cells[1].textContent.toLowerCase();
            const nama = row.cells[2].textContent.toLowerCase();
            const keterangan = row.cells[5].textContent.toLowerCase();

            if (nis.includes(search) || nama.includes(search) || keterangan.includes(search)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function saveAchievement(event) {
        event.preventDefault();
        const siswa = document.getElementById('selectSiswa').value;
        const kategori = document.getElementById('selectKategori').value;
        const keterangan = document.getElementById('inputKeterangan').value;
        const poin = document.getElementById('inputPoin').value;

        if (!siswa || !kategori || !keterangan || !poin) {
            alert('Silakan lengkapi data terlebih dahulu.');
            return;
        }

        alert('Poin prestasi berhasil disimpan!');
        closeModal();
    }

    function deleteRecord(button) {
        if (confirm('Apakah Anda yakin ingin menghapus catatan poin prestasi ini?')) {
            const row = button.closest('tr');
            if (row) row.remove();
        }
    }
</script>
@endpush
