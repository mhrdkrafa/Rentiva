<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = [
            ['name' => 'Tipe Standard (Kamar Mandi Luar)', 'slug' => 'standard-kamar-mandi-luar', 'description' => 'Kamar tidur nyaman dengan kasur dan lemari, kamar mandi bersih di luar kamar.'],
            ['name' => 'Tipe Deluxe (Kamar Mandi Dalam & AC)', 'slug' => 'deluxe-kamar-mandi-dalam', 'description' => 'Kamar tidur privat ber-AC dengan kamar mandi di dalam dan water heater.'],
            ['name' => 'Tipe Suite / Studio Luas', 'slug' => 'suite-studio', 'description' => 'Unit kamar ekstra luas dengan area santai, meja kerja, dan fasilitas lengkap.'],
            ['name' => 'Tipe Paviliun / 2 Kamar', 'slug' => 'paviliun-2-kamar', 'description' => 'Unit paviliun mandiri dengan ruang tamu dan dapur privat.'],
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::updateOrCreate(['slug' => $roomType['slug']], $roomType);
        }
    }
}
