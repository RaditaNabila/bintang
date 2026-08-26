<?php
namespace App\Http\Controllers;
use App\Models\ArsipAlumni;
use Illuminate\Http\Request;
class ArsipAlumniController extends Controller
{
    public function index(Request $request)
    {
        $arsip=ArsipAlumni::orderByDesc('tahun_kelulusan')->orderBy('nama_lengkap')->get();
        $pindah=$arsip->where('jenis_arsip','pindah')->values();
        $alumni=$arsip->where('jenis_arsip','lulus');
        $tahunAlumni=$alumni->whereNotNull('tahun_kelulusan')->groupBy('tahun_kelulusan')->map(function($data){
            return [
                'tahun'=>$data->first()->tahun_kelulusan,
                'nama_angkatan'=>$data->first()->nama_angkatan,
                'jumlah'=>$data->count(),
                'data'=>$data->values()
            ];
        })->sortByDesc('tahun')->values();
        return view('guru.arsip',compact('arsip','pindah','alumni','tahunAlumni'));
    }
}