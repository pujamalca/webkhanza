<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';
    
    protected $primaryKey = 'dep_id';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'dep_id',
        'nama',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'departemen', 'dep_id');
    }
    
    public function pegawaiRelation()
    {
        return $this->hasMany(Pegawai::class, 'departemen', 'dep_id');
    }
}