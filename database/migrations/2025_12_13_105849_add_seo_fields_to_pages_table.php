<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Add SEO fields
            if (!Schema::hasColumn('pages', 'seo_title')) {
                $table->string('seo_title', 255)->nullable()->after('title');
            }
            if (!Schema::hasColumn('pages', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('pages', 'seo_keywords')) {
                $table->string('seo_keywords', 500)->nullable()->after('seo_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'seo_title')) {
                $table->dropColumn('seo_title');
            }
            if (Schema::hasColumn('pages', 'seo_description')) {
                $table->dropColumn('seo_description');
            }
            if (Schema::hasColumn('pages', 'seo_keywords')) {
                $table->dropColumn('seo_keywords');
            }
        });
    }
};
