<?php

namespace App\Policies;

use App\Models\RentalIssue;
use App\Models\User;

class RentalIssuePolicy
{
    public function view(User $user, RentalIssue $issue): bool
    {
        $ownerId = $issue->rental->unit->property->owner_id;

        return $user->id === $issue->tenant_id 
            || $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId)
            || $user->isAdmin();
    }

    public function update(User $user, RentalIssue $issue): bool
    {
        $ownerId = $issue->rental->unit->property->owner_id;

        return $user->id === $issue->tenant_id 
            || $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId)
            || $user->isAdmin();
    }
}
