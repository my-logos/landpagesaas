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
        Schema::table('products', function (Blueprint $table) {
            $table->string('currency', 3)->default('EGP')->after('price_cents');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency', 3)->nullable()->after('total_cents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
