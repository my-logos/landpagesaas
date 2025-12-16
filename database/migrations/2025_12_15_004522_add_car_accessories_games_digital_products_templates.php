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
        // Car Accessories Template
        $carExists = DB::table('landing_page_templates')
            ->where('slug', 'car-accessories-default')
            ->exists();

        if (!$carExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Car Accessories Template',
                'slug' => 'car-accessories-default',
                'description' => 'تصميم احترافي لصفحات الهبوط لإكسسوارات السيارات مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'car-accessories.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#dc2626',
                        'secondary' => '#1a1a1a',
                        'accent' => '#f97316'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'automotive',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Games Template
        $gamesExists = DB::table('landing_page_templates')
            ->where('slug', 'games-default')
            ->exists();

        if (!$gamesExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Games Template',
                'slug' => 'games-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للألعاب مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'games.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#9333ea',
                        'secondary' => '#ec4899',
                        'accent' => '#06b6d4'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'games',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Digital Products Template
        $digitalExists = DB::table('landing_page_templates')
            ->where('slug', 'digital-products-default')
            ->exists();

        if (!$digitalExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Digital Products Template',
                'slug' => 'digital-products-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للمنتجات الرقمية مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'digital-products.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#2563eb',
                        'secondary' => '#06b6d4',
                        'accent' => '#14b8a6'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'digital',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('landing_page_templates')
            ->whereIn('slug', ['car-accessories-default', 'games-default', 'digital-products-default'])
            ->delete();
    }
};
