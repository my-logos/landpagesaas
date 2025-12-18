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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // User (store owner)
            $table->string('sender_name');
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->text('subject');
            $table->text('message');
            $table->string('order_number')->nullable(); // Related order number if from order tracking/success page
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete(); // Related order if exists
            $table->enum('status', ['new', 'read', 'replied', 'closed'])->default('new');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_number');
            $table->index('status');
        });

        Schema::create('message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // User who replied (null if customer)
            $table->text('reply');
            $table->boolean('is_customer_reply')->default(false); // True if customer replied, false if user replied
            $table->timestamps();

            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_replies');
        Schema::dropIfExists('messages');
    }
};
