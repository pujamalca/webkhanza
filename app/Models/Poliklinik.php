<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poliklinik extends Model
{
    use HasFactory;

    protected $table = 'poliklinik';
    protected $primaryKey = 'kd_poli';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_poli',
        'nm_poli',
        'registrasi',
        'registrasilama',
        'status',
    ];

    public function regPeriksa()
    {
        return $this->hasMany(RegPeriksa::class, 'kd_poli', 'kd_poli');
    }

    public function dokter()
    {
        return $this->belongsToMany(Dokter::class, 'jadwal', 'kd_poli', 'kd_dokter');
    }
}