<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('price_cents')->default(0);
            $table->boolean('is_free')->default(false);
            $table->string('interval')->default('monthly');
            $table->unsignedInteger('pages_limit')->nullable();
            $table->unsignedInteger('products_limit')->nullable();
            $table->unsignedInteger('daily_orders_limit')->nullable();
            $table->unsignedInteger('monthly_orders_limit')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_packages');
    }
};
