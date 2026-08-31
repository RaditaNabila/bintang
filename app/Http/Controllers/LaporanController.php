<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->query('kelas_id', 'all');
        $search = $request->query('search', '');

        $kelas = Kelas::all();

        $siswaQuery = Siswa::with('kelas')->where('status', 'aktif');

        // Filter berdasarkan kelas
        if ($kelasId !== 'all') {
            $siswaQuery->where('kelas_id', $kelasId);
        }

        // Filter berdasarkan nama / NISN
        if (!empty($search)) {
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $siswa = $siswaQuery->orderBy('nama_lengkap', 'asc')->paginate(15)->withQueryString();

        return view('guru.laporan', compact('siswa', 'kelas'));
    }
}
