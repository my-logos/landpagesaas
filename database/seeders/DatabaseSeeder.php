<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguagesSeeder::class,
            RolesSeeder::class,
            PackagesSeeder::class,
            SettingsSeeder::class,
            AdminUserSeeder::class,
        ]);

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        // Assign user role
        if (method_exists($testUser, 'assignRole')) {
            try {
                $testUser->assignRole('user');
            } catch (\Throwable $e) {
                // ignore
            }
        } else {
            $testUser->role = 'user';
            $testUser->save();
        }

        // Subscribe to free package if available
        try {
            $service = app(\App\Services\SubscriptionService::class);
            $free = \App\Models\SubscriptionPackage::where('is_free', true)->first();
            if ($free) {
                $service->subscribeUserToPackage($testUser, $free->id);
            }
        } catch (\Throwable $e) {
            // swallow errors
        }
    }
}
