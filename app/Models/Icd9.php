<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Icd9 extends Model
{
    protected $table = 'icd9';

    public $timestamps = false;

    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'deskripsi_panjang',
        'deskripsi_pendek'
    ];

    // Relationships
    public function prosedurPasien(): HasMany
    {
        return $this->hasMany(ProsedurPasien::class, 'kode', 'kode');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('kode')
                    ->where('kode', '!=', '')
                    ->where('kode', '!=', '-');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('icd9.kode', 'like', "%{$search}%")
              ->orWhere('icd9.deskripsi_pendek', 'like', "%{$search}%")
              ->orWhere('icd9.deskripsi_panjang', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function isActive(): bool
    {
        return !empty($this->kode) && $this->kode !== '-';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->kode . ' - ' . $this->deskripsi_pendek;
    }

    public function getFullDisplayNameAttribute(): string
    {
        return $this->kode . ' - ' . $this->deskripsi_panjang;
    }
}