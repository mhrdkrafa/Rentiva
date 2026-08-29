<?php

namespace App\Actions\Booking;

use App\Models\AvailabilityBlock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class CreateAvailabilityBlockAction
{
    public function execute(User $owner, Unit $unit, string $startDate, string $endDate, string $reason = 'maintenance', ?string $notes = null): AvailabilityBlock
    {
        $ownerId = $unit->property->owner_id;
        if ($owner->id !== $ownerId && ! $owner->canManageOwnerProperty($ownerId, 'manage_assigned_availability') && ! $owner->isAdmin()) {
            throw new AuthorizationException('Anda tidak berhak mengatur ketersediaan unit ini.');
        }

        return AvailabilityBlock::create([
            'unit_id' => $unit->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }
}
