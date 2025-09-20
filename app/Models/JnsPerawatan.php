<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JnsPerawatan extends Model
{
    protected $table = 'jns_perawatan';

    public $timestamps = false;

    protected $primaryKey = 'kd_jenis_prw';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_jenis_prw',
        'nm_perawatan',
        'kd_kategori',
        'material',
        'bhp',
        'tarif_tindakandr',
        'tarif_tindakanpr',
        'kso',
        'menejemen',
        'total_byrdr',
        'total_byrpr',
        'total_byrdrpr',
        'kd_pj',
        'kd_poli',
        'status'
    ];

    protected $casts = [
        'material' => 'decimal:2',
        'bhp' => 'decimal:2',
        'tarif_tindakandr' => 'decimal:2',
        'tarif_tindakanpr' => 'decimal:2',
        'kso' => 'decimal:2',
        'menejemen' => 'decimal:2',
        'total_byrdr' => 'decimal:2',
        'total_byrpr' => 'decimal:2',
        'total_byrdrpr' => 'decimal:2',
    ];

    // Relationships
    public function rawatJlDr(): HasMany
    {
        return $this->hasMany(RawatJlDr::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function rawatJlPr(): HasMany
    {
        return $this->hasMany(RawatJlPr::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function rawatJlDrPr(): HasMany
    {
        return $this->hasMany(RawatJlDrPr::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    // You may need to create these models if they don't exist
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPerawatan::class, 'kd_kategori', 'kd_kategori');
    }

    public function penjab(): BelongsTo
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function poliklinik(): BelongsTo
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kd_kategori', $kategori);
    }

    public function scopeByPoli($query, $poli)
    {
        return $query->where('kd_poli', $poli);
    }

    public function scopeByPenjab($query, $penjab)
    {
        return $query->where('kd_pj', $penjab);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('kd_jenis_prw', 'like', "%{$search}%")
              ->orWhere('nm_perawatan', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === '1';
    }

    public function getFormattedMaterialAttribute(): string
    {
        return number_format($this->material, 0, ',', '.');
    }

    public function getFormattedBhpAttribute(): string
    {
        return number_format($this->bhp, 0, ',', '.');
    }

    public function getFormattedTotalByrdrAttribute(): string
    {
        return number_format($this->total_byrdr, 0, ',', '.');
    }

    public function getFormattedTotalByrprAttribute(): string
    {
        return number_format($this->total_byrpr, 0, ',', '.');
    }

    public function getFormattedTotalByrdrprAttribute(): string
    {
        return number_format($this->total_byrdrpr, 0, ',', '.');
    }

    // Get total cost for doctor
    public function getTotalDokterAttribute(): float
    {
        return ($this->material ?? 0) + $this->bhp + ($this->tarif_tindakandr ?? 0) + ($this->kso ?? 0) + ($this->menejemen ?? 0);
    }

    // Get total cost for petugas
    public function getTotalPetugasAttribute(): float
    {
        return ($this->material ?? 0) + $this->bhp + ($this->tarif_tindakanpr ?? 0) + ($this->kso ?? 0) + ($this->menejemen ?? 0);
    }

    // Get total cost for doctor + petugas
    public function getTotalDokterPetugasAttribute(): float
    {
        return ($this->material ?? 0) + $this->bhp + ($this->tarif_tindakandr ?? 0) + ($this->tarif_tindakanpr ?? 0) + ($this->kso ?? 0) + ($this->menejemen ?? 0);
    }
}