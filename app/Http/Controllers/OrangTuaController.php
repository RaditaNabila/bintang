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
        // 1. Ambil data user yang sedang login
        $orangTua = Auth::user();

        // Jika belum login atau bukan orang tua / wali
        if (!$orangTua || !in_array($orangTua->peran, ['orang_tua', 'wali'])) {
            return redirect()
                ->route('welcome')
                ->with(
                    'error',
                    'Silakan login sebagai orang tua/wali terlebih dahulu.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA SISWA MILIK WALI YANG LOGIN
        |--------------------------------------------------------------------------
        */
        $siswaIds = OrangTuaSiswa::where('pengguna_id', $orangTua->id)
            ->pluck('siswa_id');

        // Ambil data siswa berdasarkan relasi wali
        $anak = Siswa::with(['kelas'])
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
                    ->with(
                        'error',
                        'Data siswa tidak ditemukan.'
                    );
            }

        } else {

            // Tampilkan anak pertama
            $siswa = $anak->first();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA WALI BELUM MEMILIKI ANAK
        |--------------------------------------------------------------------------
        */
        if (!$siswa) {
            return view('wali', [
                'orangTua'          => $orangTua,
                'anak'              => collect(),
                'siswa'             => null,
                'totalScore'        => 250,
                'totalPrestasi'     => 0,
                'jumlahPrestasi'    => 0,
                'totalPelanggaran'  => 0,
                'jumlahPelanggaran' => 0,
                'riwayatPoin'      => collect(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL TRANSAKSI POIN
        |
        | Hanya transaksi setelah reset terakhir yang dihitung
        |--------------------------------------------------------------------------
        */
        $queryTransaksi = $siswa->transaksiPoin();

        if ($siswa->poin_reset_at) {
            $queryTransaksi->where(
                'created_at',
                '>=',
                $siswa->poin_reset_at
            );
        }

        $riwayatPoin = $queryTransaksi
            ->orderByDesc('tanggal_transaksi')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL PRESTASI
        |--------------------------------------------------------------------------
        */
        $totalPrestasi = $riwayatPoin
            ->where('jenis', 'apresiasi')
            ->sum('poin');

        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL PELANGGARAN
        |--------------------------------------------------------------------------
        */
        $totalPelanggaran = $riwayatPoin
            ->where('jenis', 'pelanggaran')
            ->sum('poin');

        /*
        |--------------------------------------------------------------------------
        | HITUNG JUMLAH PRESTASI
        |--------------------------------------------------------------------------
        */
        $jumlahPrestasi = $riwayatPoin
            ->where('jenis', 'apresiasi')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | HITUNG JUMLAH PELANGGARAN
        |--------------------------------------------------------------------------
        */
        $jumlahPelanggaran = $riwayatPoin
            ->where('jenis', 'pelanggaran')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | POIN AKHIR
        |
        | Sumber utama adalah poin_saat_ini.
        |
        | JANGAN lagi:
        | 250 + seluruh transaksi lama
        |--------------------------------------------------------------------------
        */
        $totalScore = $siswa->poin_saat_ini ?? 250;

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('wali', [
            'orangTua'          => $orangTua,
            'anak'              => $anak,
            'siswa'             => $siswa,
            'totalScore'        => $totalScore,
            'totalPrestasi'     => $totalPrestasi,
            'jumlahPrestasi'    => $jumlahPrestasi,
            'totalPelanggaran'  => $totalPelanggaran,
            'jumlahPelanggaran' => $jumlahPelanggaran,
            'riwayatPoin'      => $riwayatPoin,
        ]);
    }
}