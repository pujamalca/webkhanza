<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Dokter extends Model
{
    use HasFactory, LogsActivity;
    
    protected $table = 'dokter';
    
    protected $primaryKey = 'kd_dokter';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kd_dokter',
        'nm_dokter',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_drh',
        'agama',
        'almt_tgl',
        'no_telp',
        'email',
        'stts_nikah',
        'kd_sps',
        'alumni',
        'no_ijn_praktek',
        'status',
    ];
    
    protected $casts = [
        'tgl_lahir' => 'date',
        'status' => 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }

    public function spesialis()
    {
        return $this->belongsTo(Spesialis::class, 'kd_sps', 'kd_sps');
    }

    public function getEnumValues($column)
    {
        $instance = new static;
        $type = \DB::select(\DB::raw("SHOW COLUMNS FROM {$instance->getTable()} WHERE Field = '{$column}'"))[0]->Type;
        preg_match_all("/'([^']+)'/", $type, $matches);
        return $matches[1];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['kd_dokter', 'nm_dokter', 'jk', 'kd_sps', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('dokter');
    }
}