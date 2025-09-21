<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPeriksaLab extends Model
{
    protected $table = 'detail_periksa_lab';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'no_rawat',
        'kd_jenis_prw',
        'tgl_periksa',
        'jam',
        'id_template',
        'nilai',
        'nilai_rujukan',
        'keterangan',
        'bagian_rs',
        'bhp',
        'bagian_perujuk',
        'bagian_dokter',
        'bagian_laborat',
        'kso',
        'menejemen',
        'biaya_item'
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

    public function templateLaboratorium(): BelongsTo
    {
        return $this->belongsTo(TemplateLaboratorium::class, ['kd_jenis_prw', 'id_template'], ['kd_jenis_prw', 'id_template']);
    }

    public function jenisPerawatan(): BelongsTo
    {
        return $this->belongsTo(JenisPerawatan::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function periksaLab(): BelongsTo
    {
        return $this->belongsTo(PeriksaLab::class, 'no_rawat', 'no_rawat');
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

    public function scopeSearchByKeyword($query, $keyword)
    {
        return $query->whereHas('templateLaboratorium', function($q) use ($keyword) {
            $q->where('Pemeriksaan', 'like', '%' . $keyword . '%');
        })
        ->orWhere('nilai', 'like', '%' . $keyword . '%')
        ->orWhere('keterangan', 'like', '%' . $keyword . '%');
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

    public function getIsAbnormalAttribute(): bool
    {
        return in_array(strtoupper($this->keterangan), ['L', 'T', 'H']);
    }

    public function getStatusNormalAttribute(): string
    {
        $ket = strtoupper($this->keterangan);
        if ($ket === 'L') return 'Rendah';
        if ($ket === 'H') return 'Tinggi';
        if ($ket === 'T') return 'Tidak Normal';
        return 'Normal';
    }
}