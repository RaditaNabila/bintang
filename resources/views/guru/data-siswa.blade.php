@extends('layouts.app')

@section('title', 'Data Siswa - Guru')
@section('page_title', 'Data Siswa')
@section('page_description', 'Daftar siswa dan total perolehan poin per kelas')

@section('content')

<!-- Navigasi Tingkat Kelas -->
<div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
    <button type="button" onclick="selectGrade('all', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-amber-500 text-white shadow-sm transition">
        Semua Kelas
    </button>
    @for($i = 1; $i <= 6; $i++)
        <button type="button" onclick="selectGrade('{{ $i }}', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
            Kelas {{ $i }}
        </button>
    @endfor
</div>

<!-- Aksi -->
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
<div class="mt-4 bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center">
    <div class="relative w-full sm:w-80">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="searchInput" onkeyup="applyFilters()" placeholder="Cari NISN atau Nama Siswa..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
    </div>
</div>

<!-- Tabel Siswa -->
<div class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-3">
        <div class="flex items-center gap-2 overflow-x-auto" id="subClassContainer">
            <span class="text-xs font-medium text-gray-400 mr-1 shrink-0">Pilih Rombel:</span>
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
                    <th class="py-3 px-4">NISN</th>
                    <th class="py-3 px-4">Nama Siswa</th>
                    <th class="py-3 px-4">Kelas</th>
                    <th class="py-3 px-4">Jenis Kelamin</th>
                    <th class="py-3 px-4 text-center">Total Poin</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($siswa as $item)
                    <tr class="hover:bg-gray-50/50"  data-id="{{ $item->id }}" data-tingkat="{{ $item->kelas?->tingkat }}" data-kelas-id="{{ $item->kelas?->id }}" data-kelas="{{ $item->kelas?->nama_kelas }}">
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">{{ $item->nisn }}</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $item->nama_lengkap }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">
                                {{ $item->kelas?->nama_kelas ?? '-' }}
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
                                <button type="button" onclick="deleteStudent('{{ $item->id }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
            <button type="button" onclick="promptAddNewRoom()" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-sm">
                <i class="fa-solid fa-plus"></i>
                Tambah Ruangan Baru
            </button>
            <button type="button" onclick="closeClassListModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-medium transition">
                Tutup
            </button>
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

        <!-- Mengarah ke rute guru.data-siswa.store -->
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
                            <option value="{{ $item->id }}" data-tingkat="{{ $item->tingkat }}">
                                {{ $item->nama_kelas }}
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
                <p class="text-[10px] text-gray-400 mt-1">*Poin standar siswa baru adalah 250.</p>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">
                    Simpan
                </button>
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
            <button type="button" onclick="closeResetSemesterModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-medium transition">
                Batal
            </button>
            <button type="button" onclick="executeResetSemester()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-check-double"></i>
                Jalankan Reset Poin
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@php
    $kelasData = $kelas->map(function($item) {
        return [
            'id' => $item->id,
            'tingkat' => (string) $item->tingkat,
            'nama_kelas' => $item->nama_kelas,
        ];
    })->values();
@endphp

<script>
const kelasDatabase = @json($kelasData);

let activeGrade = 'all';
let activeSubClass = 'all';

document.addEventListener('DOMContentLoaded', function() {
    renderSubClasses('all');
    applyFilters();
});

function selectGrade(grade, btn) {
    activeGrade = String(grade);
    activeSubClass = 'all';

    document.querySelectorAll('.grade-btn').forEach(function(button) {
        button.className = 'grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition';
    });

    btn.className = 'grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-amber-500 text-white shadow-sm transition';

    renderSubClasses(activeGrade);
    applyFilters();
}

function renderSubClasses(grade) {
    const container = document.getElementById('subClassContainer');
    if (!container) return;

    container.innerHTML = `<span class="text-xs font-medium text-gray-400 mr-1 shrink-0">Pilih Rombel:</span>`;

    const allBtn = document.createElement('button');
    allBtn.type = 'button';
    allBtn.textContent = grade === 'all' ? 'Semua Kelas' : `Semua (${grade})`;
    allBtn.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 transition shrink-0';
    allBtn.onclick = function() { selectSubClass('all', allBtn); };
    container.appendChild(allBtn);

    if (grade === 'all') return;

    const filteredClasses = kelasDatabase.filter(function(item) {
        return String(item.tingkat) === String(grade);
    });

    filteredClasses.forEach(function(item) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = item.nama_kelas;
        button.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 hover:bg-gray-100 transition shrink-0';
        button.onclick = function() { selectSubClass(String(item.id), button); };
        button.dataset.kelasId = item.id;
        container.appendChild(button);
    });
}

