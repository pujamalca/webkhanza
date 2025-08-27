<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetUserLoggedOutOnLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        \Log::info('=== LOGOUT EVENT LISTENER TRIGGERED ===', [
            'user_id' => $event->user?->id,
            'username' => $event->user?->username,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        if ($event->user) {
            \Log::info('=== CALLING setLoggedOut ===', [
                'user_id' => $event->user->id
            ]);
            
            // Set logged out status and clear device info when user logs out
            $event->user->setLoggedOut();
            
            \Log::info('=== setLoggedOut COMPLETED ===', [
                'user_id' => $event->user->id
            ]);
        } else {
            \Log::warning('=== LOGOUT EVENT WITHOUT USER ===');
        }
    }
}
