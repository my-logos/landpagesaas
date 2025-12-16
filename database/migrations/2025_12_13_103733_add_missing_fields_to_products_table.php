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
            // Add description field if not exists
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            // Add short_description field if not exists
            if (!Schema::hasColumn('products', 'short_description')) {
                $table->string('short_description', 250)->nullable()->after('description');
            }

            // Add shipping_price_cents field if not exists
            if (!Schema::hasColumn('products', 'shipping_price_cents')) {
                $table->unsignedBigInteger('shipping_price_cents')->default(0)->after('price_cents');
            }

            // Add images field if not exists
            if (!Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable()->after('ai_version');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('products', 'short_description')) {
                $table->dropColumn('short_description');
            }
            if (Schema::hasColumn('products', 'shipping_price_cents')) {
                $table->dropColumn('shipping_price_cents');
            }
            if (Schema::hasColumn('products', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};
