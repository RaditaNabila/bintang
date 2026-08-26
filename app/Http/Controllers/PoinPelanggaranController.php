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
     * Menampilkan halaman Pelanggaran Siswa
     */
    public function index(Request $request)
    {
        // =====================================================
        // FILTER BULAN
        // =====================================================

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


        // =====================================================
        // DATA TRANSAKSI PELANGGARAN
        // =====================================================

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
            ->get();


        // =====================================================
        // DATA SISWA AKTIF
        // =====================================================

        $siswa = Siswa::with('kelas')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();


        // =====================================================
        // KATEGORI PELANGGARAN
        // =====================================================

        $kategori = Kategori::where('jenis', 'pelanggaran')
            ->orderBy('nama_kategori')
            ->get();


        // =====================================================
        // ATURAN / JENIS PELANGGARAN
        // =====================================================

        $aturanPelanggaran = AturanPoin::where('kategori_id', 1)
            ->orderBy('nilai_poin', 'asc')
            ->get();


        // =====================================================
        // TOTAL KASUS BULAN YANG DIPILIH
        // =====================================================

        $totalKasus = TransaksiPoin::where('jenis', 'pelanggaran')
            ->whereMonth('tanggal_transaksi', $bulanFilter)
            ->whereYear('tanggal_transaksi', $tahunFilter)
            ->count();


        // =====================================================
        // TOTAL POIN PELANGGARAN BULAN YANG DIPILIH
        // =====================================================

        $totalPoinBulanIni = TransaksiPoin::where('jenis', 'pelanggaran')
            ->whereMonth('tanggal_transaksi', $bulanFilter)
            ->whereYear('tanggal_transaksi', $tahunFilter)
            ->sum('poin');


        // =====================================================
        // PELANGGARAN TERBANYAK
        // =====================================================

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


        // =====================================================
        // KIRIM DATA KE VIEW
        // =====================================================

        return view('guru.pelanggaran', compact(
            'transaksi',
            'siswa',
            'kategori',
            'aturanPelanggaran',
            'totalKasus',
            'totalPoinBulanIni',
            'namaPelanggaranTerbanyak',
            'bulan'
        ));
    }


    /**
     * Menyimpan pelanggaran baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'aturan_poin_id' => 'required|integer|exists:aturan_poin,id',
            'keterangan' => 'required|string|max:1000',
            'tanggal_transaksi' => 'required|date',
            'sanksi' => 'nullable|string|max:1000',
        ]);


        DB::transaction(function () use ($validated) {

            // =================================================
            // KUNCI DATA SISWA
            // =================================================

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($validated['siswa_id']);


            // =================================================
            // AMBIL ATURAN PELANGGARAN
            // =================================================

            $aturan = AturanPoin::where('id', $validated['aturan_poin_id'])
                ->where('kategori_id', 1)
                ->firstOrFail();


            // =================================================
            // AMBIL POIN DARI ATURAN
            // =================================================

            $poin = abs((int) $aturan->nilai_poin);


            // =================================================
            // PENGGUNA YANG LOGIN
            // =================================================

            $penggunaId = Auth::id();

            if (!$penggunaId) {
                $penggunaId = Pengguna::where('peran', 'guru')
                    ->where('status', 'aktif')
                    ->value('id');
            }

            if (!$penggunaId) {
                abort(422, 'Pengguna guru aktif tidak ditemukan.');
            }


            // =================================================
            // SIMPAN TRANSAKSI
            // =================================================

            TransaksiPoin::create([
                'siswa_id' => $siswa->id,
                'pengguna_id' => $penggunaId,
                'kategori_id' => $aturan->kategori_id,
                'aturan_poin_id' => $aturan->id,
                'jenis' => 'pelanggaran',

                // Poin disimpan sebagai nilai positif
                'poin' => $poin,

                'keterangan' => $validated['keterangan'],

                'sanksi' => $validated['sanksi'] ?? null,

                'tanggal_transaksi' => $validated['tanggal_transaksi'],
            ]);


            // =================================================
            // KURANGI POIN SISWA
            // =================================================

            $poinSekarang = $siswa->poin_saat_ini ?? 0;

            $siswa->poin_saat_ini = max(
                0,
                $poinSekarang - $poin
            );

            $siswa->save();
        });


        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Catatan pelanggaran siswa berhasil disimpan.'
            );
    }


    /**
     * Menghapus transaksi pelanggaran
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            // =================================================
            // AMBIL TRANSAKSI
            // =================================================

            $transaksi = TransaksiPoin::lockForUpdate()
                ->where('jenis', 'pelanggaran')
                ->findOrFail($id);


            // =================================================
            // AMBIL SISWA
            // =================================================

            $siswa = Siswa::lockForUpdate()
                ->findOrFail($transaksi->siswa_id);


            // =================================================
            // KEMBALIKAN POIN SISWA
            // =================================================

            $siswa->poin_saat_ini =
                ($siswa->poin_saat_ini ?? 0)
                + abs((int) $transaksi->poin);

            $siswa->save();


            // =================================================
            // HAPUS TRANSAKSI
            // =================================================

            $transaksi->delete();
        });


        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Catatan pelanggaran berhasil dihapus dan poin siswa dikembalikan.'
            );
    }


    /**
     * Menambahkan jenis pelanggaran baru
     */
    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'poin' => 'required|integer|min:1|max:100',
        ]);


        DB::transaction(function () use ($validated) {

            AturanPoin::create([
                'kategori_id' => 1,
                'judul' => $validated['nama_kategori'],
                'nilai_poin' => abs((int) $validated['poin']),
            ]);
        });


        return redirect()
            ->route('guru.pelanggaran')
            ->with(
                'success',
                'Jenis pelanggaran berhasil ditambahkan.'
            );
    }
}