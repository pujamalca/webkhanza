<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GudangBarang extends Model
{
    protected $table = 'gudangbarang';
    public $timestamps = false;

    // Composite primary key
    protected $primaryKey = ['kode_brng', 'kd_bangsal', 'no_batch', 'no_faktur'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_brng',
        'kd_bangsal',
        'stok',
        'no_batch',
        'no_faktur'
    ];

    protected $casts = [
        'stok' => 'double',
    ];

    // Relationships
    public function databarang(): BelongsTo
    {
        return $this->belongsTo(Databarang::class, 'kode_brng', 'kode_brng');
    }

    // Scopes
    public function scopeByKodeBarang($query, $kodeBarang)
    {
        return $query->where('kode_brng', $kodeBarang);
    }

    public function scopeByBangsal($query, $kdBangsal)
    {
        return $query->where('kd_bangsal', $kdBangsal);
    }

    // Helper methods
    public function getTotalStokByBarangAttribute(): float
    {
        return self::where('kode_brng', $this->kode_brng)->sum('stok');
    }
}