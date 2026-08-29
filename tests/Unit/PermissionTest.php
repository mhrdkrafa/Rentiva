<?php

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;

test('permission enum returns labels', function () {
    expect(Permission::ACCESS_ADMIN_PANEL->label())->toBe('Akses Panel Admin')
        ->and(Permission::MANAGE_PROPERTIES->label())->toBe('Kelola Properti Sendiri')
        ->and(Permission::REQUEST_BOOKING->label())->toBe('Ajukan Permintaan Sewa');
});

test('user hasPermission checks role permissions correctly', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();
    $manager = User::factory()->propertyManager()->create();
    $suspendedOwner = User::factory()->owner()->suspended()->create();

    // Super admin has all permissions
    expect($superAdmin->hasPermission(Permission::ACCESS_ADMIN_PANEL))->toBeTrue()
        ->and($superAdmin->hasPermission(Permission::MANAGE_PROPERTIES))->toBeTrue()
        ->and($superAdmin->hasPermission(Permission::REQUEST_BOOKING))->toBeTrue();

    // Admin has admin permissions
    expect($admin->hasPermission(Permission::ACCESS_ADMIN_PANEL))->toBeTrue()
        ->and($admin->hasPermission(Permission::MANAGE_USERS))->toBeTrue()
        ->and($admin->hasPermission(Permission::MANAGE_PROPERTIES))->toBeFalse();

    // Owner has owner permissions
    expect($owner->hasPermission(Permission::MANAGE_PROPERTIES))->toBeTrue()
        ->and($owner->hasPermission(Permission::ACCEPT_BOOKINGS))->toBeTrue()
        ->and($owner->hasPermission(Permission::ASSIGN_MANAGERS))->toBeTrue()
        ->and($owner->hasPermission(Permission::ACCESS_ADMIN_PANEL))->toBeFalse();

    // Tenant has tenant permissions
    expect($tenant->hasPermission(Permission::REQUEST_BOOKING))->toBeTrue()
        ->and($tenant->hasPermission(Permission::MANAGE_PROPERTIES))->toBeFalse();

    // Manager has manager permissions
    expect($manager->hasPermission(Permission::MANAGE_ASSIGNED_UNITS))->toBeTrue()
        ->and($manager->hasPermission(Permission::ACCESS_ADMIN_PANEL))->toBeFalse();

    // Suspended users have no active permissions
    expect($suspendedOwner->hasPermission(Permission::MANAGE_PROPERTIES))->toBeFalse();
});
