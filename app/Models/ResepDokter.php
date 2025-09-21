<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepDokter extends Model
{
    protected $table = 'resep_dokter';
    public $timestamps = false;

    // Composite primary key
    protected $primaryKey = ['no_resep', 'kode_brng'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_resep',
        'kode_brng',
        'jml',
        'aturan_pakai'
    ];

    protected $casts = [
        'jml' => 'double',
    ];

    // Relationships
    public function resepObat(): BelongsTo
    {
        return $this->belongsTo(ResepObat::class, 'no_resep', 'no_resep');
    }

    public function databarang(): BelongsTo
    {
        return $this->belongsTo(Databarang::class, 'kode_brng', 'kode_brng');
    }

    // Scopes
    public function scopeByNoResep($query, $noResep)
    {
        return $query->where('no_resep', $noResep);
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

    // Helper methods
    public function getFormattedJmlAttribute(): string
    {
        return number_format($this->jml, 0);
    }

    public function getTotalHargaAttribute(): float
    {
        if ($this->databarang) {
            return $this->jml * $this->databarang->ralan; // Using ralan price
        }
        return 0;
    }

    public function getFormattedTotalHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }
}