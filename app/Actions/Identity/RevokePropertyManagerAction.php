<?php

namespace App\Actions\Identity;

use App\Models\PropertyManagerAssignment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class RevokePropertyManagerAction
{
    public function execute(User $owner, PropertyManagerAssignment $assignment): bool
    {
        if ($assignment->owner_id !== $owner->id && ! $owner->isAdmin()) {
            throw new AuthorizationException('Anda tidak berhak mencabut penugasan ini.');
        }

        $assignment->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        return true;
    }
}
