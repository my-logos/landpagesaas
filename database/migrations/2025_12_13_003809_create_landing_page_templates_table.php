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
        Schema::create('landing_page_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Template name
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->text('description')->nullable(); // Template description
            $table->string('preview_image')->nullable(); // Preview image path
            $table->string('template_file')->nullable(); // Blade template file path
            $table->boolean('is_enabled')->default(false); // Is template active
            $table->json('settings')->nullable(); // Template-specific settings (colors, fonts, etc.)
            $table->integer('sort_order')->default(0); // Display order
            $table->string('category')->nullable(); // Template category (e.g., 'modern', 'classic', 'minimal')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_templates');
    }
};
