<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProsedurPasien extends Model
{
    protected $table = 'prosedur_pasien';
    protected $primaryKey = ['no_rawat', 'kode'];
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'kode',
        'status',
        'prioritas'
    ];

    protected $casts = [
        'prioritas' => 'integer',
        'status' => 'string'
    ];

    // Relationships
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function icd9(): BelongsTo
    {
        return $this->belongsTo(Icd9::class, 'kode', 'kode');
    }

    // Scopes
    public function scopeByNoRawat($query, $noRawat)
    {
        return $query->where('no_rawat', $noRawat);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    public function scopeRalan($query)
    {
        return $query->where('status', 'Ralan');
    }

    public function scopeRanap($query)
    {
        return $query->where('status', 'Ranap');
    }

    // Helper methods
    public function isRalan(): bool
    {
        return $this->status === 'Ralan';
    }

    public function isRanap(): bool
    {
        return $this->status === 'Ranap';
    }

    public function getPrioritasTextAttribute(): string
    {
        return match($this->prioritas) {
            1 => '🔴 Primer',
            2 => '🟡 Sekunder',
            3 => '🔵 Tersier',
            default => '⚫ Tidak Ditetapkan'
        };
    }

    public function getFormattedStatusAttribute(): string
    {
        return $this->status === 'Ralan' ? '🏥 Rawat Jalan' : '🛏️ Rawat Inap';
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
}