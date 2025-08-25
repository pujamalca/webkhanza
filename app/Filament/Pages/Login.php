<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('login')
                    ->label('Email atau Username')
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        $loginField = $data['login'] ?? '';
        
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            return [
                'email' => $loginField,
                'password' => $data['password'],
            ];
        } else {
            return [
                'name' => $loginField,
                'password' => $data['password'],
            ];
        }
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('Email/Username atau password salah.'),
        ]);
    }

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        // Dapatkan credentials sebelum proses login
        $data = $this->form->getState();
        $credentials = $this->getCredentialsFromFormData($data);
        
        // Cek apakah user ada dan password benar
        $authProvider = Auth::getProvider();
        $user = $authProvider->retrieveByCredentials($credentials);
        
        if ($user && $authProvider->validateCredentials($user, $credentials)) {
            // Cek apakah user sudah login (is_logged_in = true)
            if ($user->isCurrentlyLoggedIn()) {
                // Tampilkan notifikasi bahwa sudah login di perangkat lain
                $deviceInfo = json_decode($user->device_info, true) ?? [];
                $this->showActiveSessionNotification($deviceInfo, $user->logged_in_at ?? $user->last_login_at);
                
                // Blokir login dengan validation exception
                throw ValidationException::withMessages([
                    'data.login' => 'Akun sudah aktif di perangkat lain. Hubungi administrator untuk bantuan.',
                ]);
            }
        }
        
        // Lanjutkan proses login normal
        $response = parent::authenticate();
        
        if ($response && auth()->check()) {
            $user = auth()->user();
            $userAgent = request()->header('User-Agent', '');
            $ipAddress = request()->ip();
            
            // Set device token dan status login
            $user->setDeviceToken($userAgent, $ipAddress);
            
            // Refresh model untuk mendapatkan device_token yang baru
            $user->refresh();
            
            // Simpan device token di session untuk middleware
            Session::put('device_token', $user->device_token);
        }
        
        return $response;
    }
    
    protected function isSameDevice(string $currentUserAgent, string $currentIp, string $existingUserAgent, string $existingIp): bool
    {
        // Bandingkan User-Agent dan IP address
        $userAgentSimilarity = similar_text($currentUserAgent, $existingUserAgent, $percent);
        
        // Anggap sama jika User-Agent 90% sama DAN IP address sama
        return ($percent >= 90 && $currentIp === $existingIp);
    }
    
    protected function showActiveSessionNotification(array $deviceInfo, $lastLoginAt): void
    {
        $browser = $deviceInfo['browser'] ?? 'Unknown';
        $os = $deviceInfo['os'] ?? 'Unknown';
        $loginTime = $lastLoginAt ? $lastLoginAt->format('d/m/Y H:i') : 'Unknown';
        
        Notification::make()
            ->title('Akun Sudah Login di Perangkat Lain')
            ->body("Akun Anda sudah aktif di perangkat lain:\n• Browser: {$browser}\n• OS: {$os}\n• Waktu login: {$loginTime}\n\nUntuk keamanan, hanya satu perangkat yang diizinkan login dalam satu waktu. Silakan hubungi administrator jika Anda memerlukan akses.")
            ->danger()
            ->duration(12000)
            ->persistent()
            ->send();
    }
}