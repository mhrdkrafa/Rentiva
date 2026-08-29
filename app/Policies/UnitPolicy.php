<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    public function view(?User $user, Unit $unit): bool
    {
        return true;
    }

    public function create(User $user, int $ownerId): bool
    {
        return $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId, 'manage_assigned_units')
            || $user->isAdmin();
    }

    public function update(User $user, Unit $unit): bool
    {
        $ownerId = $unit->property->owner_id;

        return $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId, 'manage_assigned_units')
            || $user->isAdmin();
    }

    public function delete(User $user, Unit $unit): bool
    {
        $ownerId = $unit->property->owner_id;

        return $user->id === $ownerId || $user->isAdmin();
    }
}
