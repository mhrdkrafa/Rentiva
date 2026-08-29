<?php

namespace App\Actions\Booking;

use App\Models\AvailabilityBlock;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteAvailabilityBlockAction
{
    public function execute(User $owner, AvailabilityBlock $block): bool
    {
        $ownerId = $block->unit->property->owner_id;
        if ($owner->id !== $ownerId && ! $owner->canManageOwnerProperty($ownerId, 'manage_assigned_availability') && ! $owner->isAdmin()) {
            throw new AuthorizationException('Anda tidak berhak menghapus blok ketersediaan ini.');
        }

        return (bool) $block->delete();
    }
}
