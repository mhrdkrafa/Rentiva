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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->unique()->constrained('rentals')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            
            // Ratings 1 to 5
            $table->unsignedTinyInteger('rating');
            $table->unsignedTinyInteger('cleanliness_rating')->default(5);
            $table->unsignedTinyInteger('accuracy_rating')->default(5);
            $table->unsignedTinyInteger('communication_rating')->default(5);
            $table->unsignedTinyInteger('location_rating')->default(5);
            $table->unsignedTinyInteger('value_rating')->default(5);
            
            $table->text('comment');
            $table->string('moderation_status')->default('approved')->index(); // 'pending', 'approved', 'rejected'
            
            $table->text('owner_reply')->nullable();
            $table->timestamp('owner_replied_at')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'moderation_status'], 'rev_prop_mod_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
