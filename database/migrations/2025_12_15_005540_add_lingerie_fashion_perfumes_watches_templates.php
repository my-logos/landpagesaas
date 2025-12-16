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
        // Lingerie Template
        $lingerieExists = DB::table('landing_page_templates')
            ->where('slug', 'lingerie-default')
            ->exists();

        if (!$lingerieExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Lingerie Template',
                'slug' => 'lingerie-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للانجيري مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'lingerie.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#ec4899',
                        'secondary' => '#f43f5e',
                        'accent' => '#ff6b9d'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'lingerie',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Fashion Template
        $fashionExists = DB::table('landing_page_templates')
            ->where('slug', 'fashion-default')
            ->exists();

        if (!$fashionExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Fashion Template',
                'slug' => 'fashion-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للأزياء مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'fashion.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#1a1a1a',
                        'secondary' => '#d4af37',
                        'accent' => '#c0c0c0'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'fashion',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Perfumes Template
        $perfumesExists = DB::table('landing_page_templates')
            ->where('slug', 'perfumes-default')
            ->exists();

        if (!$perfumesExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Perfumes Template',
                'slug' => 'perfumes-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للعطور مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'perfumes.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#d4af37',
                        'secondary' => '#9333ea',
                        'accent' => '#f59e0b'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'perfumes',
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Watches Template
        $watchesExists = DB::table('landing_page_templates')
            ->where('slug', 'watches-default')
            ->exists();

        if (!$watchesExists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Watches Template',
                'slug' => 'watches-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للساعات مع جميع الأقسام المطلوبة',
                'preview_image' => null,
                'template_file' => 'watches.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#94a3b8',
                        'secondary' => '#3b82f6',
                        'accent' => '#1e3a8a'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true
                    ]
                ]),
                'category' => 'watches',
                'sort_order' => 9,
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
            ->whereIn('slug', ['lingerie-default', 'fashion-default', 'perfumes-default', 'watches-default'])
            ->delete();
    }
};
