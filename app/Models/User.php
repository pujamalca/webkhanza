<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'device_token',
        'device_info',
        'last_login_at',
        'last_login_ip',
        'is_logged_in',
        'logged_in_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'device_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'logged_in_at' => 'datetime',
            'is_logged_in' => 'boolean',
        ];
    }

    public function generateDeviceToken(): string
    {
        return hash('sha256', Str::random(60) . time() . $this->id);
    }

    public function setDeviceToken(string $userAgent, string $ipAddress): void
    {
        $this->update([
            'device_token' => $this->generateDeviceToken(),
            'device_info' => $this->formatDeviceInfo($userAgent),
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'is_logged_in' => true,
            'logged_in_at' => now(),
        ]);
    }

    public function isDeviceAllowed(string $deviceToken): bool
    {
        return $this->device_token === $deviceToken;
    }

    
    public function isCurrentlyLoggedIn(): bool
    {
        return $this->is_logged_in === true;
    }
    
    public function setLoggedOut(): void
    {
        $this->update([
            'is_logged_in' => false,
            'logged_in_at' => null,
            'device_token' => null,
            'device_info' => null,
        ]);
    }
    
    public function setLoggedIn(): void
    {
        $this->update([
            'is_logged_in' => true,
            'logged_in_at' => now(),
        ]);
    }

    private function formatDeviceInfo(string $userAgent): string
    {
        $browser = $this->getBrowserInfo($userAgent);
        $os = $this->getOSInfo($userAgent);
        
        return json_encode([
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $userAgent,
        ]);
    }

    private function getBrowserInfo(string $userAgent): string
    {
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    private function getOSInfo(string $userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false) return 'iOS';
        return 'Unknown';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_logged_in'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('users');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url && file_exists(storage_path('app/public/' . $this->avatar_url))) {
            return asset('storage/' . $this->avatar_url);
        }
        
        return null;
    }

    /**
     * Relationship with absents (attendance records)
     */
    public function absents()
    {
        return $this->hasMany(Absent::class, 'employee_id', 'id');
    }

    /**
     * Relationship with leave requests (cutis)
     */
    public function cutis()
    {
        return $this->hasMany(Cuti::class, 'employee_id', 'id');
    }

    /**
     * Relationship with approved leave requests
     */
    public function approvedCutis()
    {
        return $this->hasMany(Cuti::class, 'approved_by', 'id');
    }
}
