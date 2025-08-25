<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nik',
        'nama',
        'jk',
        'jbtn',
        'jnj_jabatan',
        'kode_kelompok',
        'kode_resiko',
        'kode_emergency',
        'departemen',
        'bidang',
        'stts_wp',
        'stts_kerja',
        'npwp',
        'pendidikan',
        'gapok',
        'tmp_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'mulai_kerja',
        'ms_kerja',
        'indexins',
        'bpd',
        'rekening',
        'stts_aktif',
        'wajibmasuk',
        'pengurang',
        'indek',
        'mulai_kontrak',
        'cuti_diambil',
        'dankes',
        'photo',
        'no_ktp',
    ];
    
    protected $casts = [
        'tgl_lahir' => 'date',
        'mulai_kerja' => 'date',
        'mulai_kontrak' => 'date',
        'gapok' => 'decimal:2',
        'pengurang' => 'decimal:2',
        'dankes' => 'decimal:2',
        'wajibmasuk' => 'integer',
        'cuti_diambil' => 'integer',
        'indek' => 'integer',
    ];

    public function departemenRelation()
    {
        return $this->belongsTo(Departemen::class, 'departemen', 'dep_id');
    }

    public function bidangRelation()
    {
        return $this->belongsTo(Bidang::class, 'bidang', 'nama');
    }

    public function jabatanRelation()
    {
        return $this->belongsTo(Jabatan::class, 'jbtn', 'nm_jbtn');
    }
}