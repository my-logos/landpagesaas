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
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->text('additional_description')->nullable();
            $table->string('ai_version')->default('v2');
            $table->string('form_type')->default('default');
            $table->json('form_fields')->nullable();
            $table->string('facebook_pixel')->nullable();
            $table->string('tiktok_pixel')->nullable();
            $table->string('snapchat_pixel')->nullable();
            $table->string('google_analytics_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn([
                'product_id',
                'additional_description',
                'ai_version',
                'form_type',
                'form_fields',
                'facebook_pixel',
                'tiktok_pixel',
                'snapchat_pixel',
                'google_analytics_id',
            ]);
        });
    }
};
