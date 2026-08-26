<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipAlumni extends Model
{
    protected $table = 'arsip_alumni';

    public $timestamps = false;

    protected $fillable = [
        'siswa_id',
        'nis',
        'nama_lengkap',
        'kelas_terakhir',
        'poin_akhir',
        'jenis_arsip',
        'tahun_kelulusan',
        'nama_angkatan',
        'catatan_status',
        'diarsipkan_pada',
        'dibuat_pada',
        'diperbarui_pada',
    ];
}