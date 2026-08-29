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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // uppercase promo voucher code
            $table->string('name');
            $table->string('discount_type')->default('percentage'); // 'percentage' or 'fixed'
            $table->unsignedBigInteger('discount_value'); // e.g. 10 for 10% or 100000 for Rp 100k
            $table->unsignedBigInteger('max_discount_amount')->nullable(); // cap for percentage discount in IDR
            $table->unsignedBigInteger('min_transaction_amount')->default(0); // min IDR amount to qualify
            $table->dateTime('starts_at')->nullable()->index();
            $table->dateTime('ends_at')->nullable()->index();
            $table->unsignedInteger('max_uses')->nullable(); // total global usage limit
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('booking_request_id')->nullable()->constrained('booking_requests')->nullOnDelete();
            $table->unsignedBigInteger('discount_amount'); // integer IDR
            $table->timestamp('used_at');
            $table->timestamps();

            $table->index(['promotion_id', 'user_id'], 'promo_user_usage_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotions');
    }
};
