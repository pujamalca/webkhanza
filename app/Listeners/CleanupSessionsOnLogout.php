<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;

class CleanupSessionsOnLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the logout event and clean up user sessions
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        
        if ($user) {
            // Clean up all sessions for this user from database
            $deletedCount = DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
                
            if ($deletedCount > 0) {
                \Log::info("Logout: Cleaned up {$deletedCount} sessions for user ID: {$user->id}");
            }
        }
    }
}
