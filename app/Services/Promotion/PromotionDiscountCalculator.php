<?php

namespace App\Services\Promotion;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PromotionDiscountCalculator
{
    /**
     * Validate promotion code and calculate applicable integer discount.
     */
    public function validateAndCalculate(string $code, int $amount, ?User $user = null): array
    {
        $code = strtoupper(trim($code));

        $promotion = Promotion::where('code', $code)->first();

        if (! $promotion || ! $promotion->is_active) {
            throw ValidationException::withMessages([
                'promo_code' => ['Kode voucher promo tidak ditemukan atau sudah tidak aktif.'],
            ]);
        }

        if ($promotion->starts_at && $promotion->starts_at->isFuture()) {
            throw ValidationException::withMessages([
                'promo_code' => ['Periode promo ini belum dimulai.'],
            ]);
        }

        if ($promotion->ends_at && $promotion->ends_at->isPast()) {
            throw ValidationException::withMessages([
                'promo_code' => ['Masa berlaku kode voucher promo telah berakhir.'],
            ]);
        }

        if ($promotion->max_uses && $promotion->used_count >= $promotion->max_uses) {
            throw ValidationException::withMessages([
                'promo_code' => ['Kuota pemakaian kode voucher promo ini telah habis.'],
            ]);
        }

        if ($amount < $promotion->min_transaction_amount) {
            throw ValidationException::withMessages([
                'promo_code' => ['Minimal transaksi untuk menggunakan promo ini adalah ' . $promotion->formatted_discount_label],
            ]);
        }

        $discountAmount = $promotion->calculateDiscount($amount);

        return [
            'promotion' => $promotion,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $amount - $discountAmount),
        ];
    }
}
