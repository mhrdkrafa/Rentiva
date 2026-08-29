<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\BookingRequest;

class ExpireStaleBookingsAction
{
    /**
     * Expire all pending or approved bookings whose expiration deadline has passed.
     */
    public function execute(): int
    {
        $expiredCount = 0;

        $staleBookings = BookingRequest::whereIn('status', [BookingStatus::PENDING_APPROVAL, BookingStatus::APPROVED])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($staleBookings as $booking) {
            $booking->update([
                'status' => BookingStatus::EXPIRED,
                'expired_at' => now(),
            ]);
            $expiredCount++;
        }

        return $expiredCount;
    }
}
