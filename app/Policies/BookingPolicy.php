<?php

namespace App\Policies;

use App\Models\BookingRequest;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, BookingRequest $booking): bool
    {
        $ownerId = $booking->unit->property->owner_id;

        return $user->id === $booking->tenant_id 
            || $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId)
            || $user->isAdmin();
    }

    public function cancel(User $user, BookingRequest $booking): bool
    {
        return $user->id === $booking->tenant_id || $user->isAdmin();
    }

    public function approve(User $user, BookingRequest $booking): bool
    {
        $ownerId = $booking->unit->property->owner_id;

        return $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId, 'review_assigned_bookings') 
            || $user->isAdmin();
    }

    public function reject(User $user, BookingRequest $booking): bool
    {
        $ownerId = $booking->unit->property->owner_id;

        return $user->id === $ownerId 
            || $user->canManageOwnerProperty($ownerId, 'review_assigned_bookings') 
            || $user->isAdmin();
    }
}
