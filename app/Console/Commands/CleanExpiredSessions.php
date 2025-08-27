<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean-expired {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired sessions from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds
        $expiredTime = time() - $sessionLifetime;
        
        // Get expired sessions with user info
        $expiredSessions = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->whereNotNull('user_id')
            ->get();
            
        $expiredCount = $expiredSessions->count();
        
        // Count sessions without user_id
        $expiredNoUserCount = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->whereNull('user_id')
            ->count();
            
        $totalExpired = $expiredCount + $expiredNoUserCount;
        
        if ($totalExpired === 0) {
            $this->info('No expired sessions found.');
            return 0;
        }
        
        $this->info("Found {$totalExpired} expired sessions ({$expiredCount} with users, {$expiredNoUserCount} without users).");
        
        if (!$this->option('force') && !$this->confirm('Do you want to clean these expired sessions?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        // Update user login status for expired sessions
        if ($expiredCount > 0) {
            $userIds = $expiredSessions->pluck('user_id')->unique();
            
            $updatedUsers = DB::table('users')
                ->whereIn('id', $userIds)
                ->update([
                    'is_logged_in' => false,
                    'logged_in_at' => null,
                    'device_token' => null,
                    'device_info' => null,
                ]);
                
            $this->info("Updated login status for {$updatedUsers} users.");
        }
        
        // Delete expired sessions
        $deletedCount = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->delete();
            
        $this->info("Successfully cleaned {$deletedCount} expired sessions.");
        
        // Also clean orphaned sessions (sessions without valid user_id)
        $orphanedCount = DB::table('sessions')
            ->whereNotNull('user_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'sessions.user_id');
            })
            ->delete();
            
        if ($orphanedCount > 0) {
            $this->info("Also cleaned {$orphanedCount} orphaned sessions.");
        }
        
        // Clean up users that are marked as logged in but have no active sessions
        $usersWithoutSessions = DB::table('users')
            ->where('is_logged_in', 1)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sessions')
                    ->whereColumn('sessions.user_id', 'users.id');
            })
            ->update([
                'is_logged_in' => false,
                'logged_in_at' => null,
                'device_token' => null,
                'device_info' => null,
            ]);
            
        if ($usersWithoutSessions > 0) {
            $this->info("Cleaned {$usersWithoutSessions} users without active sessions.");
        }
        
        return 0;
    }
}
