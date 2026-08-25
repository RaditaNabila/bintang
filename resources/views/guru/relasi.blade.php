@extends('layouts.app')

@section('title', 'Relasi Wali-Siswa - Bintang Poin')
@section('page_title', 'Relasi Wali-Siswa')
@section('page_description', 'Hubungkan akun orang tua dengan data siswa untuk akses portal wali')

@section('content')
<div class="space-y-6">

    <!-- Form Tambah Hubungan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm">
                <i class="fa-solid fa-link"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-base leading-tight">Hubungkan Wali Murid & Siswa</h3>
                <p class="text-xs text-gray-400">Pilih akun wali dan data siswa yang ingin ditautkan</p>
            </div>
        </div>

        <form onsubmit="addRelation(event)" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Pilih Wali -->
                <div>
                    <label for="selectWali" class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Akun Wali Murid</label>
                    <select id="selectWali" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-gray-50/50">
                        <option value="">-- Pilih Akun Wali --</option>
                        <option value="Bpk. Hendra Zulkarnain">Bpk. Hendra Zulkarnain (wali.hendra)</option>
                        <option value="Ibu Sarah Humaira">Ibu Sarah Humaira (wali.sarah)</option>
                        <option value="Bpk. Rahmat">Bpk. Rahmat (wali.rahmat)</option>
                    </select>
                </div>

                <!-- Pilih Siswa -->
                <div>
                    <label for="selectSiswa" class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Siswa (Anak)</label>
                    <select id="selectSiswa" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-gray-50/50">
                        <option value="">-- Pilih Siswa --</option>
                        <option value="Fikri Zulkarnain (Kelas 3-B)">Fikri Zulkarnain - Kelas 3-B (20260105)</option>
                        <option value="Aisyah Humaira (Kelas 1-A)">Aisyah Humaira - Kelas 1-A (20260119)</option>
                        <option value="Muhammad Rizky (Kelas 3-B)">Muhammad Rizky - Kelas 3-B (20260108)</option>
                    </select>
                </div>
            </div>

            <!-- Footer Form -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-2">
                <p class="text-xs text-gray-400">*Satu akun wali dapat dihubungkan ke beberapa siswa jika memiliki lebih dari 1 anak.</p>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-plus"></i>
                    Simpan Relasi
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Relasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <!-- Header Tabel -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Daftar Relasi Wali & Siswa Aktif</h3>
                <p class="text-xs text-gray-400">Menampilkan relasi akun yang terhubung di sistem</p>
            </div>

            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text" id="relationSearch" onkeyup="filterRelations()" placeholder="Cari nama wali atau siswa..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="relationTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Nama Wali Murid</th>
                        <th class="py-3 px-4">Siswa Terhubung (Anak)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="relationTableBody">
                    <!-- Relasi 1 -->
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 font-semibold text-gray-700">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 font-bold text-xs flex items-center justify-center shrink-0">H</div>
                                <span>Bpk. Hendra Zulkarnain</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-lg text-xs font-medium flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-graduate text-[10px]"></i>
                                    Fikri Zulkarnain (3-B)
                                </span>
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-lg text-xs font-medium flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-graduate text-[10px]"></i>
                                    Aisyah Humaira (1-A)
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">2 Anak</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button onclick="removeRelation(this)" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Putuskan Hubungan">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Relasi 2 -->
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 font-semibold text-gray-700">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold text-xs flex items-center justify-center shrink-0">R</div>
                                <span>Bpk. Rahmat</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-lg text-xs font-medium flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-graduate text-[10px]"></i>
                                    Muhammad Rizky (3-B)
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">1 Anak</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button onclick="removeRelation(this)" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Putuskan Hubungan">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function addRelation(event) {
        event.preventDefault();

        const wali = document.getElementById('selectWali').value;
        const siswa = document.getElementById('selectSiswa').value;

        if (!wali || !siswa) return;

        const tbody = document.getElementById('relationTableBody');
        const newRow = document.createElement('tr');
        newRow.className = "hover:bg-gray-50/50";

        newRow.innerHTML = `
            <td class="py-3.5 px-4 font-semibold text-gray-700">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 font-bold text-xs flex items-center justify-center shrink-0">
                        ${wali.charAt(0)}
                    </div>
                    <span>${wali}</span>
                </div>
            </td>
            <td class="py-3.5 px-4">
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-lg text-xs font-medium flex items-center gap-1.5">
                        <i class="fa-solid fa-user-graduate text-[10px]"></i>
                        ${siswa}
                    </span>
                </div>
            </td>
            <td class="py-3.5 px-4">
                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">1 Anak</span>
            </td>
            <td class="py-3.5 px-4 text-center">
                <button onclick="removeRelation(this)" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Putuskan Hubungan">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;

        tbody.prepend(newRow);
        alert('Relasi Wali dan Siswa berhasil disimpan!');

        document.getElementById('selectWali').value = '';
        document.getElementById('selectSiswa').value = '';
    }

    function removeRelation(button) {
        if (confirm('Apakah Anda yakin ingin menghapus relasi ini?')) {
            button.closest('tr').remove();
        }
    }

    function filterRelations() {
        const input = document.getElementById('relationSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#relationTableBody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }
</script>
@endpush
