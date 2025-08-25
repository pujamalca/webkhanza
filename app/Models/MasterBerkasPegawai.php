<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterBerkasPegawai extends Model
{
    protected $table = 'master_berkas_pegawai';
    
    protected $primaryKey = 'kode';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kode',
        'kategori',
        'nama_berkas',
        'no_urut',
    ];
    
    protected $casts = [
        'no_urut' => 'integer',
    ];

    public function berkasPegawai()
    {
        return $this->hasMany(BerkasPegawai::class, 'kode_berkas', 'kode');
    }
}