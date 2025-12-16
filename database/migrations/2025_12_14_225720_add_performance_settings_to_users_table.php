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
        Schema::table('users', function (Blueprint $table) {
            $table->string('performance_mode')->default('fast')->after('additional_sales_enabled');
            $table->boolean('include_session_data')->default(false)->after('performance_mode');
            $table->boolean('include_location_data')->default(false)->after('include_session_data');
            $table->boolean('include_device_type')->default(false)->after('include_location_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'performance_mode',
                'include_session_data',
                'include_location_data',
                'include_device_type'
            ]);
        });
    }
};
