<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SttsWp extends Model
{
    protected $table = 'stts_wp';
    
    protected $primaryKey = 'stts';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'stts',
        'ktg',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'stts_wp', 'stts');
    }
}