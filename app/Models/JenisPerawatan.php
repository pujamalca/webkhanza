<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPerawatan extends Model
{
    use HasFactory;

    protected $table = 'jns_perawatan';
    protected $primaryKey = 'kd_jenis_prw';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_jenis_prw',
        'nm_perawatan',
        'kd_kategori',
        'material',
        'bhp',
        'tarif_tindakandr',
        'tarif_tindakanpr',
        'kso',
        'menejemen',
        'total_byrdr',
        'total_byrpr',
        'kd_pj',
        'kd_poli',
        'status',
    ];

    protected $casts = [
        'material' => 'decimal:2',
        'bhp' => 'decimal:2',
        'tarif_tindakandr' => 'decimal:2',
        'tarif_tindakanpr' => 'decimal:2',
        'kso' => 'decimal:2',
        'menejemen' => 'decimal:2',
        'total_byrdr' => 'decimal:2',
        'total_byrpr' => 'decimal:2',
    ];

    public function tindakanRalan()
    {
        return $this->hasMany(TindakanRalan::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }
}