<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;
use App\Models\UserProfile;

class UserProfilePolicy
{
    /**
     * Determine whether the user can view the profile (including private info like emergency contact / ID).
     */
    public function viewPrivateDetails(User $user, UserProfile $profile): bool
    {
        return $user->id === $profile->user_id || $user->hasPermission(Permission::MANAGE_USERS);
    }

    /**
     * Determine whether the user can update the profile.
     */
    public function update(User $user, UserProfile $profile): bool
    {
        return $user->id === $profile->user_id || $user->hasPermission(Permission::MANAGE_USERS);
    }

    /**
     * Determine whether the user can verify the identity.
     */
    public function verifyIdentity(User $user): bool
    {
        return $user->hasPermission(Permission::MANAGE_USERS);
    }
}
