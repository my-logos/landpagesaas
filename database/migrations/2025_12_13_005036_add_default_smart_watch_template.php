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
        // Check if template already exists
        $exists = DB::table('landing_page_templates')
            ->where('slug', 'smart-watch-default')
            ->exists();

        if (!$exists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Smart Watch Template',
                'slug' => 'smart-watch-default',
                'description' => 'تصميم احترافي لصفحات الهبوط للمنتجات الإلكترونية مع جميع الأقسام المطلوبة: Hero Section، المميزات، التفاصيل، الشهادات، الأسئلة الشائعة، ونموذج الطلب',
                'preview_image' => null,
                'template_file' => 'smart-watch.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#667eea',
                        'secondary' => '#764ba2',
                        'accent' => '#10b981'
                    ],
                    'features' => [
                        'countdown_timer' => true,
                        'testimonials' => true,
                        'faq' => true,
                        'social_sharing' => true
                    ]
                ]),
                'category' => 'ecommerce',
                'sort_order' => 1,
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
            ->where('slug', 'smart-watch-default')
            ->delete();
    }
};
