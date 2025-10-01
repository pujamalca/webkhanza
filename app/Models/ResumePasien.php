<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumePasien extends Model
{
    public $timestamps = false;
    protected $table = 'resume_pasien';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'kd_dokter',
        'keluhan_utama',
        'jalannya_penyakit',
        'pemeriksaan_penunjang',
        'hasil_laborat',
        'diagnosa_utama',
        'kd_diagnosa_utama',
        'diagnosa_sekunder',
        'kd_diagnosa_sekunder',
        'diagnosa_sekunder2',
        'kd_diagnosa_sekunder2',
        'diagnosa_sekunder3',
        'kd_diagnosa_sekunder3',
        'diagnosa_sekunder4',
        'kd_diagnosa_sekunder4',
        'prosedur_utama',
        'kd_prosedur_utama',
        'prosedur_sekunder',
        'kd_prosedur_sekunder',
        'prosedur_sekunder2',
        'kd_prosedur_sekunder2',
        'prosedur_sekunder3',
        'kd_prosedur_sekunder3',
        'kondisi_pulang',
        'obat_pulang',
    ];

    protected $casts = [
        'kondisi_pulang' => 'string',
    ];

    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function diagnosisUtama(): BelongsTo
    {
        return $this->belongsTo(PenyakitIcd10::class, 'kd_diagnosa_utama', 'kd_penyakit');
    }

    public function diagnosisSekunder(): BelongsTo
    {
        return $this->belongsTo(PenyakitIcd10::class, 'kd_diagnosa_sekunder', 'kd_penyakit');
    }

    public function diagnosisSekunder2(): BelongsTo
    {
        return $this->belongsTo(PenyakitIcd10::class, 'kd_diagnosa_sekunder2', 'kd_penyakit');
    }

    public function diagnosisSekunder3(): BelongsTo
    {
        return $this->belongsTo(PenyakitIcd10::class, 'kd_diagnosa_sekunder3', 'kd_penyakit');
    }

    public function diagnosisSekunder4(): BelongsTo
    {
        return $this->belongsTo(PenyakitIcd10::class, 'kd_diagnosa_sekunder4', 'kd_penyakit');
    }

    public function prosedurUtama(): BelongsTo
    {
        return $this->belongsTo(Icd9::class, 'kd_prosedur_utama', 'kode');
    }

    public function prosedurSekunder(): BelongsTo
    {
        return $this->belongsTo(Icd9::class, 'kd_prosedur_sekunder', 'kode');
    }

    public function prosedurSekunder2(): BelongsTo
    {
        return $this->belongsTo(Icd9::class, 'kd_prosedur_sekunder2', 'kode');
    }

    public function prosedurSekunder3(): BelongsTo
    {
        return $this->belongsTo(Icd9::class, 'kd_prosedur_sekunder3', 'kode');
    }
}