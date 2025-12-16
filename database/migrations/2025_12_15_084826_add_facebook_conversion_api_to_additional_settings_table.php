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
        Schema::table('additional_settings', function (Blueprint $table) {
            $table->boolean('facebook_conversion_api_enabled')->default(false)->after('facebook_pixel_id');
            $table->text('facebook_conversion_api_access_token')->nullable()->after('facebook_conversion_api_enabled');
            $table->string('facebook_conversion_api_pixel_id')->nullable()->after('facebook_conversion_api_access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_settings', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_conversion_api_enabled',
                'facebook_conversion_api_access_token',
                'facebook_conversion_api_pixel_id',
            ]);
        });
    }
};
