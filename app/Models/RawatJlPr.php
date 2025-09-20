<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawatJlPr extends Model
{
    protected $table = 'rawat_jl_pr';

    public $timestamps = false;

    protected $primaryKey = ['no_rawat', 'kd_jenis_prw', 'nip', 'tgl_perawatan', 'jam_rawat'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'kd_jenis_prw',
        'nip',
        'tgl_perawatan',
        'jam_rawat',
        'material',
        'bhp',
        'tarif_tindakanpr',
        'kso',
        'menejemen',
        'biaya_rawat',
        'stts_bayar'
    ];

    protected $casts = [
        'tgl_perawatan' => 'date',
        'jam_rawat' => 'datetime:H:i:s',
        'material' => 'decimal:2',
        'bhp' => 'decimal:2',
        'tarif_tindakanpr' => 'decimal:2',
        'kso' => 'decimal:2',
        'menejemen' => 'decimal:2',
        'biaya_rawat' => 'decimal:2',
    ];

    // Relationships
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JnsPerawatan::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    // Helper methods
    public function getTotalBiayaAttribute(): float
    {
        return $this->material + $this->bhp + $this->tarif_tindakanpr + ($this->kso ?? 0) + ($this->menejemen ?? 0);
    }

    public function getFormattedJamRawatAttribute(): string
    {
        return $this->jam_rawat->format('H:i:s');
    }

    public function getFormattedTglPerawatanAttribute(): string
    {
        return $this->tgl_perawatan->format('Y-m-d');
    }

    // Override getKey method for composite primary key
    public function getKey()
    {
        $key = [];
        foreach ($this->primaryKey as $keyName) {
            $key[$keyName] = $this->getAttribute($keyName);
        }
        return $key;
    }

    // Override find method for composite primary key
    public static function find($id)
    {
        if (is_array($id)) {
            $query = static::query();
            foreach ($id as $key => $value) {
                $query->where($key, $value);
            }
            return $query->first();
        }

        return null;
    }

    // Scope for filtering by no_rawat
    public function scopeByNoRawat($query, $noRawat)
    {
        return $query->where('no_rawat', $noRawat);
    }

    // Scope for filtering by date range
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_perawatan', [$startDate, $endDate]);
    }
}