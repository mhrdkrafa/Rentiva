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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // BK-20260829-XXXX
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('price_plan_id')->constrained('price_plans');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('duration_months')->default(1);
            $table->string('duration_unit', 20)->default('month'); // day, week, month, year

            // Integer money amounts (IDR)
            $table->unsignedBigInteger('base_amount');
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->unsignedBigInteger('additional_fees_amount')->default(0);
            $table->unsignedBigInteger('total_amount');

            $table->string('status', 30)->default('pending_approval')->index(); // pending_approval, approved, rejected, cancelled, expired, payment_pending, confirmed
            $table->text('tenant_notes')->nullable();
            $table->text('owner_rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index(); // auto expiration deadline

            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'check_in_date', 'check_out_date', 'status'], 'bk_unit_dates_status_idx');
            $table->index(['tenant_id', 'status'], 'bk_tenant_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
