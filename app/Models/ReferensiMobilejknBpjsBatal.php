<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferensiMobilejknBpjsBatal extends Model
{
    protected $table = 'referensi_mobilejkn_bpjs_batal';
    protected $primaryKey = 'nobooking';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rkm_medis',
        'no_rawat_batal',
        'nomorreferensi',
        'tanggalbatal',
        'keterangan',
        'statuskirim',
        'nobooking',
    ];

    protected $casts = [
        'tanggalbatal' => 'datetime',
    ];

    // Relasi ke Pasien
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    // Relasi ke Reg Periksa
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat_batal', 'no_rawat');
    }
}
