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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // RNT-20260829-XXXX
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('booking_request_id')->nullable()->constrained('booking_requests')->nullOnDelete();

            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('monthly_rent');
            $table->unsignedBigInteger('deposit_held')->default(0);

            $table->string('status', 30)->default('active')->index(); // pending_move_in, active, completed, terminated
            $table->text('check_in_notes')->nullable();
            $table->text('check_out_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status'], 'rnt_tenant_status_idx');
            $table->index(['unit_id', 'status'], 'rnt_unit_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
