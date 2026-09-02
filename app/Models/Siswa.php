<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $primaryKey = 'id'; 

    // Sesuaikan nama kolom timestamps agar cocok dengan database Anda
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nisn',
        'nis',
        'nama_lengkap',
        'kelas_id',
        'jenis_kelamin',
        'poin_saat_ini',
        'status',
    ];

    protected $casts = [
        'poin_saat_ini' => 'integer',
        'kelas_id'      => 'integer',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }

    public function TransaksiPoin()
    {
        return $this->hasMany(
            TransaksiPoin::class,
            'siswa_id'
        );
    }
}