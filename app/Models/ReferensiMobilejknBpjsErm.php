<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReferensiMobilejknBpjsErm extends Model
{
    use LogsActivity;

    protected $table = 'referensi_mobilejkn_bpjs_erm';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'tanggal_periksa',
        'jam_periksa',
        'no_rkm_medis',
        'no_rawat',
        'no_kartu',
        'kodepoli',
        'nama_poli',
        'nomor_referensi',
        'jenis_kunjungan',
        'taskid',
        'taskid1',
        'taskid2',
        'taskid3',
        'taskid4',
        'taskid5',
        'taskid6',
        'taskid7',
        'taskid99',
        'status_kirim',
    ];

    protected $casts = [
        'tanggal_periksa' => 'date',
        'jam_periksa' => 'datetime:H:i:s',
        'taskid1' => 'datetime',
        'taskid2' => 'datetime',
        'taskid3' => 'datetime',
        'taskid4' => 'datetime',
        'taskid5' => 'datetime',
        'taskid6' => 'datetime',
        'taskid7' => 'datetime',
        'taskid99' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "Service JKN ERM {$eventName}")
            ->useLogName('service_jkn_erm');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kodepoli', 'kd_poli');
    }

    protected function getKeyForSaveQuery()
    {
        return null;
    }

    public function getKey()
    {
        return implode('-', [
            $this->tanggal_periksa,
            $this->jam_periksa,
            $this->no_rkm_medis
        ]);
    }
}
