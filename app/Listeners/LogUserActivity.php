<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Spatie\Activitylog\Models\Activity;

class LogUserActivity
{
    public function handleLogin(Login $event)
    {
        activity('auth')
            ->causedBy($event->user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('User logged in');
    }

    public function handleLogout(Logout $event)
    {
        if ($event->user) {
            activity('auth')
                ->causedBy($event->user)
                ->withProperties([
                    'ip' => request()->ip(),
                ])
                ->log('User logged out');
        }
    }

    public function handleFailedLogin(Failed $event)
    {
        activity('auth')
            ->withProperties([
                'email' => $event->credentials['email'] ?? 'Unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Failed login attempt');
    }

    public function subscribe($events)
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailedLogin',
        ];
    }
}