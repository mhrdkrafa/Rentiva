<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOwner() || $user->isPropertyManager() || $user->isAdmin();
    }

    public function view(?User $user, Property $property): bool
    {
        if ($property->isPublished() && $property->isVerified()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $user->id === $property->owner_id || $user->canManageOwnerProperty($property->owner_id) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::MANAGE_PROPERTIES) || $user->isAdmin();
    }

    public function update(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id 
            || $user->canManageOwnerProperty($property->owner_id, 'manage_assigned_units')
            || $user->isAdmin();
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id || $user->isAdmin();
    }

    public function verify(User $user): bool
    {
        return $user->hasPermission(Permission::MODERATE_PROPERTIES);
    }
}
