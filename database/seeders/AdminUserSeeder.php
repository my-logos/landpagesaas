<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@example.com';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // assign role if method exists (Spatie), otherwise set role column
        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('admin');
            } catch (\Throwable $e) {
                // ignore
            }
        } else {
            $user->role = 'admin';
            $user->save();
        }

        // try to subscribe admin to a free package if available
        try {
            $service = app(SubscriptionService::class);
            $free = \App\Models\SubscriptionPackage::where('is_free', true)->first();
            if ($free) {
                $service->subscribeUserToPackage($user, $free->id);
            }
        } catch (\Throwable $e) {
            // swallow errors; admin can be set manually
        }
    }
}
