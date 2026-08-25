@extends('layouts.app')

@section('title', 'Pelanggaran Siswa - Bintang Poin')
@section('page_title', 'Pencatatan Pelanggaran Siswa')
@section('page_description', 'Pantau dan evaluasi kedisiplinan serta ketertiban siswa')

@section('content')

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Total Poin --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">Poin Pelanggaran Bulan Ini</p>
                <h3 id="statTotalPoin" class="text-2xl font-bold text-rose-600 mt-1">-40</h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 text-lg">
                <i class="fa-solid fa-circle-minus"></i>
            </div>
        </div>

        {{-- Pelanggaran Terbanyak --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">Pelanggaran Terbanyak</p>
                <h3 class="text-base font-bold text-gray-800 mt-1">Keterlambatan</h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 text-lg">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        {{-- Total Kasus --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">Total Catatan Pelanggaran</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    <span id="statTotalKasus">3</span>
                    <span class="text-xs font-normal text-gray-400">Kasus</span>
                </h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>

    </div>


    {{-- SEARCH & ACTION BUTTON --}}
    <div class="mt-6 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
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

        <button
            onclick="openModal()"
            class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition"
        >
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Catat Pelanggaran</span>
        </button>
    </div>


    {{-- TABLE SECTION --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Riwayat Catatan Pelanggaran</h3>
                <p class="text-xs text-gray-500">Log pengurangan poin siswa pada periode berjalan</p>
            </div>

            <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
                <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>
                <select id="filterBulan" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer">
                    <option value="08-2026" selected>Agustus 2026 (Bulan Ini)</option>
                    <option value="07-2026">Juli 2026</option>
                    <option value="06-2026">Juni 2026</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="violationTable" class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Bentuk Pelanggaran</th>
                        <th class="py-3 px-4 text-center">Poin (-)</th>
                        <th class="py-3 px-4">Tindakan / Sanksi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="violationTableBody" class="divide-y divide-gray-50">

                    {{-- DATA 1 --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 text-xs text-gray-400">22 Aug 2026</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260105</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">Fikri Zulkarnain</td>
                        <td class="py-3.5 px-4 text-gray-500">3-B</td>
                        <td class="py-3.5 px-4 text-gray-600">Terlambat masuk sekolah (&gt;15 menit)</td>
                        <td class="py-3.5 px-4 font-bold text-rose-600 text-center">-5</td>
                        <td class="py-3.5 px-4 text-xs text-gray-500">Teguran lisan &amp; piket kebersihan</td>
                        <td class="py-3.5 px-4 text-center">
                            <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- DATA 2 --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 text-xs text-gray-400">21 Aug 2026</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260108</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">Bagas Pratama</td>
                        <td class="py-3.5 px-4 text-gray-500">5-A</td>
                        <td class="py-3.5 px-4 text-gray-600">Tidak mengerjakan tugas &amp; seragam tidak lengkap</td>
                        <td class="py-3.5 px-4 font-bold text-rose-600 text-center">-10</td>
                        <td class="py-3.5 px-4 text-xs text-gray-500">Catatan buku penghubung ortu</td>
                        <td class="py-3.5 px-4 text-center">
                            <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- DATA 3 --}}
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 text-xs text-gray-400">19 Aug 2026</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">20260112</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">Davin Rizky</td>
                        <td class="py-3.5 px-4 text-gray-500">6-B</td>
                        <td class="py-3.5 px-4 text-gray-600">Meninggalkan area sekolah tanpa izin</td>
                        <td class="py-3.5 px-4 font-bold text-rose-600 text-center">-25</td>
                        <td class="py-3.5 px-4 text-xs text-gray-500">Pemanggilan Orang Tua / Wali</td>
                        <td class="py-3.5 px-4 text-center">
                            <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>


    {{-- MODAL CATAT PELANGGARAN --}}
    <div id="violationModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">

            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-6 py-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-base">Catat Pelanggaran Siswa</h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="violationForm" onsubmit="saveViolation(event)" class="p-6 space-y-4">

                {{-- SISWA --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Siswa</label>
                    <select id="selectSiswa" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500">
                        <option value="20260105|Fikri Zulkarnain|3-B">20260105 - Fikri Zulkarnain (3-B)</option>
                        <option value="20260108|Bagas Pratama|5-A">20260108 - Bagas Pratama (5-A)</option>
                        <option value="20260112|Davin Rizky|6-B">20260112 - Davin Rizky (6-B)</option>
                    </select>
                </div>

                {{-- KATEGORI --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-gray-700">Pilih Jenis / Kategori Pelanggaran</label>
                        <button type="button" onclick="openCategoryModal()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px]"></i> Kategori Baru
                        </button>
                    </div>
                    <select id="selectKategori" onchange="autoFillViolationData()" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500 bg-amber-50/50">
                        <option value="" disabled selected>-- Pilih Jenis Pelanggaran --</option>
                    </select>
                </div>

                {{-- POIN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Poin Pengurangan (-)</label>
                    <input type="number" id="inputPoin" required min="1" max="100" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-rose-600 focus:outline-none focus:border-rose-500">
                </div>

                {{-- KETERANGAN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bentuk / Rincian Pelanggaran</label>
                    <textarea id="inputKeterangan" required rows="2" placeholder="Detail keterlambatan atau tindakan siswa..." class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500"></textarea>
                </div>

                {{-- SANKSI --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tindakan Pembinaan / Sanksi</label>
                    <input type="text" id="inputSanksi" required placeholder="Contoh: Nasihat &amp; pengerjaan tugas tambahan" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-rose-500">
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-medium hover:bg-rose-600 transition">
                        Simpan Pelanggaran
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- MODAL KATEGORI BARU --}}
    <div id="categoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">

            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-base">Tambah Kategori Pelanggaran Baru</h3>
                <button onclick="closeCategoryModal()" class="text-white/80 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="categoryForm" onsubmit="saveNewCategory(event)" class="p-6 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jenis Pelanggaran</label>
                    <input type="text" id="newCatName" required placeholder="Contoh: Bermain Game Saat Jam Belajar" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Default Poin (-)</label>
                    <input type="number" id="newCatPoin" value="5" required min="1" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Default Sanksi / Tindakan</label>
                    <input type="text" id="newCatSanksi" required placeholder="Contoh: Penyitaan barang &amp; pembinaan" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">
                        Simpan Kategori
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- JAVASCRIPT --}}
    <script>
        // MASTER KATEGORI
        let masterCategories = [
            {
                name: "Terlambat Masuk Sekolah (>15 Menit)",
                poin: 5,
                sanksi: "Teguran lisan & piket kebersihan"
            },
            {
                name: "Tidak Mengerjakan Tugas & Seragam Tidak Lengkap",
                poin: 10,
                sanksi: "Catatan buku penghubung ortu"
            },
            {
                name: "Meninggalkan Area Sekolah Tanpa Izin",
                poin: 25,
                sanksi: "Pemanggilan Orang Tua / Wali"
            },
            {
                name: "Membuat Kegaduhan / Mengganggu Kelas",
                poin: 5,
                sanksi: "Teguran lisan & nasihat wali kelas"
            }
        ];

        // RENDER KATEGORI
        function renderCategoryDropdown() {
            const select = document.getElementById('selectKategori');
            select.innerHTML = '<option value="" disabled selected>-- Pilih Jenis Pelanggaran --</option>';

            masterCategories.forEach((cat, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = cat.name;
                select.appendChild(option);
            });
        }

        // AUTO FILL DATA KATEGORI
        function autoFillViolationData() {
            const index = document.getElementById('selectKategori').value;
            if (index === "") return;

            const selectedCategory = masterCategories[index];
            document.getElementById('inputPoin').value = selectedCategory.poin;
            document.getElementById('inputKeterangan').value = selectedCategory.name;
            document.getElementById('inputSanksi').value = selectedCategory.sanksi;
        }

        // MODAL PELANGGARAN CONTROLLER
        function openModal() {
            renderCategoryDropdown();
            const modal = document.getElementById('violationModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('violationModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('violationForm').reset();
        }

        // MODAL KATEGORI CONTROLLER
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
        }

        // SIMPAN KATEGORI BARU
        function saveNewCategory(e) {
            e.preventDefault();

            const newCat = {
                name: document.getElementById('newCatName').value,
                poin: parseInt(document.getElementById('newCatPoin').value),
                sanksi: document.getElementById('newCatSanksi').value
            };

            masterCategories.push(newCat);
            renderCategoryDropdown();

            document.getElementById('selectKategori').value = masterCategories.length - 1;
            autoFillViolationData();
            closeCategoryModal();
        }

        // FILTER SEARCH
        function searchViolations() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#violationTableBody tr');

            rows.forEach(row => {
                const nis = row.cells[1].textContent.toLowerCase();
                const nama = row.cells[2].textContent.toLowerCase();
                const bentuk = row.cells[4].textContent.toLowerCase();

                if (nis.includes(search) || nama.includes(search) || bentuk.includes(search)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // SIMPAN PELANGGARAN BARU
        function saveViolation(e) {
            e.preventDefault();

            const siswaData = document.getElementById('selectSiswa').value.split('|');
            const nis = siswaData[0];
            const nama = siswaData[1];
            const kelas = siswaData[2];

            const poin = document.getElementById('inputPoin').value;
            const keterangan = document.getElementById('inputKeterangan').value;
            const sanksi = document.getElementById('inputSanksi').value;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50';

            const today = new Date().toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

            tr.innerHTML = `
                <td class="py-3.5 px-4 text-xs text-gray-400">${today}</td>
                <td class="py-3.5 px-4 font-mono text-xs text-gray-500">${nis}</td>
                <td class="py-3.5 px-4 font-semibold text-gray-700">${nama}</td>
                <td class="py-3.5 px-4 text-gray-500">${kelas}</td>
                <td class="py-3.5 px-4 text-gray-600">${keterangan}</td>
                <td class="py-3.5 px-4 font-bold text-rose-600 text-center">-${poin}</td>
                <td class="py-3.5 px-4 text-xs text-gray-500">${sanksi}</td>
                <td class="py-3.5 px-4 text-center">
                    <button onclick="deleteRecord(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center transition" title="Hapus">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </td>
            `;

            document.getElementById('violationTableBody').prepend(tr);
            closeModal();
            updateStats();
        }

        // HAPUS RECORD
        function deleteRecord(btn) {
            if (confirm('Hapus catatan pelanggaran ini?')) {
                btn.closest('tr').remove();
                updateStats();
            }
        }

        // UPDATE STATISTIK AUTOMATIS
        function updateStats() {
            const rows = document.querySelectorAll('#violationTableBody tr');
            document.getElementById('statTotalKasus').textContent = rows.length;

            let totalPoin = 0;
            rows.forEach(row => {
                const poinText = row.cells[5].textContent.replace('-', '').trim();
                totalPoin += parseInt(poinText) || 0;
            });

            document.getElementById('statTotalPoin').textContent = `-${totalPoin}`;
        }

        // INITIALIZATION
        renderCategoryDropdown();
    </script>

@endsection
