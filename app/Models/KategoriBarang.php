<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama'
    ];

    // Relationships
    public function databarang(): HasMany
    {
        return $this->hasMany(Databarang::class, 'kode_kategori', 'kode');
    }
}