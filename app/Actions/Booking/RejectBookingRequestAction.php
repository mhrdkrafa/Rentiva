<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RejectBookingRequestAction
{
    public function execute(User $rejector, BookingRequest $booking, string $rejectionReason): BookingRequest
    {
        return DB::transaction(function () use ($rejector, $booking, $rejectionReason) {
            $lockedBooking = BookingRequest::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            $ownerId = $lockedBooking->unit->property->owner_id;

            if ($rejector->id !== $ownerId && ! $rejector->canManageOwnerProperty($ownerId, 'review_assigned_bookings') && ! $rejector->isAdmin()) {
                throw new AuthorizationException('Anda tidak memiliki hak untuk menolak pengajuan sewa ini.');
            }

            if ($lockedBooking->status !== BookingStatus::PENDING_APPROVAL) {
                throw ValidationException::withMessages([
                    'status' => ['Pengajuan sewa ini sudah tidak dapat ditolak karena berstatus: ' . $lockedBooking->status->label()],
                ]);
            }

            $lockedBooking->update([
                'status' => BookingStatus::REJECTED,
                'rejected_at' => now(),
                'owner_rejection_reason' => $rejectionReason,
            ]);

            return $lockedBooking;
        });
    }
}
