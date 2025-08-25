<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';
    
    protected $primaryKey = 'nip';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nip',
        'nama',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_darah',
        'agama',
        'stts_nikah',
        'alamat',
        'kd_jbtn',
        'no_telp',
        'email',
        'status',
    ];
    
    protected $casts = [
        'tgl_lahir' => 'date',
        'status' => 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'kd_jbtn', 'kd_jbtn');
    }

    public function getEnumValues($column)
    {
        $instance = new static;
        $type = \DB::select(\DB::raw("SHOW COLUMNS FROM {$instance->getTable()} WHERE Field = '{$column}'"))[0]->Type;
        preg_match_all("/'([^']+)'/", $type, $matches);
        return $matches[1];
    }
}