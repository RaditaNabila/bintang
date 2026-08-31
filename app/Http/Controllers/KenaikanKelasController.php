<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\ArsipAlumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    /**
     * Halaman kenaikan kelas
     */
    public function index()
    {
        $siswa = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('guru.naik-kelas', compact('siswa', 'kelas'));
    }

    /**
     * Proses kenaikan kelas otomatis
     */
    public function prosesOtomatis()
    {
        DB::beginTransaction();

        try {
            $siswa = Siswa::with('kelas')
                ->where('status', 'aktif')
                ->get();

            $jumlahNaik = 0;
            $jumlahLulus = 0;

            foreach ($siswa as $item) {
                if (!$item->kelas) {
                    continue;
                }

                $namaKelas = $item->kelas->nama_kelas;
                $tingkat = (int) preg_replace('/[^0-9]/', '', $namaKelas);

                // KELAS 6 -> LULUS
                if ($tingkat === 6) {
                    ArsipAlumni::create([
                        'siswa_id' => $item->id_siswa ?? $item->id,
                        'nis' => $item->nis,
                        'nama_lengkap' => $item->nama_lengkap,
                        'kelas_terakhir' => $namaKelas,
                        'poin_akhir' => $item->poin_saat_ini ?? 0,
                        'jenis_arsip' => 'lulus',
                        'tahun_kelulusan' => date('Y'),
                        'nama_angkatan' => 'Angkatan ' . date('Y'),
                        'catatan_status' => 'Lulus',
                    ]);

                    $item->status = 'lulus';
                    $item->is_active = 0;
                    $item->save();

                    $jumlahLulus++;
                    continue;
                }

                // KELAS 5 -> KELAS 6
                if ($tingkat === 5) {
                    $suffix = substr($namaKelas, -1);
                    $kelasBaru = '6-' . $suffix;
                    $kelasTujuan = Kelas::where('nama_kelas', $kelasBaru)->first();

                    if ($kelasTujuan) {
                        $item->id_kelas = $kelasTujuan->id_kelas;
                        $item->save();
                        $jumlahNaik++;
                    }
                    continue;
                }

                // KELAS 1 -> 2, 2 -> 3, 3 -> 4, 4 -> 5
                if ($tingkat >= 1 && $tingkat <= 4) {
                    $tingkatBaru = $tingkat + 1;
                    $suffix = substr($namaKelas, -1);
                    $kelasBaru = $tingkatBaru . '-' . $suffix;
                    $kelasTujuan = Kelas::where('nama_kelas', $kelasBaru)->first();

                    if ($kelasTujuan) {
                        $item->id_kelas = $kelasTujuan->id_kelas;
                        $item->save();
                        $jumlahNaik++;
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('guru.naik-kelas')
                ->with('success', "Proses selesai. {$jumlahNaik} siswa naik kelas dan {$jumlahLulus} siswa lulus.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Proses kenaikan kelas gagal: ' . $e->getMessage());
        }
    }

    /**
     * Pemindahan siswa secara manual / plotting rombel (Nama method disesuaikan dengan Route: pindahkan)
     */
    public function pindahkan(Request $request)
    {
        $request->validate([
            'siswa' => ['required', 'array'],
            'siswa.*' => ['required', 'integer'],
            'kelas_tujuan' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'reset_poin' => ['nullable', 'in:0,1'],
        ]);

        DB::beginTransaction();

        try {
            $primaryKeyName = (new Siswa())->getKeyName();

            $daftarSiswa = Siswa::whereIn($primaryKeyName, $request->siswa)
                ->where('status', 'aktif')
                ->get();

            $jumlah = 0;

            foreach ($daftarSiswa as $item) {
                $item->id_kelas = $request->kelas_tujuan;

                if ($request->input('reset_poin') == '1') {
                    $item->poin_saat_ini = 250;
                }

                $item->save();
                $jumlah++;
            }

            DB::commit();

            return redirect()
                ->route('guru.naik-kelas')
                ->with('success', "{$jumlah} siswa berhasil dipindahkan ke rombel tujuan.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Pemindahan siswa gagal: ' . $e->getMessage());
        }
    }
}
