<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AturanPoin extends Model
{
    protected $table = 'aturan_poin';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'kategori_id',
        'judul',
        'nilai_poin',
        'dibuat_pada',
        'diperbarui_pada',
    ];

    protected $casts = [
        'kategori_id' => 'integer',
        'nilai_poin' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            Kategori::class,
            'kategori_id',
            'id'
        );
    }
}