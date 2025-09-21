<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kodesatuan extends Model
{
    protected $table = 'kodesatuan';
    protected $primaryKey = 'kode_sat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_sat',
        'satuan'
    ];

    // Relationships
    public function databarangBesar(): HasMany
    {
        return $this->hasMany(Databarang::class, 'kode_satbesar', 'kode_sat');
    }

    public function databarangKecil(): HasMany
    {
        return $this->hasMany(Databarang::class, 'kode_sat', 'kode_sat');
    }
}