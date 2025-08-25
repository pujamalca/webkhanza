<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
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
}