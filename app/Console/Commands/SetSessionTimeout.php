<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetSessionTimeout extends Command
{
    protected $signature = 'session:timeout {seconds : Session timeout in seconds}';
    protected $description = 'Set session timeout for testing purposes';

    public function handle()
    {
        $seconds = (int) $this->argument('seconds');
        $minutes = round($seconds / 60, 2);
        
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        // Update SESSION_LIFETIME in .env file
        if (preg_match('/^SESSION_LIFETIME=(.*)$/m', $envContent)) {
            $envContent = preg_replace('/^SESSION_LIFETIME=(.*)$/m', "SESSION_LIFETIME={$minutes}", $envContent);
        } else {
            $envContent .= "\nSESSION_LIFETIME={$minutes}";
        }
        
        File::put($envFile, $envContent);
        
        // Clear config cache
        $this->call('config:clear');
        
        $this->info("✅ Session timeout set to {$seconds} seconds ({$minutes} minutes)");
        $this->info("📝 Updated .env file with SESSION_LIFETIME={$minutes}");
        $this->info("🔄 Config cache cleared");
        
        if ($seconds < 60) {
            $this->warn("⚠️  Very short session timeout set! Remember to restore it after testing.");
            $this->info("💡 To restore: php artisan session:timeout 7200 (2 hours)");
        }
        
        return Command::SUCCESS;
    }
}