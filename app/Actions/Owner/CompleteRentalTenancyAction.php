<?php

namespace App\Actions\Owner;

use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class CompleteRentalTenancyAction
{
    public function execute(User $owner, Rental $rental, ?string $checkOutNotes = null): Rental
    {
        $ownerId = $rental->unit->property->owner_id;
        if ($owner->id !== $ownerId && ! $owner->canManageOwnerProperty($ownerId) && ! $owner->isAdmin()) {
            throw new AuthorizationException('Anda tidak berhak menyelesaikan kontrak sewa ini.');
        }

        return DB::transaction(function () use ($rental, $checkOutNotes) {
            $rental->update([
                'status' => RentalStatus::COMPLETED,
                'check_out_notes' => $checkOutNotes,
            ]);

            // Release unit status back to AVAILABLE
            $rental->unit->update([
                'status' => UnitStatus::AVAILABLE,
            ]);

            return $rental;
        });
    }
}
