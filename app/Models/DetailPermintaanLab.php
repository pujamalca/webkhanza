<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPermintaanLab extends Model
{
    protected $table = 'detail_permintaan_lab';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'noorder',
        'kd_jenis_prw',
        'id_template',
        'stts_bayar'
    ];

    public function permintaanLab(): BelongsTo
    {
        return $this->belongsTo(PermintaanLab::class, 'noorder', 'noorder');
    }

    public function templateLaboratorium(): BelongsTo
    {
        return $this->belongsTo(TemplateLaboratorium::class, ['kd_jenis_prw', 'id_template'], ['kd_jenis_prw', 'id_template']);
    }

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatan::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    // Scopes
    public function scopeByNoOrder($query, $noOrder)
    {
        return $query->where('noorder', $noOrder);
    }

    public function scopeByJenisPerawatan($query, $kdJenisPrw)
    {
        return $query->where('kd_jenis_prw', $kdJenisPrw);
    }

    // Accessors
    public function getStatusBayarLabelAttribute(): string
    {
        return match($this->stts_bayar) {
            'Sudah' => 'Sudah Bayar',
            'Belum' => 'Belum Bayar',
            'Subsidi' => 'Subsidi',
            default => 'Belum Bayar'
        };
    }
}