<?php

use App\Actions\Booking\ApproveBookingRequestAction;
use App\Actions\Booking\CancelBookingRequestAction;
use App\Actions\Booking\CreateAvailabilityBlockAction;
use App\Actions\Booking\CreateBookingRequestAction;
use App\Actions\Booking\ExpireStaleBookingsAction;
use App\Actions\Booking\RejectBookingRequestAction;
use App\Enums\BillingPeriod;
use App\Enums\BookingStatus;
use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\BookingRequest;
use App\Models\Location;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingPriceCalculator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();
    $this->otherTenant = User::factory()->tenant()->create();
    $this->otherOwner = User::factory()->owner()->create();

    $this->location = Location::create(['name' => 'Sleman, Yogyakarta', 'slug' => 'sleman']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Putra', 'slug' => 'kost-putra']);
    $this->roomType = RoomType::create(['name' => 'VIP', 'slug' => 'vip']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Pandega Sleman',
        'slug' => 'kost-pandega-sleman',
        'description' => 'Kost nyaman fasilitas lengkap di Pandega',
        'address' => 'Jl. Pandega Marta No. 20',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $this->property->additionalFees()->create([
        'name' => 'Biaya Kebersihan',
        'amount' => 50000, // 50rb IDR
        'is_required' => true,
        'is_active' => true,
    ]);

    $this->unit = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar VIP 01',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->pricePlan = PricePlan::create([
        'unit_id' => $this->unit->id,
        'billing_period' => BillingPeriod::MONTHLY,
        'amount' => 2000000, // 2jt IDR/bulan
        'deposit_amount' => 500000, // 500rb IDR
        'is_active' => true,
    ]);

    $this->availabilityService = new AvailabilityService();
    $this->priceCalculator = new BookingPriceCalculator();
});

test('price calculator computes base, deposit, and required additional fees accurately in integer units', function () {
    // 3 months rent: (2,000,000 * 3) + 500,000 (deposit) + 50,000 (fees) = 6,550,000
    $pricing = $this->priceCalculator->calculate($this->unit, $this->pricePlan, 3);

    expect($pricing['base_amount'])->toBe(6000000)
        ->and($pricing['deposit_amount'])->toBe(500000)
        ->and($pricing['additional_fees_amount'])->toBe(50000)
        ->and($pricing['total_amount'])->toBe(6550000)
        ->and($pricing['duration_months'])->toBe(3);
});

test('tenant can submit a booking request and system sets initial state and code', function () {
    $action = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);

    $booking = $action->execute(
        $this->tenant,
        $this->unit,
        $this->pricePlan,
        now()->addDays(5)->toDateString(),
        2,
        'Saya mahasiswa semester awal'
    );

    expect($booking)->toBeInstanceOf(BookingRequest::class)
        ->and($booking->status)->toBe(BookingStatus::PENDING_APPROVAL)
        ->and($booking->code)->toStartWith('BK-')
        ->and($booking->base_amount)->toBe(4000000)
        ->and($booking->deposit_amount)->toBe(500000)
        ->and($booking->total_amount)->toBe(4550000)
        ->and($booking->tenant_notes)->toBe('Saya mahasiswa semester awal')
        ->and($booking->expires_at)->not->toBeNull();
});

test('availability block prevents booking creation on conflicting dates', function () {
    $blockAction = new CreateAvailabilityBlockAction();
    $blockAction->execute(
        $this->owner,
        $this->unit,
        now()->addDays(10)->toDateString(),
        now()->addDays(20)->toDateString(),
        'maintenance'
    );

    $action = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);

    // Request overlapping with maintenance dates should fail
    expect(fn () => $action->execute(
        $this->tenant,
        $this->unit,
        $this->pricePlan,
        now()->addDays(12)->toDateString(),
        1
    ))->toThrow(ValidationException::class);
});

test('owner can approve booking request and transitions to approved status', function () {
    $createAction = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);
    $booking = $createAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->addDays(5)->toDateString(), 1);

    $approveAction = new ApproveBookingRequestAction($this->availabilityService);
    $approvedBooking = $approveAction->execute($this->owner, $booking);

    expect($approvedBooking->status)->toBe(BookingStatus::APPROVED)
        ->and($approvedBooking->approved_at)->not->toBeNull()
        ->and($approvedBooking->expires_at)->not->toBeNull();
});

test('owner can reject booking request with mandatory reason', function () {
    $createAction = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);
    $booking = $createAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->addDays(5)->toDateString(), 1);

    $rejectAction = new RejectBookingRequestAction();
    $rejectedBooking = $rejectAction->execute($this->owner, $booking, 'Kamar sedang dalam masa renovasi interior');

    expect($rejectedBooking->status)->toBe(BookingStatus::REJECTED)
        ->and($rejectedBooking->rejected_at)->not->toBeNull()
        ->and($rejectedBooking->owner_rejection_reason)->toBe('Kamar sedang dalam masa renovasi interior');
});

test('tenant can cancel own pending booking request', function () {
    $createAction = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);
    $booking = $createAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->addDays(5)->toDateString(), 1);

    $cancelAction = new CancelBookingRequestAction();
    $cancelledBooking = $cancelAction->execute($this->tenant, $booking);

    expect($cancelledBooking->status)->toBe(BookingStatus::CANCELLED)
        ->and($cancelledBooking->cancelled_at)->not->toBeNull();
});

test('unauthorized tenant cannot cancel another tenants booking', function () {
    $createAction = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);
    $booking = $createAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->addDays(5)->toDateString(), 1);

    $cancelAction = new CancelBookingRequestAction();
    expect(fn () => $cancelAction->execute($this->otherTenant, $booking))
        ->toThrow(AuthorizationException::class);
});

test('unauthorized owner cannot approve or reject another owners property booking', function () {
    $createAction = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);
    $booking = $createAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->addDays(5)->toDateString(), 1);

    $approveAction = new ApproveBookingRequestAction($this->availabilityService);
    expect(fn () => $approveAction->execute($this->otherOwner, $booking))
        ->toThrow(AuthorizationException::class);
});

test('stale booking expiration action expires overdue bookings', function () {
    $createAction = new CreateBookingRequestAction($this->availabilityService, $this->priceCalculator);
    $booking = $createAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->addDays(5)->toDateString(), 1);

    // Artificially set expiration to past
    $booking->update(['expires_at' => now()->subHour()]);

    $expireAction = new ExpireStaleBookingsAction();
    $count = $expireAction->execute();

    expect($count)->toBe(1)
        ->and($booking->fresh()->status)->toBe(BookingStatus::EXPIRED);
});
