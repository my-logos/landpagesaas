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
            ->where('slug', 'decoration-furniture-default')
            ->exists();

        if (!$exists) {
            DB::table('landing_page_templates')->insert([
                'name' => 'Decoration & Furniture Template',
                'slug' => 'decoration-furniture-default',
                'description' => 'تصميم احترافي لصفحات الهبوط لأنظمة الديكورات والأثاث مع جميع الأقسام المطلوبة: Hero Section، المعرض، المميزات، التفاصيل، الشهادات، الأسئلة الشائعة، ونموذج الطلب',
                'preview_image' => null,
                'template_file' => 'decoration-furniture.blade.php',
                'is_enabled' => true,
                'settings' => json_encode([
                    'colors' => [
                        'primary' => '#d4af37',
                        'secondary' => '#8b4513',
                        'accent' => '#800020'
                    ],
                    'features' => [
                        'gallery' => true,
                        'testimonials' => true,
                        'faq' => true,
                        'social_sharing' => true
                    ]
                ]),
                'category' => 'decoration',
                'sort_order' => 2,
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
            ->where('slug', 'decoration-furniture-default')
            ->delete();
    }
};
