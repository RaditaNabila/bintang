@extends('layouts.app')

@section('page_title', 'Kelola Akun Pengguna')
@section('page_description', 'Manajemen hak akses ustadz, ustazah, dan wali kelas')

@section('content')
<div class="space-y-6">
    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm shadow-sm">
            <div class="font-bold mb-1 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                Terjadi kesalahan input:
            </div>
            <ul class="list-disc list-inside space-y-1 pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Action & Search -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-amber-100 shadow-sm">
        <form method="GET" action="{{ route('guru.kelola') }}" class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau username (tekan Enter)..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
            >
        </form>

        <button
            type="button"
            onclick="openModal('add')"
            class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition"
        >
            <i class="fa-solid fa-plus text-sm"></i>
            Tambah Pengguna Baru
        </button>
    </div>

    <!-- Tabel Data Pengguna -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="accountTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Role / Peran</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($pengguna as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-4 text-gray-500 text-xs">{{ $loop->iteration + ($pengguna->currentPage() - 1) * $pengguna->perPage() }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $user->nama }}</td>
                            <td class="py-3.5 px-4 text-gray-500">{{ $user->nama_pengguna }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ strtolower($user->peran) == 'guru' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800' }} uppercase">
                                    {{ str_replace('_', ' ', $user->peran) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if(strtolower($user->status ?? 'aktif') === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-medium border border-emerald-200 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-medium border border-rose-200 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
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
                                    <form action="{{ route('guru.kelola.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');" class="inline">
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
                            <td colspan="6" class="py-8 text-center text-gray-400 text-sm">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Render Pagination Links -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            {{ $pengguna->links() }}
        </div>
    </div>
</div>

<!-- Modal Form (Tambah & Edit) -->
<div id="userModal" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-xl transform transition-all overflow-hidden flex flex-col max-h-[90vh]">

        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center shrink-0">
            <h3 id="modalTitle" class="font-bold text-base">Tambah Pengguna Baru</h3>
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body & Form -->
        <form id="userForm" action="" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf
            <input type="hidden" id="userId" name="id">

            <!-- Field Nama -->
            <div>
                <label for="nama" class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    required
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
                    placeholder="Contoh: Ustadz Abdullah, S.Pd."
                >
            </div>

            <!-- Field Nama Pengguna / Username -->
            <div>
                <label for="nama_pengguna" class="block text-xs font-semibold text-gray-700 mb-1">Username <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input
                        type="text"
                        id="nama_pengguna"
                        name="nama_pengguna"
                        required
                        class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
                        placeholder="username_ustadz"
                    >
                </div>
            </div>

            <!-- Field Peran / Role -->
            <div>
                <label for="peran" class="block text-xs font-semibold text-gray-700 mb-1">Peran / Jabatan <span class="text-rose-500">*</span></label>
                <select
                    id="peran"
                    name="peran"
                    required
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition cursor-pointer"
                >
                    <option value="" disabled selected>Pilih Peran Akun</option>
                    <option value="guru">Guru</option>
                    <option value="orang_tua">Orang Tua</option>
                </select>
            </div>

            <!-- Field Status (Saat Edit) -->
            <div id="statusContainer" class="hidden">
                <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">Status Akun</label>
                <select
                    id="status"
                    name="status"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition cursor-pointer"
                >
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- Field Kata Sandi -->
            <div>
                <label for="kata_sandi" class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input
                        type="password"
                        id="kata_sandi"
                        name="kata_sandi"
                        class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
                        placeholder="••••••••"
                    >
                </div>
                <p id="passwordHelp" class="text-[11px] text-amber-600 mt-1 font-medium"></p>
            </div>

            <!-- Field Konfirmasi Kata Sandi -->
            <div>
                <label for="kata_sandi_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input
                        type="password"
                        id="kata_sandi_confirmation"
                        name="kata_sandi_confirmation"
                        class="w-full pl-9 pr-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 shrink-0">
                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition shadow-sm"
                >
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

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
            userForm.action = `/guru/kelola/${id}`;

            let methodInput = document.getElementById('methodField');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.id = 'methodField';
                userForm.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

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
            modalTitle.innerText = 'Tambah Pengguna Baru';
            userForm.action = "{{ route('guru.kelola.store') }}";

            const methodInput = document.getElementById('methodField');
            if (methodInput) {
                methodInput.remove();
            }

            userForm.reset();
            userIdInput.value = '';
            statusContainer.classList.add('hidden');
            passwordInput.required = true;

            if (passwordHelp) {
                passwordHelp.innerText = '';
            }
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
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
        modal.classList.remove('flex');
    }
</script>
@endsection
