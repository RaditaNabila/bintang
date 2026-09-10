@extends('layouts.app')

@section('title', 'Laporan Poin - Bintang Poin')
@section('page_title', 'Laporan Akhir Poin Siswa')
@section('page_description', 'Unduh rekapitulasi sisa poin serta predikat karakter siswa per kelas')

@section('content')
<div class="space-y-6">

    <!-- Filter Controls Bar -->
    <form method="GET" action="{{ route('pengajar.laporan') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center gap-3">

        <!-- Input Pencarian -->
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari NIS, NISN, atau Nama Siswa..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
            >
        </div>

        <!-- Filter Kelas -->
        <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 border border-gray-200 rounded-xl">
            <i class="fa-solid fa-filter text-amber-500 text-xs"></i>
            <select
                name="kelas_id"
                onchange="this.form.submit()"
                class="bg-transparent text-xs font-semibold text-gray-700 focus:outline-none cursor-pointer"
            >
                <option value="all" {{ request('kelas_id') == 'all' ? 'selected' : '' }}>
                    Semua Kelas
                </option>

                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        Kelas {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        <button
            type="submit"
            class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition"
        >
            Cari
        </button>

        @if(request('search') || request('kelas_id') != 'all')
            <a
                href="{{ route('pengajar.laporan') }}"
                class="text-xs text-gray-500 hover:text-gray-700 font-medium px-2 py-2"
            >
                Reset Filter
            </a>
        @endif
    </form>


    <!-- TABEL REKAP SISA POIN -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">

        <!-- Header Tabel + Tombol Export Excel -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">

            <div>
                <h3 class="text-base font-bold text-gray-800">
                    Daftar Sisa Poin Siswa
                </h3>

                <p class="text-xs text-gray-500">
                    Rekap saldo akhir poin kedisiplinan dan karakter siswa
                </p>
            </div>

            <div class="flex items-center gap-3">

                <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-full border border-amber-200">
                    Total {{ $siswa->total() }} Siswa
                </span>

                <!-- Export Excel -->
                @if(Route::has('guru.laporan.export'))
                    <a
                        href="{{ route('guru.laporan.export', request()->all()) }}"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl flex items-center gap-2 shadow-sm transition"
                    >
                        <i class="fa-solid fa-file-excel text-sm"></i>
                        Export Excel
                    </a>
                @endif

            </div>
        </div>


        <!-- Content Tabel -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">

                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">

                        <th class="py-3.5 px-4 w-12">
                            No
                        </th>

                        <th class="py-3.5 px-4">
                            NIS / NISN
                        </th>

                        <th class="py-3.5 px-4">
                            Nama Siswa
                        </th>

                        <th class="py-3.5 px-4">
                            Kelas
                        </th>

                        <th class="py-3.5 px-4 text-center">
                            Sisa Poin Aktif
                        </th>

                        <th class="py-3.5 px-4 text-center">
                            Predikat Karakter
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-50">

                    @forelse($siswa as $index => $item)

                        @php
                            $poin = $item->poin_saat_ini ?? 0;

                            // Logika Predikat Karakter
                            if ($poin >= 250) {
                                $predikat = 'Sangat Baik';
                                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
                                $poinBadge = 'bg-amber-50 text-amber-600 border-amber-200/60';

                            } elseif ($poin >= 100) {
                                $predikat = 'Baik';
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200/80';
                                $poinBadge = 'bg-slate-100 text-slate-700 border-slate-200';

                            } else {
                                $predikat = 'Perlu Pembinaan';
                                $badgeColor = 'bg-rose-50 text-rose-700 border-rose-200/80';
                                $poinBadge = 'bg-rose-50 text-rose-600 border-rose-200';
                            }
                        @endphp


                        <tr class="hover:bg-gray-50/50">

                            <td class="py-3.5 px-4 text-xs font-bold text-gray-400">
                                {{ $siswa->firstItem() + $index }}
                            </td>

                            <td class="py-3.5 px-4 font-mono text-xs text-gray-500">
                                {{ $item->nis ?? $item->nisn ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4 font-bold text-gray-800">
                                {{ $item->nama_lengkap }}
                            </td>

                            <td class="py-3.5 px-4 text-gray-500 font-semibold">
                                {{ $item->kelas->nama_kelas ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-extrabold text-base border {{ $poinBadge }}">
                                    <i class="fa-solid fa-star text-xs"></i>
                                    {{ $poin }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-3 py-1 border rounded-full text-[11px] font-bold {{ $badgeColor }}">
                                    {{ $predikat }}
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 text-xs">

                                <i class="fa-solid fa-folder-open text-2xl mb-2 block"></i>

                                Data siswa tidak ditemukan.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>


        <!-- Pagination -->
        @if($siswa->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $siswa->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
