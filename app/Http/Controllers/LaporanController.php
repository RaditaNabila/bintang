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

        $siswaQuery = Siswa::with('kelas')
            ->where('status', 'aktif');

        // =========================
        // FILTER BERDASARKAN KELAS
        // =========================

        if ($kelasId !== 'all') {
            $siswaQuery->where('kelas_id', $kelasId);
        }

        // =========================
        // FILTER NAMA / NIS / NISN
        // =========================

        if (!empty($search)) {
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        // =========================
        // DATA SISWA
        // =========================

        $siswa = $siswaQuery
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(15)
            ->withQueryString();

        // =========================
        // TENTUKAN VIEW BERDASARKAN ROLE
        // =========================

        $view = $request->routeIs('pengajar.laporan')
            ? 'pengajar.laporan'
            : 'guru.laporan';

        return view($view, compact(
            'siswa',
            'kelas'
        ));
    }
}