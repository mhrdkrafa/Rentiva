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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types');
            $table->string('name');
            $table->string('floor')->nullable();
            $table->string('size')->nullable(); // e.g. "3x4 m²"
            $table->integer('capacity')->default(1);
            $table->text('description')->nullable();
            $table->string('status', 30)->default('available')->index(); // available, reserved, occupied, maintenance, unavailable
            $table->date('available_from')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
