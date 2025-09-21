<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResepTemplate extends Model
{
    protected $table = 'resep_templates';

    protected $fillable = [
        'nama_template',
        'keterangan',
        'nip',
        'is_public',
        'kategori'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    public function resepTemplateDetail(): HasMany
    {
        return $this->hasMany(ResepTemplateDetail::class, 'template_id', 'id');
    }

    public function scopeForUser($query, $nip)
    {
        return $query->where(function($q) use ($nip) {
            $q->where('nip', $nip)
              ->orWhere('is_public', true);
        });
    }

    public function scopeByCategory($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}