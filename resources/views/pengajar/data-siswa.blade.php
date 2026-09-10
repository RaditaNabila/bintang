@extends('layouts.app')

@section('title', 'Data Siswa - Pengajar')
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

    <a href="{{ route('pengajar.data-siswa', array_filter(['search' => $currentSearch])) }}"
       class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold
       {{ $currentTingkat === 'all'
            ? 'bg-amber-500 text-white shadow-sm'
            : 'bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm' }}
       transition shrink-0">
        Semua Kelas
    </a>

    @for($i = 1; $i <= 6; $i++)

        <a href="{{ route('pengajar.data-siswa', array_filter([
                'tingkat' => $i,
                'search' => $currentSearch
            ])) }}"
           class="grade-btn px-4 py-2 rounded-xl text-xs font-semibold
           {{ (string)$currentTingkat === (string)$i
                ? 'bg-amber-500 text-white shadow-sm'
                : 'bg-white text-gray-600 hover:bg-amber-100 border border-gray-200 shadow-sm' }}
           transition shrink-0">
            Kelas {{ $i }}
        </a>

    @endfor

</div>


<!-- Pencarian -->
<form method="GET"
      action="{{ route('pengajar.data-siswa') }}"
      class="mt-4 bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3">

    @if(request('tingkat'))
        <input type="hidden"
               name="tingkat"
               value="{{ request('tingkat') }}">
    @endif

    @if(request('kelas_id'))
        <input type="hidden"
               name="kelas_id"
               value="{{ request('kelas_id') }}">
    @endif

    <div class="relative w-full sm:w-80">

        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

        <input
            type="text"
            name="search"
            value="{{ $currentSearch }}"
            placeholder="Cari NISN atau Nama Siswa..."
            class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-amber-500 focus:bg-white transition"
        >

    </div>

    <button
        type="submit"
        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold transition">
        Cari
    </button>

</form>


<!-- Tabel Siswa -->
<div class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-3">

        <!-- Filter Rombel -->
        <div class="flex items-center gap-2 overflow-x-auto"
             id="subClassContainer">

            @if($currentTingkat !== 'all')

                <span class="text-xs font-medium text-gray-400 mr-1 shrink-0">
                    Pilih Rombel:
                </span>

                <a
                    href="{{ route('pengajar.data-siswa', array_filter([
                        'tingkat' => $currentTingkat,
                        'search' => $currentSearch
                    ])) }}"
                    class="sub-btn px-3 py-1.5 rounded-lg text-xs
                    {{ $currentKelasId === 'all'
                        ? 'font-bold bg-amber-100 text-amber-800'
                        : 'font-medium bg-gray-50 text-gray-600 hover:bg-gray-100' }}
                    transition shrink-0">

                    Semua (Kelas {{ $currentTingkat }})

                </a>


                @foreach($kelas as $item)

                    @if((string)$item->tingkat === (string)$currentTingkat)

                        @php
                            $namaDisplay = \Illuminate\Support\Str::startsWith(
                                strtolower($item->nama_kelas),
                                'kelas'
                            )
                                ? $item->nama_kelas
                                : 'Kelas ' . $item->nama_kelas;
                        @endphp

                        <a
                            href="{{ route('pengajar.data-siswa', array_filter([
                                'tingkat' => $item->tingkat,
                                'kelas_id' => $item->id,
                                'search' => $currentSearch
                            ])) }}"
                            class="sub-btn px-3 py-1.5 rounded-lg text-xs
                            {{ (string)$currentKelasId === (string)$item->id
                                ? 'font-bold bg-amber-100 text-amber-800'
                                : 'font-medium bg-gray-50 text-gray-600 hover:bg-gray-100' }}
                            transition shrink-0">

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

    </div>


    <!-- Tabel -->
    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse text-sm">

            <thead>

                <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase font-medium">

                    <th class="py-3 px-4 text-center">
                        No
                    </th>

                    <th class="py-3 px-4">
                        NISN
                    </th>

                    <th class="py-3 px-4">
                        Nama Siswa
                    </th>

                    <th class="py-3 px-4">
                        Kelas
                    </th>

                    <th class="py-3 px-4">
                        Jenis Kelamin
                    </th>

                    <th class="py-3 px-4 text-center">
                        Total Poin
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-50">

                @forelse($siswa as $index => $item)

                    @php

                        $namaKelasTabel = $item->kelas?->nama_kelas;

                        if (
                            $namaKelasTabel &&
                            !\Illuminate\Support\Str::startsWith(
                                strtolower($namaKelasTabel),
                                'kelas'
                            )
                        ) {
                            $namaKelasTabel = 'Kelas ' . $namaKelasTabel;
                        }

                    @endphp


                    <tr class="hover:bg-gray-50/50">

                        <td class="py-3.5 px-4 text-center font-mono text-xs text-gray-400">

                            {{ $loop->iteration + ($siswa->currentPage() - 1) * $siswa->perPage() }}

                        </td>


                        <td class="py-3.5 px-4 font-mono text-xs text-gray-500">

                            {{ $item->nisn }}

                        </td>


                        <td class="py-3.5 px-4 font-semibold text-gray-700">

                            {{ $item->nama_lengkap }}

                        </td>


                        <td class="py-3.5 px-4">

                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">

                                {{ $namaKelasTabel ?? '-' }}

                            </span>

                        </td>


                        <td class="py-3.5 px-4 text-gray-600">

                            {{ $item->jenis_kelamin }}

                        </td>


                        <td class="py-3.5 px-4 font-bold text-emerald-600 text-center">

                            {{ $item->poin_saat_ini }}

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="py-8 text-center text-gray-400">

                            Belum ada data siswa.

                        </td>

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

@endsection
