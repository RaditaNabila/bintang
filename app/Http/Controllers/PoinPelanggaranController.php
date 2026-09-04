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

class PoinPelanggaranController extends Controller
{
    /**
     * Halaman Pelanggaran Siswa
     */
    public function index(Request $request)
    {
        // ================================
        // FILTER BULAN
        // ================================
        $bulan = $request->get('bulan', now()->format('m-Y'));

        try {
            [$bulanFilter, $tahunFilter] = explode('-', $bulan);

            $bulanFilter = (int) $bulanFilter;
            $tahunFilter = (int) $tahunFilter;

            if (
                $bulanFilter < 1 ||
                $bulanFilter > 12 ||
                $tahunFilter < 2000 ||
                $tahunFilter > 2100
            ) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            $bulanFilter = now()->month;
            $tahunFilter = now()->year;
            $bulan = now()->format('m-Y');
        }

        // ================================
        // TRANSAKSI PELANGGARAN
        // ================================
        $transaksi = TransaksiPoin::with([
            'siswa.kelas',
            'kategori',
            'pengguna',
            'aturanPoin'
        ])
            ->where('jenis', 'pelanggaran')
            ->whereMonth('tanggal_transaksi', $bulanFilter)
            ->whereYear('tanggal_transaksi', $tahunFilter)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ================================
        // SISWA AKTIF
        // ================================
        $siswa = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // ================================
        // KATEGORI PELANGGARAN
        // ================================
        $kategoriPelanggaran = Kategori::where('jenis', 'pelanggaran')
            ->orderBy('nama_kategori')
            ->get();

        // ================================
        // JENIS PELANGGARAN
        // ================================
        $aturanPelanggaran = AturanPoin::with('kategori')
            ->whereIn(
                'kategori_id',
                $kategoriPelanggaran->pluck('id')
            )
            ->orderBy('nilai_poin', 'asc')
            ->orderBy('judul', 'asc')
            ->get();

        // ================================
        // TOTAL KASUS
        // ================================
        $totalKasus = TransaksiPoin::where('jenis', 'pelanggaran')
            ->whereMonth('tanggal_transaksi', $bulanFilter)
            ->whereYear('tanggal_transaksi', $tahunFilter)
            ->count();

        // ================================
        // TOTAL POIN
        // ================================
        $totalPoinBulanIni = TransaksiPoin::where('jenis', 'pelanggaran')
            ->whereMonth('tanggal_transaksi', $bulanFilter)
            ->whereYear('tanggal_transaksi', $tahunFilter)
            ->sum('poin');

        // ================================
        // PELANGGARAN TERBANYAK
        // ================================
        $pelanggaranTerbanyak = TransaksiPoin::with('aturanPoin')
            ->where('jenis', 'pelanggaran')
            ->whereMonth('tanggal_transaksi', $bulanFilter)
            ->whereYear('tanggal_transaksi', $tahunFilter)
            ->select(
                'aturan_poin_id',
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy('aturan_poin_id')
            ->orderByDesc('jumlah')
            ->first();

        $namaPelanggaranTerbanyak = '-';

        if (
            $pelanggaranTerbanyak &&
            $pelanggaranTerbanyak->aturanPoin
        ) {
            $namaPelanggaranTerbanyak =
                $pelanggaranTerbanyak->aturanPoin->judul;
        }

        return view('guru.pelanggaran', compact(
            'transaksi',
            'siswa',
            'kategoriPelanggaran',
            'aturanPelanggaran',
            'totalKasus',
            'totalPoinBulanIni',
            'namaPelanggaranTerbanyak',
            'bulan'
        ));
    }


    /**
     * ==========================================
     * SIMPAN CATATAN PELANGGARAN
     * ==========================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'kategori_id' => 'required|integer|exists:kategori,id',
            'aturan_poin_id' => 'required|integer|exists:aturan_poin,id',
            'keterangan' => 'required|string|max:1000',
            'tanggal_transaksi' => 'required|date',
            'sanksi' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($validated['siswa_id']);

            // Pastikan jenis pelanggaran memang berada
            // di kategori yang dipilih
            $aturan = AturanPoin::with('kategori')
                ->where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', $validated['kategori_id'])
                ->whereHas('kategori', function ($query) {
                    $query->where('jenis', 'pelanggaran');
                })
                ->firstOrFail();

            $poin = abs((int) $aturan->nilai_poin);

            if ($poin <= 0) {
                abort(422, 'Poin pelanggaran tidak valid.');
            }

            $penggunaId = Auth::id();

            if (!$penggunaId) {
                $penggunaId = Pengguna::where('peran', 'guru')
                    ->where('status', 'aktif')
                    ->value('id');
            }

            if (!$penggunaId) {
                abort(422, 'Pengguna guru aktif tidak ditemukan.');
            }

            TransaksiPoin::create([
                'siswa_id' => $siswa->id,
                'pengguna_id' => $penggunaId,
                'kategori_id' => $aturan->kategori_id,
                'aturan_poin_id' => $aturan->id,
                'jenis' => 'pelanggaran',
                'poin' => $poin,
                'keterangan' => $validated['keterangan'],
                'sanksi' => $validated['sanksi'] ?? null,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);

            // Kurangi poin siswa
            $siswa->poin_saat_ini -= $poin;

            if ($siswa->poin_saat_ini < 0) {
                $siswa->poin_saat_ini = 0;
            }

            $siswa->save();
        });

        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Catatan pelanggaran siswa berhasil disimpan dan poin siswa telah dikurangi.'
            );
    }


    /**
     * ==========================================
     * TAMBAH KATEGORI PELANGGARAN
     * ==========================================
     */
    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ]);

        Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
            'jenis' => 'pelanggaran',
            'poin' => $validated['poin'],
        ]);

        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Kategori pelanggaran berhasil ditambahkan.'
            );
    }


    /**
     * ==========================================
     * HAPUS KATEGORI PELANGGARAN
     * ==========================================
     */
    public function destroyKategori($id)
    {
        $kategori = Kategori::where('jenis', 'pelanggaran')
            ->findOrFail($id);

        // Jangan hapus jika masih punya jenis pelanggaran
        $adaJenis = AturanPoin::where(
            'kategori_id',
            $kategori->id
        )->exists();

        if ($adaJenis) {
            return redirect()
                ->route('guru.pelanggaran')
                ->with(
                    'error',
                    'Kategori tidak dapat dihapus karena masih memiliki jenis pelanggaran.'
                );
        }

        // Jangan hapus jika sudah pernah digunakan transaksi
        $adaTransaksi = TransaksiPoin::where(
            'kategori_id',
            $kategori->id
        )->exists();

        if ($adaTransaksi) {
            return redirect()
                ->route('guru.pelanggaran')
                ->with(
                    'error',
                    'Kategori tidak dapat dihapus karena sudah digunakan dalam catatan pelanggaran.'
                );
        }

        $kategori->delete();

        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Kategori pelanggaran berhasil dihapus.'
            );
    }


    /**
     * ==========================================
     * TAMBAH JENIS PELANGGARAN
     * ==========================================
     */
    public function storeJenisPelanggaran(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|integer|exists:kategori,id',
            'nama_jenis' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ]);

        $kategori = Kategori::where('id', $validated['kategori_id'])
            ->where('jenis', 'pelanggaran')
            ->firstOrFail();

        AturanPoin::create([
            'kategori_id' => $kategori->id,
            'judul' => $validated['nama_jenis'],
            'nilai_poin' => abs((int) $validated['poin']),
        ]);

        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Jenis pelanggaran berhasil ditambahkan.'
            );
    }


    /**
     * ==========================================
     * HAPUS JENIS PELANGGARAN
     * ==========================================
     */
    public function destroyJenisPelanggaran($id)
    {
        $aturan = AturanPoin::with('kategori')
            ->whereHas('kategori', function ($query) {
                $query->where('jenis', 'pelanggaran');
            })
            ->findOrFail($id);

        $adaTransaksi = TransaksiPoin::where(
            'aturan_poin_id',
            $aturan->id
        )->exists();

        if ($adaTransaksi) {
            return redirect()
                ->route('guru.pelanggaran')
                ->with(
                    'error',
                    'Jenis pelanggaran tidak dapat dihapus karena sudah digunakan dalam catatan pelanggaran.'
                );
        }

        $aturan->delete();

        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Jenis pelanggaran berhasil dihapus.'
            );
    }


    /**
     * ==========================================
     * HAPUS CATATAN PELANGGARAN
     * ==========================================
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('jenis', 'pelanggaran')
                ->findOrFail($id);

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($transaksi->siswa_id);

            // Kembalikan poin siswa
            $siswa->poin_saat_ini =
                ($siswa->poin_saat_ini ?? 0)
                + abs((int) $transaksi->poin);

            $siswa->save();

            $transaksi->delete();
        });

        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Catatan pelanggaran berhasil dihapus dan poin siswa dikembalikan.'
            );
    }
}