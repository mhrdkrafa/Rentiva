<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\PropertyManagerAssignment;
use App\Models\User;

class PropertyManagerAssignmentPolicy
{
    /**
     * Determine whether the user can assign a manager.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ASSIGN_MANAGERS);
    }

    /**
     * Determine whether the user can revoke the assignment.
     */
    public function revoke(User $user, PropertyManagerAssignment $assignment): bool
    {
        return $user->id === $assignment->owner_id || $user->hasPermission(Permission::MANAGE_USERS);
    }
}
