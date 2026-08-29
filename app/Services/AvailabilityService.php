<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\AvailabilityBlock;
use App\Models\BookingRequest;
use App\Models\Unit;

class AvailabilityService
{
    /**
     * Check if a unit is strictly available for a given date range.
     * Prevents double-booking and overlapping reservations.
     */
    public function isUnitAvailable(Unit|int $unit, string $checkInDate, string $checkOutDate, ?int $excludeBookingId = null): bool
    {
        $unitId = $unit instanceof Unit ? $unit->id : $unit;

        // 1. Check Availability Blocks overlap
        $hasBlockOverlap = AvailabilityBlock::where('unit_id', $unitId)
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>', $checkInDate);
            })
            ->exists();

        if ($hasBlockOverlap) {
            return false;
        }

        // 2. Check Overlapping Active/Confirmed Booking Requests
        $bookingQuery = BookingRequest::where('unit_id', $unitId)
            ->whereIn('status', [
                BookingStatus::APPROVED,
                BookingStatus::PAYMENT_PENDING,
                BookingStatus::CONFIRMED,
            ])
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('check_in_date', '<', $checkOutDate)
                      ->where('check_out_date', '>', $checkInDate);
            });

        if ($excludeBookingId) {
            $bookingQuery->where('id', '!=', $excludeBookingId);
        }

        return ! $bookingQuery->exists();
    }

    /**
     * Get all blocked and booked date ranges for a unit (for calendar display).
     */
    public function getUnavailableRanges(int $unitId): array
    {
        $blocks = AvailabilityBlock::where('unit_id', $unitId)
            ->where('end_date', '>=', now()->toDateString())
            ->get(['start_date', 'end_date', 'reason']);

        $bookings = BookingRequest::where('unit_id', $unitId)
            ->whereIn('status', [
                BookingStatus::APPROVED,
                BookingStatus::PAYMENT_PENDING,
                BookingStatus::CONFIRMED,
            ])
            ->where('check_out_date', '>=', now()->toDateString())
            ->get(['check_in_date as start_date', 'check_out_date as end_date', 'status as reason']);

        return $blocks->concat($bookings)->toArray();
    }
}
