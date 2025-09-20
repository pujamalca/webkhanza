<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPenyakit extends Model
{
    protected $table = 'kategori_penyakit';

    public $timestamps = false;

    protected $primaryKey = 'kd_ktg';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_ktg',
        'nm_kategori',
        'ciri_umum'
    ];

    // Relationships
    public function penyakit(): HasMany
    {
        return $this->hasMany(Penyakit::class, 'kd_ktg', 'kd_ktg');
    }

    // Helper methods
    public function getDisplayNameAttribute(): string
    {
        return $this->kd_ktg . ' - ' . $this->nm_kategori;
    }
}