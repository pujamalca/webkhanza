<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class SqlQueryTracker
{
    protected static bool $enabled = true;
    protected static bool $tracking = false;
    protected static array $ignoredTables = [
        'activity_log',
        'sessions',
        'cache',
        'jobs',
        'failed_jobs',
    ];

    public static function enable(): void
    {
        static::$enabled = true;
    }

    public static function disable(): void
    {
        static::$enabled = false;
    }

    public static function track(): void
    {
        if (!static::$enabled) {
            return;
        }

        DB::listen(function ($query) {
            // Prevent recursive logging
            if (static::$tracking) {
                return;
            }

            // Skip if disabled or query involves ignored tables
            if (!static::$enabled || static::shouldIgnoreQuery($query->sql)) {
                return;
            }

            // Set tracking flag to prevent recursion
            static::$tracking = true;

            try {
                // Double check table exists before logging
                if (!\Illuminate\Support\Facades\Schema::hasTable('activity_log')) {
                    return;
                }

                $properties = [
                    'query' => static::formatQuery($query->sql),
                    'bindings' => $query->bindings,
                    'execution_time' => $query->time . ' ms',
                    'connection' => $query->connectionName,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                ];

                // Determine query type
                $queryType = static::getQueryType($query->sql);

                // Get affected table(s)
                $tables = static::extractTables($query->sql);

                activity('sql_queries')
                    ->causedBy(auth()->user())
                    ->withProperties($properties)
                    ->log("SQL {$queryType} on " . implode(', ', $tables));
            } catch (\Exception $e) {
                // Silently fail if logging not available
            } finally {
                // Always reset tracking flag
                static::$tracking = false;
            }
        });
    }

    protected static function shouldIgnoreQuery(string $sql): bool
    {
        $sql = strtolower($sql);
        
        foreach (static::$ignoredTables as $table) {
            if (strpos($sql, $table) !== false) {
                return true;
            }
        }

        // Ignore some system queries
        if (strpos($sql, 'show columns') !== false ||
            strpos($sql, 'show tables') !== false ||
            strpos($sql, 'describe') !== false ||
            strpos($sql, 'information_schema') !== false) {
            return true;
        }

        return false;
    }

    protected static function formatQuery(string $sql): string
    {
        // Format SQL for better readability
        $sql = preg_replace('/\s+/', ' ', $sql);
        return trim($sql);
    }

    protected static function getQueryType(string $sql): string
    {
        $sql = strtolower(trim($sql));
        
        if (strpos($sql, 'select') === 0) return 'SELECT';
        if (strpos($sql, 'insert') === 0) return 'INSERT';
        if (strpos($sql, 'update') === 0) return 'UPDATE';
        if (strpos($sql, 'delete') === 0) return 'DELETE';
        if (strpos($sql, 'create') === 0) return 'CREATE';
        if (strpos($sql, 'alter') === 0) return 'ALTER';
        if (strpos($sql, 'drop') === 0) return 'DROP';
        
        return 'UNKNOWN';
    }

    protected static function extractTables(string $sql): array
    {
        $sql = strtolower($sql);
        $tables = [];

        // Simple table extraction (can be improved)
        if (preg_match('/from\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables[] = $matches[1];
        }
        
        if (preg_match('/into\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables[] = $matches[1];
        }
        
        if (preg_match('/update\s+`?(\w+)`?/i', $sql, $matches)) {
            $tables[] = $matches[1];
        }

        return array_unique($tables) ?: ['unknown'];
    }
}