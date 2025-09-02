<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'leave_type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'total_days',
        'status_badge',
        'leave_type_label'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTotalDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Menunggu', 'color' => 'warning'],
            'approved' => ['label' => 'Disetujui', 'color' => 'success'], 
            'rejected' => ['label' => 'Ditolak', 'color' => 'danger'],
            default => ['label' => 'Unknown', 'color' => 'gray']
        };
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return match($this->leave_type) {
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'darurat' => 'Cuti Darurat',
            'melahirkan' => 'Cuti Melahirkan',
            'menikah' => 'Cuti Menikah',
            'lainnya' => 'Lainnya',
            default => $this->leave_type
        };
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForLeaveType($query, $leaveType)
    {
        return $query->where('leave_type', $leaveType);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('start_date', now()->month)
                    ->whereYear('start_date', now()->year);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                    });
    }

    public function approve($approverId): bool
    {
        $this->status = 'approved';
        $this->approved_by = $approverId;
        $this->approved_at = now();
        
        return $this->save();
    }

    public function reject($approverId): bool
    {
        $this->status = 'rejected';
        $this->approved_by = $approverId;
        $this->approved_at = now();
        
        return $this->save();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}