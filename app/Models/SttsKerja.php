<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SttsKerja extends Model
{
    protected $table = 'stts_kerja';
    
    protected $primaryKey = 'stts';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'stts',
        'ktg',
        'indek',
    ];
    
    protected $casts = [
        'indek' => 'integer',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'stts_kerja', 'stts');
    }
}