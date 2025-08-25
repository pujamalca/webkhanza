<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    
    protected $primaryKey = 'kd_jbtn';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kd_jbtn',
        'nm_jbtn',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'jbtn', 'nm_jbtn');
    }
}