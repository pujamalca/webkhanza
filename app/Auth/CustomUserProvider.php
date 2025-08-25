<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class CustomUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || 
            (count($credentials) === 1 && 
             array_key_exists('password', $credentials))) {
            return null;
        }

        $query = $this->newModelQuery();

        if (isset($credentials['email'])) {
            $query->where('email', $credentials['email']);
        } elseif (isset($credentials['name'])) {
            $query->where('name', $credentials['name']);
        }

        foreach ($credentials as $key => $value) {
            if ($key !== 'password' && $key !== 'email' && $key !== 'name') {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if ($user instanceof User) {
            $password = $credentials['password'] ?? null;
            
            if (!$password) {
                return false;
            }

            return Hash::check($password, $user->getAuthPassword());
        }

        return false;
    }
}