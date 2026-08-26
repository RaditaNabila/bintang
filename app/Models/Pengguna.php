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
}