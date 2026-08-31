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
     * Pemindahan siswa secara manual
     *
     * Kelas 1 -> 2
     * Kelas 2 -> 3
     * Kelas 3 -> 4
     * Kelas 4 -> 5
     * Kelas 5 -> 6
     *
     * Kelas 6 -> Lulus -> Arsip Alumni
     */
    public function pindahkan(Request $request)
    {
        $request->validate([
            'siswa' => ['required', 'array', 'min:1'],
            'siswa.*' => ['required', 'integer', 'exists:siswa,id'],
            'kelas_tujuan' => ['nullable', 'integer', 'exists:kelas,id'],
            'reset_poin' => ['nullable', 'in:0,1'],
            'aksi' => ['required', 'in:pindah,lulus'],
        ], [
            'siswa.required' => 'Silakan pilih minimal satu siswa.',
            'siswa.array' => 'Data siswa tidak valid.',
            'siswa.min' => 'Silakan pilih minimal satu siswa.',
            'siswa.*.exists' => 'Data siswa tidak ditemukan.',
            'kelas_tujuan.exists' => 'Kelas tujuan tidak ditemukan.',
            'aksi.required' => 'Aksi tidak ditemukan.',
        ]);

        DB::beginTransaction();

        try {
            $daftarSiswa = Siswa::with('kelas')
                ->whereIn('id', $request->siswa)
                ->where('status', 'aktif')
                ->get();

            if ($daftarSiswa->isEmpty()) {
                DB::rollBack();

                return back()->with(
                    'error',
                    'Siswa yang dipilih tidak ditemukan atau sudah tidak aktif.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PROSES LULUS KELAS 6
            |--------------------------------------------------------------------------
            */

            if ($request->aksi === 'lulus') {

                $jumlahLulus = 0;

                foreach ($daftarSiswa as $item) {

                    if (!$item->kelas) {
                        continue;
                    }

                    $namaKelas = $item->kelas->nama_kelas;

                    // Ambil angka tingkat kelas
                    preg_match('/\d+/', $namaKelas, $matches);

                    $tingkat = isset($matches[0])
                        ? (int) $matches[0]
                        : 0;

                    // Hanya kelas 6 yang boleh diluluskan
                    if ($tingkat !== 6) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEK AGAR TIDAK MEMBUAT ARSIP DUPLIKAT
                    |--------------------------------------------------------------------------
                    */

                    $sudahDiarsipkan = ArsipAlumni::where(
                        'siswa_id',
                        $item->id
                    )->exists();

                    if (!$sudahDiarsipkan) {

                        ArsipAlumni::create([
                            'siswa_id' => $item->id,
                            'nis' => $item->nis,
                            'nama_lengkap' => $item->nama_lengkap,
                            'kelas_terakhir' => $namaKelas,
                            'poin_akhir' => $item->poin_saat_ini ?? 0,
                            'jenis_arsip' => 'lulus',
                            'tahun_kelulusan' => date('Y'),
                            'nama_angkatan' => 'Angkatan ' . date('Y'),
                            'catatan_status' => 'Lulus',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | UBAH STATUS SISWA MENJADI LULUS
                    |--------------------------------------------------------------------------
                    */

                    $item->status = 'lulus';

                    if ($request->input('reset_poin') === '1') {
                        $item->poin_saat_ini = 250;
                    }

                    $item->save();

                    $jumlahLulus++;
                }

                if ($jumlahLulus === 0) {
                    DB::rollBack();

                    return back()->with(
                        'error',
                        'Tidak ada siswa kelas 6 yang dapat diluluskan.'
                    );
                }

                DB::commit();

                $pesan = "{$jumlahLulus} siswa berhasil diluluskan dan dimasukkan ke Arsip Alumni.";

                if ($request->input('reset_poin') === '1') {
                    $pesan .= ' Poin siswa direset menjadi 250.';
                }

                return redirect()
                    ->route('guru.naik-kelas')
                    ->with('success', $pesan);
            }

            /*
            |--------------------------------------------------------------------------
            | PROSES PINDAH KELAS 1 - 5
            |--------------------------------------------------------------------------
            */

            if (!$request->kelas_tujuan) {
                DB::rollBack();

                return back()->with(
                    'error',
                    'Silakan pilih rombel tujuan terlebih dahulu.'
                );
            }

            $kelasTujuan = Kelas::where(
                'id',
                $request->kelas_tujuan
            )->first();

            if (!$kelasTujuan) {
                DB::rollBack();

                return back()->with(
                    'error',
                    'Rombel tujuan tidak ditemukan.'
                );
            }

            // Ambil tingkat kelas tujuan
            preg_match('/\d+/', $kelasTujuan->nama_kelas, $matchesTujuan);

            $tingkatTujuan = isset($matchesTujuan[0])
                ? (int) $matchesTujuan[0]
                : 0;

            $jumlah = 0;

            foreach ($daftarSiswa as $item) {

                if (!$item->kelas) {
                    continue;
                }

                $namaKelasAsal = $item->kelas->nama_kelas;

                // Ambil tingkat kelas asal
                preg_match('/\d+/', $namaKelasAsal, $matchesAsal);

                $tingkatAsal = isset($matchesAsal[0])
                    ? (int) $matchesAsal[0]
                    : 0;

                /*
                |--------------------------------------------------------------------------
                | CEGAH KELAS 6 DIPINDAHKAN
                |--------------------------------------------------------------------------
                */

                if ($tingkatAsal === 6) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | HANYA BOLEH NAIK SATU TINGKAT
                |--------------------------------------------------------------------------
                */

                if ($tingkatTujuan !== ($tingkatAsal + 1)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | PINDAHKAN KELAS
                |--------------------------------------------------------------------------
                */

                $item->kelas_id = $kelasTujuan->id;

                /*
                |--------------------------------------------------------------------------
                | RESET POIN JIKA DIPILIH
                |--------------------------------------------------------------------------
                */

                if ($request->input('reset_poin') === '1') {
                    $item->poin_saat_ini = 250;
                }

                $item->status = 'aktif';

                $item->save();

                $jumlah++;
            }

            if ($jumlah === 0) {
                DB::rollBack();

                return back()->with(
                    'error',
                    'Tidak ada siswa yang dapat dipindahkan. Pastikan kelas tujuan adalah satu tingkat di atas kelas asal.'
                );
            }

            DB::commit();

            $pesan = "{$jumlah} siswa berhasil dipindahkan ke {$kelasTujuan->nama_kelas}.";

            if ($request->input('reset_poin') === '1') {
                $pesan .= ' Poin siswa direset menjadi 250.';
            }

            return redirect()
                ->route('guru.naik-kelas')
                ->with('success', $pesan);

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Proses gagal: ' . $e->getMessage()
            );
        }
    }
}