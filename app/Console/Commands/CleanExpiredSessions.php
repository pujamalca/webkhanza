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
        
        // Count expired sessions
        $expiredCount = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->count();
            
        if ($expiredCount === 0) {
            $this->info('No expired sessions found.');
            return 0;
        }
        
        $this->info("Found {$expiredCount} expired sessions.");
        
        if (!$this->option('force') && !$this->confirm('Do you want to clean these expired sessions?')) {
            $this->info('Operation cancelled.');
            return 0;
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
        
        return 0;
    }
}
