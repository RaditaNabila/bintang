@extends('layouts.app')
@section('title','Poin Prestasi - Bintang Poin')
@section('page_title','Apresiasi & Prestasi Siswa')
@section('page_description','Catat kebaikan, kedisiplinan, dan capaian siswa')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($ranking as $index => $top)
        <div class="@if($index==0) bg-gradient-to-br from-amber-500 via-amber-400 to-orange-400 text-white @elseif($index==1) bg-gradient-to-br from-slate-100 to-slate-200/60 text-slate-800 border border-slate-200/80 @else bg-gradient-to-br from-orange-50 to-amber-100/60 text-amber-950 border border-amber-200/70 @endif p-5 rounded-2xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl pointer-events-none"><i class="fa-solid fa-trophy"></i></div>
            <div>
                <div class="flex justify-between items-start mb-3">
                    <span class="@if($index==0) bg-white/20 border-white/30 text-white @elseif($index==1) bg-white/80 border-slate-300 text-slate-700 @else bg-white/80 border-amber-300 text-amber-900 @endif backdrop-blur-sm text-[11px] font-bold px-3 py-1 rounded-full border flex items-center gap-1.5">
                        <i class="fa-solid @if($index==0) fa-crown text-yellow-200 @elseif($index==1) fa-medal text-slate-400 @else fa-award text-amber-700 @endif"></i>
                        Rank {{ $index+1 }} @if($index==0)(Tertinggi)@endif
                    </span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold tracking-tight">{{ $top->poin_saat_ini ?? 0 }}</span>
                        <span class="@if($index==0) text-amber-100 @else text-slate-500 @endif text-xs font-medium block -mt-1">Poin</span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-lg font-bold leading-snug">{{ $top->nama_lengkap ?? '-' }}</h3>
                    <p class="@if($index==0) text-amber-100 @else text-slate-500 @endif text-xs font-medium">Kelas {{ $top->kelas->nama_kelas ?? '-' }} &bull; NIS: {{ $top->nis ?? '-' }}</p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t @if($index==0) border-white/20 @else border-slate-300/50 @endif">
                <p class="@if($index==0) text-amber-50 @else text-slate-600 @endif text-xs leading-relaxed"><i class="fa-solid fa-trophy opacity-60 mr-1"></i>Siswa dengan perolehan poin apresiasi tertinggi.</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" onkeyup="filterData()" placeholder="Cari NIS, nama, atau keterangan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
        </div>
        <button type="button" onclick="openModal('add')" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-plus text-sm"></i><span>Catat Poin Prestasi</span>
        </button>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-800">Riwayat Catatan Prestasi</h3>
            <p class="text-xs text-gray-500">Log penambahan poin siswa pada periode yang dipilih</p>
        </div>
        <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
            <i class="fa-solid fa-filter text-gray-400 text-xs ml-2"></i>
            <select id="filterBulan" onchange="filterData()" class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none pr-2 cursor-pointer">
                <option value="">Semua Bulan</option>
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $value=>$nama)
                <option value="{{ $value }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="achievementTable">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
                        <th class="py-3 px-4">Tanggal</th><th class="py-3 px-4">NIS</th><th class="py-3 px-4">Nama Siswa</th><th class="py-3 px-4">Kelas</th><th class="py-3 px-4">Kategori</th><th class="py-3 px-4">Keterangan Prestasi</th><th class="py-3 px-4 text-center">Poin (+)</th><th class="py-3 px-4 text-center">Total Poin</th><th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $item)
                    <tr class="hover:bg-gray-50/50" data-bulan="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('m') }}">
                        <td class="py-3.5 px-4 text-xs text-gray-400">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">{{ $item->siswa->nis ?? '-' }}</td>
                        <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-500">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">{{ $item->aturanPoin->judul ?? '-' }}</span></td>
                        <td class="py-3.5 px-4 text-gray-600">{{ $item->keterangan ?? '-' }}</td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">+{{ $item->poin }}</td>
                        <td class="py-3.5 px-4 text-center"><span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-xs">{{ $item->siswa->poin_saat_ini ?? 0 }}</span></td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openEditModal({{ $item->id }},{{ $item->siswa_id }},{{ $item->aturan_poin_id }},@js($item->keterangan),{{ $item->poin }},'{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') }}')" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                                <form action="{{ route('guru.poin-prestasi.destroy',$item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan poin ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-10 text-center text-gray-400 text-sm">Belum ada catatan poin prestasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="achievementModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="font-bold text-base">Catat Poin Prestasi</h3>
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="achievementForm" action="{{ route('guru.poin-prestasi.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="tanggal_transaksi" id="inputTanggal" value="{{ date('Y-m-d') }}">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Siswa</label>
                <select name="siswa_id" id="selectSiswa" required class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)<option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama_lengkap }} @if($s->kelas) ({{ $s->kelas->nama_kelas }}) @endif</option>@endforeach
                </select>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-semibold text-gray-700">Kategori Prestasi</label>
                    <button type="button" onclick="openAddCategoryModal()" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold"><i class="fa-solid fa-plus"></i> Kategori Baru</button>
                </div>
                <!-- Perbaikan di bagian ini: menggunakan data-poin agar sinkron dengan JS -->
                <select name="aturan_poin_id" id="selectAturanPoin" onchange="updatePointFromAturan()" required
                    class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Prestasi --</option>
                    @foreach($aturanPrestasi as $aturan)
                        <option
                            value="{{ $aturan->id }}"
                            data-poin="{{ $aturan->nilai_poin }}"
                        >
                            {{ $aturan->judul }} (+{{ $aturan->nilai_poin }} Poin)
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Prestasi / Apresiasi</label>
                <textarea name="keterangan" id="inputKeterangan" required rows="3" placeholder="Contoh: Menjawab pertanyaan di depan kelas dengan sangat baik" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></textarea>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1"><label class="block text-xs font-semibold text-gray-700">Jumlah Poin (+)</label><span class="text-[10px] text-amber-600 font-medium">*Otomatis berdasarkan kategori</span></div>
                <input type="number" name="poin" id="inputPoin" required min="1" max="100" class="w-full px-3.5 py-2 border border-amber-300 bg-amber-50/50 font-bold text-amber-900 rounded-xl text-xs focus:outline-none focus:border-amber-500">
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600">Simpan Poin</button>
            </div>
        </form>
    </div>
