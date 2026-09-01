@extends('layouts.app')

@section('title', 'Data Siswa - Guru')
@section('page_title', 'Data Siswa')
@section('page_description', 'Daftar siswa dan total perolehan poin per kelas')

@section('content')

@php
    $currentTingkat = request('tingkat', 'all');
    $currentKelasId = request('kelas_id', 'all');
    $currentSearch = request('search', '');
@endphp

<!-- Navigasi Tingkat Kelas -->
<div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
    <a href="{{ route('guru.data-siswa', array_filter(['search' => $currentSearch])) }}"
       class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold {{ $currentTingkat === 'all' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm' }} transition shrink-0">
        Semua Kelas
    </a>
    @for($i = 1; $i <= 6; $i++)
        <a href="{{ route('guru.data-siswa', array_filter(['tingkat' => $i, 'search' => $currentSearch])) }}"
           class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold {{ (string)$currentTingkat === (string)$i ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm' }} transition shrink-0">
            Kelas {{ $i }}
        </a>
    @endfor
</div>

<!-- Aksi Periodik -->
<div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white p-3.5 rounded-2xl border border-amber-100 shadow-sm gap-3">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
        <i class="fa-solid fa-sliders text-amber-500"></i>
        <span>Pengaturan & Aksi Periodik:</span>
    </div>
    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <button type="button" onclick="openResetSemesterModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
            <i class="fa-solid fa-arrows-rotate text-xs"></i>
            Reset Poin Semester → 250
        </button>
        <a href="{{ route('guru.naik-kelas') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
            <i class="fa-solid fa-angles-up text-xs"></i>
            Kenaikan Kelas & Pemindahan
        </a>
        <button type="button" onclick="openClassListModal()" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300/80 rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
            <i class="fa-solid fa-list-check text-xs"></i>
            Kelola Ruangan Kelas
        </button>
    </div>
</div>

<!-- Pencarian -->
<form method="GET" action="{{ route('guru.data-siswa') }}" class="mt-4 bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3">
    @if(request('tingkat'))
        <input type="hidden" name="tingkat" value="{{ request('tingkat') }}">
    @endif
    @if(request('kelas_id'))
        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
    @endif
    <div class="relative w-full sm:w-80">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Cari NISN atau Nama Siswa..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
    </div>
    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold transition">
        Cari
    </button>
</form>

<!-- Tabel Siswa -->
<div class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-3">

        <!-- Sub-Class / Rombel Filter -->
        <div class="flex items-center gap-2 overflow-x-auto" id="subClassContainer">
            @if($currentTingkat !== 'all')
                <span class="text-xs font-medium text-gray-400 mr-1 shrink-0">Pilih Rombel:</span>

                <a href="{{ route('guru.data-siswa', array_filter(['tingkat' => $currentTingkat, 'search' => $currentSearch])) }}"
                   class="sub-btn px-3 py-1.5 rounded-lg text-xs {{ $currentKelasId === 'all' ? 'font-bold bg-amber-100 text-amber-800' : 'font-medium bg-gray-50 text-gray-600 hover:bg-gray-100' }} transition shrink-0">
                    Semua (Kelas {{ $currentTingkat }})
                </a>

                @foreach($kelas as $item)
                    @if((string)$item->tingkat === (string)$currentTingkat)
                        @php
                            $namaDisplay = \Illuminate\Support\Str::startsWith(strtolower($item->nama_kelas), 'kelas')
                                ? $item->nama_kelas
                                : 'Kelas ' . $item->nama_kelas;
                        @endphp
                        <a href="{{ route('guru.data-siswa', array_filter(['tingkat' => $item->tingkat, 'kelas_id' => $item->id, 'search' => $currentSearch])) }}"
                           class="sub-btn px-3 py-1.5 rounded-lg text-xs {{ (string)$currentKelasId === (string)$item->id ? 'font-bold bg-amber-100 text-amber-800' : 'font-medium bg-gray-50 text-gray-600 hover:bg-gray-100' }} transition shrink-0">
                            {{ $namaDisplay }}
                        </a>
                    @endif
                @endforeach
            @else
                <div class="text-xs font-semibold text-gray-500">
                    Menampilkan seluruh siswa dari semua kelas
                </div>
            @endif
        </div>

        <button type="button" onclick="openModal('add')" class="shrink-0 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-plus text-sm"></i>
            Tambah Siswa Baru
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm" id="studentTable">
            <thead>
                <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                    <th class="py-3 px-4 text-center">No</th>
                    <th class="py-3 px-4">NISN</th>
                    <th class="py-3 px-4">Nama Siswa</th>
                    <th class="py-3 px-4">Kelas</th>
                    <th class="py-3 px-4">Jenis Kelamin</th>
                    <th class="py-3 px-4 text-center">Total Poin</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($siswa as $index => $item)
                    @php
                        $namaKelasTabel = $item->kelas?->nama_kelas;
                        if ($namaKelasTabel && !\Illuminate\Support\Str::startsWith(strtolower($namaKelasTabel), 'kelas')) {
                            $namaKelasTabel = 'Kelas ' . $namaKelasTabel;
                        }
                    @endphp
                    <tr class="hover:bg-gray-50/50" data-id="{{ $item->id }}" data-tingkat="{{ $item->kelas?->tingkat }}" data-kelas-id="{{ $item->kelas?->id }}" data-kelas="{{ $namaKelasTabel }}">
                        <td class="py-3.5 px-4 text-center font-mono text-xs text-gray-400">
                            {{ $loop->iteration + ($siswa->currentPage() - 1) * $siswa->perPage() }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">{{ $item->nisn }}</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $item->nama_lengkap }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">
                                {{ $namaKelasTabel ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $item->jenis_kelamin }}</td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">{{ $item->poin_saat_ini }}</td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                               <button
                                  type="button"
                                  onclick='openModal(
                                      "edit",
                                      @json($item->nisn),
                                      @json($item->nama_lengkap),
                                      @json($item->kelas?->id),
                                      @json($item->jenis_kelamin),
                                      @json($item->poin_saat_ini),
                                      @json($item->id)
                                  )'
                                  class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition"
                              >
                                  <i class="fa-solid fa-pen-to-square text-xs"></i>
                              </button>
                                <button type="button" data-id="{{ $item->id }}" data-name="{{ $item->nama_lengkap }}" onclick="archiveStudent(this.dataset.id, this.dataset.name)" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 flex items-center justify-center transition" title="Arsipkan Siswa">
                                    <i class="fa-solid fa-box-archive text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <div class="mt-4 pt-4 border-t border-gray-100 [&>nav]:w-full [&>nav]:flex [&>nav]:items-center [&>nav]:justify-between">
        {{ $siswa->links() }}
    </div>
</div>

<!-- Modal Kelola Kelas -->
<div id="classListModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-base">Kelola Ruangan Kelas</h3>
                <p class="text-[11px] text-amber-100">Daftar ruangan kelas</p>
            </div>
            <button type="button" onclick="closeClassListModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto" id="classListContent"></div>

        <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
            <button type="button" onclick="openAddRoomModal()" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-sm">
                <i class="fa-solid fa-plus"></i>
                Tambah Ruangan Baru
            </button>
            <button type="button" onclick="closeClassListModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-medium transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Tambah Ruangan Kelas Baru -->
<div id="addRoomModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <h3 class="font-bold text-base">Tambah Ruangan Kelas</h3>
            <button type="button" onclick="closeAddRoomModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="addRoomForm" action="{{ route('guru.data-siswa.kelas.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat Kelas</label>
                <select name="tingkat" id="roomTingkat" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Tingkat --</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}">Kelas {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Ruangan / Rombel</label>
                <input type="text" name="nama_kelas" id="roomName" required placeholder="Contoh: Otomatis A/B/C..." class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 bg-gray-50">
                <p class="text-[10px] text-gray-400 mt-1">Otomatis mendeteksi abjad selanjutnya (Misal: sudah ada C, otomatis jadi D).</p>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeAddRoomModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold hover:bg-amber-600 transition">Simpan Ruangan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Ruangan Kelas -->
<div id="deleteRoomModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="font-bold text-base text-gray-800">Hapus Ruangan Kelas?</h3>
            <p class="text-xs text-gray-500 mt-1">Anda akan menghapus <span id="deleteRoomNameLabel" class="font-bold text-gray-700"></span>.</p>
        </div>
        <div class="flex items-center justify-center gap-2 pt-2">
            <button type="button" onclick="closeDeleteRoomModal()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <form id="deleteRoomForm" method="POST" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Siswa -->
<div id="studentModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-base">Tambah Siswa Baru</h3>
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="studentForm" action="{{ route('guru.data-siswa.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">NISN</label>
                <input type="text" name="nisn" id="inputNisn" required placeholder="0012345678" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap Siswa</label>
                <input type="text" name="nama_lengkap" id="inputNama" required placeholder="Contoh: Bilal Bin Rabah" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kelas</label>
                    <select id="selectKelas" name="kelas_id" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                        @foreach($kelas as $item)
                            @php
                                $optionDisplay = \Illuminate\Support\Str::startsWith(strtolower($item->nama_kelas), 'kelas')
                                    ? $item->nama_kelas
                                    : 'Kelas ' . $item->nama_kelas;
                            @endphp
                            <option value="{{ $item->id }}" data-tingkat="{{ $item->tingkat }}">
                                {{ $optionDisplay }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="selectGender" name="jenis_kelamin" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Poin Awal Siswa</label>
                <div class="relative">
                    <input type="number" name="poin_saat_ini" id="inputPoin" required min="0" value="250" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-emerald-600 focus:outline-none focus:border-amber-500">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-gray-400">Poin Default</span>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Poin -->
<div id="resetSemesterModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 text-white flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-arrows-rotate text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base leading-tight">Reset Poin Kenaikan Semester</h3>
                    <p class="text-[11px] text-blue-100">Kembalikan saldo poin siswa ke 250 Poin</p>
                </div>
            </div>
            <button type="button" onclick="closeResetSemesterModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-1">
                <p class="font-bold flex items-center gap-1.5 text-blue-800">
                    <i class="fa-solid fa-circle-info"></i>
                    Ketentuan Reset Semester:
                </p>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-blue-700 pl-1">
                    <li>Ruangan & kelas siswa <strong>TIDAK BERUBAH</strong>.</li>
                    <li>Seluruh poin target akan di-reset menjadi <strong>250 Poin</strong>.</li>
                </ul>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Target Siswa</label>
                <select id="resetTargetScope" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-blue-500">
                    <option value="all">Semua Siswa (Seluruh Kelas 1 - 6)</option>
                    <option value="current">Hanya Siswa di Tampilan/Filter Saat Ini</option>
                </select>
            </div>

            <div class="pt-2 border-t">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Ketik <span class="text-rose-600 font-bold">RESET</span> untuk mengonfirmasi:
                </label>
                <input type="text" id="confirmResetInput" placeholder="Ketik RESET di sini..." class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 font-bold tracking-widest text-gray-700">
            </div>
        </div>

        <div class="p-4 bg-gray-50 border-t flex justify-end gap-2">
            <button type="button" onclick="closeResetSemesterModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <button type="button" onclick="executeResetSemester()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-check-double"></i>
                Jalankan Reset Poin
            </button>
        </div>
    </div>
</div>

<!-- Modal Kustom Konfirmasi Pemindahan Siswa (Pengganti Confirm Bawaan Browser) -->
<div id="transferStudentModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-xl">
            <i class="fa-solid fa-right-left"></i>
        </div>
        <div>
            <h3 class="font-bold text-base text-gray-800">Konfirmasi Pemindahan</h3>
            <p id="transferStudentMessage" class="text-xs text-gray-500 mt-1 leading-relaxed"></p>
        </div>
        <div class="flex items-center justify-center gap-2 pt-2">
            <button type="button" onclick="closeTransferModal()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <button type="button" id="transferConfirmBtn" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Pindahkan</button>
        </div>
    </div>
</div>

<!-- Modal Kustom Notifikasi / Peringatan Umum -->
<div id="customAlertModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-6 space-y-4">
        <div id="customAlertIcon" class="w-12 h-12 rounded-full flex items-center justify-center mx-auto text-xl"></div>
        <div>
            <h3 id="customAlertTitle" class="font-bold text-base text-gray-800">Pemberitahuan</h3>
            <p id="customAlertMessage" class="text-xs text-gray-500 mt-1 whitespace-pre-line"></p>
        </div>
        <div id="customAlertButtons" class="flex items-center justify-center gap-2 pt-2"></div>
    </div>
</div>

<!-- Modal Arsip Siswa -->
<div id="archiveStudentModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-base">Arsipkan Siswa</h3>
                <p class="text-[11px] text-amber-100">Pilih status siswa sebelum diarsipkan</p>
            </div>
            <button type="button" onclick="closeArchiveModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="archiveStudentForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs text-gray-500">Siswa yang akan diarsipkan:</p>
                <p id="archiveStudentName" class="font-bold text-gray-800 mt-1">-</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status Arsip</label>
                <select name="jenis_arsip" id="archiveType" required class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Status --</option>
                    <option value="pindah">Pindah / Keluar</option>
                    <option value="lulus">Lulus / Alumni</option>
                </select>
            </div>

            <div id="graduationYearField" class="hidden">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun Kelulusan</label>
                <input type="text" name="tahun_kelulusan" id="archiveYear" placeholder="Contoh: 2026" maxlength="4" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan_status" id="archiveNote" rows="3" placeholder="Contoh: Pindah ke sekolah lain..." class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeArchiveModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold hover:bg-amber-600"><i class="fa-solid fa-box-archive mr-1"></i> Arsipkan Siswa</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@php
    $kelasData = $kelas->map(function($item) {
        $formattedName = \Illuminate\Support\Str::startsWith(strtolower($item->nama_kelas), 'kelas')
            ? $item->nama_kelas
            : 'Kelas ' . $item->nama_kelas;

        return [
            'id' => $item->id,
            'tingkat' => (string) $item->tingkat,
            'nama_kelas' => $formattedName,
        ];
    })->values();
@endphp

<script>
const kelasDatabase = @json($kelasData);

function showCustomAlert(title, message, type = 'warning', callback = null) {
    const modal = document.getElementById('customAlertModal');
    const iconContainer = document.getElementById('customAlertIcon');
    const titleEl = document.getElementById('customAlertTitle');
    const msgEl = document.getElementById('customAlertMessage');
    const btnContainer = document.getElementById('customAlertButtons');

    titleEl.textContent = title;
    msgEl.textContent = message;

    if (type === 'warning') {
        iconContainer.className = 'w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
    } else if (type === 'danger') {
        iconContainer.className = 'w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
    } else {
        iconContainer.className = 'w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto text-xl';
        iconContainer.innerHTML = '<i class="fa-solid fa-circle-info"></i>';
    }

    if (callback) {
        btnContainer.innerHTML = `
            <button type="button" onclick="closeCustomAlert()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-medium transition">Batal</button>
            <button type="button" id="customAlertConfirmBtn" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition">Ya, Lanjutkan</button>
        `;
        document.getElementById('customAlertConfirmBtn').onclick = function() {
            closeCustomAlert();
            callback();
        };
    } else {
        btnContainer.innerHTML = `
            <button type="button" onclick="closeCustomAlert()" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition">Mengerti</button>
        `;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCustomAlert() {
    const modal = document.getElementById('customAlertModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Fungsi Modal Kustom Pemindahan Siswa (Pengganti confirm())
function openTransferModal(totalSiswa, targetKelasName, callback) {
    const modal = document.getElementById('transferStudentModal');
    const msgEl = document.getElementById('transferStudentMessage');
    const confirmBtn = document.getElementById('transferConfirmBtn');

    if (!modal || !msgEl || !confirmBtn) return;

    msgEl.innerHTML = `Yakin ingin memindahkan <span class="font-bold text-gray-800">${totalSiswa} siswa terpilih</span> ke <span class="font-bold text-amber-600">${targetKelasName}</span>?`;

    // Pasang aksi callback saat tombol konfirmasi diklik
    confirmBtn.onclick = function() {
        closeTransferModal();
        if (typeof callback === 'function') callback();
    };

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeTransferModal() {
    const modal = document.getElementById('transferStudentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openModal(mode, nisn = '', nama = '', kelasId = '', gender = 'Laki-laki', poin = 250, studentId = '') {
    const modal = document.getElementById('studentModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('studentForm');
    const formMethod = document.getElementById('formMethod');

    if (!modal || !form) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (mode === 'add') {
        title.textContent = 'Tambah Siswa Baru';
        form.reset();
        form.action = "{{ route('guru.data-siswa.store') }}";
        formMethod.value = 'POST';
        document.getElementById('inputPoin').value = 250;
    } else if (mode === 'edit') {
        title.textContent = 'Edit Data Siswa';
        form.action = "{{ url('/guru/data-siswa') }}/" + studentId;
        formMethod.value = 'PUT';
        document.getElementById('inputNisn').value = nisn;
        document.getElementById('inputNama').value = nama;
        document.getElementById('selectKelas').value = kelasId;
        document.getElementById('selectGender').value = gender;
        document.getElementById('inputPoin').value = poin;
    }
}

function closeModal() {
    const modal = document.getElementById('studentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function archiveStudent(id, nama) {
    const modal = document.getElementById('archiveStudentModal');
    const form = document.getElementById('archiveStudentForm');
    const nameElement = document.getElementById('archiveStudentName');
    const archiveType = document.getElementById('archiveType');
    const archiveYear = document.getElementById('archiveYear');
    const archiveNote = document.getElementById('archiveNote');
    const graduationField = document.getElementById('graduationYearField');

    if (!modal || !form || !nameElement) return;

    nameElement.textContent = nama;
    form.action = "{{ url('/guru/data-siswa') }}/" + id + "/arsip";

    archiveType.value = '';
    archiveYear.value = '';
    archiveNote.value = '';

    graduationField.classList.add('hidden');
    archiveYear.required = false;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeArchiveModal() {
    const modal = document.getElementById('archiveStudentModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

document.getElementById('archiveType')?.addEventListener('change', function() {
    const graduationField = document.getElementById('graduationYearField');
    if (this.value === 'lulus') {
        graduationField.classList.remove('hidden');
        document.getElementById('archiveYear').required = true;
    } else {
        graduationField.classList.add('hidden');
        document.getElementById('archiveYear').required = false;
        document.getElementById('archiveYear').value = '';
    }
});

function openResetSemesterModal() {
    document.getElementById('confirmResetInput').value = '';
    const modal = document.getElementById('resetSemesterModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeResetSemesterModal() {
    const modal = document.getElementById('resetSemesterModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function executeResetSemester() {
    const confirmation = document.getElementById('confirmResetInput').value.trim();

    if (confirmation !== 'RESET') {
        showCustomAlert('Konfirmasi Salah', 'Kata konfirmasi salah! Silakan ketik RESET dengan huruf kapital.', 'warning');
        return;
    }

    const scope = document.getElementById('resetTargetScope').value;
    let ids = [];

    if (scope === 'current') {
        const rows = document.querySelectorAll('#studentTable tbody tr');
        rows.forEach(function(row) {
            if (row.dataset.id) {
                ids.push(row.dataset.id);
            }
        });

        if (ids.length === 0) {
            showCustomAlert('Data Kosong', 'Tidak ada siswa yang ditemukan pada halaman saat ini.', 'warning');
            return;
        }
    }

    closeResetSemesterModal();

    if (scope === 'all') {
        showCustomAlert(
            'Konfirmasi Reset Poin',
            'PERINGATAN!\n\nPoin SELURUH siswa aktif akan direset menjadi 250.\n\nApakah Anda yakin ingin melanjutkan?',
            'danger',
            function() {
                submitResetForm(scope, ids);
            }
        );
        return;
    }

    submitResetForm(scope, ids);
}

function submitResetForm(scope, ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('guru.data-siswa.reset-poin') }}";

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="scope" value="${scope}">
    `;

    ids.forEach(function(id) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

function openClassListModal() {
    renderClassListModal();
    const modal = document.getElementById('classListModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeClassListModal() {
    const modal = document.getElementById('classListModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function renderClassListModal() {
    const content = document.getElementById('classListContent');
    if (!content) return;
    content.innerHTML = '';

    for (let grade = 1; grade <= 6; grade++) {
        const section = document.createElement('div');
        section.className = 'border-b border-gray-100 pb-3 last:border-0';
        section.innerHTML = `
            <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">
                Tingkat Kelas ${grade}
            </h4>
            <div class="flex flex-wrap gap-2" id="class-list-${grade}"></div>
        `;
        content.appendChild(section);

        const grid = document.getElementById(`class-list-${grade}`);
        const classes = kelasDatabase.filter(function(item) {
            return String(item.tingkat) === String(grade);
        });

        if (classes.length === 0) {
            grid.innerHTML = '<span class="text-xs text-gray-400 italic">Belum ada ruangan</span>';
            continue;
        }

        classes.forEach(function(item) {
            const chip = document.createElement('div');
            chip.className = 'flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200/60 text-amber-900 rounded-xl text-xs font-medium shadow-sm';
            chip.innerHTML = `
                <span>${item.nama_kelas}</span>
                <button type="button" onclick="deleteRoom(${item.id}, '${item.nama_kelas}')" class="text-rose-400 hover:text-rose-600 transition ml-1" title="Hapus Ruangan">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            `;
            grid.appendChild(chip);
        });
    }
}

function deleteRoom(id, namaKelas) {
    const modal = document.getElementById('deleteRoomModal');
    document.getElementById('deleteRoomForm').action = `/guru/data-siswa/kelas/${id}`;
    document.getElementById('deleteRoomNameLabel').textContent = `"${namaKelas}"`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteRoomModal() {
    const modal = document.getElementById('deleteRoomModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openAddRoomModal() {
    document.getElementById('roomTingkat').value = '';
    document.getElementById('roomName').value = '';
    const modal = document.getElementById('addRoomModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeAddRoomModal() {
    const modal = document.getElementById('addRoomModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Fitur Otomatis Menentukan Abjad Rombel Berikutnya
document.getElementById('roomTingkat')?.addEventListener('change', function() {
    const selectedTingkat = this.value;
    const roomNameInput = document.getElementById('roomName');

    if (!selectedTingkat) {
        roomNameInput.value = '';
        return;
    }

    const existingClasses = kelasDatabase.filter(item => String(item.tingkat) === String(selectedTingkat));

    if (existingClasses.length === 0) {
        roomNameInput.value = `Kelas ${selectedTingkat}A`;
    } else {
        let maxCharCode = 64;

        existingClasses.forEach(item => {
            const cleanName = item.nama_kelas.replace(/^kelas\s*\d+/i, '').trim();
            if (cleanName.length > 0) {
                const lastChar = cleanName.charAt(cleanName.length - 1).toUpperCase();
                const code = lastChar.charCodeAt(0);
                if (code >= 65 && code <= 90) {
                    if (code > maxCharCode) {
                        maxCharCode = code;
                    }
                }
            }
        });

        let nextLetter = 'A';
        if (maxCharCode >= 65 && maxCharCode < 90) {
            nextLetter = String.fromCharCode(maxCharCode + 1);
        }

        roomNameInput.value = `Kelas ${selectedTingkat}${nextLetter}`;
    }
});

document.addEventListener('click', function(e) {
    if (e.target === document.getElementById('studentModal')) closeModal();
    if (e.target === document.getElementById('classListModal')) closeClassListModal();
    if (e.target === document.getElementById('resetSemesterModal')) closeResetSemesterModal();
    if (e.target === document.getElementById('archiveStudentModal')) closeArchiveModal();
    if (e.target === document.getElementById('deleteRoomModal')) closeDeleteRoomModal();
    if (e.target === document.getElementById('customAlertModal')) closeCustomAlert();
    if (e.target === document.getElementById('transferStudentModal')) closeTransferModal();
    if (e.target === document.getElementById('addRoomModal')) closeAddRoomModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeClassListModal();
        closeResetSemesterModal();
        closeArchiveModal();
        closeDeleteRoomModal();
        closeCustomAlert();
        closeTransferModal();
        closeAddRoomModal();
    }
});
</script>
@endpush
