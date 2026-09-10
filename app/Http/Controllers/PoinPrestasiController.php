<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kategori;
use App\Models\AturanPoin;
use App\Models\TransaksiPoin;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PoinPrestasiController extends Controller
{
    /**
     * Menampilkan halaman Poin Prestasi
     *
     * ADMIN    : /guru/poin-prestasi
     * PENGAJAR : /pengajar/poin-prestasi
     */
    public function index(Request $request)
    {
        $transaksi = TransaksiPoin::with([
                'siswa.kelas',
                'aturanPoin',
                'pengguna'
            ])
            ->where('jenis', 'apresiasi')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $siswa = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $kategori = Kategori::where('jenis', 'apresiasi')
            ->orderBy('nama_kategori')
            ->get();

        // Semua aturan poin yang termasuk kategori prestasi/apresiasi
        $aturanPrestasi = AturanPoin::where('kategori_id', 2)
            ->orderBy('id')
            ->get();

        // Ranking 3 siswa dengan poin tertinggi
        $ranking = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderByDesc('poin_saat_ini')
            ->limit(3)
            ->get();

        /*
         * PENTING:
         *
         * Kalau URL yang dibuka adalah:
         * /pengajar/poin-prestasi
         *
         * maka yang dipanggil:
         * resources/views/pengajar/poin-prestasi.blade.php
         *
         * Kalau URL:
         * /guru/poin-prestasi
         *
         * maka yang dipanggil:
         * resources/views/guru/poin-prestasi.blade.php
         */
        if ($request->routeIs('pengajar.poin-prestasi')) {

            return view('pengajar.poin-prestasi', compact(
                'transaksi',
                'siswa',
                'kategori',
                'aturanPrestasi',
                'ranking'
            ));
        }

        // Default untuk ADMIN
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
     *
     * Fungsi ini boleh digunakan oleh:
     * - Admin
     * - Pengajar
     *
     * Tetapi halaman pengajar hanya menyediakan
     * form untuk menambahkan poin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'aturan_poin_id' => 'required|integer|exists:aturan_poin,id',
            'keterangan' => 'required|string|max:255',
            'tanggal_transaksi' => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($validated['siswa_id']);

            // Pastikan aturan yang dipilih adalah aturan prestasi
            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 2)
                ->first();

            if (!$aturan) {
                abort(422, 'Aturan poin prestasi tidak ditemukan.');
            }

            $penggunaId = Auth::id();

            /*
             * Jika tidak ada pengguna yang login,
             * gunakan guru aktif sebagai fallback.
             */
            if (!$penggunaId) {
                $penggunaId = Pengguna::where('peran', 'guru')
                    ->where('status', 'aktif')
                    ->value('id');
            }

            if (!$penggunaId) {
                abort(422, 'Pengguna guru aktif tidak ditemukan.');
            }

            $poin = (int) $aturan->nilai_poin;

            TransaksiPoin::create([
                'siswa_id' => $siswa->id,
                'pengguna_id' => $penggunaId,
                'kategori_id' => $aturan->kategori_id,
                'aturan_poin_id' => $aturan->id,
                'jenis' => 'apresiasi',
                'poin' => $poin,
                'keterangan' => $validated['keterangan'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);

            // Tambahkan poin ke total poin siswa
            $siswa->poin_saat_ini = (int) $siswa->poin_saat_ini + $poin;
            $siswa->save();
        });


        /*
         * Redirect sesuai halaman asal.
         *
         * Pengajar:
         * /pengajar/poin-prestasi
         *
         * Admin:
         * /guru/poin-prestasi
         */
        if ($request->routeIs('pengajar.poin-prestasi.store')) {

            return redirect()
                ->route('pengajar.poin-prestasi')
                ->with('success', 'Poin prestasi berhasil ditambahkan.');
        }

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Poin prestasi berhasil ditambahkan.');
    }


    /**
     * Update poin prestasi
     *
     * KHUSUS ADMIN
     *
     * Route hanya dibuat di prefix guru.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'aturan_poin_id' => 'required|integer|exists:aturan_poin,id',
            'keterangan' => 'required|string|max:255',
            'tanggal_transaksi' => 'required|date',
        ]);

        DB::transaction(function () use ($validated, $id) {

            /*
             * Ambil transaksi yang akan diedit.
             * Hanya transaksi apresiasi/prestasi.
             */
            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('id', $id)
                ->where('jenis', 'apresiasi')
                ->firstOrFail();

            /*
             * Pastikan aturan yang dipilih adalah
             * aturan prestasi.
             */
            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 2)
                ->first();

            if (!$aturan) {
                abort(422, 'Aturan poin prestasi tidak ditemukan.');
            }

            $poinBaru = (int) $aturan->nilai_poin;

            /*
             * Ambil siswa lama dan siswa baru.
             */
            $siswaLama = Siswa::lockForUpdate()
                ->findOrFail($transaksi->siswa_id);

            $siswaBaru = Siswa::lockForUpdate()
                ->findOrFail($validated['siswa_id']);

            /*
             * Kembalikan poin transaksi lama
             * dari siswa sebelumnya.
             */
            $siswaLama->poin_saat_ini = max(
                0,
                (int) $siswaLama->poin_saat_ini - (int) $transaksi->poin
            );

            $siswaLama->save();

            /*
             * Update transaksi.
             *
             * pengguna_id tidak diubah agar pencatat
             * asli tetap tersimpan.
             */
            $transaksi->siswa_id = $siswaBaru->id;
            $transaksi->kategori_id = $aturan->kategori_id;
            $transaksi->aturan_poin_id = $aturan->id;
            $transaksi->poin = $poinBaru;
            $transaksi->keterangan = $validated['keterangan'];
            $transaksi->tanggal_transaksi = $validated['tanggal_transaksi'];
            $transaksi->jenis = 'apresiasi';

            $transaksi->save();

            /*
             * Tambahkan poin baru ke siswa yang dipilih.
             */
            $siswaBaru->poin_saat_ini = (int) $siswaBaru->poin_saat_ini + $poinBaru;
            $siswaBaru->save();
        });

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Poin prestasi berhasil diperbarui.');
    }


    /**
     * Hapus transaksi poin prestasi
     *
     * KHUSUS ADMIN
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('id', $id)
                ->where('jenis', 'apresiasi')
                ->firstOrFail();

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($transaksi->siswa_id);

            /*
             * Kurangi poin siswa sesuai poin transaksi
             * yang dihapus.
             */
            $siswa->poin_saat_ini = max(
                0,
                (int) $siswa->poin_saat_ini - (int) $transaksi->poin
            );

            $siswa->save();

            // Hapus transaksi
            $transaksi->delete();
        });

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Catatan poin prestasi berhasil dihapus.');
    }


    /**
     * Menambahkan kategori / aturan poin prestasi baru
     *
     * KHUSUS ADMIN
     */
    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ]);

        /*
         * Cek apakah aturan dengan nama yang sama
         * sudah ada di kategori prestasi.
         */
        $cek = AturanPoin::where('kategori_id', 2)
            ->where('judul', $validated['nama_kategori'])
            ->exists();

        if ($cek) {
            return redirect()
                ->route('guru.poin-prestasi')
                ->with(
                    'error',
                    'Kategori/Aturan prestasi tersebut sudah ada.'
                );
        }

        AturanPoin::create([
            'kategori_id' => 2,
            'judul' => $validated['nama_kategori'],
            'nilai_poin' => $validated['poin'],
        ]);

        return redirect()
            ->route('guru.poin-prestasi')
            ->with(
                'success',
                'Kategori prestasi berhasil ditambahkan.'
            );
    }


    /**
     * Hapus kategori / aturan poin prestasi
     *
     * KHUSUS ADMIN
     */
    public function destroyKategori($id)
    {
        $aturan = AturanPoin::where('id', $id)
            ->where('kategori_id', 2)
            ->firstOrFail();

        /*
         * Jangan izinkan aturan dihapus jika masih
         * digunakan oleh transaksi poin.
         */
        $dipakai = TransaksiPoin::where(
            'aturan_poin_id',
            $aturan->id
        )->exists();

        if ($dipakai) {
            return redirect()
                ->route('guru.poin-prestasi')
                ->with(
                    'error',
                    'Aturan prestasi tidak dapat dihapus karena sudah digunakan pada transaksi poin.'
                );
        }

        $aturan->delete();

        return redirect()
            ->route('guru.poin-prestasi')
            ->with(
                'success',
                'Kategori prestasi berhasil dihapus.'
            );
    }
}
