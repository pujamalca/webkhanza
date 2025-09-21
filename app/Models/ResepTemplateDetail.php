<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepTemplateDetail extends Model
{
    protected $table = 'resep_template_details';

    protected $fillable = [
        'template_id',
        'kode_brng',
        'jumlah',
        'aturan_pakai'
    ];

    public function resepTemplate(): BelongsTo
    {
        return $this->belongsTo(ResepTemplate::class, 'template_id', 'id');
    }

    public function databarang(): BelongsTo
    {
        return $this->belongsTo(Databarang::class, 'kode_brng', 'kode_brng');
    }
}