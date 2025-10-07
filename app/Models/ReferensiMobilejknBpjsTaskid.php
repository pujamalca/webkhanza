<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferensiMobilejknBpjsTaskid extends Model
{
    protected $table = 'referensi_mobilejkn_bpjs_taskid';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'taskid',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    // Relasi ke Reg Periksa
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}
