<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatan';
    protected $primaryKey = 'kd_kec';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_kec',
        'nm_kec',
        'kd_kab',
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kd_kab', 'kd_kab');
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'kd_kec', 'kd_kec');
    }

    public function pasien()
    {
        return $this->hasMany(Pasien::class, 'kd_kec', 'kd_kec');
    }
}