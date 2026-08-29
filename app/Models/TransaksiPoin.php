<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPoin extends Model
{
    protected $table = 'transaksi_poin';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
    'siswa_id',
    'pengguna_id',
    'kategori_id',
    'aturan_poin_id',
    'jenis',
    'poin',
    'keterangan',
    'sanksi',
    'tanggal_transaksi',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function aturanPoin()
    {
        return $this->belongsTo(AturanPoin::class, 'aturan_poin_id');
    }
}