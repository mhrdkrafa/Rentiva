<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Kost Putri', 'slug' => 'kost-putri', 'description' => 'Hunian kost khusus wanita/mahasiswi dengan keamanan dan privasi terjamin.'],
            ['name' => 'Kost Putra', 'slug' => 'kost-putra', 'description' => 'Hunian kost khusus pria/mahasiswa dengan fasilitas lengkap dekat kampus dan perkantoran.'],
            ['name' => 'Kost Campur', 'slug' => 'kost-campur', 'description' => 'Hunian kost terbuka untuk pria, wanita, maupun karyawan.'],
            ['name' => 'Apartemen / Studio', 'slug' => 'apartemen', 'description' => 'Unit apartemen modern dengan fasilitas gedung seperti kolam renang dan gym.'],
            ['name' => 'Rumah Kontrakan', 'slug' => 'rumah-kontrakan', 'description' => 'Satu unit rumah tinggal penuh yang cocok untuk keluarga atau sewa bersama.'],
        ];

        foreach ($types as $type) {
            PropertyType::updateOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
