<?php

namespace App\Actions\Tenant;

use App\Enums\BookingStatus;
use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Models\BookingRequest;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateRentalTenancyAction
{
    /**
     * Create an official rental tenancy lease from a confirmed booking.
     */
    public function execute(BookingRequest $booking): Rental
    {
        return DB::transaction(function () use ($booking) {
            $code = 'RNT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

            $monthlyRent = $booking->duration_months > 0 
                ? (int) ($booking->base_amount / $booking->duration_months) 
                : $booking->base_amount;

            $rental = Rental::create([
                'code' => $code,
                'tenant_id' => $booking->tenant_id,
                'unit_id' => $booking->unit_id,
                'booking_request_id' => $booking->id,
                'start_date' => $booking->check_in_date,
                'end_date' => $booking->check_out_date,
                'monthly_rent' => $monthlyRent,
                'deposit_held' => $booking->deposit_amount,
                'status' => now()->gte($booking->check_in_date) ? RentalStatus::ACTIVE : RentalStatus::PENDING_MOVE_IN,
            ]);

            // Update Unit status to OCCUPIED
            $booking->unit->update([
                'status' => UnitStatus::OCCUPIED,
            ]);

            // Update Booking status to CONFIRMED
            $booking->update([
                'status' => BookingStatus::CONFIRMED,
            ]);

            return $rental;
        });
    }
}
