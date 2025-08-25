<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $table = 'tracker';

    // Gunakan nip sebagai primary key sementara untuk Filament
    protected $primaryKey = 'nip';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'tgl_login',
        'jam_login',
    ];

    // Method untuk mendapatkan custom key dari kombinasi primary keys
    public function getCustomKeyAttribute()
    {
        return $this->nip . '|' . $this->tgl_login . '|' . $this->jam_login;
    }

    // Method untuk mencari berdasarkan custom key
    public static function findByCustomKey($customKey)
    {
        $parts = explode('|', $customKey);
        if (count($parts) !== 3) {
            return null;
        }
        
        return static::where('nip', $parts[0])
                    ->where('tgl_login', $parts[1])
                    ->where('jam_login', $parts[2])
                    ->first();
    }

    // Override untuk routing
    public function getRouteKey()
    {
        return $this->getCustomKeyAttribute();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::findByCustomKey($value);
    }

    protected $casts = [
        'tgl_login' => 'date',
    ];

    

    // Scope untuk filter berdasarkan rentang tanggal
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_login', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay(),
        ]);
    }
}
