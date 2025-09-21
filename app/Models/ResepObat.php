<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResepObat extends Model
{
    protected $table = 'resep_obat';
    protected $primaryKey = 'no_resep';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_resep',
        'tgl_perawatan',
        'jam',
        'no_rawat',
        'kd_dokter',
        'tgl_peresepan',
        'jam_peresepan',
        'status',
        'tgl_penyerahan',
        'jam_penyerahan'
    ];

    protected $casts = [
        'tgl_peresepan' => 'date',
    ];

    protected $attributes = [
        'tgl_perawatan' => '0000-00-00',
        'jam' => '00:00:00',
        'tgl_penyerahan' => '0000-00-00',
        'jam_penyerahan' => '00:00:00',
    ];

    // Relationships
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function resepDokter(): HasMany
    {
        return $this->hasMany(ResepDokter::class, 'no_resep', 'no_resep');
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

    // Helper methods
    public function getFormattedTglPerawatanAttribute(): string
    {
        return $this->tgl_perawatan && $this->tgl_perawatan !== '0000-00-00' ?
            \Carbon\Carbon::parse($this->tgl_perawatan)->format('d/m/Y') : '-';
    }

    public function getFormattedTglPeresepanAttribute(): string
    {
        return $this->tgl_peresepan ? $this->tgl_peresepan->format('d/m/Y') : '-';
    }

    public function getFormattedTglPenyerahanAttribute(): string
    {
        return $this->tgl_penyerahan && $this->tgl_penyerahan !== '0000-00-00' ?
            \Carbon\Carbon::parse($this->tgl_penyerahan)->format('d/m/Y') : '-';
    }

    // Generate new resep number (format: YYYYMMDD0001)
    public static function generateNoResep(): string
    {
        $today = date('Ymd'); // 20250920
        $prefix = $today;

        $lastResep = self::where('no_resep', 'like', $prefix . '%')
            ->orderBy('no_resep', 'desc')
            ->first();

        if ($lastResep) {
            $lastNumber = (int) substr($lastResep->no_resep, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Validate resep number format
    public static function validateNoResep($noResep): bool
    {
        return preg_match('/^\d{8}\d{4}$/', $noResep) === 1;
    }
}