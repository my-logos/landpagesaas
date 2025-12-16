<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Sawa',
            'site_logo' => null,
            'site_email' => 'info@Sawa.com',
            'site_phone' => '+201234567890',
            'seo_title' => 'Sawa - Create Professional Landing Pages',
            'seo_description' => 'Enjoy creating simple and effective landing pages',
            'seo_keywords' => 'landing pages, e-commerce, saas',
            'currency_code' => 'EGP',
            'default_language' => 'en',
        ];

        foreach ($defaults as $key => $value) {
            try {
                // Create for both English and Arabic
                Settings::updateOrCreate(
                    ['key' => $key, 'locale' => 'en'],
                    ['value' => $value]
                );
                Settings::updateOrCreate(
                    ['key' => $key, 'locale' => 'ar'],
                    ['value' => $value]
                );
            } catch (\Exception $e) {
                // Table might not exist yet, skip
                continue;
            }
        }
    }
}
