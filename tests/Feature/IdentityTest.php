<?php

use App\Actions\Identity\AssignPropertyManagerAction;
use App\Actions\Identity\RevokePropertyManagerAction;
use App\Actions\Identity\UpdateOwnerProfileAction;
use App\Actions\Identity\UpdateTenantProfileAction;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PropertyManagerAssignment;
use App\Models\User;
use App\Models\UserProfile;
use App\Policies\UserPolicy;
use App\Policies\UserProfilePolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('tenant can update their profile information and emergency contact', function () {
    Storage::fake('public');

    $tenant = User::factory()->tenant()->create([
        'name' => 'Original Name',
        'phone' => '08111111111',
    ]);

    $action = new UpdateTenantProfileAction();
    $avatar = UploadedFile::fake()->create('avatar.png', 50, 'image/png');

    $profile = $action->execute($tenant, [
        'name' => 'Updated Tenant Name',
        'phone' => '08222222222',
        'gender' => 'male',
        'date_of_birth' => '1998-05-15',
        'occupation' => 'Software Engineer',
        'bio' => 'Looking for a cozy room',
        'emergency_contact_name' => 'John Parent',
        'emergency_contact_phone' => '08333333333',
        'emergency_contact_relation' => 'Father',
    ], $avatar);

    expect($tenant->fresh()->name)->toBe('Updated Tenant Name')
        ->and($tenant->fresh()->phone)->toBe('08222222222')
        ->and($profile->gender)->toBe('male')
        ->and($profile->emergency_contact_name)->toBe('John Parent')
        ->and($profile->avatar_path)->not->toBeNull();

    expect(Storage::disk('public')->exists($profile->avatar_path))->toBeTrue();
});

test('owner can update owner profile with business and bank details', function () {
    $owner = User::factory()->owner()->create();
    $action = new UpdateOwnerProfileAction();

    $ownerProfile = $action->execute($owner, [
        'name' => 'Owner Juragan Kost',
        'phone' => '08555555555',
        'company_name' => 'Griya Makmur Sejahtera',
        'tax_number' => '12.345.678.9-012.000',
        'bank_name' => 'BCA',
        'bank_account_number' => '88800112233',
        'bank_account_holder' => 'Owner Juragan Kost',
    ]);

    expect($owner->fresh()->name)->toBe('Owner Juragan Kost')
        ->and($ownerProfile->company_name)->toBe('Griya Makmur Sejahtera')
        ->and($ownerProfile->bank_name)->toBe('BCA')
        ->and($ownerProfile->bank_account_number)->toBe('88800112233');
});

test('owner can assign and revoke a property manager', function () {
    $owner = User::factory()->owner()->create();
    $managerUser = User::factory()->create([
        'email' => 'manager@rentiva.test',
        'role' => UserRole::TENANT,
    ]);

    $assignAction = new AssignPropertyManagerAction();
    $assignment = $assignAction->execute(
        $owner,
        'manager@rentiva.test',
        null,
        ['manage_assigned_units', 'review_assigned_bookings']
    );

    expect($assignment)->toBeInstanceOf(PropertyManagerAssignment::class)
        ->and($assignment->owner_id)->toBe($owner->id)
        ->and($assignment->manager_id)->toBe($managerUser->id)
        ->and($assignment->isActive())->toBeTrue()
        ->and($assignment->hasPermission('manage_assigned_units'))->toBeTrue()
        ->and($assignment->hasPermission('unknown_permission'))->toBeFalse();

    // Check manager scoping helper on user
    expect($managerUser->canManageOwnerProperty($owner->id))->toBeTrue()
        ->and($managerUser->canManageOwnerProperty($owner->id, 'manage_assigned_units'))->toBeTrue()
        ->and($managerUser->canManageOwnerProperty($owner->id, 'non_granted_permission'))->toBeFalse();

    // Revoke
    $revokeAction = new RevokePropertyManagerAction();
    $revokeAction->execute($owner, $assignment);

    expect($assignment->fresh()->status)->toBe('revoked')
        ->and($managerUser->canManageOwnerProperty($owner->id))->toBeFalse();
});

test('user policy prevents unauthorized profile modification', function () {
    $admin = User::factory()->admin()->create();
    $userA = User::factory()->tenant()->create();
    $userB = User::factory()->tenant()->create();

    $policy = new UserPolicy();

    // User A can update own account
    expect($policy->update($userA, $userA))->toBeTrue();

    // User A cannot update User B
    expect($policy->update($userA, $userB))->toBeFalse();

    // Admin can manage User B
    expect($policy->update($admin, $userB))->toBeTrue();
});

test('user profile policy restricts viewing private emergency contact and id data', function () {
    $userA = User::factory()->tenant()->create();
    $userB = User::factory()->tenant()->create();
    $admin = User::factory()->admin()->create();

    $profileA = UserProfile::create([
        'user_id' => $userA->id,
        'emergency_contact_name' => 'Private Contact',
    ]);

    $policy = new UserProfilePolicy();

    expect($policy->viewPrivateDetails($userA, $profileA))->toBeTrue();
    expect($policy->viewPrivateDetails($userB, $profileA))->toBeFalse();
    expect($policy->viewPrivateDetails($admin, $profileA))->toBeTrue();
});
