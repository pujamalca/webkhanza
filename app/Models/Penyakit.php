<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    use HasFactory;

    protected $table = 'penyakit';
    protected $primaryKey = 'kd_penyakit';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_penyakit',
        'nm_penyakit',
        'ciri_ciri',
        'keterangan',
        'kd_ktg',
        'status',
    ];

    public function diagnosaPasien()
    {
        return $this->hasMany(DiagnosaPasien::class, 'kd_penyakit', 'kd_penyakit');
    }
}