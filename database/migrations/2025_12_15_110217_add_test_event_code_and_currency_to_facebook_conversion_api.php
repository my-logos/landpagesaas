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
            $table->string('facebook_conversion_api_test_event_code')->nullable()->after('facebook_conversion_api_pixel_id');
            $table->string('facebook_conversion_api_currency')->default('EGP')->after('facebook_conversion_api_test_event_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_settings', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_conversion_api_test_event_code',
                'facebook_conversion_api_currency',
            ]);
        });
    }
};
