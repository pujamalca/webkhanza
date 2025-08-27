<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Logout;

class CleanupExpiredSessions extends Command
{
    protected $signature = 'sessions:cleanup {--dry-run : Show what would be cleaned without actually doing it}';
    protected $description = 'Automatically logout users with expired sessions (same logic as Reset Session button)';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $sessionLifetime = config('session.lifetime') * 60; // Convert to seconds
        $currentTime = now()->timestamp;

        $this->info('=== AUTOMATIC SESSION CLEANUP ===');
        $this->line('Time: ' . now()->format('H:i:s'));
        $this->line('Session Lifetime: ' . config('session.lifetime') . ' minutes (' . $sessionLifetime . ' seconds)');
        $this->line('Mode: ' . ($isDryRun ? 'DRY RUN (no changes)' : 'LIVE CLEANUP'));
        $this->line('');

        // Find users with expired sessions
        $expiredUsers = User::where('is_logged_in', true)
            ->where(function ($query) use ($currentTime, $sessionLifetime) {
                $query->whereNull('logged_in_at')
                      ->orWhere('logged_in_at', '<', now()->subSeconds($sessionLifetime));
            })
            ->get();

        if ($expiredUsers->isEmpty()) {
            $this->info('✅ No expired sessions found. All users are within session lifetime.');
            return;
        }

        $this->warn("Found {$expiredUsers->count()} user(s) with expired sessions:");

        foreach ($expiredUsers as $user) {
            $loggedInAt = $user->logged_in_at ? $user->logged_in_at->format('H:i:s') : 'never';
            $secondsSinceLogin = $user->logged_in_at ? ($currentTime - $user->logged_in_at->timestamp) : 'unknown';
            
            $this->line("  - User {$user->id} ({$user->name}): logged in at {$loggedInAt}, {$secondsSinceLogin}s ago");
        }

        $this->line('');

        if ($isDryRun) {
            $this->info('DRY RUN: No changes made. Use without --dry-run to perform cleanup.');
            return;
        }

        $cleanedCount = 0;
        $userNames = [];

        // Log the cleanup operation
        Log::info('=== AUTOMATIC SESSION CLEANUP STARTED ===', [
            'users_to_cleanup' => $expiredUsers->count(),
            'user_ids' => $expiredUsers->pluck('id')->toArray(),
            'session_lifetime' => $sessionLifetime
        ]);

        $this->line('Cleaning up expired sessions...');

        foreach ($expiredUsers as $user) {
            // **EXACT SAME LOGIC AS RESET SESSION BUTTON**
            
            // 1. Fire logout event first (for consistency)
            if ($user->is_logged_in) {
                event(new Logout('web', $user));
                $this->line("  ✓ Fired logout event for user {$user->id}");
            }
            
            // 2. Clean up all database sessions for this user
            $sessionsDeleted = DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
            
            // 3. Ensure user is logged out in database
            $user->setLoggedOut();
            
            $cleanedCount++;
            $userNames[] = $user->name;

            $this->line("  ✓ Cleaned user {$user->id} ({$user->name}) - {$sessionsDeleted} session(s) deleted");

            // Log individual cleanup
            Log::info('=== USER SESSION AUTOMATICALLY CLEANED ===', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'sessions_deleted' => $sessionsDeleted,
                'logged_in_at' => $user->logged_in_at?->toDateTimeString(),
                'cleanup_reason' => 'expired_session_timeout'
            ]);
        }

        // Log completion
        Log::info('=== AUTOMATIC SESSION CLEANUP COMPLETED ===', [
            'users_cleaned' => $cleanedCount,
            'user_names' => $userNames,
        ]);

        $this->line('');
        $this->info("✅ Cleanup completed! {$cleanedCount} user(s) automatically logged out:");
        
        foreach ($userNames as $name) {
            $this->line("  - {$name}");
        }

        $this->line('');
        $this->info('Database updated using same logic as admin "Reset Session" button.');
    }
}