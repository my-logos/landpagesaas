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
            $table->string('recaptcha_version')->nullable()->after('google_tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additional_settings', function (Blueprint $table) {
            $table->dropColumn('recaptcha_version');
        });
    }
};
