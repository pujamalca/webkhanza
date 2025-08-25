<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerSQL extends Model
{
    protected $table = 'trackersql';

    // Gunakan tanggal sebagai primary key sementara untuk Filament
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'sqle',
        'usere',
    ];

    // Method untuk mendapatkan custom key dari kombinasi primary keys
    public function getCustomKeyAttribute()
    {
        return $this->tanggal . '|' . $this->sqle . '|' . $this->usere;
    }

    // Method untuk mencari berdasarkan custom key
    public static function findByCustomKey($customKey)
    {
        $parts = explode('|', $customKey);
        if (count($parts) !== 3) {
            return null;
        }
        
        return static::where('tanggal', $parts[0])
                    ->where('sqle', $parts[1])
                    ->where('usere', $parts[2])
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
        'tanggal' => 'date',
    ];

    // Scope untuk filter berdasarkan rentang tanggal
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay(),
        ]);
    }
}