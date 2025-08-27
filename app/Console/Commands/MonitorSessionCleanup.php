<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MonitorSessionCleanup extends Command
{
    protected $signature = 'sessions:monitor-cleanup {--watch : Monitor cleanup in real-time} {--stats : Show cleanup statistics}';
    protected $description = 'Monitor automatic session cleanup operations';

    public function handle()
    {
        if ($this->option('stats')) {
            $this->showStats();
        } elseif ($this->option('watch')) {
            $this->watchCleanup();
        } else {
            $this->showCurrentState();
        }
    }

    private function showCurrentState()
    {
        $this->info('=== SESSION CLEANUP MONITOR ===');
        $this->line('Time: ' . now()->format('H:i:s'));
        $this->line('');

        // Current session state
        $loggedInUsers = User::where('is_logged_in', true)->count();
        $totalUsers = User::count();
        $activeSessions = DB::table('sessions')->count();

        $this->info("📊 CURRENT STATE:");
        $this->line("  Logged in users: {$loggedInUsers}/{$totalUsers}");
        $this->line("  Active sessions: {$activeSessions}");

        // Expired sessions (would be cleaned up)
        $sessionLifetime = config('session.lifetime') * 60;
        $expiredUsers = User::where('is_logged_in', true)
            ->where(function ($query) use ($sessionLifetime) {
                $query->whereNull('logged_in_at')
                      ->orWhere('logged_in_at', '<', now()->subSeconds($sessionLifetime));
            })
            ->get();

        if ($expiredUsers->count() > 0) {
            $this->warn("⚠️  USERS WITH EXPIRED SESSIONS:");
            foreach ($expiredUsers as $user) {
                $loggedInAt = $user->logged_in_at ? $user->logged_in_at->format('H:i:s') : 'never';
                $secondsExpired = $user->logged_in_at ? 
                    (now()->timestamp - $user->logged_in_at->timestamp) : 'unknown';
                $this->line("    - {$user->name}: login at {$loggedInAt} ({$secondsExpired}s ago)");
            }
            $this->line('');
            $this->info('💡 Run cleanup: php artisan sessions:cleanup');
        } else {
            $this->info("✅ No expired sessions found");
        }

        $this->line('');
        $this->info('🔧 AVAILABLE COMMANDS:');
        $this->line('  --watch    : Real-time monitoring');
        $this->line('  --stats    : Cleanup statistics');
    }

    private function showStats()
    {
        $this->info('=== SESSION CLEANUP STATISTICS ===');
        
        // Read recent logs for statistics
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            $this->warn('No log file found for statistics');
            return;
        }

        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        
        $cleanupEvents = 0;
        $usersCleanedToday = [];
        $today = now()->format('Y-m-d');

        foreach ($lines as $line) {
            if (strpos($line, 'AUTOMATIC SESSION CLEANUP COMPLETED') !== false && 
                strpos($line, $today) !== false) {
                $cleanupEvents++;
                
                // Extract user names from log
                if (preg_match('/user_names.*?\[(.*?)\]/', $line, $matches)) {
                    $userNames = explode(',', $matches[1]);
                    foreach ($userNames as $name) {
                        $name = trim(str_replace('"', '', $name));
                        if ($name) {
                            $usersCleanedToday[] = $name;
                        }
                    }
                }
            }
        }

        $this->line("📊 TODAY'S STATISTICS ({$today}):");
        $this->line("  Cleanup runs: {$cleanupEvents}");
        $this->line("  Users auto-logged-out: " . count($usersCleanedToday));
        
        if (count($usersCleanedToday) > 0) {
            $uniqueUsers = array_unique($usersCleanedToday);
            $this->line("  Unique users affected: " . count($uniqueUsers));
            
            if (count($uniqueUsers) <= 10) {
                $this->line("  Users: " . implode(', ', $uniqueUsers));
            }
        }

        $this->line('');
        $this->info('🕐 SCHEDULE:');
        $this->line('  Cleanup runs every 5 minutes automatically');
        $this->line('  Next cleanup: within 5 minutes');
    }

    private function watchCleanup()
    {
        $this->info('=== REAL-TIME CLEANUP MONITORING ===');
        $this->info('Watching for automatic session cleanup...');
        $this->info('Press Ctrl+C to stop');
        $this->line('');

        $previousLoggedIn = User::where('is_logged_in', true)->count();
        $previousSessions = DB::table('sessions')->count();
        
        $this->line(now()->format('H:i:s') . " - Starting monitor:");
        $this->line("  Logged in users: {$previousLoggedIn}");
        $this->line("  Active sessions: {$previousSessions}");
        $this->line('');

        while (true) {
            $currentLoggedIn = User::where('is_logged_in', true)->count();
            $currentSessions = DB::table('sessions')->count();

            // Check for changes
            if ($previousLoggedIn !== $currentLoggedIn || 
                $previousSessions !== $currentSessions) {
                
                $this->line(now()->format('H:i:s') . " - Changes detected:");
                
                if ($previousLoggedIn > $currentLoggedIn) {
                    $loggedOut = $previousLoggedIn - $currentLoggedIn;
                    $this->info("  🚪 {$loggedOut} user(s) automatically logged out!");
                }
                
                if ($previousSessions > $currentSessions) {
                    $sessionsDeleted = $previousSessions - $currentSessions;
                    $this->line("  🗑️  {$sessionsDeleted} session(s) cleaned up");
                }
                
                $this->line("  📊 Current: {$currentLoggedIn} users, {$currentSessions} sessions");
                $this->line('');

                $previousLoggedIn = $currentLoggedIn;
                $previousSessions = $currentSessions;
            }

            sleep(30); // Check every 30 seconds
        }
    }
}