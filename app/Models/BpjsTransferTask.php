<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BpjsTransferTask extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'bpjs_transfer_id',
        'category_id',
        'is_completed',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => "BPJS Transfer Task {$eventName}")
            ->useLogName('bpjs_transfer_task');
    }

    // Relationships
    public function bpjsTransfer()
    {
        return $this->belongsTo(BpjsTransfer::class, 'bpjs_transfer_id');
    }

    public function category()
    {
        return $this->belongsTo(MarketingCategory::class, 'category_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }
}
