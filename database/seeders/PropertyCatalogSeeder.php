<?php

namespace Database\Seeders;

use App\Enums\BillingPeriod;
use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Models\Facility;
use App\Models\Location;
use App\Models\OwnerProfile;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PropertyCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Sample Owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@rentiva.test'],
            [
                'name' => 'H. Bambang Sugiarto',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => UserRole::OWNER,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $owner->id],
            [
                'bio' => 'Pemilik properti dan pengelola kost berpengalaman di kawasan Yogyakarta dan Bandung sejak 2015.',
                'occupation' => 'Wiraswasta & Landlord',
                'gender' => 'male',
                'is_identity_verified' => true,
                'identity_verified_at' => now(),
            ]
        );

        OwnerProfile::updateOrCreate(
            ['user_id' => $owner->id],
            [
                'company_name' => 'Griya Makmur Property Group',
                'bank_name' => 'BCA',
                'bank_account_number' => '8800112233',
                'bank_account_holder' => 'Bambang Sugiarto',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // Fetch helper models
        $locations = Location::all()->keyBy('slug');
        $types = PropertyType::all()->keyBy('slug');
        $roomTypes = RoomType::all()->keyBy('slug');
        $facilities = Facility::all()->keyBy('slug');

        $sampleProperties = [
            [
                'name' => 'Kost Griya Asri Pogung UGM',
                'slug' => 'kost-griya-asri-pogung-ugm',
                'property_type' => 'kost-putri',
                'location' => 'ugm-yogyakarta',
                'address' => 'Jl. Pogung Dalangan No. 42, Sleman, DI Yogyakarta',
                'gender_policy' => GenderPolicy::FEMALE_ONLY,
                'description' => "Kost putri eksklusif sangat dekat dengan Fakultas Teknik & MIPA UGM (hanya 5 menit jalan kaki).\nLingkungan tenang, aman dengan gerbang otomatis dan CCTV 24 jam. Dilengkapi dapur bersama lengkap dan WiFi kencang 100 Mbps.",
                'featured' => true,
                'facilities' => ['wifi', 'cctv-24jam', 'parkir-motor', 'dapur-bersama', 'dispenser', 'cleaning-service'],
                'units' => [
                    [
                        'name' => 'Kamar 101 (Lantai 1)',
                        'room_type' => 'deluxe-kamar-mandi-dalam',
                        'floor' => '1',
                        'size' => '3.5 x 4 m',
                        'capacity' => 1,
                        'price_monthly' => 1750000,
                        'deposit' => 500000,
                        'facilities' => ['ac', 'kamar-mandi-dalam', 'water-heater', 'kasur-springbed', 'lemari-pakaian', 'meja-belajar', 'jendela-luar'],
                    ],
                    [
                        'name' => 'Kamar 102 (Lantai 1)',
                        'room_type' => 'deluxe-kamar-mandi-dalam',
                        'floor' => '1',
                        'size' => '3.5 x 4 m',
                        'capacity' => 1,
                        'price_monthly' => 1750000,
                        'deposit' => 500000,
                        'facilities' => ['ac', 'kamar-mandi-dalam', 'water-heater', 'kasur-springbed', 'lemari-pakaian', 'meja-belajar', 'jendela-luar'],
                    ],
                    [
                        'name' => 'Kamar 201 (Lantai 2 - Balkon)',
                        'room_type' => 'suite-studio',
                        'floor' => '2',
                        'size' => '4 x 5 m',
                        'capacity' => 1,
                        'price_monthly' => 2300000,
                        'deposit' => 750000,
                        'facilities' => ['ac', 'kamar-mandi-dalam', 'water-heater', 'kasur-springbed', 'lemari-pakaian', 'meja-belajar', 'smart-tv'],
                    ],
                ],
            ],
            [
                'name' => 'Kost Dago Harmony ITB',
                'slug' => 'kost-dago-harmony-itb',
                'property_type' => 'kost-putra',
                'location' => 'itb-bandung',
                'address' => 'Jl. Cisitu Indah No. 18, Dago, Coblong, Kota Bandung',
                'gender_policy' => GenderPolicy::MALE_ONLY,
                'description' => "Kost putra nyaman berhawa sejuk di kawasan Dago Atas Bandung. Hanya 7 menit ke Gerbang Belakang ITB.\nAkses mudah ke cafe, minimarket, dan kuliner Dago. Tersedia parkir motor luas dan keamanan terpadu.",
                'featured' => true,
                'facilities' => ['wifi', 'cctv-24jam', 'parkir-motor', 'parkir-mobil', 'dapur-bersama', 'listrik-termasuk'],
                'units' => [
                    [
                        'name' => 'Kamar A-01',
                        'room_type' => 'standard-kamar-mandi-luar',
                        'floor' => '1',
                        'size' => '3 x 3.5 m',
                        'capacity' => 1,
                        'price_monthly' => 1100000,
                        'deposit' => 300000,
                        'facilities' => ['kasur-springbed', 'lemari-pakaian', 'meja-belajar', 'jendela-luar'],
                    ],
                    [
                        'name' => 'Kamar B-05',
                        'room_type' => 'deluxe-kamar-mandi-dalam',
                        'floor' => '2',
                        'size' => '3.5 x 4 m',
                        'capacity' => 1,
                        'price_monthly' => 1650000,
                        'deposit' => 500000,
                        'facilities' => ['ac', 'kamar-mandi-dalam', 'water-heater', 'kasur-springbed', 'lemari-pakaian', 'meja-belajar'],
                    ],
                ],
            ],
            [
                'name' => 'Grand Kukusan Residence UI Depok',
                'slug' => 'grand-kukusan-residence-ui-depok',
                'property_type' => 'kost-campur',
                'location' => 'ui-depok',
                'address' => 'Jl. Kukusan Teknik No. 99, Beji, Kota Depok',
                'gender_policy' => GenderPolicy::ALL,
                'description' => "Kost campur modern premium dengan konsep co-living. Hanya 3 menit ke Pintu Kutek UI Depok.\nDilengkapi fasilitas coworking lounge, pantry modern tiap lantai, smart lock door, dan penjaga 24 jam.",
                'featured' => true,
                'facilities' => ['wifi', 'cctv-24jam', 'parkir-motor', 'parkir-mobil', 'dapur-bersama', 'ruang-tamu', 'cleaning-service'],
                'units' => [
                    [
                        'name' => 'Studio Room 302',
                        'room_type' => 'suite-studio',
                        'floor' => '3',
                        'size' => '4 x 4 m',
                        'capacity' => 1,
                        'price_monthly' => 2500000,
                        'deposit' => 1000000,
                        'facilities' => ['ac', 'kamar-mandi-dalam', 'water-heater', 'kasur-springbed', 'lemari-pakaian', 'meja-belajar', 'smart-tv'],
                    ],
                ],
            ],
            [
                'name' => 'Apartemen Sudirman Park Studio',
                'slug' => 'apartemen-sudirman-park-studio',
                'property_type' => 'apartemen',
                'location' => 'jakarta-selatan',
                'address' => 'Jl. KH Mas Mansyur Kav. 35, Karet Tengsin, Tanah Abang, Jakarta Selatan',
                'gender_policy' => GenderPolicy::ALL,
                'description' => "Unit apartemen studio full furnished di pusat bisnis Sudirman - Thamrin. Akses jalan kaki ke Stasiun MRT Dukuh Atas dan BNI City.\nFasilitas gedung: Kolam renang resort, fitness center, lapangan basket, dan minimarket 24 jam.",
                'featured' => false,
                'facilities' => ['wifi', 'cctv-24jam', 'parkir-mobil', 'cleaning-service'],
                'units' => [
                    [
                        'name' => 'Unit Tower B - 18A',
                        'room_type' => 'suite-studio',
                        'floor' => '18',
                        'size' => '30 m²',
                        'capacity' => 2,
                        'price_monthly' => 4500000,
                        'deposit' => 2000000,
                        'facilities' => ['ac', 'kamar-mandi-dalam', 'water-heater', 'kasur-springbed', 'lemari-pakaian', 'smart-tv'],
                    ],
                ],
            ],
        ];

        foreach ($sampleProperties as $propData) {
            $loc = $locations->get($propData['location']) ?? $locations->first();
            $ptype = $types->get($propData['property_type']) ?? $types->first();

            $property = Property::updateOrCreate(
                ['slug' => $propData['slug']],
                [
                    'owner_id' => $owner->id,
                    'property_type_id' => $ptype->id,
                    'location_id' => $loc->id,
                    'name' => $propData['name'],
                    'description' => $propData['description'],
                    'address' => $propData['address'],
                    'gender_policy' => $propData['gender_policy'],
                    'verification_status' => VerificationStatus::VERIFIED,
                    'status' => PropertyStatus::PUBLISHED,
                    'featured' => $propData['featured'],
                    'published_at' => now(),
                    'verified_at' => now(),
                    'public_location_precision' => 'approximate',
                    'seo_title' => $propData['name'] . ' — Sewa di Rentiva',
                    'seo_description' => Str::limit(strip_tags($propData['description']), 160),
                ]
            );

            // Sync property facilities
            $propFacIds = collect($propData['facilities'])
                ->map(fn ($slug) => $facilities->get($slug)?->id)
                ->filter()
                ->values()
                ->toArray();

            $property->facilities()->sync($propFacIds);

            // Create Units & Price Plans
            foreach ($propData['units'] as $unitData) {
                $rtype = $roomTypes->get($unitData['room_type']) ?? $roomTypes->first();

                $unit = Unit::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'name' => $unitData['name'],
                    ],
                    [
                        'room_type_id' => $rtype->id,
                        'floor' => $unitData['floor'],
                        'size' => $unitData['size'],
                        'capacity' => $unitData['capacity'],
                        'status' => UnitStatus::AVAILABLE,
                        'available_from' => now()->toDateString(),
                    ]
                );

                // Sync unit facilities
                $unitFacIds = collect($unitData['facilities'])
                    ->map(fn ($slug) => $facilities->get($slug)?->id)
                    ->filter()
                    ->values()
                    ->toArray();

                $unit->facilities()->sync($unitFacIds);

                // Price Plan (integer amount IDR)
                PricePlan::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'billing_period' => BillingPeriod::MONTHLY,
                    ],
                    [
                        'amount' => $unitData['price_monthly'],
                        'deposit_amount' => $unitData['deposit'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
