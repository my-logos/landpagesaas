<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_faq', function (Blueprint $table) {
            $table->id();
            $table->string('question_en');
            $table->string('question_ar');
            $table->text('answer_en');
            $table->text('answer_ar');
            $table->json('keywords_en')->nullable(); // For search matching
            $table->json('keywords_ar')->nullable(); // For search matching
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_faq');
    }
};

