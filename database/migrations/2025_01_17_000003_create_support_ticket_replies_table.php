<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reply');
            $table->boolean('is_admin_reply')->default(false);
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');
    }
};

