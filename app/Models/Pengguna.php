<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama',
        'nama_pengguna',
        'kata_sandi',
        'peran',
        'status',
        'token_ingat',
    ];

    protected $hidden = [
        'kata_sandi',
        'token_ingat',
    ];

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function getRememberTokenName()
    {
        return 'token_ingat';
    }

    /**
     * Relasi ke tabel pivot / perantara orang_tua_siswa
     */
    public function orangTuaSiswa()
    {
        return $this->hasMany(OrangTuaSiswa::class, 'pengguna_id', 'id');
    }

    /**
     * Relasi langsung ke data Siswa melalui tabel perantara orang_tua_siswa
     */
    public function siswa()
    {
        return $this->hasManyThrough(
            Siswa::class,
            OrangTuaSiswa::class,
            'pengguna_id', // Foreign key di tabel orang_tua_siswa
            'id',          // Foreign key di tabel siswa
            'id',          // Local key di tabel pengguna
            'siswa_id'     // Local key di tabel orang_tua_siswa
        );
    }
}
