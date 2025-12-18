<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_support_ticket_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->year('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedInteger('tickets_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_support_ticket_counts');
    }
};

