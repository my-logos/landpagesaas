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

        // Check if column already renamed by checking all columns
        // MySQL-specific: SHOW COLUMNS to inspect table structure
        try {
            $columns = DB::select("SHOW COLUMNS FROM `facebook_conversion_api_settings`");
        } catch (\Exception $e) {
            // If SHOW COLUMNS fails, assume table structure is already correct
            return;
        }

        $hasNewColumn = false;
        $hasOldColumn = false;

        foreach ($columns as $column) {
            if ($column->Field === 'fb_conversion_api_setting_id') {
                $hasNewColumn = true;
            }
            if ($column->Field === 'id') {
                $hasOldColumn = true;
            }
        }

        // If new column exists, skip migration (already done)
        if ($hasNewColumn) {
            // Column already renamed, skip migration completely
            return;
        }

        // If old column doesn't exist, something is wrong - skip
        if (!$hasOldColumn) {
            // Old column doesn't exist and new column doesn't exist either? Skip
            return;
        }

        // Change primary key column name from 'id' to 'fb_conversion_api_setting_id'
        // Step 1: Remove AUTO_INCREMENT first (must be done before dropping primary key)
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        } catch (\Exception $e) {
            // If it fails, column might already be modified, continue
        }

        // Step 2: Drop the primary key constraint
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` DROP PRIMARY KEY');
        } catch (\Exception $e) {
            // If it fails, primary key might not exist, continue
        }

        // Step 3: Change column name (only if old column exists)
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` CHANGE COLUMN `id` `fb_conversion_api_setting_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } catch (\Exception $e) {
            // If it fails, column might already be renamed, skip rest
            return;
        }

        // Step 4: Add primary key back
        try {
            DB::statement('ALTER TABLE `facebook_conversion_api_settings` ADD PRIMARY KEY (`fb_conversion_api_setting_id`)');
        } catch (\Exception $e) {
            // If it fails, primary key might already exist, that's okay
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

        // Check if column is already 'id' (not 'fb_conversion_api_setting_id')
        try {
            $columns = DB::select("SHOW COLUMNS FROM `facebook_conversion_api_settings`");
        } catch (\Exception $e) {
            // If SHOW COLUMNS fails, skip rollback
            return;
        }

        $hasIdColumn = false;
        foreach ($columns as $column) {
            if ($column->Field === 'id') {
                $hasIdColumn = true;
                break;
            }
        }
        if ($hasIdColumn) {
            // Column already 'id', skip rollback
            return;
        }

        // Revert back to 'id'
        // Step 1: Remove AUTO_INCREMENT first
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` MODIFY `fb_conversion_api_setting_id` BIGINT UNSIGNED NOT NULL');
        // Step 2: Drop the primary key constraint
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` DROP PRIMARY KEY');
        // Step 3: Change column name back
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` CHANGE COLUMN `fb_conversion_api_setting_id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        // Step 4: Add primary key back
        DB::statement('ALTER TABLE `facebook_conversion_api_settings` ADD PRIMARY KEY (`id`)');
    }
};
