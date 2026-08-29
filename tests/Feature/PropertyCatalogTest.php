<?php

use App\Actions\Property\CreatePropertyAction;
use App\Actions\Property\CreateUnitAction;
use App\Actions\Property\SubmitPropertyForVerificationAction;
use App\Actions\Property\VerifyPropertyAction;
use App\Enums\BillingPeriod;
use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Policies\PropertyPolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->location = Location::create(['name' => 'Yogyakarta', 'slug' => 'yogyakarta']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Putri', 'slug' => 'kost-putri']);
    $this->roomType = RoomType::create(['name' => 'Deluxe Room', 'slug' => 'deluxe-room']);
    $this->facility = Facility::create(['name' => 'WiFi', 'slug' => 'wifi', 'type' => 'general']);
});

test('owner can create a new property listing with facilities and cover photo', function () {
    Storage::fake('public');

    $owner = User::factory()->owner()->create();
    $action = new CreatePropertyAction();
    $photo = UploadedFile::fake()->create('cover.jpg', 50, 'image/jpeg');

    $property = $action->execute(
        $owner,
        [
            'name' => 'Kost Cantik Pogung',
            'property_type_id' => $this->propertyType->id,
            'location_id' => $this->location->id,
            'description' => 'Kost putri asri dan strategis.',
            'address' => 'Jl. Kaliurang KM 5',
            'gender_policy' => 'female_only',
        ],
        [$photo],
        [$this->facility->id]
    );

    expect($property)->toBeInstanceOf(Property::class)
        ->and($property->owner_id)->toBe($owner->id)
        ->and($property->status)->toBe(PropertyStatus::DRAFT)
        ->and($property->verification_status)->toBe(VerificationStatus::UNVERIFIED)
        ->and($property->facilities()->count())->toBe(1)
        ->and($property->images()->count())->toBe(1)
        ->and($property->images()->first()->is_cover)->toBeTrue();
});

test('owner can add a room unit with price plan in integer minor units', function () {
    $owner = User::factory()->owner()->create();
    $property = Property::create([
        'owner_id' => $owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Griya Indah',
        'slug' => 'kost-griya-indah',
        'description' => 'Kost nyaman',
        'address' => 'Jl. Damai No. 1',
        'gender_policy' => GenderPolicy::ALL,
    ]);

    $action = new CreateUnitAction();
    $unit = $action->execute(
        $property,
        [
            'room_type_id' => $this->roomType->id,
            'name' => 'Kamar 101',
            'floor' => '1',
            'size' => '3x4 m',
            'capacity' => 1,
            'status' => UnitStatus::AVAILABLE,
        ],
        [
            [
                'billing_period' => BillingPeriod::MONTHLY->value,
                'amount' => 1500000,
                'deposit_amount' => 500000,
            ],
        ],
        [],
        [$this->facility->id]
    );

    expect($unit)->toBeInstanceOf(Unit::class)
        ->and($unit->name)->toBe('Kamar 101')
        ->and($unit->pricePlans()->count())->toBe(1)
        ->and($unit->pricePlans->first()->amount)->toBe(1500000)
        ->and($unit->monthly_price)->toBe(1500000)
        ->and($unit->formatted_monthly_price)->toBe('Rp 1.500.000');
});

test('property verification workflow transitions states correctly', function () {
    $owner = User::factory()->owner()->create();
    $property = Property::create([
        'owner_id' => $owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Pengajuan',
        'slug' => 'kost-pengajuan',
        'description' => 'Kost nyaman',
        'address' => 'Jl. Kaliurang No. 10',
        'gender_policy' => GenderPolicy::ALL,
        'verification_status' => VerificationStatus::UNVERIFIED,
        'status' => PropertyStatus::DRAFT,
    ]);

    // Add unit and image
    Unit::create([
        'property_id' => $property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar 01',
        'status' => UnitStatus::AVAILABLE,
    ]);
    $property->images()->create(['path' => 'properties/sample.jpg', 'is_cover' => true]);

    // 1. Submit for verification
    $submitAction = new SubmitPropertyForVerificationAction();
    $submitAction->execute($property);

    expect($property->fresh()->verification_status)->toBe(VerificationStatus::PENDING)
        ->and($property->fresh()->status)->toBe(PropertyStatus::PENDING_REVIEW);

    // 2. Admin approves verification
    $verifyAction = new VerifyPropertyAction();
    $verifyAction->approve($property);

    expect($property->fresh()->verification_status)->toBe(VerificationStatus::VERIFIED)
        ->and($property->fresh()->status)->toBe(PropertyStatus::PUBLISHED)
        ->and($property->fresh()->verified_at)->not->toBeNull();
});

test('property policy enforces owner isolation', function () {
    $ownerA = User::factory()->owner()->create();
    $ownerB = User::factory()->owner()->create();

    $propertyA = Property::create([
        'owner_id' => $ownerA->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Owner A',
        'slug' => 'kost-owner-a',
        'description' => 'Kost A',
        'address' => 'Alamat A',
        'gender_policy' => GenderPolicy::ALL,
    ]);

    $policy = new PropertyPolicy();

    expect($policy->update($ownerA, $propertyA))->toBeTrue();
    expect($policy->update($ownerB, $propertyA))->toBeFalse();
});

test('public marketplace catalog and detail routes render successfully', function () {
    $owner = User::factory()->owner()->create();
    $property = Property::create([
        'owner_id' => $owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Publik Terverifikasi',
        'slug' => 'kost-publik-terverifikasi',
        'description' => 'Kost siap huni untuk umum',
        'address' => 'Jl. Gejayan No. 20',
        'gender_policy' => GenderPolicy::ALL,
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    // Public catalog list
    $response = $this->get(route('properties.index'));
    $response->assertOk();
    $response->assertSee('Kost Publik Terverifikasi');

    // Public detail page
    $detailResponse = $this->get(route('properties.show', $property->slug));
    $detailResponse->assertOk();
    $detailResponse->assertSee('Kost Publik Terverifikasi');
});
