<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JnjJabatan extends Model
{
    use HasFactory;
    
    protected $table = 'jnj_jabatan';
    
    protected $primaryKey = 'kode';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kode',
        'nama',
        'tnj',
        'indek',
    ];
    
    protected $casts = [
        'tnj' => 'decimal:2',
        'indek' => 'integer',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'jnj_jabatan', 'kode');
    }
}