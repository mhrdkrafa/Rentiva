<?php

use App\Actions\Booking\CreateBookingRequestAction;
use App\Actions\Owner\CompleteRentalTenancyAction;
use App\Actions\Owner\UpdateUnitPricingAction;
use App\Actions\Tenant\CreateRentalTenancyAction;
use App\Actions\Tenant\ReportRentalIssueAction;
use App\Actions\Tenant\UpdateRentalIssueStatusAction;
use App\Enums\BillingPeriod;
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
    $this->otherOwner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();

    $this->location = Location::create(['name' => 'Kecamatan Mlati, Sleman', 'slug' => 'mlati-sleman']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Mahasiswa', 'slug' => 'kost-mahasiswa']);
    $this->roomType = RoomType::create(['name' => 'Single Bed', 'slug' => 'single-bed']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Pogung Asri Sleman',
        'slug' => 'kost-pogung-asri-sleman',
        'description' => 'Kost nyaman dekat kampus teknik UGM',
        'address' => 'Jl. Kaliurang KM 5 No. 45',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $this->unit1 = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar A1',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->unit2 = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar A2',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->pricePlan = PricePlan::create([
        'unit_id' => $this->unit1->id,
        'billing_period' => BillingPeriod::MONTHLY,
        'amount' => 1800000,
        'deposit_amount' => 500000,
        'is_active' => true,
    ]);
});

test('owner dashboard calculates occupancy rate and estimated monthly revenue correctly', function () {
    // 1. Initially 0% occupied and 0 revenue
    $response = $this->actingAs($this->owner)->get(route('owner.dashboard'));
    $response->assertOk()
        ->assertSee('0%')
        ->assertSee('Kost Pogung Asri Sleman');

    // 2. Add an active rental on unit1 (1.8jt)
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit1, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $createRentalAction->execute($booking);

    // 3. Now 1 out of 2 units occupied = 50% occupancy, 1.8jt revenue
    $responseAfter = $this->actingAs($this->owner)->get(route('owner.dashboard'));
    $responseAfter->assertOk()
        ->assertSee('50%')
        ->assertSee('Rp 1.800.000');
});

test('owner can update unit pricing plans with integer Rupiah amounts', function () {
    $action = new UpdateUnitPricingAction();

    $unit = $action->execute(
        $this->owner,
        $this->unit1,
        monthlyAmount: 2000000,
        depositAmount: 600000,
        dailyAmount: 150000,
        yearlyAmount: 22000000
    );

    expect($unit->pricePlans()->where('billing_period', BillingPeriod::MONTHLY)->first()->amount)->toBe(2000000)
        ->and($unit->pricePlans()->where('billing_period', BillingPeriod::MONTHLY)->first()->deposit_amount)->toBe(600000)
        ->and($unit->pricePlans()->where('billing_period', BillingPeriod::DAILY)->first()->amount)->toBe(150000)
        ->and($unit->pricePlans()->where('billing_period', BillingPeriod::YEARLY)->first()->amount)->toBe(22000000);
});

test('owner can view tenant directory and complete a rental tenancy check-out', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit1, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    // View directory
    $response = $this->actingAs($this->owner)->get(route('owner.tenants'));
    $response->assertOk()
        ->assertSee($this->tenant->name)
        ->assertSee($rental->code);

    // Check out / complete tenancy
    $completeAction = new CompleteRentalTenancyAction();
    $completed = $completeAction->execute($this->owner, $rental, 'Kamar ditinggalkan dalam kondisi bersih.');

    expect($completed->status)->toBe(RentalStatus::COMPLETED)
        ->and($this->unit1->fresh()->status)->toBe(UnitStatus::AVAILABLE);
});

test('owner can review and resolve maintenance issue tickets from tenants', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit1, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    $reportAction = new ReportRentalIssueAction();
    $issue = $reportAction->execute($this->tenant, $rental, 'Kran Air Patah', 'Kran kamar mandi patah dan air mengucur', 'urgent');

    // Owner views issues list
    $response = $this->actingAs($this->owner)->get(route('owner.issues.index'));
    $response->assertOk()
        ->assertSee('Kran Air Patah');

    // Owner resolves issue
    $updateAction = new UpdateRentalIssueStatusAction();
    $resolvedIssue = $updateAction->execute($this->owner, $issue, 'resolved', 'Sudah diganti kran baru oleh tukang ledeng.');

    expect($resolvedIssue->status)->toBe(IssueStatus::RESOLVED)
        ->and($resolvedIssue->owner_notes)->toBe('Sudah diganti kran baru oleh tukang ledeng.');
});

test('owner statistics page aggregates occupancy and revenue accurately', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit1, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $createRentalAction->execute($booking);

    $response = $this->actingAs($this->owner)->get(route('owner.statistics'));
    $response->assertOk()
        ->assertSee('Kost Pogung Asri Sleman')
        ->assertSee('Rp 1.800.000')
        ->assertSee('50%');
});

test('unauthorized owner cannot modify pricing or complete rental of another owner', function () {
    $createBookingAction = new CreateBookingRequestAction(new AvailabilityService(), new BookingPriceCalculator());
    $booking = $createBookingAction->execute($this->tenant, $this->unit1, $this->pricePlan, now()->toDateString(), 1);

    $createRentalAction = new CreateRentalTenancyAction();
    $rental = $createRentalAction->execute($booking);

    // Other owner attempts to update pricing
    $pricingAction = new UpdateUnitPricingAction();
    expect(fn () => $pricingAction->execute($this->otherOwner, $this->unit1, 3000000))
        ->toThrow(AuthorizationException::class);

    // Other owner attempts to complete rental
    $completeAction = new CompleteRentalTenancyAction();
    expect(fn () => $completeAction->execute($this->otherOwner, $rental))
        ->toThrow(AuthorizationException::class);
});
