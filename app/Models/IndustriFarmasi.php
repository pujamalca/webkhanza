<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndustriFarmasi extends Model
{
    protected $table = 'industrifarmasi';
    protected $primaryKey = 'kode_industri';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_industri',
        'nama_industri',
        'alamat',
        'kota',
        'no_telp'
    ];

    // Relationships
    public function databarang(): HasMany
    {
        return $this->hasMany(Databarang::class, 'kode_industri', 'kode_industri');
    }
}