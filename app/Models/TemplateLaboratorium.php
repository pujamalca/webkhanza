<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateLaboratorium extends Model
{
    protected $table = 'template_laboratorium';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'kd_jenis_prw',
        'id_template',
        'Pemeriksaan',
        'satuan',
        'nilai_rujukan_ld',
        'nilai_rujukan_la',
        'nilai_rujukan_pd',
        'nilai_rujukan_pa',
        'bagian_rs',
        'bhp',
        'bagian_perujuk',
        'bagian_dokter',
        'bagian_laborat',
        'kso',
        'menejemen',
        'biaya_item',
        'urut'
    ];

    protected $casts = [
        'bagian_rs' => 'decimal:2',
        'bhp' => 'decimal:2',
        'bagian_perujuk' => 'decimal:2',
        'bagian_dokter' => 'decimal:2',
        'bagian_laborat' => 'decimal:2',
        'kso' => 'decimal:2',
        'menejemen' => 'decimal:2',
        'biaya_item' => 'decimal:2'
    ];

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatan::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function jenisPerawatanLab(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function detailPeriksaLab(): HasMany
    {
        return $this->hasMany(DetailPeriksaLab::class, ['kd_jenis_prw', 'id_template'], ['kd_jenis_prw', 'id_template']);
    }

    // Scopes
    public function scopeByJenisPerawatan($query, $kdJenisPrw)
    {
        return $query->where('kd_jenis_prw', $kdJenisPrw);
    }

    public function scopeSearchByName($query, $keyword)
    {
        return $query->where('Pemeriksaan', 'like', '%' . $keyword . '%');
    }

    // Accessors
    public function getNamaPemeriksaanAttribute(): string
    {
        return $this->Pemeriksaan;
    }

    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_item, 0, ',', '.');
    }
}