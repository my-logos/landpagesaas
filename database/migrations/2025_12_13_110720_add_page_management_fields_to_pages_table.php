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
            // Add slug for custom URLs
            if (!Schema::hasColumn('pages', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }
            
            // Add status (draft/published)
            if (!Schema::hasColumn('pages', 'status')) {
                $table->string('status')->default('draft')->after('slug'); // draft, published
            }
            
            // Add store name for link generation
            if (!Schema::hasColumn('pages', 'store_name')) {
                $table->string('store_name')->nullable()->after('status');
            }
            
            // Add custom domain
            if (!Schema::hasColumn('pages', 'custom_domain')) {
                $table->string('custom_domain')->nullable()->after('store_name');
            }
            
            // Add settings JSON for page-specific settings (smart assistant, fixed button, coupons, counter, etc.)
            if (!Schema::hasColumn('pages', 'settings')) {
                $table->json('settings')->nullable()->after('google_analytics_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('pages', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('pages', 'store_name')) {
                $table->dropColumn('store_name');
            }
            if (Schema::hasColumn('pages', 'custom_domain')) {
                $table->dropColumn('custom_domain');
            }
            if (Schema::hasColumn('pages', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};
