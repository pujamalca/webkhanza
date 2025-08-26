<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjab extends Model
{
    use HasFactory;

    protected $table = 'penjab';
    protected $primaryKey = 'kd_pj';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_pj',
        'png_jawab',
        'nama_perusahaan',
        'alamat_asuransi',
        'no_telp',
        'attn',
    ];

    public function pasien()
    {
        return $this->hasMany(Pasien::class, 'kd_pj', 'kd_pj');
    }

    public function regPeriksa()
    {
        return $this->hasMany(RegPeriksa::class, 'kd_pj', 'kd_pj');
    }
}