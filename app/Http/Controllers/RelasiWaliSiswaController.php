<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Siswa;
use App\Models\OrangTuaSiswa;
use Illuminate\Http\Request;

class RelasiWaliSiswaController extends Controller
{
    public function index()
    {
        // Data dropdown pilihan wali murid
        $wali = Pengguna::where('peran', 'orang_tua')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        // Data dropdown pilihan siswa
        $siswa = Siswa::with('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        // Mengambil Wali yang memiliki relasi, di-paginate 15 wali per halaman
        $waliRelasi = Pengguna::whereHas('orangTuaSiswa')
            ->with(['orangTuaSiswa.siswa.kelas'])
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('guru.relasi', compact(
            'wali',
            'siswa',
            'waliRelasi'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'siswa_id' => 'required|exists:siswa,id',
        ], [
            'pengguna_id.required' => 'Akun wali wajib dipilih.',
            'pengguna_id.exists' => 'Akun wali tidak ditemukan.',
            'siswa_id.required' => 'Siswa wajib dipilih.',
            'siswa_id.exists' => 'Data siswa tidak ditemukan.',
        ]);

        $wali = Pengguna::where('id', $request->pengguna_id)
            ->where('peran', 'orang_tua')
            ->where('status', 'aktif')
            ->first();

        if (!$wali) {
            return back()
                ->withInput()
                ->with('error', 'Akun yang dipilih bukan akun orang tua yang aktif.');
        }

        $siswa = Siswa::where('id', $request->siswa_id)->first();

        if (!$siswa) {
            return back()
                ->withInput()
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        $sudahAda = OrangTuaSiswa::where('pengguna_id', $request->pengguna_id)
            ->where('siswa_id', $request->siswa_id)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->with('error', 'Relasi wali dan siswa tersebut sudah terdaftar.');
        }

        OrangTuaSiswa::create([
            'pengguna_id' => $request->pengguna_id,
            'siswa_id' => $request->siswa_id,
        ]);

        return redirect()
            ->route('guru.relasi-wali-siswa.index')
            ->with('success', 'Relasi wali dan siswa berhasil disimpan.');
    }

    public function destroy($id)
    {
        $relasi = OrangTuaSiswa::find($id);

        if (!$relasi) {
            return back()->with('error', 'Relasi tidak ditemukan.');
        }

        $relasi->delete();

        return redirect()
            ->route('guru.relasi-wali-siswa.index')
            ->with('success', 'Relasi wali dan siswa berhasil dihapus.');
    }
}
