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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_id')->unique()->nullable(); // Payment ID from gateway
            $table->string('payment_method'); // paymob, kashier, fawry, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->string('type')->default('package'); // package, wallet
            $table->nullableMorphs('payable'); // For package_id or other models
            $table->string('status')->default('pending'); // pending, paid, failed, cancelled
            $table->json('metadata')->nullable(); // Store additional payment data
            $table->text('response_data')->nullable(); // Store full gateway response
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
