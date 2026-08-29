<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Filament\Facades\Filament;

test('admin panel login page is reachable', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('super admin and admin can access panel', function () {
    $panel = Filament::getPanel('admin');

    $superAdmin = User::factory()->create([
        'role' => UserRole::SUPER_ADMIN,
        'status' => UserStatus::ACTIVE,
    ]);

    $admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'status' => UserStatus::ACTIVE,
    ]);

    expect($superAdmin->canAccessPanel($panel))->toBeTrue()
        ->and($admin->canAccessPanel($panel))->toBeTrue();
});

test('tenant and owner cannot access admin panel', function () {
    $panel = Filament::getPanel('admin');

    $tenant = User::factory()->create([
        'role' => UserRole::TENANT,
        'status' => UserStatus::ACTIVE,
    ]);

    $owner = User::factory()->create([
        'role' => UserRole::OWNER,
        'status' => UserStatus::ACTIVE,
    ]);

    expect($tenant->canAccessPanel($panel))->toBeFalse()
        ->and($owner->canAccessPanel($panel))->toBeFalse();
});

test('suspended admin cannot access admin panel', function () {
    $panel = Filament::getPanel('admin');

    $suspendedAdmin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'status' => UserStatus::SUSPENDED,
    ]);

    expect($suspendedAdmin->canAccessPanel($panel))->toBeFalse();
});
