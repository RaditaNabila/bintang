@extends('layouts.app') {{-- Sesuaikan nama layout Anda --}}

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm">
            <div class="font-bold mb-1">Terjadi kesalahan input:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Page & Tombol Tambah -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Akun Pengguna</h1>
            <p class="text-sm text-gray-500">Kelola daftar akun pengguna sistem di sini.</p>
        </div>
        <button
            type="button"
            onclick="openModal('add')"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm flex items-center gap-2"
        >
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Pengguna
        </button>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Nama Pengguna</th>
                        <th class="px-6 py-4">Peran</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pengguna as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->nama }}</td>
                            <td class="px-6 py-4">{{ $user->nama_pengguna }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-600 uppercase">
                                    {{ str_replace('_', ' ', $user->peran) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $user->status === 'aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} uppercase">
                                    {{ $user->status ?? 'aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Tombol Edit -->
                                    <button
                                        type="button"
                                        data-id="{{ $user->id }}"
                                        data-nama="{{ $user->nama }}"
                                        data-username="{{ $user->nama_pengguna }}"
                                        data-role="{{ $user->peran }}"
                                        data-status="{{ $user->status ?? 'aktif' }}"
                                        onclick="openEditModal(this)"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition"
                                        title="Edit"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('guru.kelola.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun pengguna ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"
                                            title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form (Tambah & Edit) -->
<div id="userModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-xl transform transition-all overflow-hidden">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Pengguna</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modal Body & Form -->
        <form id="userForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="userId" name="id">

            <!-- Field Nama -->
            <div>
                <label for="nama" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Masukkan nama lengkap"
                >
            </div>

            <!-- Field Nama Pengguna / Username -->
            <div>
                <label for="nama_pengguna" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Pengguna</label>
                <input
                    type="text"
                    id="nama_pengguna"
                    name="nama_pengguna"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Masukkan username"
                >
            </div>

            <!-- Field Peran / Role -->
            <div>
                <label for="peran" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Peran</label>
                <select
                    id="peran"
                    name="peran"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                >
                    <option value="" disabled selected>Pilih Peran</option>
                    <option value="guru">Guru</option>
                    <option value="orang_tua">Orang Tua</option>
                </select>
            </div>

            <!-- Field Status (Hanya Muncul/Digunakan Saat Edit) -->
            <div id="statusContainer" class="hidden">
                <label for="status" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                >
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- Field Kata Sandi -->
            <div>
                <label for="kata_sandi" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kata Sandi</label>
                <input
                    type="password"
                    id="kata_sandi"
                    name="kata_sandi"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="••••••••"
                >
                <p id="passwordHelp" class="text-xs text-amber-600 mt-1 font-medium"></p>
            </div>

            <!-- Field Konfirmasi Kata Sandi (Wajib karena validasi 'confirmed' di Controller) -->
            <div>
                <label for="kata_sandi_confirmation" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Konfirmasi Kata Sandi</label>
                <input
                    type="password"
                    id="kata_sandi_confirmation"
                    name="kata_sandi_confirmation"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="••••••••"
                >
            </div>

            <!-- Modal Footer / Action Buttons -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Logic -->
<script>
    function openModal(mode, id = '', nama = '', username = '', role = '', status = 'aktif') {
        const modal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        
        const userIdInput = document.getElementById('userId');
        const namaInput = document.getElementById('nama');
        const usernameInput = document.getElementById('nama_pengguna');
        const roleInput = document.getElementById('peran');
        const statusInput = document.getElementById('status');
        const statusContainer = document.getElementById('statusContainer');
        const passwordInput = document.getElementById('kata_sandi');
        const passwordConfirmInput = document.getElementById('kata_sandi_confirmation');
        const passwordHelp = document.getElementById('passwordHelp');

        if (mode === 'edit') {
            modalTitle.innerText = 'Edit Pengguna';
            userForm.action = `/guru/kelola-akun/${id}`; // URL update
            
            // Atur Method Spoofing PUT
            let methodInput = document.getElementById('methodField');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.id = 'methodField';
                userForm.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // Fill values
            userIdInput.value = id;
            namaInput.value = nama;
            usernameInput.value = username;
            roleInput.value = role;
            statusInput.value = status;
            statusContainer.classList.remove('hidden');

            passwordInput.value = '';
            passwordConfirmInput.value = '';
            passwordInput.required = false;

            if (passwordHelp) {
                passwordHelp.innerText = '*Kosongkan jika tidak ingin mengubah kata sandi';
            }
        } else {
            modalTitle.innerText = 'Tambah Pengguna';
            userForm.action = '/guru/kelola-akun'; // URL store
            
            const methodInput = document.getElementById('methodField');
            if (methodInput) {
                methodInput.remove();
            }

            // Reset values
            userForm.reset();
            userIdInput.value = '';
            statusContainer.classList.add('hidden');
            passwordInput.required = true;

            if (passwordHelp) {
                passwordHelp.innerText = '';
            }
        }

        modal.classList.remove('hidden');
    }

    function openEditModal(button) {
        const id = button.getAttribute('data-id');
        const nama = button.getAttribute('data-nama');
        const username = button.getAttribute('data-username');
        const role = button.getAttribute('data-role');
        const status = button.getAttribute('data-status');

        openModal('edit', id, nama, username, role, status);
    }

    function closeModal() {
        const modal = document.getElementById('userModal');
        modal.classList.add('hidden');
    }
</script>
@endsection