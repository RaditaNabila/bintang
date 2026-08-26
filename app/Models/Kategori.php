<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'jenis',
    ];

    public function transaksiPoin()
    {
        return $this->hasMany(TransaksiPoin::class, 'kategori_id');
    }
}