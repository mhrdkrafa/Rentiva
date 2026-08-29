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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->string('item_type')->default('rent'); // 'rent', 'deposit', 'additional_fee', 'discount'
            $table->unsignedBigInteger('unit_price'); // integer minor unit
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('total_amount'); // integer minor unit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
