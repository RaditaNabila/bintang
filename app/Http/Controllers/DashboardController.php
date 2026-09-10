<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\TransaksiPoin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TANGGAL
        |--------------------------------------------------------------------------
        */
        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | TOTAL SISWA & STATISTIK BULAN INI
        |--------------------------------------------------------------------------
        */
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        $poinApresiasi = TransaksiPoin::where('jenis', 'apresiasi')
            ->whereBetween('tanggal_transaksi', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString()
            ])
            ->sum('poin');

        $totalPelanggaran = TransaksiPoin::where('jenis', 'pelanggaran')
            ->whereBetween('tanggal_transaksi', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString()
            ])
            ->count();

        $pencatatanHariIni = TransaksiPoin::whereDate(
            'tanggal_transaksi',
            Carbon::today()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | TREN 7 HARI TERAKHIR
        |--------------------------------------------------------------------------
        */
        $trendLabels = [];
        $trendApresiasi = [];
        $trendPelanggaran = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);

            $trendLabels[] = $tanggal->translatedFormat('l');

            $trendApresiasi[] = TransaksiPoin::where('jenis', 'apresiasi')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->sum('poin');

            $trendPelanggaran[] = TransaksiPoin::where('jenis', 'pelanggaran')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI KATEGORI BULAN INI
        |--------------------------------------------------------------------------
        */
        $kategoriDisiplin = TransaksiPoin::where('kategori_id', 1)
            ->whereBetween('tanggal_transaksi', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString()
            ])
            ->count();

        $kategoriIbadah = TransaksiPoin::where('kategori_id', 2)
            ->whereBetween('tanggal_transaksi', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString()
            ])
            ->count();

        $kategoriPelanggaran = TransaksiPoin::where('kategori_id', 3)
            ->whereBetween('tanggal_transaksi', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString()
            ])
            ->count();

        $kategoriAkademik = 0;

        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */
        $aktivitasTerbaru = TransaksiPoin::with([
            'siswa.kelas',
            'kategori',
            'aturanPoin'
        ])
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        |
        | Admin / Kepala Sekolah:
        | resources/views/guru/dashboard.blade.php
        |
        | Guru / Pengajar:
        | resources/views/pengajar/dashboard.blade.php
        |
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->peran === 'guru') {
            return view('pengajar.dashboard', compact(
                'totalSiswa',
                'poinApresiasi',
                'totalPelanggaran',
                'pencatatanHariIni',
                'trendLabels',
                'trendApresiasi',
                'trendPelanggaran',
                'kategoriIbadah',
                'kategoriDisiplin',
                'kategoriAkademik',
                'kategoriPelanggaran',
                'aktivitasTerbaru'
            ));
        }

        // Admin / Kepala Sekolah
        return view('guru.dashboard', compact(
            'totalSiswa',
            'poinApresiasi',
            'totalPelanggaran',
            'pencatatanHariIni',
            'trendLabels',
            'trendApresiasi',
            'trendPelanggaran',
            'kategoriIbadah',
            'kategoriDisiplin',
            'kategoriAkademik',
            'kategoriPelanggaran',
            'aktivitasTerbaru'
        ));
    }
}
