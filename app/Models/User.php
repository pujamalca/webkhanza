<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'device_token',
        'device_info',
        'last_login_at',
        'last_login_ip',
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
        ]);
    }

    public function isDeviceAllowed(string $deviceToken): bool
    {
        return $this->device_token === $deviceToken;
    }

    public function logoutFromAllDevices(): void
    {
        $this->update([
            'device_token' => null,
            'device_info' => null,
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
}
