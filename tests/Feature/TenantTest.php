<?php

use App\Actions\Booking\CreateBookingRequestAction;
use App\Actions\Tenant\CreateRentalTenancyAction;
use App\Actions\Tenant\ReportRentalIssueAction;
use App\Actions\Tenant\ToggleFavoriteAction;
use App\Actions\Tenant\UpdateRentalIssueStatusAction;
use App\Enums\BillingPeriod;
use App\Enums\BookingStatus;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\PropertyStatus;
use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\Location;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Rental;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingPriceCalculator;
use Illuminate\Auth\Access\AuthorizationException;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();
    $this->otherTenant = User::factory()->tenant()->create();

    $this->location = Location::create(['name' => 'Depok, Sleman', 'slug' => 'depok-sleman']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Eksklusif', 'slug' => 'kost-eksklusif']);
    $this->roomType = RoomType::create(['name' => 'Deluxe', 'slug' => 'deluxe']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Caturtunggal Sleman',
        'slug' => 'kost-caturtunggal-sleman',
        'description' => 'Kost mewah fasilitas lengkap dekat kampus',
        'address' => 'Jl. Babarsari No. 12',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $this->unit = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar 201',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->pricePlan = PricePlan::create([
        'unit_id' => $this->unit->id,
        'billing_period' => BillingPeriod::MONTHLY,
        'amount' => 2500000,
        'deposit_amount' => 500000,
        'is_active' => true,
    ]);
});

test('tenant can toggle property favorites and view favorites page', function () {
    $toggleAction = new ToggleFavoriteAction();

    // 1. Add to favorite
    $added = $toggleAction->execute($this->tenant, $this->property);
    expect($added)->toBeTrue()
        ->and($this->tenant->favorites()->count())->toBe(1);

    // 2. View favorites page
    $response = $this->actingAs($this->tenant)->get(route('tenant.favorites'));
    $response->assertOk()
        ->assertSee('Kost Caturtunggal Sleman');

    // 3. Remove from favorite
    $removed = $toggleAction->execute($this->tenant, $this->property);
    expect($removed)->toBeFalse()
        ->and($this->tenant->favorites()->count())->toBe(0);
});

test('rental tenancy is created from confirmed booking with integer money values', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->toDateString(), 3);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    expect($rental)->toBeInstanceOf(Rental::class)
        ->and($rental->code)->toStartWith('RNT-')
        ->and($rental->tenant_id)->toBe($this->tenant->id)
        ->and($rental->unit_id)->toBe($this->unit->id)
        ->and($rental->monthly_rent)->toBe(2500000)
        ->and($rental->deposit_held)->toBe(500000)
        ->and($rental->status)->toBe(RentalStatus::ACTIVE)
        ->and($this->unit->fresh()->status)->toBe(UnitStatus::OCCUPIED)
        ->and($booking->fresh()->status)->toBe(BookingStatus::CONFIRMED);
});

test('tenant dashboard renders active rental and user statistics', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    $response = $this->actingAs($this->tenant)->get(route('tenant.dashboard'));
    $response->assertOk()
        ->assertSee('Halo, ' . $this->tenant->name)
        ->assertSee($rental->code)
        ->assertSee('Kost Caturtunggal Sleman');
});

test('tenant can view lease agreement and printable digital receipt', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    // Lease agreement detail
    $detailResponse = $this->actingAs($this->tenant)->get(route('tenant.rentals.show', $rental));
    $detailResponse->assertOk()
        ->assertSee($rental->code)
        ->assertSee('Rp 2.500.000');

    // Printable receipt
    $receiptResponse = $this->actingAs($this->tenant)->get(route('tenant.rentals.receipt', $rental));
    $receiptResponse->assertOk()
        ->assertSee('Bukti Kuitansi Sewa')
        ->assertSee($rental->code)
        ->assertSee('LUNAS / TERVERIFIKASI');
});

test('tenant can report maintenance issue and owner can resolve it', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    // Tenant reports issue
    $reportAction = new ReportRentalIssueAction();
    $issue = $reportAction->execute(
        $this->tenant,
        $rental,
        'AC Tidak Dingin',
        'AC hanya mengeluarkan angin biasa, mohon dibersihkan',
        'medium'
    );

    expect($issue->status)->toBe(IssueStatus::REPORTED)
        ->and($issue->priority)->toBe(IssuePriority::MEDIUM);

    // Owner resolves issue
    $updateAction = new UpdateRentalIssueStatusAction();
    $resolvedIssue = $updateAction->execute($this->owner, $issue, 'resolved', 'Teknisi sudah datang dan mengisi freon AC.');

    expect($resolvedIssue->status)->toBe(IssueStatus::RESOLVED)
        ->and($resolvedIssue->resolved_at)->not->toBeNull()
        ->and($resolvedIssue->owner_notes)->toBe('Teknisi sudah datang dan mengisi freon AC.');
});

test('unauthorized tenant cannot access another tenants rental or report issue on it', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    // Other tenant attempts to view receipt -> 403
    $this->actingAs($this->otherTenant)
        ->get(route('tenant.rentals.receipt', $rental))
        ->assertForbidden();

    // Other tenant attempts to file issue -> throws AuthorizationException
    $reportAction = new ReportRentalIssueAction();
    expect(fn () => $reportAction->execute($this->otherTenant, $rental, 'Judul', 'Deskripsi'))
        ->toThrow(AuthorizationException::class);
});
