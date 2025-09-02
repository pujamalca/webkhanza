<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SafeMigrationCommand extends Command
{
    protected $signature = 'migrate:safe {action=status : Action to perform (fresh, rollback, status)}';
    protected $description = 'Safe migration operations that only affect Laravel tables';

    /**
     * Daftar tabel yang AMAN untuk dikelola oleh Laravel
     */
    protected $laravelTables = [
        'migrations',
        'cache',
        'cache_locks', 
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'activity_log',
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
        'personal_access_tokens',
        'password_reset_tokens',
        'absents',
        'cutis',
    ];

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'fresh':
                $this->safeFresh();
                break;
            case 'rollback':
                $this->safeRollback();
                break;
            case 'status':
                $this->showStatus();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: fresh, rollback, status");
                return 1;
        }

        return 0;
    }

    protected function safeFresh()
    {
        $this->info('🔄 Starting safe fresh migration...');
        $this->info('📋 This will ONLY affect Laravel tables:');
        
        foreach ($this->laravelTables as $table) {
            $this->line("  - {$table}");
        }
        
        if (!$this->confirm('Continue with safe fresh migration?')) {
            $this->info('Operation cancelled.');
            return;
        }

        // Drop Laravel tables only
        $this->info('🗑️  Dropping Laravel tables...');
        foreach (array_reverse($this->laravelTables) as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                $this->line("  ❌ Dropped: {$table}");
            }
        }

        // Run migrations
        $this->info('🆙 Running migrations...');
        $this->call('migrate');
        
        $this->info('✅ Safe fresh migration completed!');
    }

    protected function safeRollback()
    {
        $this->info('🔄 Starting safe rollback...');
        $this->info('📋 This will ONLY rollback Laravel tables:');
        
        foreach ($this->laravelTables as $table) {
            if (Schema::hasTable($table)) {
                $this->line("  - {$table} (exists)");
            } else {
                $this->line("  - {$table} (not found)");
            }
        }
        
        if (!$this->confirm('Continue with safe rollback?')) {
            $this->info('Operation cancelled.');
            return;
        }

        // Get last batch migrations
        $lastBatch = DB::table('migrations')->max('batch');
        
        if (!$lastBatch) {
            $this->warn('No migrations to rollback.');
            return;
        }

        $this->info("🔙 Rolling back batch: {$lastBatch}");
        $this->call('migrate:rollback');
        
        $this->info('✅ Safe rollback completed!');
    }

    protected function showStatus()
    {
        $this->info('📊 Laravel Tables Status:');
        $this->newLine();
        
        $existingTables = [];
        $missingTables = [];
        
        foreach ($this->laravelTables as $table) {
            if (Schema::hasTable($table)) {
                $existingTables[] = $table;
            } else {
                $missingTables[] = $table;
            }
        }
        
        if (!empty($existingTables)) {
            $this->info('✅ Existing tables:');
            foreach ($existingTables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->line("  - {$table} ({$count} records)");
                } catch (\Exception $e) {
                    $this->line("  - {$table} (exists but inaccessible)");
                }
            }
        }
        
        if (!empty($missingTables)) {
            $this->newLine();
            $this->warn('❌ Missing tables:');
            foreach ($missingTables as $table) {
                $this->line("  - {$table}");
            }
        }
        
        $this->newLine();
        $this->info('🔒 Protected (Desktop App) Tables:');
        
        // Show all tables that are NOT in our Laravel tables list
        $allTables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";
        
        $protectedTables = [];
        foreach ($allTables as $table) {
            $tableName = $table->$tableKey;
            if (!in_array($tableName, $this->laravelTables)) {
                $protectedTables[] = $tableName;
            }
        }
        
        if (!empty($protectedTables)) {
            foreach ($protectedTables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->line("  - {$table} ({$count} records) 🛡️");
                } catch (\Exception $e) {
                    $this->line("  - {$table} (protected) 🛡️");
                }
            }
        }
        
        $this->newLine();
        $this->info("Total Laravel tables: " . count($existingTables) . "/" . count($this->laravelTables));
        $this->info("Protected tables: " . count($protectedTables));
    }
}