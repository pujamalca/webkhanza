<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
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
        $response = parent::authenticate();
        
        if ($response && auth()->check()) {
            $user = auth()->user();
            $userAgent = request()->header('User-Agent', '');
            $ipAddress = request()->ip();
            
            // Set device token untuk user ini
            $user->setDeviceToken($userAgent, $ipAddress);
            
            // Refresh model untuk mendapatkan device_token yang baru
            $user->refresh();
            
            // Simpan device token di session untuk middleware
            Session::put('device_token', $user->device_token);
        }
        
        return $response;
    }
}