<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokJabatan extends Model
{
    protected $table = 'kelompok_jabatan';
    
    protected $primaryKey = 'kode_kelompok';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kode_kelompok',
        'nama_kelompok',
        'indek',
    ];
    
    protected $casts = [
        'indek' => 'integer',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'kode_kelompok', 'kode_kelompok');
    }
}