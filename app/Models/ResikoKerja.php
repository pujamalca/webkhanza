<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResikoKerja extends Model
{
    protected $table = 'resiko_kerja';
    
    protected $primaryKey = 'kode_resiko';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kode_resiko',
        'nama_resiko',
        'indek',
    ];
    
    protected $casts = [
        'indek' => 'integer',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'kode_resiko', 'kode_resiko');
    }
}