function selectSubClass(subClass, btn) {
    activeSubClass = String(subClass);

    document.querySelectorAll('.sub-btn').forEach(function(button) {
        button.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 hover:bg-gray-100 transition shrink-0';
    });

    btn.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 transition shrink-0';
    applyFilters();
}

function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    const search = searchInput.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#studentTable tbody tr');

    rows.forEach(function(row) {
        if (!row.cells || row.cells.length < 6) return;

        const nisn = row.cells[0].textContent.toLowerCase();
        const nama = row.cells[1].textContent.toLowerCase();
        const rowGrade = row.dataset.tingkat;
        const rowClassId = row.dataset.kelasId;

        const matchGrade = activeGrade === 'all' || String(rowGrade) === String(activeGrade);
        const matchSubClass = activeSubClass === 'all' || String(rowClassId) === String(activeSubClass);
        const matchSearch = nisn.includes(search) || nama.includes(search);

        row.style.display = matchGrade && matchSubClass && matchSearch ? '' : 'none';
    });
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

        if (activeSubClass !== 'all') {
            document.getElementById('selectKelas').value = activeSubClass;
        } else if (activeGrade !== 'all') {
            const firstClass = kelasDatabase.find(function(item) {
                return String(item.tingkat) === String(activeGrade);
            });

            if (firstClass) {
                document.getElementById('selectKelas').value = firstClass.id;
            }
        }

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

function deleteStudent(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        // Mengarah ke URL /guru/data-siswa/{id} sesuai file web.php
        form.action = `/guru/data-siswa/${id}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}

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
        alert('Kata konfirmasi salah! Silakan ketik RESET dengan huruf kapital.');
        return;
    }

    const scope = document.getElementById('resetTargetScope').value;

    let ids = [];

    // Jika memilih siswa pada tampilan/filter saat ini
    if (scope === 'current') {
        const rows = document.querySelectorAll('#studentTable tbody tr');

        rows.forEach(function(row) {
            if (row.style.display !== 'none' && row.dataset.id) {
                ids.push(row.dataset.id);
            }
        });

        if (ids.length === 0) {
            alert('Tidak ada siswa yang ditemukan pada tampilan/filter saat ini.');
            return;
        }
    }

    if (scope === 'all') {
        const yakin = confirm(
            'PERINGATAN!\n\n' +
            'Poin SELURUH siswa aktif akan direset menjadi 250.\n\n' +
            'Apakah Anda yakin ingin melanjutkan?'
        );

        if (!yakin) {
            return;
        }
    }

    const form = document.createElement('form');

    form.method = 'POST';
    form.action = "{{ route('guru.data-siswa.reset-poin') }}";

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || '{{ csrf_token() }}';

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
            chip.innerHTML = `<span>${item.nama_kelas}</span>`;
            grid.appendChild(chip);
        });
    }
}

function promptAddNewRoom() {
    const tingkat = prompt(
        'Masukkan tingkat kelas (1-6):'
    );

    if (tingkat === null) {
        return;
    }

    const tingkatNumber = parseInt(tingkat);

    if (isNaN(tingkatNumber) || tingkatNumber < 1 || tingkatNumber > 6) {
        alert('Tingkat kelas harus berupa angka 1 sampai 6.');
        return;
    }

    const namaKelas = prompt(
        'Masukkan nama ruangan kelas:\nContoh: 1A, 1B, 2A, 6B'
    );

    if (namaKelas === null || namaKelas.trim() === '') {
        alert('Nama ruangan kelas wajib diisi.');
        return;
    }

    const form = document.createElement('form');

    form.method = 'POST';
    form.action = "{{ route('guru.data-siswa.kelas.store') }}";

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || '{{ csrf_token() }}';

    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="tingkat" value="${tingkatNumber}">
        <input type="hidden" name="nama_kelas" value="${namaKelas.trim()}">
    `;

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush