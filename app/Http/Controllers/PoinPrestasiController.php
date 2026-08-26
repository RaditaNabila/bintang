<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kategori;
use App\Models\AturanPoin;
use App\Models\TransaksiPoin;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Tambahkan facade Auth jika diperlukan

class PoinPrestasiController extends Controller
{
    /**
     * Menampilkan halaman Poin Prestasi
     */
    public function index(Request $request)
    {
        // Ambil semua transaksi apresiasi
        $transaksi = TransaksiPoin::with([
                'siswa.kelas',
                'kategori',
                'pengguna'
            ])
            ->where('jenis', 'apresiasi')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->get();

        // Ambil semua siswa aktif
        $siswa = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Ambil kategori apresiasi
        $kategori = Kategori::where('jenis', 'apresiasi')
            ->orderBy('nama_kategori')
            ->get();

        // =====================================================
        // AMBIL SEMUA ATURAN PENAMBAHAN POIN / PRESTASI
        // =====================================================
        $aturanPrestasi = AturanPoin::where('kategori_id', 2)
            ->orderBy('id')
            ->get();

        // Ranking 3 siswa dengan poin tertinggi
        $ranking = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderByDesc('poin_saat_ini')
            ->limit(3)
            ->get();

        // Hitung total poin apresiasi setiap transaksi/siswa
        foreach ($transaksi as $item) {
            $item->total_poin_siswa = TransaksiPoin::where(
                    'siswa_id',
                    $item->siswa_id
                )
                ->where('jenis', 'apresiasi')
                ->sum('poin');
        }

        return view('guru.poin-prestasi', compact(
            'transaksi',
            'siswa',
            'kategori',
            'aturanPrestasi',
            'ranking'
        ));
    }


    /**
     * Menyimpan poin prestasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'aturan_poin_id' => 'required|integer|exists:aturan_poin,id',
            'keterangan' => 'required|string',
            'tanggal_transaksi' => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {

            // Kunci data siswa agar aman dari transaksi bersamaan
            $siswa = Siswa::lockForUpdate()
                ->findOrFail($validated['siswa_id']);

            // =====================================================
            // AMBIL ATURAN PRESTASI
            // =====================================================
            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 2)
                ->first();

            if (!$aturan) {
                abort(422, 'Aturan poin prestasi tidak ditemukan.');
            }

            // Poin otomatis dari aturan_poin
            $poin = $aturan->nilai_poin;

            // =====================================================
            // AMBIL ID GURU YANG SEDANG LOGIN
            // =====================================================
            $penggunaId = Auth::id() ?? Pengguna::where('peran', 'guru')->where('status', 'aktif')->value('id');

            if (!$penggunaId) {
                abort(422, 'Pengguna guru aktif tidak ditemukan.');
            }

            // =====================================================
            // SIMPAN TRANSAKSI
            // =====================================================
            TransaksiPoin::create([
                'siswa_id' => $siswa->id,
                'pengguna_id' => $penggunaId,

                // Ambil kategori_id secara dinamis dari data aturan yang dipilih
                'kategori_id' => $aturan->kategori_id,

                // Hubungkan dengan aturan poin
                'aturan_poin_id' => $aturan->id,

                'jenis' => 'apresiasi',

                // Poin otomatis dari aturan
                'poin' => $poin,

                'keterangan' => $validated['keterangan'],

                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);

            // =====================================================
            // TAMBAHKAN POIN KE SISWA
            // =====================================================
            $siswa->poin_saat_ini += $poin;
            $siswa->save();
        });

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Poin prestasi berhasil ditambahkan.');
    }


    /**
     * Update poin prestasi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'aturan_poin_id' => 'required|integer|exists:aturan_poin,id',
            'keterangan' => 'required|string',
            'tanggal_transaksi' => 'required|date',
        ]);

        DB::transaction(function () use ($validated, $id) {

            // =====================================================
            // AMBIL TRANSAKSI LAMA
            // =====================================================
            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('jenis', 'apresiasi')
                ->findOrFail($id);

            // =====================================================
            // KURANGI POIN SISWA LAMA
            // =====================================================
            $siswaLama = Siswa::lockForUpdate()
                ->findOrFail($transaksi->siswa_id);

            $siswaLama->poin_saat_ini -= $transaksi->poin;

            if ($siswaLama->poin_saat_ini < 0) {
                $siswaLama->poin_saat_ini = 0;
            }

            $siswaLama->save();

            // =====================================================
            // AMBIL ATURAN PRESTASI BARU
            // =====================================================
            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 2)
                ->first();

            if (!$aturan) {
                abort(422, 'Aturan poin prestasi tidak ditemukan.');
            }

            // Poin otomatis dari aturan
            $poin = $aturan->nilai_poin;

            // =====================================================
            // SISWA BARU
            // =====================================================
            $siswaBaru = Siswa::lockForUpdate()
                ->findOrFail($validated['siswa_id']);

            // =====================================================
            // UPDATE TRANSAKSI
            // =====================================================
            $transaksi->update([
                'siswa_id' => $siswaBaru->id,
                'kategori_id' => $aturan->kategori_id, // Dinamis dari aturan
                'aturan_poin_id' => $aturan->id,
                'poin' => $poin,
                'keterangan' => $validated['keterangan'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);

            // =====================================================
            // TAMBAHKAN POIN BARU
            // =====================================================
            $siswaBaru->poin_saat_ini += $poin;
            $siswaBaru->save();
        });

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Poin prestasi berhasil diperbarui.');
    }


    /**
     * Hapus transaksi poin prestasi
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('jenis', 'apresiasi')
                ->findOrFail($id);

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($transaksi->siswa_id);

            $siswa->poin_saat_ini -= $transaksi->poin;

            if ($siswa->poin_saat_ini < 0) {
                $siswa->poin_saat_ini = 0;
            }

            $siswa->save();

            $transaksi->delete();
        });

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Catatan poin prestasi berhasil dihapus.');
    }


    /**
     * Menambahkan kategori apresiasi
     */
    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'poin' => 'required|integer|min:1|max:100',
        ]);

        $cek = Kategori::where('nama_kategori', $validated['nama_kategori'])
            ->where('jenis', 'apresiasi')
            ->exists();

        if ($cek) {
            return redirect()
                ->route('guru.poin-prestasi')
                ->with('error', 'Kategori prestasi tersebut sudah ada.');
        }

        Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
            'jenis' => 'apresiasi',
            'poin' => $validated['poin'],
        ]);

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Kategori prestasi berhasil ditambahkan.');
    }
}