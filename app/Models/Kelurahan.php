<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'kelurahan';
    protected $primaryKey = 'kd_kel';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_kel',
        'nm_kel',
        'kd_kec',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kd_kec', 'kd_kec');
    }

    public function pasien()
    {
        return $this->hasMany(Pasien::class, 'kd_kel', 'kd_kel');
    }
}