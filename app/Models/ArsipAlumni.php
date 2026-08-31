<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipAlumni extends Model
{
    protected $table = 'arsip_alumni';

    protected $primaryKey = 'id';

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

    protected $casts = [
        'diarsipkan_pada' => 'datetime',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}