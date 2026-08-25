@extends('layouts.app')

@section('title', 'Kelola Akun - Bintang Poin')
@section('page_title', 'Kelola Akun Pengguna')
@section('page_description', 'Manajemen hak akses guru dan orangtua/wali')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-amber-100 shadow-sm">
    <div class="relative w-full sm:w-80">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari nama atau username..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
    </div>

    <button onclick="openModal('add')" class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
        <i class="fa-solid fa-plus text-sm"></i>
        Tambah Pengguna Baru
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm" id="accountTable">
            <thead>
                <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                    <th class="py-3 px-4">Pengguna</th>
                    <th class="py-3 px-4">Username</th>
                    <th class="py-3 px-4">Role / Peran</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <tr class="hover:bg-gray-50/50">
                    <td class="py-3.5 px-4 font-semibold text-gray-700">Ustadz Ahmad</td>
                    <td class="py-3.5 px-4 text-gray-500">ahmad_nf</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Guru</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-medium border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openModal('edit', 'Ustadz Ahmad', 'ahmad_nf', 'Guru')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button onclick="deleteAccount(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50/50">
                    <td class="py-3.5 px-4 font-semibold text-gray-700">Ustazah Siti Sarah</td>
                    <td class="py-3.5 px-4 text-gray-500">sitisarah</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Guru</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-medium border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openModal('edit', 'Ustazah Siti Sarah', 'sitisarah', 'Guru')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button onclick="deleteAccount(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50/50">
                    <td class="py-3.5 px-4 font-semibold text-gray-700">Budi Pratama</td>
                    <td class="py-3.5 px-4 text-gray-500">budipratama</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">Orangtua/Wali</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-medium border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openModal('edit', 'Budi Pratama', 'budipratama', 'Orangtua/Wali')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button onclick="deleteAccount(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="accountModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-base">Tambah Pengguna Baru</h3>
            <button onclick="closeModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="accountForm" onsubmit="saveAccount(event)" class="p-6 space-y-4 overflow-y-auto">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="inputNama" required placeholder="Contoh: Ustadz Abdullah, S.Pd." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Peran / Jabatan <span class="text-rose-500">*</span>
                </label>
                <select id="selectRole" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition cursor-pointer">
                    <option value="">Pilih Peran Akun</option>
                    <option value="Guru">Guru</option>
                    <option value="Orangtua/Wali">Orangtua/Wali</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Username <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="inputUsername" required placeholder="username_ustadz" class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="password" id="inputPassword" placeholder="••••••••" class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="password" id="inputConfirmPassword" placeholder="••••••••" class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = new Date().toLocaleDateString('id-ID', options);
    }
});

function openModal(mode, nama = '', username = '', role = '') {
    const modal = document.getElementById('accountModal');
    const title = document.getElementById('modalTitle');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (mode === 'add') {
        title.textContent = 'Tambah Pengguna Baru';
        document.getElementById('inputNama').value = '';
        document.getElementById('inputUsername').value = '';
        document.getElementById('inputPassword').value = '';
        document.getElementById('inputConfirmPassword').value = '';
        document.getElementById('selectRole').value = '';
    } else {
        title.textContent = 'Edit Pengguna';
        document.getElementById('inputNama').value = nama;
        document.getElementById('inputUsername').value = username;
        document.getElementById('selectRole').value = role;
        document.getElementById('inputPassword').value = '';
        document.getElementById('inputConfirmPassword').value = '';
    }
}

function closeModal() {
    const modal = document.getElementById('accountModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function saveAccount(event) {
    event.preventDefault();
    const password = document.getElementById('inputPassword').value;
    const confirmPassword = document.getElementById('inputConfirmPassword').value;

    if (password && confirmPassword && password !== confirmPassword) {
        alert('Konfirmasi kata sandi tidak cocok!');
        return;
    }

    alert('Data pengguna berhasil disimpan!');
    closeModal();
}

function deleteAccount(button) {
    if (confirm('Apakah Anda yakin ingin menghapus akun ini?')) {
        const row = button.closest('tr');
        row.remove();
    }
}

function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#accountTable tbody tr');

    rows.forEach(row => {
        const nama = row.cells[0].textContent.toLowerCase();
        const username = row.cells[1].textContent.toLowerCase();

        if (nama.includes(input) || username.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endpush
