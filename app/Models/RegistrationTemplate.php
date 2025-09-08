<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationTemplate extends Model
{
    protected $fillable = [
        'name',
        'kd_dokter',
        'kd_poli',
        'kd_pj',
        'biaya_reg',
        'status_lanjut',
        'stts_daftar',
        'is_active',
        'description'
    ];

    protected $casts = [
        'biaya_reg' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    public function penjab()
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
