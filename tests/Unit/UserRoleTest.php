<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;

test('user role enum values and labels', function () {
    expect(UserRole::SUPER_ADMIN->value)->toBe('super_admin')
        ->and(UserRole::ADMIN->value)->toBe('admin')
        ->and(UserRole::OWNER->value)->toBe('owner')
        ->and(UserRole::PROPERTY_MANAGER->value)->toBe('property_manager')
        ->and(UserRole::TENANT->value)->toBe('tenant')
        ->and(UserRole::GUEST->value)->toBe('guest');

    expect(UserRole::SUPER_ADMIN->canAccessAdminPanel())->toBeTrue()
        ->and(UserRole::ADMIN->canAccessAdminPanel())->toBeTrue()
        ->and(UserRole::OWNER->canAccessAdminPanel())->toBeFalse()
        ->and(UserRole::TENANT->canAccessAdminPanel())->toBeFalse();
});

test('user status enum values and active check', function () {
    expect(UserStatus::ACTIVE->value)->toBe('active')
        ->and(UserStatus::PENDING->value)->toBe('pending')
        ->and(UserStatus::SUSPENDED->value)->toBe('suspended')
        ->and(UserStatus::INACTIVE->value)->toBe('inactive');

    expect(UserStatus::ACTIVE->isActive())->toBeTrue()
        ->and(UserStatus::SUSPENDED->isActive())->toBeFalse();
});
