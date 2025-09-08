<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kd_sat_kecil',
        'letak_barang',
        'dasar',
        'h_beli',
        'ralan',
        'kelas1',
        'kelas2', 
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'karyawan',
        'stokminimal',
        'kd_kategori',
        'kd_golongan',
        'ekspire',
        'status',
        'kd_industri',
    ];

    protected $casts = [
        'h_beli' => 'decimal:2',
        'ralan' => 'decimal:2',
        'kelas1' => 'decimal:2',
        'kelas2' => 'decimal:2',
        'kelas3' => 'decimal:2',
        'utama' => 'decimal:2',
        'vip' => 'decimal:2',
        'vvip' => 'decimal:2',
        'beliluar' => 'decimal:2',
        'jualbebas' => 'decimal:2',
        'karyawan' => 'decimal:2',
        'stokminimal' => 'decimal:2',
        'ekspire' => 'date',
    ];

    public function resepDokter()
    {
        return $this->hasMany(ResepDokter::class, 'kode_brng', 'kode_brng');
    }
}