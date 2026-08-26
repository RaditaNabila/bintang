<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\OrangTuaSiswa;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil ID akun orang tua dari session
        $penggunaId = session('pengguna_id');

        // Jika belum login
        if (!$penggunaId) {
            return redirect()
                ->route('pilih-peran')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil akun orang tua
        $orangTua = Pengguna::where('id', $penggunaId)
            ->where('peran', 'orang_tua')
            ->where('status', 'aktif')
            ->first();

        // Jika akun tidak ditemukan
        if (!$orangTua) {
            return redirect()
                ->route('pilih-peran')
                ->with('error', 'Akun orang tua tidak ditemukan atau tidak aktif.');
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA ANAK YANG TERHUBUNG DENGAN WALI
        |--------------------------------------------------------------------------
        */

        $relasi = OrangTuaSiswa::with([
            'siswa.kelas',
            'siswa.transaksiPoin'
        ])
        ->where('pengguna_id', $orangTua->id)
        ->get();

        $anak = $relasi
            ->map(function ($item) {
                return $item->siswa;
            })
            ->filter()
            ->values();

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

            // Jika belum memilih anak, tampilkan anak pertama
            $siswa = $anak->first();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA WALI BELUM MEMILIKI ANAK
        |--------------------------------------------------------------------------
        */

        if (!$siswa) {
            return view('wali', [
                'orangTua'           => $orangTua,
                'anak'               => $anak,
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
            ->sortByDesc('tanggal_transaksi')
            ->values();

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
        | HITUNG POIN AKHIR
        |--------------------------------------------------------------------------
        |
        | Poin awal = 250
        | Apresiasi   = menambah poin
        | Pelanggaran  = mengurangi poin
        |
        */

        $poinAwal = 250;

        $totalScore = $poinAwal
            + $totalPrestasi
            - $totalPelanggaran;

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