@extends('layouts.app')

@section('title', 'Dashboard - Bintang Poin')
@section('page_title', 'Dashboard Utama')
@section('page_description', 'Ringkasan statistik dan aktivitas poin siswa')

@section('content')

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500 font-medium">Total Siswa</p>
        <h3 class="text-2xl font-bold text-gray-800 mt-1">
            {{ number_format($totalSiswa) }}
        </h3>
      </div>
      <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-xl">
        <i class="fa-solid fa-users"></i>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-emerald-100 flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500 font-medium">Poin Apresiasi (Bulan Ini)</p>
        <h3 class="text-2xl font-bold text-emerald-600 mt-1">
            +{{ number_format($poinApresiasi) }}
        </h3>
      </div>
      <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
        <i class="fa-solid fa-star"></i>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-rose-100 flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500 font-medium">Pelanggaran (Bulan Ini)</p>
        <h3 class="text-2xl font-bold text-rose-600 mt-1">
          {{ number_format($totalPelanggaran) }}
      </h3>
      </div>
      <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center text-xl">
        <i class="fa-solid fa-circle-exclamation"></i>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500 font-medium">Pencatatan Hari Ini</p>
        <h3 class="text-2xl font-bold text-orange-500 mt-1">
            {{ number_format($pencatatanHariIni) }}
        </h3>
      </div>
      <div class="w-12 h-12 bg-orange-100 text-orange-500 rounded-xl flex items-center justify-center text-xl">
        <i class="fa-solid fa-pen-to-square"></i>
      </div>
    </div>

  </div>

  <!-- Section Statistik / Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
      <div class="flex justify-between items-center mb-4">
        <div>
          <h3 class="font-bold text-gray-800 text-base">Tren Poin Siswa</h3>
          <p class="text-xs text-gray-400">Perbandingan apresiasi vs pelanggaran minggu ini</p>
        </div>
        <span class="text-xs bg-amber-50 text-amber-600 font-medium px-2.5 py-1 rounded-lg border border-amber-100">7 Hari Terakhir</span>
      </div>
      <div class="h-64">
        <canvas id="trendChart"></canvas>
      </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
      <div class="mb-4">
        <h3 class="font-bold text-gray-800 text-base">Distribusi Kategori</h3>
        <p class="text-xs text-gray-400">Persentase jenis catatan bulan ini</p>
      </div>
      <div class="h-64 flex items-center justify-center">
        <canvas id="categoryChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Activity Table -->
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
    <div class="flex justify-between items-center mb-5">
      <h3 class="font-bold text-gray-800 text-base">Aktivitas Poin Terbaru</h3>
      <a href="#" class="text-xs font-semibold text-orange-500 hover:underline">Lihat Semua</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-sm">
        <thead>
          <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">
            <th class="py-3 px-4">Siswa</th>
            <th class="py-3 px-4">Kelas</th>
            <th class="py-3 px-4">Kategori</th>
            <th class="py-3 px-4">Keterangan</th>
            <th class="py-3 px-4 text-center">Poin</th>
            <th class="py-3 px-4">Tanggal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">

            @forelse($aktivitasTerbaru as $aktivitas)

                <tr class="hover:bg-gray-50/50">

                    {{-- Siswa --}}
                    <td class="py-3.5 px-4 font-semibold text-gray-700">
                        {{ $aktivitas->siswa->nama_lengkap ?? '-' }}
                    </td>

                    {{-- Kelas --}}
                    <td class="py-3.5 px-4 text-gray-500">
                        {{ $aktivitas->siswa->kelas->nama_kelas ?? '-' }}
                    </td>

                    {{-- Kategori --}}
                    <td class="py-3.5 px-4">
                        @if($aktivitas->jenis === 'apresiasi')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                Apresiasi
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-medium">
                                Pelanggaran
                            </span>
                        @endif
                    </td>

                    {{-- Keterangan --}}
                    <td class="py-3.5 px-4 text-gray-600">
                        {{ $aktivitas->keterangan }}
                    </td>

                    {{-- Poin --}}
                    <td class="py-3.5 px-4 font-bold text-center {{ $aktivitas->jenis === 'apresiasi' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $aktivitas->jenis === 'apresiasi' ? '+' : '-' }}{{ abs($aktivitas->poin) }}
                    </td>

                    {{-- Tanggal --}}
                    <td class="py-3.5 px-4 text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($aktivitas->tanggal_transaksi)->translatedFormat('d M Y') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        Belum ada aktivitas poin.
                    </td>
                </tr>

            @endforelse

        </tbody>
      </table>
    </div>
  </div>

@endsection

@push('scripts')
<script>
    // ==========================================
    // TANGGAL REAL-TIME
    // ==========================================
    const currentDate = document.getElementById('currentDate');
    if (currentDate) {
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        currentDate.textContent = new Date().toLocaleDateString('id-ID', options);
    }

    // ==========================================
    // LINE CHART - TREN POIN
    // ==========================================
    const trendCanvas = document.getElementById('trendChart');
    if (trendCanvas) {
        const ctxTrend = trendCanvas.getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        label: 'Apresiasi',
                        data: @json($trendApresiasi),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pelanggaran',
                        data: @json($trendPelanggaran),
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ==========================================
    // DOUGHNUT CHART - DISTRIBUSI KATEGORI
    // ==========================================
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const ctxCategory = categoryCanvas.getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: [
                    'Pengurangan Harian',
                    'Penambahan Poin',
                    'Pengurangan Khusus'
                ],
                datasets: [{
                    data: [
                        {{ $kategoriDisiplin }},
                        {{ $kategoriIbadah }},
                        {{ $kategoriPelanggaran }}
                    ],
                    backgroundColor: [
                        '#f59e0b',
                        '#10b981',
                        '#f43f5e'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>
@endpush