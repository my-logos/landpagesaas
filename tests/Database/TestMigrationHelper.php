<?php

namespace Tests\Database;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Helper class to handle migrations that use MySQL-specific features
 * This allows tests to run with SQLite without modifying the original migrations
 */
class TestMigrationHelper
{
    /**
     * Check if we're using SQLite for tests
     */
    public static function isUsingSqlite(): bool
    {
        return config('database.default') === 'sqlite';
    }

    /**
     * Get table columns using database-agnostic method
     */
    public static function getTableColumns(string $table): array
    {
        if (self::isUsingSqlite()) {
            // For SQLite, use pragma table_info
            $columns = DB::select("PRAGMA table_info({$table})");
            return array_map(fn($col) => (object)['Field' => $col->name], $columns);
        }

        // For MySQL, use SHOW COLUMNS
        return DB::select("SHOW COLUMNS FROM `{$table}`");
    }

    /**
     * Check if column exists in table
     */
    public static function columnExists(string $table, string $column): bool
    {
        $columns = self::getTableColumns($table);
        foreach ($columns as $col) {
            if ($col->Field === $column) {
                return true;
            }
        }
        return false;
    }
}
