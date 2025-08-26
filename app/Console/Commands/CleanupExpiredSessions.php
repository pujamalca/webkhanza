<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired sessions and update user login status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds
        $cutoffTime = time() - $sessionLifetime;

        // Find users with expired sessions
        $expiredUserIds = DB::table('sessions')
            ->where('last_activity', '<', $cutoffTime)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique();

        if ($expiredUserIds->isNotEmpty()) {
            // Update users' login status and clear device info
            DB::table('users')
                ->whereIn('id', $expiredUserIds)
                ->update([
                    'is_logged_in' => false,
                    'logged_in_at' => null,
                    'device_token' => null,
                    'device_info' => null,
                ]);

            $this->info("Updated login status for " . $expiredUserIds->count() . " users with expired sessions.");
        }

        // Clean up expired sessions
        $deletedCount = DB::table('sessions')
            ->where('last_activity', '<', $cutoffTime)
            ->delete();

        $this->info("Cleaned up {$deletedCount} expired sessions.");

        return Command::SUCCESS;
    }
}