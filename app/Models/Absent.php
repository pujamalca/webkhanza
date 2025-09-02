<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Absent extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'check_in_photo',
        'check_out_photo',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    protected $appends = [
        'check_in_photo_url',
        'check_out_photo_url',
        'total_working_hours'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        if (!$this->check_in_photo) {
            return null;
        }

        return Storage::url($this->check_in_photo);
    }

    public function getCheckOutPhotoUrlAttribute(): ?string
    {
        if (!$this->check_out_photo) {
            return null;
        }

        return Storage::url($this->check_out_photo);
    }

    public function getTotalWorkingHoursAttribute(): ?string
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        $checkIn = $this->check_in instanceof \Carbon\Carbon 
            ? $this->check_in 
            : \Carbon\Carbon::createFromTimeString($this->check_in);
            
        $checkOut = $this->check_out instanceof \Carbon\Carbon 
            ? $this->check_out 
            : \Carbon\Carbon::createFromTimeString($this->check_out);

        $diff = $checkIn->diff($checkOut);
        return $diff->format('%H:%I');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($absent) {
            if ($absent->check_in_photo && Storage::exists($absent->check_in_photo)) {
                Storage::delete($absent->check_in_photo);
            }
            
            if ($absent->check_out_photo && Storage::exists($absent->check_out_photo)) {
                Storage::delete($absent->check_out_photo);
            }
        });
    }
}