</div>

<div id="addCategoryModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-amber-500 px-5 py-3.5 text-white flex justify-between items-center"><h4 class="font-bold text-sm">Tambah Kategori Prestasi</h4><button type="button" onclick="closeAddCategoryModal()"><i class="fa-solid fa-xmark"></i></button></div>
        <form action="{{ route('guru.poin-prestasi.kategori.store') }}" method="POST" class="p-5 space-y-3">
            @csrf
            <input type="hidden" name="jenis" value="apresiasi">
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori Prestasi</label><input type="text" name="nama_kategori" id="newCategoryName" required placeholder="Misal: Aktif Menjawab di Kelas" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Default Poin (+)</label><input type="number" name="poin" id="newCategoryPoints" required min="1" max="100" placeholder="5" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></div>
            <div class="flex justify-end gap-2 pt-3 border-t"><button type="button" onclick="closeAddCategoryModal()" class="px-3.5 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-xs">Batal</button><button type="submit" class="px-3.5 py-1.5 bg-amber-500 text-white rounded-xl text-xs">Tambah</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Perbaikan pada fungsi JavaScript untuk membaca data-poin dengan benar
function updatePointFromAturan() {
    const select = document.getElementById('selectAturanPoin');
    const inputPoin = document.getElementById('inputPoin');

    const option = select.options[select.selectedIndex];

    inputPoin.value = option?.dataset.poin || '';
}

function openModal(mode){
    const m = document.getElementById('achievementModal'), f = document.getElementById('achievementForm');
    document.getElementById('modalTitle').textContent = mode === 'edit' ? 'Edit Poin Prestasi' : 'Catat Poin Prestasi';
    if(mode === 'add'){
        f.action = "{{ route('guru.poin-prestasi.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('selectSiswa').value = '';
        document.getElementById('selectAturanPoin').value = '';
        document.getElementById('inputKeterangan').value = '';
        document.getElementById('inputPoin').value = '';
        document.getElementById('inputTanggal').value = "{{ date('Y-m-d') }}";
    }
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function openEditModal(
    id,
    siswaId,
    aturanPoinId,
    keterangan,
    poin,
    tanggal
) {
    const modal = document.getElementById('achievementModal');
    document.getElementById('achievementForm').action =
        "{{ url('guru/poin-prestasi') }}/" + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent =
        'Edit Poin Prestasi';
    document.getElementById('selectSiswa').value = siswaId;
    document.getElementById('selectAturanPoin').value = aturanPoinId;
    document.getElementById('inputKeterangan').value = keterangan;
    document.getElementById('inputPoin').value = poin;
    document.getElementById('inputTanggal').value = tanggal;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(){
    const m = document.getElementById('achievementModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function openAddCategoryModal(){
    const m = document.getElementById('addCategoryModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeAddCategoryModal(){
    const m = document.getElementById('addCategoryModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.getElementById('newCategoryName').value = '';
    document.getElementById('newCategoryPoints').value = '';
}

function filterData(){
    const search = document.getElementById('searchInput').value.toLowerCase().trim(),
          bulan = document.getElementById('filterBulan').value;
    document.querySelectorAll('#achievementTable tbody tr').forEach(row => {
        if(row.cells.length < 9) return;
        const cocokSearch = row.innerText.toLowerCase().includes(search),
              cocokBulan = !bulan || row.dataset.bulan === bulan;
        row.style.display = cocokSearch && cocokBulan ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => updatePointFromCategory());
</script>
@endpush