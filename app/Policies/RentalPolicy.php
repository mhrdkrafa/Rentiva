<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;

class RentalPolicy
{
    public function view(User $user, Rental $rental): bool
    {
        $ownerId = $rental->unit->property->owner_id;

        return $user->id === $rental->tenant_id 
            || $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId)
            || $user->isAdmin();
    }
}
