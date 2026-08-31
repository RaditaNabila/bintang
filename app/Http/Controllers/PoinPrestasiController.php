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

        $aturanPrestasi = AturanPoin::where('kategori_id', 2)
            ->orderBy('id')
            ->get();

        $ranking = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderByDesc('poin_saat_ini')
            ->limit(3)
            ->get();

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
            $siswa = Siswa::lockForUpdate()->findOrFail($validated['siswa_id']);

            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 2)
                ->first();

            if (!$aturan) {
                abort(422, 'Aturan poin prestasi tidak ditemukan.');
            }

            $poin = $aturan->nilai_poin;
            $penggunaId = Auth::id() ?? Pengguna::where('peran', 'guru')->where('status', 'aktif')->value('id');

            if (!$penggunaId) {
                abort(422, 'Pengguna guru aktif tidak ditemukan.');
            }

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
            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('jenis', 'apresiasi')
                ->findOrFail($id);

            $siswaLama = Siswa::lockForUpdate()->findOrFail($transaksi->siswa_id);
            $siswaLama->poin_saat_ini -= $transaksi->poin;
            if ($siswaLama->poin_saat_ini < 0) {
                $siswaLama->poin_saat_ini = 0;
            }
            $siswaLama->save();

            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 2)
                ->first();

            if (!$aturan) {
                abort(422, 'Aturan poin prestasi tidak ditemukan.');
            }

            $poin = $aturan->nilai_poin;
            $siswaBaru = Siswa::lockForUpdate()->findOrFail($validated['siswa_id']);

            $transaksi->update([
                'siswa_id' => $siswaBaru->id,
                'kategori_id' => $aturan->kategori_id,
                'aturan_poin_id' => $aturan->id,
                'poin' => $poin,
                'keterangan' => $validated['keterangan'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);

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

            $siswa = Siswa::lockForUpdate()->findOrFail($transaksi->siswa_id);
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
     * Menambahkan kategori apresiasi / aturan poin baru
     */
    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ]);

        // Cek duplikasi judul aturan di kategori prestasi yang sama
        $cek = AturanPoin::where('kategori_id', 2)
            ->where('judul', $validated['nama_kategori'])
            ->exists();

        if ($cek) {
            return redirect()
                ->route('guru.poin-prestasi')
                ->with('error', 'Kategori/Aturan prestasi tersebut sudah ada.');
        }

        AturanPoin::create([
            'kategori_id' => 2, // 2 adalah ID kategori apresiasi
            'judul' => $validated['nama_kategori'],
            'nilai_poin' => $validated['poin'],
        ]);

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Kategori prestasi berhasil ditambahkan.');
    }

    /**
     * Hapus kategori / aturan poin prestasi
     */
    public function destroyKategori($id)
    {
        $aturan = AturanPoin::findOrFail($id);
        $aturan->delete();

        return redirect()
            ->route('guru.poin-prestasi')
            ->with('success', 'Kategori prestasi berhasil dihapus.');
    }
}
