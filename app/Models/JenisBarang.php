<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBarang extends Model
{
    protected $table = 'jenis';
    protected $primaryKey = 'kdjns';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kdjns',
        'nama',
        'keterangan'
    ];

    // Relationships
    public function databarang(): HasMany
    {
        return $this->hasMany(Databarang::class, 'kdjns', 'kdjns');
    }
}