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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained('property_types');
            $table->foreignId('location_id')->constrained('locations');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('address');
            $table->string('gender_policy', 30)->default('all')->index(); // all, male_only, female_only, married_couples
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('public_location_precision', 30)->default('approximate'); // exact, approximate, area_only
            $table->string('verification_status', 30)->default('unverified')->index(); // unverified, pending, verified, rejected
            $table->string('status', 30)->default('draft')->index(); // draft, pending_review, published, suspended, archived
            $table->boolean('featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['location_id', 'status', 'verification_status']);
            $table->index(['owner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
