<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'kabupaten';
    protected $primaryKey = 'kd_kab';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_kab',
        'nm_kab',
        'kd_prop',
    ];

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'kd_kab', 'kd_kab');
    }

    public function pasien()
    {
        return $this->hasMany(Pasien::class, 'kd_kab', 'kd_kab');
    }
}