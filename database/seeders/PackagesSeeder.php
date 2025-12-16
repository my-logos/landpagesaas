<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    public function run(): void
    {
        // Free Plan
        SubscriptionPackage::updateOrCreate(
            ['slug' => 'free-plan'],
            [
                'name' => 'Free Plan',
                'price_cents' => 0,
                'is_free' => true,
                'interval' => 'monthly',
                'pages_limit' => 1,
                'products_limit' => 1,
                'daily_orders_limit' => 2,
                'monthly_orders_limit' => 10,
                'features' => [
                    'description' => 'Basic free plan with limited features',
                    'google_analytics' => false,
                    'facebook_pixel' => false,
                    'facebook_conversion_api' => false,
                    'tiktok_pixel' => false,
                    'snapchat_pixel' => false,
                ],
            ]
        );

        // Starter Plan
        SubscriptionPackage::updateOrCreate(
            ['slug' => 'starter-plan'],
            [
                'name' => 'Starter Plan',
                'price_cents' => 25000, // 250 EGP
                'is_free' => false,
                'interval' => 'monthly',
                'pages_limit' => 20,
                'products_limit' => 10,
                'daily_orders_limit' => 20,
                'monthly_orders_limit' => 500,
                'features' => [
                    'description' => 'Perfect for small businesses',
                    'google_analytics' => true,
                    'facebook_pixel' => true,
                    'facebook_conversion_api' => false,
                    'tiktok_pixel' => false,
                    'snapchat_pixel' => false,
                ],
            ]
        );

        // Professional Plan
        SubscriptionPackage::updateOrCreate(
            ['slug' => 'professional-plan'],
            [
                'name' => 'Professional Plan',
                'price_cents' => 50000, // 500 EGP
                'is_free' => false,
                'interval' => 'monthly',
                'pages_limit' => 40,
                'products_limit' => 25,
                'daily_orders_limit' => 30,
                'monthly_orders_limit' => 1000,
                'features' => [
                    'description' => 'Advanced features for growing businesses',
                    'google_analytics' => true,
                    'facebook_pixel' => true,
                    'facebook_conversion_api' => true,
                    'tiktok_pixel' => true,
                    'snapchat_pixel' => true,
                ],
            ]
        );
    }
}
