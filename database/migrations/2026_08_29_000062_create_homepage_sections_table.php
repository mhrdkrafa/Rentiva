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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // 'hero', 'campus_search', 'featured_properties', 'stats', 'testimonials', 'faq', 'articles', 'cta'
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->json('content')->nullable(); // flexible configuration parameters
            $table->unsignedInteger('order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
