<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $table = 'bidang';
    
    protected $primaryKey = 'nama';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nama',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'bidang', 'nama');
    }
    
    public function pegawaiRelation()
    {
        return $this->hasMany(Pegawai::class, 'bidang', 'nama');
    }
}