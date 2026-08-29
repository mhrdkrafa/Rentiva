<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

test('super admin seeder creates active super admin', function () {
    $this->seed(SuperAdminSeeder::class);

    $superAdmin = User::where('email', 'admin@rentiva.test')->first();

    expect($superAdmin)->not->toBeNull()
        ->and($superAdmin->role)->toBe(UserRole::SUPER_ADMIN)
        ->and($superAdmin->status)->toBe(UserStatus::ACTIVE)
        ->and($superAdmin->isSuperAdmin())->toBeTrue()
        ->and($superAdmin->isAdmin())->toBeTrue()
        ->and(Hash::check('password', $superAdmin->password))->toBeTrue();
});

test('rentiva super-admin command creates new super admin', function () {
    Artisan::call('rentiva:super-admin', [
        '--name' => 'Custom Admin',
        '--email' => 'custom@rentiva.test',
        '--password' => 'secret12345',
    ]);

    $admin = User::where('email', 'custom@rentiva.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->name)->toBe('Custom Admin')
        ->and($admin->role)->toBe(UserRole::SUPER_ADMIN)
        ->and(Hash::check('secret12345', $admin->password))->toBeTrue();
});
