<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPerawatan extends Model
{
    protected $table = 'catatan_perawatan';
    protected $primaryKey = ['tanggal', 'jam', 'no_rawat', 'nip'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'jam',
        'no_rawat',
        'nip',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relationships
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'nip', 'kd_dokter');
    }

    // Scopes
    public function scopeByNoRawat($query, $noRawat)
    {
        return $query->where('no_rawat', $noRawat);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    // Helper methods
    public function getFormattedTanggalAttribute(): string
    {
        return $this->tanggal ? $this->tanggal->format('d/m/Y') : '-';
    }

    public function getFormattedWaktuAttribute(): string
    {
        return $this->formatted_tanggal . ' ' . $this->jam;
    }

    public function getPetugasOrDokterNameAttribute(): string
    {
        if ($this->petugas) {
            return $this->petugas->nama;
        } elseif ($this->dokter) {
            return $this->dokter->nm_dokter;
        }
        return 'Tidak diketahui';
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