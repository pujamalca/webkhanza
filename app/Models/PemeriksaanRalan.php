<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PemeriksaanRalan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pemeriksaan_ralan';
    protected $primaryKey = ['no_rawat', 'tgl_perawatan', 'jam_rawat'];
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    
    public function getKeyName()
    {
        return $this->primaryKey;
    }
    
    public function getKey()
    {
        $attributes = [];
        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }
        return $attributes;
    }
    
    public function getKeyForSelectQuery()
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[] = $this->getAttribute($key);
        }
        return implode('-', $keys);
    }
    
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    protected $fillable = [
        'no_rawat',
        'tgl_perawatan',
        'jam_rawat',
        'suhu_tubuh',
        'tensi',
        'nadi',
        'respirasi',
        'tinggi',
        'berat',
        'spo2',
        'gcs',
        'kesadaran',
        'keluhan',
        'pemeriksaan',
        'alergi',
        'lingkar_perut',
        'rtl',
        'penilaian',
        'instruksi',
        'evaluasi',
        'nip',
    ];

    protected $casts = [
        'tgl_perawatan' => 'date',
        'suhu_tubuh' => 'decimal:1',
        'tinggi' => 'decimal:1',
        'berat' => 'decimal:1',
        'spo2' => 'decimal:1',
        'lingkar_perut' => 'decimal:1',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontLogIfAttributesChangedOnly([])
            ->logOnly([])
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Pemeriksaan ralan {$eventName}")
            ->useLogName('pemeriksaan_ralan');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function petugas()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }
}