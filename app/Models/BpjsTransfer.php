<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BpjsTransfer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nama_pasien',
        'jumlah_keluarga', 
        'no_peserta_lama',
        'nik',
        'no_telepon',
        'alamat',
        'tanggal_rencana_pindah',
        'foto_bukti_mjkn',
        'foto_pasien',
        'is_edukasi_completed',
        'edukasi_completed_by',
        'edukasi_completed_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'tanggal_rencana_pindah' => 'date',
        'is_edukasi_completed' => 'boolean',
        'edukasi_completed_at' => 'datetime',
        'jumlah_keluarga' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "BPJS Transfer {$eventName}")
            ->useLogName('bpjs_transfer');
    }

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function edukasiCompletedBy()
    {
        return $this->belongsTo(User::class, 'edukasi_completed_by');
    }

    public function tasks()
    {
        return $this->hasMany(BpjsTransferTask::class);
    }

    public function completedTasks()
    {
        return $this->hasMany(BpjsTransferTask::class)->where('is_completed', true);
    }

    public function pendingTasks()
    {
        return $this->hasMany(BpjsTransferTask::class)->where('is_completed', false);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_edukasi_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_edukasi_completed', false);
    }

    // Accessors & Mutators
    public function getFotoBuktiMjknUrlAttribute()
    {
        return $this->foto_bukti_mjkn ? asset('storage/' . $this->foto_bukti_mjkn) : null;
    }

    public function getFotoPasienUrlAttribute()
    {
        return $this->foto_pasien ? asset('storage/' . $this->foto_pasien) : null;
    }
}
