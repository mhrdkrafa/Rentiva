<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            // General / Services
            ['name' => 'WiFi Kecepatan Tinggi', 'slug' => 'wifi', 'type' => 'general'],
            ['name' => 'Listrik Termasuk (Free Token)', 'slug' => 'listrik-termasuk', 'type' => 'general'],
            ['name' => 'Keamanan / CCTV 24 Jam', 'slug' => 'cctv-24jam', 'type' => 'general'],
            ['name' => 'Penjaga Kost / Pengelola Standby', 'slug' => 'penjaga-kost', 'type' => 'general'],
            ['name' => 'Layanan Kebersihan (Cleaning)', 'slug' => 'cleaning-service', 'type' => 'general'],

            // Property Facilities
            ['name' => 'Area Parkir Motor Luas', 'slug' => 'parkir-motor', 'type' => 'property'],
            ['name' => 'Area Parkir Mobil', 'slug' => 'parkir-mobil', 'type' => 'property'],
            ['name' => 'Dapur Bersama & Kulkas', 'slug' => 'dapur-bersama', 'type' => 'property'],
            ['name' => 'Mesin Cuci & Jemuran', 'slug' => 'mesin-cuci', 'type' => 'property'],
            ['name' => 'Ruang Tamu & Bersantai', 'slug' => 'ruang-tamu', 'type' => 'property'],
            ['name' => 'Dispenser Air Minum Gratis', 'slug' => 'dispenser', 'type' => 'property'],

            // Room Facilities
            ['name' => 'AC (Pendingin Ruangan)', 'slug' => 'ac', 'type' => 'room'],
            ['name' => 'Kamar Mandi Dalam', 'slug' => 'kamar-mandi-dalam', 'type' => 'room'],
            ['name' => 'Water Heater (Air Panas)', 'slug' => 'water-heater', 'type' => 'room'],
            ['name' => 'Kasur Springbed & Bantal', 'slug' => 'kasur-springbed', 'type' => 'room'],
            ['name' => 'Lemari Pakaian', 'slug' => 'lemari-pakaian', 'type' => 'room'],
            ['name' => 'Meja Belajar & Kursi Kerja', 'slug' => 'meja-belajar', 'type' => 'room'],
            ['name' => 'Jendela Ventilasi Luar', 'slug' => 'jendela-luar', 'type' => 'room'],
            ['name' => 'Smart TV', 'slug' => 'smart-tv', 'type' => 'room'],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(['slug' => $facility['slug']], $facility);
        }
    }
}
