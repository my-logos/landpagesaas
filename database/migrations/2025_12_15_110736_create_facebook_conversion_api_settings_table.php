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
        Schema::create('facebook_conversion_api_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_enabled')->default(false);
            $table->string('pixel_id')->nullable();
            $table->text('access_token')->nullable();
            $table->string('test_event_code')->nullable();
            $table->string('currency')->default('EGP');
            $table->boolean('is_system_default')->default(false)->comment('For admin/system landing page');
            $table->timestamps();

            // Unique constraint: one row per user (or null for system default)
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_conversion_api_settings');
    }
};
