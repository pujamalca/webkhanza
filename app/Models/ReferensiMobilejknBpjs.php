<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReferensiMobilejknBpjs extends Model
{
    use LogsActivity;

    protected $table = 'referensi_mobilejkn_bpjs';
    protected $primaryKey = 'nobooking';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nobooking',
        'no_rawat',
        'nomorkartu',
        'nik',
        'nohp',
        'kodepoli',
        'pasienbaru',
        'norm',
        'tanggalperiksa',
        'kodedokter',
        'jampraktek',
        'jeniskunjungan',
        'nomorreferensi',
        'nomorantrean',
        'angkaantrean',
        'estimasidilayani',
        'sisakuotajkn',
        'kuotajkn',
        'sisakuotanonjkn',
        'kuotanonjkn',
        'status',
        'validasi',
        'statuskirim',
    ];

    protected $casts = [
        'tanggalperiksa' => 'date',
        'validasi' => 'datetime',
        'sisakuotajkn' => 'integer',
        'kuotajkn' => 'integer',
        'sisakuotanonjkn' => 'integer',
        'kuotanonjkn' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "Service JKN {$eventName}")
            ->useLogName('service_jkn');
    }

    // Relasi ke Pasien
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'norm', 'no_rkm_medis');
    }

    // Relasi ke Reg Periksa
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    // Relasi ke Poliklinik
    public function poliklinik(): BelongsTo
    {
        return $this->belongsTo(Poliklinik::class, 'kodepoli', 'kd_poli');
    }

    // Relasi ke Dokter
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'kodedokter', 'kd_dokter');
    }
}
