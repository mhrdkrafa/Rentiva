<?php

namespace Database\Seeders;

use App\Enums\DiscountType;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'code' => 'HEMAT2026',
                'name' => 'Promo Awal Tahun Mahasiswa & Umum',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 10,
                'max_discount_amount' => 150000,
                'min_transaction_amount' => 1000000,
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->addMonths(6),
                'max_uses' => 100,
                'used_count' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'ANAKKOST50',
                'name' => 'Potongan Langsung 50 Ribu',
                'discount_type' => DiscountType::FIXED,
                'discount_value' => 50000,
                'max_discount_amount' => null,
                'min_transaction_amount' => 500000,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addMonths(12),
                'max_uses' => 500,
                'used_count' => 45,
                'is_active' => true,
            ],
            [
                'code' => 'SUPERDEAL100',
                'name' => 'Diskon Spesial Sewa Kamar Eksklusif',
                'discount_type' => DiscountType::FIXED,
                'discount_value' => 100000,
                'max_discount_amount' => null,
                'min_transaction_amount' => 1500000,
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->addMonths(3),
                'max_uses' => 50,
                'used_count' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'RENTIVAVIP',
                'name' => 'Voucher Eksklusif Pengguna Baru Rentiva',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 15,
                'max_discount_amount' => 250000,
                'min_transaction_amount' => 1200000,
                'starts_at' => now()->subDays(1),
                'ends_at' => now()->addMonths(6),
                'max_uses' => 200,
                'used_count' => 19,
                'is_active' => true,
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::updateOrCreate(['code' => $promo['code']], $promo);
        }
    }
}
