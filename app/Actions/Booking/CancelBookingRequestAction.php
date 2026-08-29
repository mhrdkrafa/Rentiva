<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelBookingRequestAction
{
    public function execute(User $tenant, BookingRequest $booking): BookingRequest
    {
        return DB::transaction(function () use ($tenant, $booking) {
            $lockedBooking = BookingRequest::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            if ($tenant->id !== $lockedBooking->tenant_id && ! $tenant->isAdmin()) {
                throw new AuthorizationException('Anda tidak berhak membatalkan pengajuan sewa ini.');
            }

            if (! in_array($lockedBooking->status, [BookingStatus::PENDING_APPROVAL, BookingStatus::APPROVED], true)) {
                throw ValidationException::withMessages([
                    'status' => ['Pengajuan sewa tidak dapat dibatalkan pada status saat ini.'],
                ]);
            }

            $lockedBooking->update([
                'status' => BookingStatus::CANCELLED,
                'cancelled_at' => now(),
            ]);

            return $lockedBooking;
        });
    }
}
