@extends('layouts.app')
@section('title', 'Arsip Alumni & Siswa - Bintang Poin')
@section('page_title', 'Arsip Alumni & Siswa Keluar')
@section('page_description', 'Kumpulan riwayat poin dan histori data alumni serta siswa yang pindah sekolah')
@section('content')

<div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between gap-4">
    <div class="relative flex-1">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="searchFolder" onkeyup="filterFolders()" placeholder="Cari folder angkatan, tahun lulus, atau nama siswa..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
    </div>
</div>

<div id="folderSection" class="space-y-6 mt-6">
    <div>
        <div class="flex items-center gap-2 mb-3">
            <i class="fa-solid fa-thumbtack text-amber-500 text-xs rotate-45"></i>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Folder Disematkan (Pinned)</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div onclick="openFolder('Siswa Pindah / Keluar', 'pindah')" class="bg-amber-50/50 hover:bg-amber-100/50 border border-amber-200/80 p-5 rounded-2xl cursor-pointer transition shadow-sm group relative overflow-hidden">
                <div class="absolute top-3 right-3 text-amber-500"><i class="fa-solid fa-thumbtack text-xs rotate-45"></i></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500 text-white rounded-xl flex items-center justify-center text-xl shadow-md group-hover:scale-105 transition"><i class="fa-solid fa-person-walking-arrow-right"></i></div>
                    <div>
                        <h4 class="font-bold text-gray-800 group-hover:text-amber-600 transition">Siswa Pindah / Keluar</h4>
                        <p class="text-xs text-gray-500">{{ $pindah->count() }} Siswa Mutasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="flex items-center gap-2 mb-3">
            <i class="fa-solid fa-folder-closed text-amber-500 text-xs"></i>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Folder Alumni Kelulusan</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4" id="alumniFolders">
            @forelse($tahunAlumni as $folder)
                <div onclick="openFolder('Kelulusan {{ $folder['tahun'] }} ({{ $folder['nama_angkatan'] ?? 'Alumni' }})', '{{ $folder['tahun'] }}')" class="bg-white hover:border-amber-400 border border-gray-200 p-5 rounded-2xl cursor-pointer transition shadow-sm group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-lg group-hover:bg-amber-500 group-hover:text-white transition"><i class="fa-solid fa-folder"></i></div>
                        <div>
                            <h4 class="font-bold text-sm text-gray-800 group-hover:text-amber-600 transition">Kelulusan {{ $folder['tahun'] }}</h4>
                            <p class="text-[11px] text-gray-400">{{ $folder['jumlah'] }} Alumni • {{ $folder['nama_angkatan'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center text-gray-400 text-xs">
                    <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                    Belum ada data alumni.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div id="detailSection" class="hidden space-y-4 mt-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <button onclick="closeFolder()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </button>
            <div>
                <h3 id="currentFolderName" class="text-base font-bold text-gray-800"></h3>
                <p class="text-xs text-gray-400">Daftar riwayat poin akhir siswa pada folder ini</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">NIS</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas Terakhir</th>
                        <th class="py-3 px-4 text-center">Poin Akhir</th>
                        <th class="py-3 px-4 text-center">Status / Ket</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="studentTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const archiveData = {
    'pindah': @json($pindah->values()),
    'alumni': @json($tahunAlumni->mapWithKeys(function($item) {
        return [$item['tahun'] => $item['data']];
    }))
};

function openFolder(title, key) {
    document.getElementById('folderSection').classList.add('hidden');
    document.getElementById('detailSection').classList.remove('hidden');
    document.getElementById('currentFolderName').textContent = title;

    const tbody = document.getElementById('studentTableBody');
    tbody.innerHTML = '';

    // Ambil data berdasarkan key (apakah 'pindah' atau tahun angkatan alumni)
    let students = [];
    if (key === 'pindah') {
        students = archiveData.pindah || [];
    } else {
        students = archiveData.alumni[key] || [];
    }

    if (!students.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="py-10 text-center text-gray-400 text-xs">Belum ada data siswa pada folder ini.</td></tr>`;
        return;
    }

    students.forEach(function(student, index) {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50';
        tr.innerHTML = `
            <td class="py-3.5 px-4 text-xs font-bold text-gray-400">${index + 1}</td>
            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">${student.nis ?? '-'}</td>
            <td class="py-3.5 px-4 font-bold text-gray-800">${student.nama_lengkap ?? '-'}</td>
            <td class="py-3.5 px-4 text-gray-500 font-semibold">${student.kelas_terakhir ?? '-'}</td>
            <td class="py-3.5 px-4 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-xl font-extrabold text-xs border border-amber-200/60">
                    <i class="fa-solid fa-star text-[10px]"></i>${student.poin_akhir ?? 0}
                </span>
            </td>
            <td class="py-3.5 px-4 text-center">
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-[11px] font-semibold">${student.catatan_status ?? student.jenis_arsip ?? '-'}</span>
            </td>`;
        tbody.appendChild(tr);
    });
}

function closeFolder() {
    document.getElementById('detailSection').classList.add('hidden');
    document.getElementById('folderSection').classList.remove('hidden');
}

function filterFolders() {
    const search = document.getElementById('searchFolder').value.toLowerCase().trim();
    document.querySelectorAll('#alumniFolders > div').forEach(function(folder) {
        folder.style.display = folder.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
}
</script>
@endpush