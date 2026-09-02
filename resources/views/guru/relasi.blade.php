@extends('layouts.app')

@section('title', 'Relasi Wali-Siswa - Bintang Poin')
@section('page_title', 'Relasi Wali-Siswa')
@section('page_description', 'Hubungkan akun orang tua dengan data siswa untuk akses portal wali')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.625rem 0.75rem !important;
        border-color: #e5e7eb !important;
        background-color: rgba(249, 250, 251, .5) !important;
        font-size: .75rem !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, .2) !important;
    }
    .ts-dropdown {
        border-radius: .75rem !important;
        font-size: .75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1) !important;
        overflow: hidden !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    
    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs">
            {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM TAMBAH RELASI --}}
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

        <form action="{{ route('guru.relasi-wali-siswa.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="selectWali" class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Akun Wali Murid</label>
                    <select id="selectWali" name="pengguna_id" required placeholder="Ketik nama / username wali..." autocomplete="off">
                        <option value="">-- Pilih Akun Wali --</option>
                        @foreach($wali as $w)
                            <option value="{{ $w->id }}" {{ old('pengguna_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama }} @if(!empty($w->username)) ({{ $w->username }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="selectSiswa" class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Siswa (Anak)</label>
                    <select id="selectSiswa" name="siswa_id" required placeholder="Ketik nama / NISN / kelas siswa..." autocomplete="off">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswa as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_lengkap }} - Kelas {{ $s->kelas->nama_kelas ?? '-' }} ({{ $s->nis ?? $s->nisn ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-2">
                <p class="text-xs text-gray-400">*Satu akun wali dapat dihubungkan ke beberapa siswa jika memiliki lebih dari 1 anak.</p>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-plus"></i> Simpan Relasi
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL DAFTAR RELASI --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Daftar Relasi Wali & Siswa Aktif</h3>
                <p class="text-xs text-gray-400">Menampilkan relasi akun yang terhubung di sistem</p>
            </div>
            <form method="GET" action="{{ route('guru.relasi-wali-siswa.index') }}" class="relative w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama wali atau siswa..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-gray-50 focus:bg-white transition">
            </form>
        </div>

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
                    @forelse($waliRelasi as $w)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-4 font-semibold text-gray-700">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($w->nama ?? 'W', 0, 1)) }}
                                    </div>
                                    <span>
                                        {{ $w->nama }} 
                                        @if(!empty($w->username))
                                            <span class="text-xs text-gray-400 font-normal">({{ $w->username }})</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($w->orangTuaSiswa as $item)
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-lg text-xs font-medium flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-graduate text-[10px]"></i>
                                            {{ $item->siswa->nama_lengkap ?? 'Siswa Tidak Ditemukan' }} ({{ $item->siswa->kelas->nama_kelas ?? '-' }})
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 {{ $w->orangTuaSiswa->count() > 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }} rounded-full text-xs font-medium">
                                    {{ $w->orangTuaSiswa->count() }} Anak
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @foreach($w->orangTuaSiswa as $item)
                                        <form action="{{ route('guru.relasi-wali-siswa.destroy', $item->id) }}" method="POST" class="inline delete-relation-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-relation-btn text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Putuskan Hubungan" data-siswa="{{ $item->siswa->nama_lengkap ?? 'Siswa ini' }}" data-wali="{{ $w->nama ?? 'Wali ini' }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 text-xs">
                                <i class="fa-solid fa-link-slash text-2xl mb-2 block"></i>Belum ada relasi wali dan siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($waliRelasi->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $waliRelasi->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="deleteRelationModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[9999] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 space-y-4">
            <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-link-slash"></i>
            </div>
            <div class="text-center">
                <h3 class="font-bold text-base text-gray-800">Hapus Relasi Wali & Siswa?</h3>
                <p class="text-xs text-gray-500 mt-2">Anda akan memutuskan hubungan antara:</p>
            </div>
            <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl">
                <p class="text-xs text-gray-500">Wali:</p>
                <p id="deleteRelationWali" class="text-xs font-bold text-gray-800 mt-0.5"></p>
                <p class="text-xs text-gray-500 mt-2">Siswa:</p>
                <p id="deleteRelationSiswa" class="text-xs font-bold text-gray-800 mt-0.5"></p>
            </div>
            <p class="text-[11px] text-rose-500 text-center">Relasi ini akan dihapus dan akun wali tidak lagi terhubung dengan siswa tersebut.</p>
            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" onclick="closeDeleteRelationModal()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
                <button type="button" id="btnConfirmDelete" class="w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectWali = document.getElementById('selectWali');
        if (selectWali) {
            new TomSelect('#selectWali', { create: false, sortField: { field: 'text', direction: 'asc' } });
        }
        
        const selectSiswa = document.getElementById('selectSiswa');
        if (selectSiswa) {
            new TomSelect('#selectSiswa', { create: false, sortField: { field: 'text', direction: 'asc' } });
        }

        const modal = document.getElementById('deleteRelationModal');
        const waliText = document.getElementById('deleteRelationWali');
        const siswaText = document.getElementById('deleteRelationSiswa');
        const btnConfirm = document.getElementById('btnConfirmDelete');
        let deleteForm = null;

        document.querySelectorAll('.delete-relation-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                deleteForm = this.closest('.delete-relation-form');
                if (!deleteForm) {
                    console.error('Form hapus relasi tidak ditemukan.');
                    return;
                }
                waliText.textContent = this.dataset.wali || 'Wali ini';
                siswaText.textContent = this.dataset.siswa || 'Siswa ini';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            });
        });

        window.closeDeleteRelationModal = function () {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            deleteForm = null;
        };

        if (btnConfirm) {
            btnConfirm.addEventListener('click', function () {
                if (deleteForm) { deleteForm.submit(); }
            });
        }

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) { closeDeleteRelationModal(); }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeDeleteRelationModal(); }
        });
    });
</script>
@endpush