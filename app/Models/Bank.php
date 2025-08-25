<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank';
    
    protected $primaryKey = 'namabank';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'namabank',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'bpd', 'namabank');
    }
}