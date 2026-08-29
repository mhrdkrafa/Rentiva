<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\BookingRequest;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApproveBookingRequestAction
{
    public function __construct(
        protected AvailabilityService $availabilityService
    ) {}

    public function execute(User $approver, BookingRequest $booking): BookingRequest
    {
        return DB::transaction(function () use ($approver, $booking) {
            $lockedBooking = BookingRequest::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            $ownerId = $lockedBooking->unit->property->owner_id;

            // Authorization: Approver must be owner, assigned property manager, or admin
            if ($approver->id !== $ownerId && ! $approver->canManageOwnerProperty($ownerId, 'review_assigned_bookings') && ! $approver->isAdmin()) {
                throw new AuthorizationException('Anda tidak memiliki hak untuk menyetujui pengajuan sewa ini.');
            }

            if ($lockedBooking->status !== BookingStatus::PENDING_APPROVAL) {
                throw ValidationException::withMessages([
                    'status' => ['Pengajuan sewa ini sudah tidak dapat disetujui karena berstatus: ' . $lockedBooking->status->label()],
                ]);
            }

            // Verify dates are still free (excluding this booking)
            if (! $this->availabilityService->isUnitAvailable($lockedBooking->unit, $lockedBooking->check_in_date->toDateString(), $lockedBooking->check_out_date->toDateString(), $lockedBooking->id)) {
                throw ValidationException::withMessages([
                    'unit_id' => ['Unit ini sudah terisi atau memiliki jadwal sewa lain pada tanggal tersebut.'],
                ]);
            }

            // Transition to APPROVED and give tenant 24 hours to complete payment
            $lockedBooking->update([
                'status' => BookingStatus::APPROVED,
                'approved_at' => now(),
                'expires_at' => now()->addHours(24),
            ]);

            return $lockedBooking;
        });
    }
}
