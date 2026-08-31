<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\OrangTuaSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrangTuaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data user yang sedang login via Auth Laravel
        $orangTua = Auth::user();

        // Jika belum login atau bukan orang tua / wali
        if (!$orangTua || !in_array($orangTua->peran, ['orang_tua', 'wali'])) {
            return redirect()
                ->route('welcome')
                ->with('error', 'Silakan login sebagai orang tua/wali terlebih dahulu.');
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA ANTA KUNCI SISWA_ID MILIK WALI YANG LOGIN
        |--------------------------------------------------------------------------
        */
        $siswaIds = OrangTuaSiswa::where('pengguna_id', $orangTua->id)
            ->pluck('siswa_id');

        // Tarik data siswa berdasarkan ID yang benar dari tabel relasi
        $anak = Siswa::with(['kelas', 'transaksiPoin'])
            ->whereIn('id', $siswaIds)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TENTUKAN ANAK YANG DIPILIH
        |--------------------------------------------------------------------------
        */
        $siswaId = $request->get('siswa_id');

        if ($siswaId) {
            // Pastikan siswa memang anak dari wali yang sedang login
            $siswa = $anak->firstWhere('id', $siswaId);

            if (!$siswa) {
                return redirect()
                    ->route('wali')
                    ->with('error', 'Data siswa tidak ditemukan.');
            }
        } else {
            // Tampilkan anak pertama dari hasil query yang valid (Fajar)
            $siswa = $anak->first();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA WALI BELUM MEMILIKI ANAK / BELUM DIRELASIKAN
        |--------------------------------------------------------------------------
        */
        if (!$siswa) {
            return view('wali', [
                'orangTua'           => $orangTua,
                'anak'               => collect(),
                'siswa'              => null,
                'totalScore'         => 250,
                'totalPrestasi'      => 0,
                'jumlahPrestasi'     => 0,
                'totalPelanggaran'   => 0,
                'jumlahPelanggaran'  => 0,
                'riwayatPoin'        => collect(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL TRANSAKSI POIN SISWA
        |--------------------------------------------------------------------------
        */
        $riwayatPoin = $siswa->transaksiPoin
            ? $siswa->transaksiPoin->sortByDesc('tanggal_transaksi')->values()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL PRESTASI DAN PELANGGARAN
        |--------------------------------------------------------------------------
        */
        $totalPrestasi = $riwayatPoin
            ->where('jenis', 'apresiasi')
            ->sum('poin');

        $totalPelanggaran = $riwayatPoin
            ->where('jenis', 'pelanggaran')
            ->sum('poin');

        $jumlahPrestasi = $riwayatPoin
            ->where('jenis', 'apresiasi')
            ->count();

        $jumlahPelanggaran = $riwayatPoin
            ->where('jenis', 'pelanggaran')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | HITUNG POIN AKHIR (Awal 250 + Prestasi - Pelanggaran)
        |--------------------------------------------------------------------------
        */
        $poinAwal = 250;
        $totalScore = $poinAwal + $totalPrestasi - $totalPelanggaran;

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('wali', [
            'orangTua'           => $orangTua,
            'anak'               => $anak,
            'siswa'              => $siswa,
            'totalScore'         => $totalScore,
            'totalPrestasi'      => $totalPrestasi,
            'jumlahPrestasi'     => $jumlahPrestasi,
            'totalPelanggaran'   => $totalPelanggaran,
            'jumlahPelanggaran'  => $jumlahPelanggaran,
            'riwayatPoin'        => $riwayatPoin,
        ]);
    }
}
