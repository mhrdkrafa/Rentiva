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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. INV-YYYYMMDD-XXXX
            $table->foreignId('booking_request_id')->nullable()->constrained('booking_requests')->nullOnDelete();
            $table->foreignId('rental_id')->nullable()->constrained('rentals')->nullOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            
            // All money fields stored as integer minor units (IDR)
            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->unsignedBigInteger('additional_fees_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_amount');
            
            $table->string('status')->default('unpaid')->index(); // 'unpaid', 'paid', 'cancelled', 'refunded', 'expired'
            $table->date('due_date')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status'], 'inv_tenant_status_idx');
            $table->index(['owner_id', 'status'], 'inv_owner_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
