<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyIndex extends Model
{
    protected $table = 'emergency_index';
    
    protected $primaryKey = 'kode_emergency';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kode_emergency',
        'nama_emergency',
        'indek',
    ];
    
    protected $casts = [
        'indek' => 'integer',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'kode_emergency', 'kode_emergency');
    }
}