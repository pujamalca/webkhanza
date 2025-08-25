<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BerkasPegawai extends Model
{
    protected $table = 'berkas_pegawai';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nik',
        'tgl_uploud',
        'tgl_berakhir',
        'kode_berkas',
        'berkas',
    ];
    
    protected $casts = [
        'tgl_uploud' => 'date',
        'tgl_berakhir' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }

    public function masterBerkasPegawai()
    {
        return $this->belongsTo(MasterBerkasPegawai::class, 'kode_berkas', 'kode');
    }
}