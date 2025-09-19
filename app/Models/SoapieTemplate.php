<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoapieTemplate extends Model
{
    protected $fillable = [
        'nama_template',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'intervention',
        'evaluation',
        'nip',
        'is_public',
        'kategori',
        'keterangan'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
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
