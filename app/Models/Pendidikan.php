<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $table = 'pendidikan';
    
    protected $primaryKey = 'tingkat';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'tingkat',
        'indek',
        'gapok1',
        'kenaikan',
        'maksimal',
    ];
    
    protected $casts = [
        'indek' => 'integer',
        'gapok1' => 'decimal:2',
        'kenaikan' => 'decimal:2',
        'maksimal' => 'integer',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'pendidikan', 'tingkat');
    }
}