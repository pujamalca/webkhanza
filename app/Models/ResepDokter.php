<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ResepDokter extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'resep_dokter';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'no_resep',
        'kode_brng',
        'jml',
        'aturan_pakai',
    ];

    protected $casts = [
        'jml' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "Detail resep {$eventName}")
            ->useLogName('resep_dokter');
    }

    public function resepObat()
    {
        return $this->belongsTo(ResepObat::class, 'no_resep', 'no_resep');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'kode_brng', 'kode_brng');
    }
}