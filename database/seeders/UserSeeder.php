<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\OwnerProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // 1. Super Administrator
        $admin = User::updateOrCreate(
            ['email' => 'admin@rentiva.test'],
            [
                'name' => 'Super Admin Rentiva',
                'phone' => '+628111000100',
                'password' => $defaultPassword,
                'role' => UserRole::SUPER_ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // 2. Pemilik Kost Utama (Owner 1)
        $owner1 = User::updateOrCreate(
            ['email' => 'owner@rentiva.test'],
            [
                'name' => 'H. Bambang Sutrisno',
                'phone' => '+628122334455',
                'password' => $defaultPassword,
                'role' => UserRole::OWNER,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        OwnerProfile::updateOrCreate(
            ['user_id' => $owner1->id],
            [
                'company_name' => 'Sutrisno Residence Group',
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'Bambang Sutrisno',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $owner1->id],
            [
                'bio' => 'Pemilik kost berpengalaman di kawasan Pogung dan Kaliurang Yogyakarta sejak 2015.',
                'gender' => 'male',
                'occupation' => 'Wiraswasta & Landlord',
                'is_identity_verified' => true,
                'identity_verified_at' => now(),
            ]
        );

        // 3. Pemilik Kost 2 (Owner 2)
        $owner2 = User::updateOrCreate(
            ['email' => 'owner2@rentiva.test'],
            [
                'name' => 'Ibu Ratna Dewi, S.E.',
                'phone' => '+628133445566',
                'password' => $defaultPassword,
                'role' => UserRole::OWNER,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        OwnerProfile::updateOrCreate(
            ['user_id' => $owner2->id],
            [
                'company_name' => 'Dewi Pavilions & Kost Eksklusif',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '9876543210',
                'bank_account_holder' => 'Ratna Dewi',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $owner2->id],
            [
                'bio' => 'Pengelola apartemen studio dan kost putri eksklusif Seturan & Gejayan.',
                'gender' => 'female',
                'occupation' => 'Pengusaha Properti',
                'is_identity_verified' => true,
                'identity_verified_at' => now(),
            ]
        );

        // 4. Manajer Properti (Property Manager)
        $manager = User::updateOrCreate(
            ['email' => 'manager@rentiva.test'],
            [
                'name' => 'Rian Hidayat (Manajer Kost)',
                'phone' => '+628155667788',
                'password' => $defaultPassword,
                'role' => UserRole::OWNER,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $manager->id],
            [
                'bio' => 'Manajer operasional kost Griya Asri Pogung.',
                'gender' => 'male',
                'occupation' => 'Property Manager',
                'is_identity_verified' => true,
                'identity_verified_at' => now(),
            ]
        );

        // 5. Penyewa Utama (Tenant 1)
        $tenant1 = User::updateOrCreate(
            ['email' => 'tenant@rentiva.test'],
            [
                'name' => 'Mahardika Rafa',
                'phone' => '+628177889900',
                'password' => $defaultPassword,
                'role' => UserRole::TENANT,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $tenant1->id],
            [
                'bio' => 'Mahasiswa tingkat akhir UGM yang mencari hunian nyaman dan tenang.',
                'gender' => 'male',
                'emergency_contact_name' => 'Ibu Siti Rahmawati',
                'emergency_contact_phone' => '+6281299887766',
                'emergency_contact_relation' => 'Orang Tua (Ibu)',
                'occupation' => 'Mahasiswa S1 Informatika',
                'is_identity_verified' => true,
                'identity_verified_at' => now(),
            ]
        );

        // 6. Penyewa 2 (Tenant 2)
        $tenant2 = User::updateOrCreate(
            ['email' => 'tenant2@rentiva.test'],
            [
                'name' => 'Anisa Nur Azizah',
                'phone' => '+628188990011',
                'password' => $defaultPassword,
                'role' => UserRole::TENANT,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $tenant2->id],
            [
                'bio' => 'Karyawati swasta ramah dan disiplin.',
                'gender' => 'female',
                'emergency_contact_name' => 'Bapak Joko Santoso',
                'emergency_contact_phone' => '+6281311223344',
                'emergency_contact_relation' => 'Orang Tua (Ayah)',
                'occupation' => 'Software Engineer',
                'is_identity_verified' => true,
                'identity_verified_at' => now(),
            ]
        );
    }
}
