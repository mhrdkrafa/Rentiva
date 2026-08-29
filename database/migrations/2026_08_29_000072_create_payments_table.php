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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. PAY-YYYYMMDD-XXXX
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            
            $table->unsignedBigInteger('amount'); // integer minor units
            $table->string('payment_method'); // 'bank_transfer', 'qris', 'credit_card', 'e_wallet'
            $table->string('payment_channel')->nullable(); // 'bca_va', 'gopay', 'qris', etc.
            $table->string('status')->default('pending')->index(); // 'pending', 'settlement', 'expired', 'failed', 'refunded'
            
            $table->string('gateway_reference')->nullable()->index();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status'], 'pay_inv_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
