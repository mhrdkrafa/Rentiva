<?php

namespace Database\Seeders;

use App\Enums\LocationType;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            // Cities
            ['name' => 'Jakarta Selatan', 'slug' => 'jakarta-selatan', 'type' => LocationType::CITY],
            ['name' => 'Jakarta Barat', 'slug' => 'jakarta-barat', 'type' => LocationType::CITY],
            ['name' => 'Bandung', 'slug' => 'bandung', 'type' => LocationType::CITY],
            ['name' => 'Yogyakarta', 'slug' => 'yogyakarta', 'type' => LocationType::CITY],
            ['name' => 'Surabaya', 'slug' => 'surabaya', 'type' => LocationType::CITY],
            ['name' => 'Malang', 'slug' => 'malang', 'type' => LocationType::CITY],
            ['name' => 'Depok', 'slug' => 'depok', 'type' => LocationType::CITY],
            ['name' => 'Semarang', 'slug' => 'semarang', 'type' => LocationType::CITY],
            ['name' => 'Denpasar (Bali)', 'slug' => 'denpasar-bali', 'type' => LocationType::CITY],

            // Campus Areas
            ['name' => 'Sekitar Kampus UI Depok', 'slug' => 'ui-depok', 'type' => LocationType::CAMPUS],
            ['name' => 'Sekitar ITB Ganesha Bandung', 'slug' => 'itb-bandung', 'type' => LocationType::CAMPUS],
            ['name' => 'Sekitar UGM Sleman Yogyakarta', 'slug' => 'ugm-yogyakarta', 'type' => LocationType::CAMPUS],
            ['name' => 'Sekitar Universitas Brawijaya Malang', 'slug' => 'ub-malang', 'type' => LocationType::CAMPUS],
            ['name' => 'Sekitar Unpad Jatinangor', 'slug' => 'unpad-jatinangor', 'type' => LocationType::CAMPUS],
            ['name' => 'Sekitar ITS Surabaya', 'slug' => 'its-surabaya', 'type' => LocationType::CAMPUS],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['slug' => $loc['slug']], $loc);
        }
    }
}
