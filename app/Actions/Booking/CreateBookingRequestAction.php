<?php

namespace App\Actions\Booking;

use App\Enums\BookingStatus;
use App\Models\BookingRequest;
use App\Models\PricePlan;
use App\Models\Unit;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingPriceCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateBookingRequestAction
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected BookingPriceCalculator $priceCalculator
    ) {}

    public function execute(
        User $tenant,
        Unit $unit,
        PricePlan $pricePlan,
        string $checkInDate,
        int $durationMonths = 1,
        ?string $tenantNotes = null
    ): BookingRequest {
        return DB::transaction(function () use ($tenant, $unit, $pricePlan, $checkInDate, $durationMonths, $tenantNotes) {
            // Lock unit record to avoid race conditions
            $lockedUnit = Unit::where('id', $unit->id)->lockForUpdate()->firstOrFail();

            $checkIn = Carbon::parse($checkInDate)->startOfDay();
            $checkOut = (clone $checkIn)->addMonths($durationMonths);

            // 1. Verify unit is available for requested dates
            if (! $this->availabilityService->isUnitAvailable($lockedUnit, $checkIn->toDateString(), $checkOut->toDateString())) {
                throw ValidationException::withMessages([
                    'check_in_date' => ['Unit/kamar ini tidak tersedia pada rentang tanggal yang Anda pilih.'],
                ]);
            }

            // 2. Server-side price calculation
            $pricing = $this->priceCalculator->calculate($lockedUnit, $pricePlan, $durationMonths);

            // 3. Generate unique booking code: BK-YYYYMMDD-XXXX
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));

            // 4. Create Booking Request with 24h expiration deadline for owner approval
            return BookingRequest::create([
                'code' => $code,
                'tenant_id' => $tenant->id,
                'unit_id' => $lockedUnit->id,
                'price_plan_id' => $pricePlan->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'duration_months' => $durationMonths,
                'duration_unit' => 'month',
                'base_amount' => $pricing['base_amount'],
                'deposit_amount' => $pricing['deposit_amount'],
                'additional_fees_amount' => $pricing['additional_fees_amount'],
                'total_amount' => $pricing['total_amount'],
                'status' => BookingStatus::PENDING_APPROVAL,
                'tenant_notes' => $tenantNotes,
                'expires_at' => now()->addHours(24),
            ]);
        });
    }
}
