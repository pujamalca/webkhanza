<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MarketingPatientTask extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'patient_id',
        'category_id',
        'is_completed',
        'completed_at',
        'notes',
        'completed_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "Marketing patient task {$eventName}")
            ->useLogName('marketing_patient_task');
    }

    public function patient()
    {
        return $this->belongsTo(RegPeriksa::class, 'patient_id', 'no_rawat');
    }

    public function category()
    {
        return $this->belongsTo(MarketingCategory::class, 'category_id');
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }
}