<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Use Spatie role model if available, otherwise fallback to simple table insert
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);

            return;
        }

        // If Spatie is not installed or migrations not published yet, only attempt DB insert if table exists
        if (Schema::hasTable('roles')) {
            DB::table('roles')->updateOrInsert(['name' => 'admin'], ['guard_name' => 'web']);
            DB::table('roles')->updateOrInsert(['name' => 'user'], ['guard_name' => 'web']);
        }
    }
}
