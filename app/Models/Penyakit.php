<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyakit extends Model
{
    use HasFactory;

    protected $table = 'penyakit';
    protected $primaryKey = 'kd_penyakit';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_penyakit',
        'nm_penyakit',
        'ciri_ciri',
        'keterangan',
        'kd_ktg',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function diagnosaPasien(): HasMany
    {
        return $this->hasMany(DiagnosaPasien::class, 'kd_penyakit', 'kd_penyakit');
    }

    public function kategoriPenyakit(): BelongsTo
    {
        return $this->belongsTo(KategoriPenyakit::class, 'kd_ktg', 'kd_ktg');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('kd_penyakit')
                    ->where('kd_penyakit', '!=', '')
                    ->where('kd_penyakit', '!=', '-');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kd_ktg', $kategori);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('penyakit.kd_penyakit', 'like', "%{$search}%")
              ->orWhere('penyakit.nm_penyakit', 'like', "%{$search}%")
              ->orWhere('penyakit.ciri_ciri', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function isActive(): bool
    {
        return !empty($this->kd_penyakit) && $this->kd_penyakit !== '-';
    }

    public function isMenular(): bool
    {
        return $this->status === 'Menular';
    }

    public function getFormattedStatusAttribute(): string
    {
        return $this->status === 'Menular' ? '⚠️ Menular' : '✅ Tidak Menular';
    }

    // Get display name with code
    public function getDisplayNameAttribute(): string
    {
        return $this->kd_penyakit . ' - ' . $this->nm_penyakit;
    }
}