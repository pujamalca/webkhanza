<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriksaLab extends Model
{
    protected $table = 'periksa_lab';
    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'nip',
        'kd_jenis_prw',
        'tgl_periksa',
        'jam',
        'dokter_perujuk',
        'bagian_rs',
        'bhp',
        'bagian_perujuk',
        'bagian_dokter',
        'bagian_laborat',
        'kso',
        'menejemen',
        'biaya_item',
        'kd_dokter',
        'status'
    ];

    protected $casts = [
        'tgl_periksa' => 'date',
        'bagian_rs' => 'decimal:2',
        'bhp' => 'decimal:2',
        'bagian_perujuk' => 'decimal:2',
        'bagian_dokter' => 'decimal:2',
        'bagian_laborat' => 'decimal:2',
        'kso' => 'decimal:2',
        'menejemen' => 'decimal:2',
        'biaya_item' => 'decimal:2'
    ];

    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokterPerujuk(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_perujuk', 'kd_dokter');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatan::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function detailPeriksaLab(): HasMany
    {
        return $this->hasMany(DetailPeriksaLab::class, 'no_rawat', 'no_rawat');
    }

    // Scopes
    public function scopeByNoRawat($query, $noRawat)
    {
        return $query->where('no_rawat', $noRawat);
    }

    public function scopeByTanggal($query, $tanggalMulai, $tanggalSelesai = null)
    {
        $query->where('tgl_periksa', '>=', $tanggalMulai);
        if ($tanggalSelesai) {
            $query->where('tgl_periksa', '<=', $tanggalSelesai);
        }
        return $query;
    }

    // Accessors
    public function getFormattedTglPeriksaAttribute(): string
    {
        return $this->tgl_periksa ? $this->tgl_periksa->format('d/m/Y') : '-';
    }

    public function getFormattedJamAttribute(): string
    {
        return $this->jam ? date('H:i', strtotime($this->jam)) : '-';
    }

    public function getTotalBiayaAttribute(): float
    {
        return $this->bagian_rs + $this->bhp + $this->bagian_perujuk +
               $this->bagian_dokter + $this->bagian_laborat + $this->kso + $this->menejemen;
    }
}