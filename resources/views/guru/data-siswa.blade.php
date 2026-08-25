@extends('layouts.app')

@section('title', 'Data Siswa - Guru')
@section('page_title', 'Data Siswa')
@section('page_description', 'Daftar siswa dan total perolehan poin per kelas')

@section('content')

  <!-- 1. Navigasi Tingkat Kelas (1-6) -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
    <button onclick="selectGrade('all', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-amber-500 text-white shadow-sm transition">
      Semua Kelas
    </button>
    <button onclick="selectGrade('1', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
      Kelas 1
    </button>
    <button onclick="selectGrade('2', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
      Kelas 2
    </button>
    <button onclick="selectGrade('3', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
      Kelas 3
    </button>
    <button onclick="selectGrade('4', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
      Kelas 4
    </button>
    <button onclick="selectGrade('5', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
      Kelas 5
    </button>
    <button onclick="selectGrade('6', this)" class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition">
      Kelas 6
    </button>
  </div>

  <!-- 2. Section Tombol Aksi (Tengah) -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white p-3.5 rounded-2xl border border-amber-100 shadow-sm gap-3">
    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
      <i class="fa-solid fa-sliders text-amber-500"></i>
      <span>Pengaturan & Aksi Periodik:</span>
    </div>
    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
      <!-- Tombol Reset Poin Semester -->
      <button onclick="openResetSemesterModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
        <i class="fa-solid fa-arrows-rotate text-xs"></i> Reset Poin Semester (250)
      </button>

      <!-- Tombol Kenaikan Kelas -->
      <a href="{{ route('guru.naik-kelas') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
        <i class="fa-solid fa-angles-up text-xs"></i> Kenaikan Kelas & Pemindahan
      </a>

      <!-- Kelola Ruangan -->
      <button onclick="openClassListModal()" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300/80 rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
        <i class="fa-solid fa-list-check text-xs"></i> Kelola Ruangan Kelas
      </button>
    </div>
  </div>

  <!-- 3. Pencarian -->
  <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center">
    <div class="relative w-full sm:w-80">
      <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
      <input type="text" id="searchInput" onkeyup="searchStudent()" placeholder="Cari NISN atau Nama Siswa..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition">
    </div>
  </div>

  <!-- Container Tabel & Sub-Filter Rombel -->
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-3">
      <div class="flex items-center gap-2 overflow-x-auto" id="subClassContainer">
        <span class="text-xs font-medium text-gray-400 mr-1 shrink-0">Pilih Rombel:</span>
      </div>

      <button onclick="openModal('add')" class="shrink-0 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium text-xs rounded-xl shadow-md flex items-center justify-center gap-2 transition">
        <i class="fa-solid fa-plus text-sm"></i> Tambah Siswa Baru
      </button>
    </div>

    <!-- Table Data Siswa -->
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
          <tr class="hover:bg-gray-50/50" data-tingkat="1" data-kelas="1-A">
            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">0012345601</td>
            <td class="py-3.5 px-4 font-semibold text-gray-700">Ahmad Ibrahim</td>
            <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">1-A</span></td>
            <td class="py-3.5 px-4 text-gray-600">Laki-laki</td>
            <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">260</td>
            <td class="py-3.5 px-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <button onclick="openModal('edit', '0012345601', 'Ahmad Ibrahim', '1-A', 'Laki-laki', 260)" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                <button onclick="deleteStudent(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"><i class="fa-solid fa-trash text-xs"></i></button>
              </div>
            </td>
          </tr>
          <tr class="hover:bg-gray-50/50" data-tingkat="4" data-kelas="4-A">
            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">0012345602</td>
            <td class="py-3.5 px-4 font-semibold text-gray-700">Siti Maryam</td>
            <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">4-A</span></td>
            <td class="py-3.5 px-4 text-gray-600">Perempuan</td>
            <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">255</td>
            <td class="py-3.5 px-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <button onclick="openModal('edit', '0012345602', 'Siti Maryam', '4-A', 'Perempuan', 255)" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                <button onclick="deleteStudent(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"><i class="fa-solid fa-trash text-xs"></i></button>
              </div>
            </td>
          </tr>
          <tr class="hover:bg-gray-50/50" data-tingkat="4" data-kelas="4-B">
            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">0012345603</td>
            <td class="py-3.5 px-4 font-semibold text-gray-700">Muhammad Hasan</td>
            <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">4-B</span></td>
            <td class="py-3.5 px-4 text-gray-600">Laki-laki</td>
            <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">270</td>
            <td class="py-3.5 px-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <button onclick="openModal('edit', '0012345603', 'Muhammad Hasan', '4-B', 'Laki-laki', 270)" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
                <button onclick="deleteStudent(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"><i class="fa-solid fa-trash text-xs"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Kelola Ruangan Kelas -->
  <div id="classListModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden transform transition-all">
      <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
        <div>
          <h3 class="font-bold text-base">Kelola Ruangan Kelas</h3>
          <p class="text-[11px] text-amber-100">Daftar ruangan kelas dan opsi penambahan/penghapusan</p>
        </div>
        <button onclick="closeClassListModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto" id="classListContent"></div>

      <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
        <button onclick="promptAddNewRoom()" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-sm">
          <i class="fa-solid fa-plus"></i> Tambah Ruangan Baru
        </button>
        <button onclick="closeClassListModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-medium transition">Tutup</button>
      </div>
    </div>
  </div>

  <!-- Modal Pop-up (Tambah / Edit Siswa) -->
  <div id="studentModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden transform transition-all">
      <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 text-white flex justify-between items-center">
        <h3 id="modalTitle" class="font-bold text-base">Tambah Siswa Baru</h3>
        <button onclick="closeModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <form id="studentForm" onsubmit="saveStudent(event)" class="p-6 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">NISN</label>
          <input type="text" id="inputNisn" required placeholder="0012345678" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap Siswa</label>
          <input type="text" id="inputNama" required placeholder="Contoh: Bilal Bin Rabah" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Kelas</label>
            <select id="selectKelas" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500"></select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
            <select id="selectGender" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500">
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Poin Awal Siswa</label>
          <div class="relative">
            <input type="number" id="inputPoin" required value="250" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-emerald-600 focus:outline-none focus:border-amber-500">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-gray-400">Poin Default</span>
          </div>
          <p class="text-[10px] text-gray-400 mt-1">*Poin standar siswa baru saat pendaftaran adalah 250.</p>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition">Batal</button>
          <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-medium hover:bg-amber-600 transition">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Reset Poin Semester -->
  <div id="resetSemesterModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden transform transition-all">
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
        <button onclick="closeResetSemesterModal()" class="text-white/80 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="p-6 space-y-4">
        <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-1">
          <p class="font-bold flex items-center gap-1.5 text-blue-800">
            <i class="fa-solid fa-circle-info"></i> Ketentuan Reset Semester:
          </p>
          <ul class="list-disc list-inside space-y-0.5 text-[11px] text-blue-700 pl-1">
            <li>Ruangan & kelas siswa <strong>TIDAK BERUBAH</strong>.</li>
            <li>Seluruh poin siswa target akan di-reset kembali ke <strong>250 Poin</strong>.</li>
          </ul>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Target Siswa yang di-Reset</label>
          <select id="resetTargetScope" class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-blue-500">
            <option value="all">Semua Siswa (Seluruh Kelas 1 - 6)</option>
            <option value="current">Hanya Siswa di Tampilan/Filter Saat Ini</option>
          </select>
        </div>

        <div class="pt-2 border-t">
          <label class="block text-xs font-semibold text-gray-700 mb-1">
            Ketik <span class="text-rose-600 font-bold select-all">RESET</span> untuk mengonfirmasi:
          </label>
          <input type="text" id="confirmResetInput" placeholder="Ketik RESET di sini..." class="w-full px-3.5 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 font-bold tracking-widest text-gray-700">
        </div>
      </div>

      <div class="p-4 bg-gray-50 border-t flex justify-end gap-2">
        <button onclick="closeResetSemesterModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-medium transition">
          Batal
        </button>
        <button onclick="executeResetSemester()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
          <i class="fa-solid fa-check-double"></i> Jalankan Reset Poin
        </button>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  function openResetSemesterModal() {
    document.getElementById('confirmResetInput').value = '';
    const modal = document.getElementById('resetSemesterModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeResetSemesterModal() {
    const modal = document.getElementById('resetSemesterModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function executeResetSemester() {
    const inputConfirm = document.getElementById('confirmResetInput').value.trim();
    const targetScope = document.getElementById('resetTargetScope').value;

    if (inputConfirm !== 'RESET') {
      alert('Kata konfirmasi salah! Silakan ketik RESET dengan huruf kapital.');
      return;
    }

    const rows = document.querySelectorAll('#studentTable tbody tr');
    let resetCount = 0;

    rows.forEach(row => {
      if (targetScope === 'all' || row.style.display !== 'none') {
        row.cells[4].textContent = '250';
        resetCount++;
      }
    });

    closeResetSemesterModal();
    alert(`Berhasil! Poin sebanyak ${resetCount} siswa telah di-reset kembali ke 250 Poin untuk Semester Baru.`);
  }

  let activeGrade = 'all';
  let activeSubClass = 'all';

  const classStructure = {
    '1': ['A', 'B'],
    '2': ['A', 'B'],
    '3': ['A', 'B'],
    '4': ['A', 'B'],
    '5': ['A', 'B', 'C'],
    '6': ['A', 'B', 'C']
  };

  document.addEventListener('DOMContentLoaded', () => {
    renderSubClasses('all');
    updateSelectKelasOptions();
    applyFilters();
  });

  function selectGrade(grade, btn) {
    activeGrade = grade;
    activeSubClass = 'all';

    document.querySelectorAll('.grade-btn').forEach(b => {
      b.className = 'grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm transition';
    });
    btn.className = 'grade-btn px-4 py-2 rounded-xl text-xs font-semibold bg-amber-500 text-white shadow-sm transition';

    renderSubClasses(grade);
    applyFilters();
  }

  function renderSubClasses(grade) {
    const container = document.getElementById('subClassContainer');
    container.innerHTML = '<span class="text-xs font-medium text-gray-400 mr-1 shrink-0">Pilih Rombel:</span>';

    if (grade === 'all') {
      const button = document.createElement('button');
      button.textContent = 'Semua Kelas';
      button.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 transition shrink-0';
      container.appendChild(button);
      return;
    }

    const allBtn = document.createElement('button');
    allBtn.onclick = () => selectSubClass('all', allBtn);
    allBtn.textContent = `Semua (${grade})`;
    allBtn.className = `sub-btn px-3 py-1.5 rounded-lg text-xs font-medium transition shrink-0 ${activeSubClass === 'all' ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'}`;
    container.appendChild(allBtn);

    const currentList = classStructure[grade] || [];
    currentList.forEach(letter => {
      const classVal = `${grade}-${letter}`;
      const button = document.createElement('button');
      button.onclick = () => selectSubClass(classVal, button);
      button.textContent = classVal;
      button.className = `sub-btn px-3 py-1.5 rounded-lg text-xs font-medium transition shrink-0 ${activeSubClass === classVal ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'}`;
      container.appendChild(button);
    });
  }

  function openClassListModal() {
    renderClassListModal();
    const modal = document.getElementById('classListModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeClassListModal() {
    const modal = document.getElementById('classListModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function renderClassListModal() {
    const content = document.getElementById('classListContent');
    content.innerHTML = '';

    Object.keys(classStructure).forEach(g => {
      const section = document.createElement('div');
      section.className = 'border-b border-gray-100 pb-3 last:border-0';

      const title = document.createElement('h4');
      title.className = 'text-xs font-bold text-amber-600 uppercase tracking-wider mb-2';
      title.textContent = `Tingkat Kelas ${g}`;
      section.appendChild(title);

      const grid = document.createElement('div');
      grid.className = 'flex flex-wrap gap-2';

      const list = classStructure[g] || [];
      if (list.length === 0) {
        grid.innerHTML = '<span class="text-xs text-gray-400 italic">Belum ada ruangan</span>';
      } else {
        list.forEach((letter) => {
          const chip = document.createElement('div');
          chip.className = 'flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200/60 text-amber-900 rounded-xl text-xs font-medium shadow-sm';
          chip.innerHTML = `
            <span>Kelas ${g}-${letter}</span>
            <button onclick="removeClass('${g}', '${letter}')" class="text-rose-500 hover:text-rose-700 ml-1 transition" title="Hapus Kelas ${g}-${letter}">
              <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
          `;
          grid.appendChild(chip);
        });
      }

      section.appendChild(grid);
      content.appendChild(section);
    });
  }

  function removeClass(grade, letter) {
    if (confirm(`Apakah Anda yakin ingin menghapus ruangan Kelas ${grade}-${letter}?`)) {
      classStructure[grade] = classStructure[grade].filter(item => item !== letter);
      renderClassListModal();
      renderSubClasses(activeGrade);
      updateSelectKelasOptions();
    }
  }

  function promptAddNewRoom() {
    const targetGrade = prompt('Masukkan Tingkat Kelas (1-6):', activeGrade !== 'all' ? activeGrade : '1');
    if (!targetGrade || !classStructure[targetGrade]) {
      if (targetGrade) alert('Tingkat kelas tidak valid!');
      return;
    }

    const defaultLetter = String.fromCharCode(65 + (classStructure[targetGrade]?.length || 0));
    const roomLetter = prompt(`Masukkan nama rombel/ruangan baru untuk Kelas ${targetGrade} (misal: C):`, defaultLetter);

    if (roomLetter && roomLetter.trim() !== '') {
      const cleanLetter = roomLetter.trim().toUpperCase();
      if (!classStructure[targetGrade].includes(cleanLetter)) {
        classStructure[targetGrade].push(cleanLetter);
        renderClassListModal();
        renderSubClasses(activeGrade);
        updateSelectKelasOptions();
        alert(`Ruangan Kelas ${targetGrade}-${cleanLetter} berhasil ditambahkan!`);
      } else {
        alert(`Ruangan Kelas ${targetGrade}-${cleanLetter} sudah ada!`);
      }
    }
  }

  function updateSelectKelasOptions() {
    const select = document.getElementById('selectKelas');
    select.innerHTML = '';

    Object.keys(classStructure).forEach(g => {
      classStructure[g].forEach(letter => {
        const val = `${g}-${letter}`;
        const option = document.createElement('option');
        option.value = val;
        option.textContent = val;
        select.appendChild(option);
      });
    });
  }

  function selectSubClass(subClass, btn) {
    activeSubClass = subClass;

    document.querySelectorAll('.sub-btn').forEach(b => {
      b.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 hover:bg-gray-100 transition shrink-0';
    });
    btn.className = 'sub-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 transition shrink-0';

    applyFilters();
  }

  function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#studentTable tbody tr');

    rows.forEach(row => {
      const nisn = row.cells[0].textContent.toLowerCase();
      const nama = row.cells[1].textContent.toLowerCase();
      const rowGrade = row.getAttribute('data-tingkat');
      const rowClass = row.getAttribute('data-kelas');

      const matchGrade = (activeGrade === 'all' || rowGrade === activeGrade);
      const matchSubClass = (activeSubClass === 'all' || rowClass === activeSubClass);
      const matchSearch = (nisn.includes(search) || nama.includes(search));

      if (matchGrade && matchSubClass && matchSearch) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function searchStudent() {
    applyFilters();
  }

  function openModal(mode, nisn = '', nama = '', kelas = '', gender = 'Laki-laki', poin = 250) {
    const modal = document.getElementById('studentModal');
    const title = document.getElementById('modalTitle');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    updateSelectKelasOptions();

    if (mode === 'add') {
      title.textContent = 'Tambah Siswa Baru';
      document.getElementById('inputNisn').value = '';
      document.getElementById('inputNama').value = '';
      document.getElementById('selectGender').value = 'Laki-laki';
      document.getElementById('inputPoin').value = '250';

      let defaultClass = '1-A';
      if (activeSubClass !== 'all') {
        defaultClass = activeSubClass;
      } else if (activeGrade !== 'all') {
        const firstLetter = classStructure[activeGrade]?.[0] || 'A';
        defaultClass = `${activeGrade}-${firstLetter}`;
      }
      document.getElementById('selectKelas').value = defaultClass;

    } else {
      title.textContent = 'Edit Data Siswa';
      document.getElementById('inputNisn').value = nisn;
      document.getElementById('inputNama').value = nama;
      document.getElementById('selectKelas').value = kelas;
      document.getElementById('selectGender').value = gender;
      document.getElementById('inputPoin').value = poin;
    }
  }

  function closeModal() {
    const modal = document.getElementById('studentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function saveStudent(e) {
    e.preventDefault();
    const nisn = document.getElementById('inputNisn').value;
    const nama = document.getElementById('inputNama').value;
    const kelas = document.getElementById('selectKelas').value;
    const gender = document.getElementById('selectGender').value;
    const poin = document.getElementById('inputPoin').value;
    const tingkat = kelas.split('-')[0];

    const tbody = document.querySelector('#studentTable tbody');

    const newRow = document.createElement('tr');
    newRow.className = "hover:bg-gray-50/50";
    newRow.setAttribute('data-tingkat', tingkat);
    newRow.setAttribute('data-kelas', kelas);

    newRow.innerHTML = `
      <td class="py-3.5 px-4 font-mono text-xs text-gray-500">${nisn}</td>
      <td class="py-3.5 px-4 font-semibold text-gray-700">${nama}</td>
      <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">${kelas}</span></td>
      <td class="py-3.5 px-4 text-gray-600">${gender}</td>
      <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">${poin}</td>
      <td class="py-3.5 px-4 text-center">
        <div class="flex items-center justify-center gap-2">
          <button onclick="openModal('edit', '${nisn}', '${nama}', '${kelas}', '${gender}', ${poin})" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition"><i class="fa-solid fa-pen-to-square text-xs"></i></button>
          <button onclick="deleteStudent(this)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"><i class="fa-solid fa-trash text-xs"></i></button>
        </div>
      </td>
    `;

    tbody.prepend(newRow);
    applyFilters();
    alert(`Siswa ${nama} berhasil disimpan!`);
    closeModal();
  }

  function deleteStudent(button) {
    if (confirm('Hapus data siswa ini?')) {
      button.closest('tr')?.remove();
    }
  }
</script>
@endpush
