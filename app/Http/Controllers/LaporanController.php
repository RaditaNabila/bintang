<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\ArsipAlumni;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan
     */
    public function index(Request $request)
    {
        // =====================================================
        // DATA SISWA AKTIF
        // =====================================================
        $siswa = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // =====================================================
        // DATA KELAS
        // =====================================================
        $kelas = Kelas::orderBy('nama_kelas')
            ->get();

        // =====================================================
        // TOTAL SISWA AKTIF
        // =====================================================
        $totalSiswa = $siswa->count();

        // =====================================================
        // DATA SISWA PINDAH / KELUAR
        // =====================================================
        $pindah = ArsipAlumni::where('jenis_arsip', 'pindah')
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'siswa_id' => $item->siswa_id,
                    'nis' => $item->nis,
                    'nama_lengkap' => $item->nama_lengkap,
                    'kelas_terakhir' => $item->kelas_terakhir,
                    'poin_akhir' => $item->poin_akhir,
                    'jenis_arsip' => $item->jenis_arsip,
                    'tahun_kelulusan' => $item->tahun_kelulusan,
                    'nama_angkatan' => $item->nama_angkatan,
                    'catatan_status' => $item->catatan_status,
                    'diarsipkan_pada' => $item->diarsipkan_pada,
                ];
            })
            ->values();

        // =====================================================
        // DATA ALUMNI / LULUS
        // =====================================================
        $alumni = ArsipAlumni::where('jenis_arsip', 'lulus')
            ->orderByDesc('tahun_kelulusan')
            ->orderBy('nama_lengkap')
            ->get();

        // Kelompokkan berdasarkan tahun kelulusan
        $tahunAlumni = $alumni
            ->groupBy('tahun_kelulusan')
            ->map(function ($items, $tahun) {
                return [
                    'tahun' => $tahun,

                    'nama_angkatan' =>
                        $items->first()->nama_angkatan ?? 'Alumni',

                    'jumlah' =>
                        $items->count(),

                    // DATA SISWA DALAM FOLDER
                    'data' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'siswa_id' => $item->siswa_id,
                            'nis' => $item->nis,
                            'nama_lengkap' => $item->nama_lengkap,
                            'kelas_terakhir' => $item->kelas_terakhir,
                            'poin_akhir' => $item->poin_akhir,
                            'jenis_arsip' => $item->jenis_arsip,
                            'tahun_kelulusan' => $item->tahun_kelulusan,
                            'nama_angkatan' => $item->nama_angkatan,
                            'catatan_status' => $item->catatan_status,
                            'diarsipkan_pada' => $item->diarsipkan_pada,
                        ];
                    })->values(),
                ];
            })
            ->values();

        // =====================================================
        // KIRIM KE VIEW
        // =====================================================
        return view('guru.laporan', compact(
            'siswa',
            'kelas',
            'totalSiswa',
            'pindah',
            'tahunAlumni'
        ));
    }
}