<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPerawatanLab extends Model
{
    protected $table = 'jns_perawatan_lab';
    protected $primaryKey = 'kd_jenis_prw';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_jenis_prw',
        'nm_perawatan',
        'bagian_rs',
        'bhp',
        'bagian_perujuk',
        'bagian_dokter',
        'bagian_laborat',
        'kso',
        'menejemen',
        'total_byr',
        'kd_pj',
        'status',
        'kelas',
        'kategori'
    ];

    protected $casts = [
        'bagian_rs' => 'decimal:2',
        'bhp' => 'decimal:2',
        'bagian_perujuk' => 'decimal:2',
        'bagian_dokter' => 'decimal:2',
        'bagian_laborat' => 'decimal:2',
        'kso' => 'decimal:2',
        'menejemen' => 'decimal:2',
        'total_byr' => 'decimal:2'
    ];

    public function templateLaboratorium(): HasMany
    {
        return $this->hasMany(TemplateLaboratorium::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', '1');
    }

    public function scopeByKeyword($query, $keyword)
    {
        return $query->where('nm_perawatan', 'like', '%' . $keyword . '%');
    }

    public function scopePK($query)
    {
        return $query->where('kategori', 'PK');
    }

    // Accessors
    public function getFormattedTotalByrAttribute(): string
    {
        return 'Rp ' . number_format($this->total_byr, 0, ',', '.');
    }
}