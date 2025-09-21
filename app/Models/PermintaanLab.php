<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PermintaanLab extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'permintaan_lab';
    protected $primaryKey = 'noorder';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'noorder',
        'no_rawat',
        'tgl_permintaan',
        'jam_permintaan',
        'tgl_sampel',
        'jam_sampel',
        'tgl_hasil',
        'jam_hasil',
        'dokter_perujuk',
        'status',
        'informasi_tambahan',
        'diagnosa_klinis',
    ];

    protected $casts = [
        'tgl_permintaan' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "Permintaan lab {$eventName}")
            ->useLogName('permintaan_lab');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_perujuk', 'kd_dokter');
    }

    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaanLab::class, 'noorder', 'noorder');
    }

    // Scopes
    public function scopeByNoRawat($query, $noRawat)
    {
        return $query->where('no_rawat', $noRawat);
    }

    public function scopeByTanggal($query, $tanggalMulai, $tanggalSelesai = null)
    {
        $query->where('tgl_permintaan', '>=', $tanggalMulai);
        if ($tanggalSelesai) {
            $query->where('tgl_permintaan', '<=', $tanggalSelesai);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getFormattedTglPermintaanAttribute(): string
    {
        return $this->tgl_permintaan ? $this->tgl_permintaan->format('d/m/Y') : '-';
    }

    public function getFormattedJamPermintaanAttribute(): string
    {
        return $this->jam_permintaan ? date('H:i', strtotime($this->jam_permintaan)) : '-';
    }

    public function getFormattedTglSampelAttribute(): string
    {
        // Show '-' if sample date equals request date and jam is 00:00:00 (meaning not yet taken)
        if ($this->tgl_sampel == $this->tgl_permintaan && $this->jam_sampel == '00:00:00') {
            return '-';
        }
        return $this->tgl_sampel ? date('d/m/Y', strtotime($this->tgl_sampel)) : '-';
    }

    public function getFormattedJamSampelAttribute(): string
    {
        if ($this->tgl_sampel == $this->tgl_permintaan && $this->jam_sampel == '00:00:00') {
            return '-';
        }
        return $this->jam_sampel ? date('H:i', strtotime($this->jam_sampel)) : '-';
    }

    public function getFormattedTglHasilAttribute(): string
    {
        // Show '-' if result date equals request date and jam is 00:00:00 (meaning not yet ready)
        if ($this->tgl_hasil == $this->tgl_permintaan && $this->jam_hasil == '00:00:00') {
            return '-';
        }
        return $this->tgl_hasil ? date('d/m/Y', strtotime($this->tgl_hasil)) : '-';
    }

    public function getFormattedJamHasilAttribute(): string
    {
        if ($this->tgl_hasil == $this->tgl_permintaan && $this->jam_hasil == '00:00:00') {
            return '-';
        }
        return $this->jam_hasil ? date('H:i', strtotime($this->jam_hasil)) : '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'ralan' => 'Rawat Jalan',
            'ranap' => 'Rawat Inap',
            'sampel' => 'Sampel Diambil',
            'selesai' => 'Selesai',
            default => 'Belum Diproses'
        };
    }

    // Generate order number
    public static function generateNoOrder(): string
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'PK' . $tanggal;

        $lastOrder = static::where('noorder', 'like', $prefix . '%')
            ->orderBy('noorder', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->noorder, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}