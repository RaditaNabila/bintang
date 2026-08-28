<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class DataSiswaController extends Controller
{
    /**
     * Menampilkan data siswa dengan Pagination 15 & Filter/Search
     */
    public function index(Request $request)
    {
        $tingkat = $request->query('tingkat', 'all');
        $kelasId = $request->query('kelas_id', 'all');
        $search = $request->query('search', '');

        $kelas = Kelas::all();

        $siswaQuery = Siswa::with('kelas');

        // Filter berdasarkan Tingkat Kelas (1, 2, 3, dst.)
        if ($tingkat !== 'all') {
            $siswaQuery->whereHas('kelas', function ($q) use ($tingkat) {
                $q->where('tingkat', $tingkat);
            });
        }

        // Filter spesifik Rombel jika kelas_id dipilih
        if ($kelasId !== 'all') {
            $siswaQuery->where('kelas_id', $kelasId);
        }

        // Filter Pencarian NISN / Nama
        if (!empty($search)) {
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $siswa = $siswaQuery->paginate(10)->withQueryString();

        return view('guru.data-siswa', compact('siswa', 'kelas'));
    }

    /**
     * Menyimpan siswa baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn',
            'nis' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'kelas_id' => 'required|integer',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'poin_saat_ini' => 'nullable|integer',
        ]);

        Siswa::create([
            'nisn' => $validated['nisn'],
            'nis' => $validated['nis'] ?? null,
            'nama_lengkap' => $validated['nama_lengkap'],
            'kelas_id' => $validated['kelas_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'poin_saat_ini' => $validated['poin_saat_ini'] ?? 250,
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('guru.data-siswa')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Mengubah data siswa
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn,' . $id,
            'nis' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'kelas_id' => 'required|integer',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'poin_saat_ini' => 'required|integer|min:0',
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('guru.data-siswa')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus siswa
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        $siswa->delete();

        return redirect()
            ->route('guru.data-siswa')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Reset poin siswa menjadi 250
     */
    public function resetPoin(Request $request)
    {
        $request->validate([
            'scope' => 'required|in:all,current',
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:siswa,id',
        ]);

        if ($request->scope === 'all') {

            Siswa::where('status', 'aktif')
                ->update([
                    'poin_saat_ini' => 250
                ]);

            $message = 'Poin seluruh siswa aktif berhasil direset menjadi 250.';

        } else {

            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()
                    ->route('guru.data-siswa')
                    ->with('error', 'Tidak ada siswa yang dipilih untuk direset.');
            }

            Siswa::whereIn('id', $ids)
                ->where('status', 'aktif')
                ->update([
                    'poin_saat_ini' => 250
                ]);

            $message = 'Poin siswa pada tampilan saat ini berhasil direset menjadi 250.';
        }

        return redirect()
            ->route('guru.data-siswa')
            ->with('success', $message);
    }

    /**
     * Menambahkan ruangan kelas baru
     */
    public function storeKelas(Request $request)
    {
        $validated = $request->validate([
            'tingkat' => 'required|integer|min:1|max:6',
            'nama_kelas' => 'required|string|max:50',
        ]);

        $cek = Kelas::where('tingkat', $validated['tingkat'])
            ->where('nama_kelas', $validated['nama_kelas'])
            ->exists();

        if ($cek) {
            return redirect()
                ->route('guru.data-siswa')
                ->with('error', 'Ruangan kelas tersebut sudah ada.');
        }

        Kelas::create([
            'tingkat' => $validated['tingkat'],
            'nama_kelas' => $validated['nama_kelas'],
        ]);

        return redirect()
            ->route('guru.data-siswa')
            ->with('success', 'Ruangan kelas berhasil ditambahkan.');
    }
}