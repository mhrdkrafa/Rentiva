<?php

namespace App\Actions\Identity;

use App\Enums\UserRole;
use App\Models\PropertyManagerAssignment;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AssignPropertyManagerAction
{
    public function execute(User $owner, string $managerEmail, ?int $propertyId = null, array $permissions = []): PropertyManagerAssignment
    {
        $manager = User::where('email', $managerEmail)->first();

        if (! $manager) {
            throw ValidationException::withMessages([
                'manager_email' => ['Pengguna dengan email tersebut tidak ditemukan.'],
            ]);
        }

        if ($manager->id === $owner->id) {
            throw ValidationException::withMessages([
                'manager_email' => ['Anda tidak dapat menugaskan diri Anda sendiri sebagai manajer properti.'],
            ]);
        }

        // If manager is currently tenant or guest, upgrade/set role to property_manager if appropriate
        if ($manager->role === UserRole::TENANT || $manager->role === UserRole::GUEST) {
            $manager->update(['role' => UserRole::PROPERTY_MANAGER]);
        }

        return PropertyManagerAssignment::updateOrCreate(
            [
                'owner_id' => $owner->id,
                'manager_id' => $manager->id,
                'property_id' => $propertyId,
            ],
            [
                'permissions' => $permissions,
                'status' => 'active',
                'assigned_at' => now(),
                'revoked_at' => null,
            ]
        );
    }
}
