<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration if using SQLite (for testing)
        // SQLite doesn't support SHOW COLUMNS, and this migration is for MySQL structure changes
        if (config('database.default') === 'sqlite') {
            return;
        }

        // Check current table structure
        // MySQL-specific: SHOW COLUMNS to inspect table structure
        try {
            $columns = DB::select("SHOW COLUMNS FROM `facebook_conversion_api_settings`");
        } catch (\Exception $e) {
            // If SHOW COLUMNS fails, assume table structure is already correct
            return;
        }

        $hasIdColumn = false;
        $hasFbColumn = false;
        $fbColumnIsPrimary = false;

        foreach ($columns as $column) {
            if ($column->Field === 'id') {
                $hasIdColumn = true;
            }
            if ($column->Field === 'fb_conversion_api_setting_id') {
                $hasFbColumn = true;
                if ($column->Key === 'PRI') {
                    $fbColumnIsPrimary = true;
                }
            }
        }

        // If 'id' already exists, skip migration
        if ($hasIdColumn) {
            return;
        }

        // If 'fb_conversion_api_setting_id' doesn't exist, something is wrong
        if (!$hasFbColumn) {
            return;
        }

        // Step 1: Drop the primary key constraint if exists
        if ($fbColumnIsPrimary) {
            try {
                DB::statement('ALTER TABLE `facebook_conversion_api_settings` DROP PRIMARY KEY');
            } catch (\Exception $e) {
                // Continue if it fails
            }
        }

        // Step 2: Remove AUTO_INCREMENT from fb_conversion_api_setting_id
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` MODIFY `fb_conversion_api_setting_id` BIGINT UNSIGNED NOT NULL');
        } catch (\Exception $e) {
            // Continue if it fails
        }

        // Step 3: Change column name from fb_conversion_api_setting_id to id (without AUTO_INCREMENT first)
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` CHANGE COLUMN `fb_conversion_api_setting_id` `id` BIGINT UNSIGNED NOT NULL');
        } catch (\Exception $e) {
            // If it fails, throw exception to see what's wrong
            throw new \Exception('Failed to rename column: ' . $e->getMessage());
        }

        // Step 4: Add primary key back on id
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` ADD PRIMARY KEY (`id`)');
        } catch (\Exception $e) {
            // Continue if it fails
        }

        // Step 5: Add AUTO_INCREMENT to id (must be done after PRIMARY KEY)
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } catch (\Exception $e) {
            // Continue if it fails
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if using SQLite (for testing)
        if (config('database.default') === 'sqlite') {
            return;
        }

        // Check if column is already 'fb_conversion_api_setting_id'
        try {
            $columns = DB::select("SHOW COLUMNS FROM `facebook_conversion_api_settings`");
        } catch (\Exception $e) {
            // If SHOW COLUMNS fails, skip rollback
            return;
        }

        $hasFbColumn = false;
        foreach ($columns as $column) {
            if ($column->Field === 'fb_conversion_api_setting_id') {
                $hasFbColumn = true;
                break;
            }
        }

        if ($hasFbColumn) {
            // Column already 'fb_conversion_api_setting_id', skip rollback
            return;
        }

        // Revert back to 'fb_conversion_api_setting_id'
        // Step 1: Remove AUTO_INCREMENT first
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        // Step 2: Drop the primary key constraint
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` DROP PRIMARY KEY');
        // Step 3: Change column name back
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` CHANGE COLUMN `id` `fb_conversion_api_setting_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        // Step 4: Add primary key back
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` ADD PRIMARY KEY (`fb_conversion_api_setting_id`)');
    }
};
