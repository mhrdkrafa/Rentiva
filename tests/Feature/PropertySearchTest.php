<?php

use App\Actions\Property\CreatePropertyAction;
use App\Actions\Property\CreateUnitAction;
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
use App\Services\PropertySearchService;

beforeEach(function () {
    $this->locJogja = Location::create(['name' => 'Yogyakarta', 'slug' => 'yogyakarta']);
    $this->locBandung = Location::create(['name' => 'Bandung', 'slug' => 'bandung']);

    $this->typeKostPutri = PropertyType::create(['name' => 'Kost Putri', 'slug' => 'kost-putri']);
    $this->typeApartemen = PropertyType::create(['name' => 'Apartemen', 'slug' => 'apartemen']);

    $this->roomType = RoomType::create(['name' => 'Standard', 'slug' => 'standard']);

    $this->facWifi = Facility::create(['name' => 'WiFi Cepat', 'slug' => 'wifi', 'type' => 'general']);
    $this->facAc = Facility::create(['name' => 'AC', 'slug' => 'ac', 'type' => 'room']);

    $this->owner = User::factory()->owner()->create();

    // Property 1: Jogja, Kost Putri, 1.5jt, has WiFi & AC, available
    $this->prop1 = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->typeKostPutri->id,
        'location_id' => $this->locJogja->id,
        'name' => 'Kost Melati Pogung UGM',
        'slug' => 'kost-melati-pogung-ugm',
        'description' => 'Kost nyaman dekat kampus',
        'address' => 'Jl. Pogung Baru No. 10',
        'gender_policy' => GenderPolicy::FEMALE_ONLY,
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'featured' => true,
        'published_at' => now()->subDays(2),
    ]);
    $this->prop1->facilities()->attach([$this->facWifi->id, $this->facAc->id]);
    $unit1 = Unit::create([
        'property_id' => $this->prop1->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar 101',
        'status' => UnitStatus::AVAILABLE,
    ]);
    $unit1->pricePlans()->create([
        'billing_period' => BillingPeriod::MONTHLY,
        'amount' => 1500000,
        'is_active' => true,
    ]);

    // Property 2: Bandung, Apartemen, 3.5jt, has WiFi only, 100% occupied
    $this->prop2 = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->typeApartemen->id,
        'location_id' => $this->locBandung->id,
        'name' => 'Apartemen Grand Dago',
        'slug' => 'apartemen-grand-dago',
        'description' => 'Apartemen modern di Dago Bandung',
        'address' => 'Jl. Ir. H. Juanda No. 100',
        'gender_policy' => GenderPolicy::ALL,
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'featured' => false,
        'published_at' => now()->subDays(1),
    ]);
    $this->prop2->facilities()->attach([$this->facWifi->id]);
    $unit2 = Unit::create([
        'property_id' => $this->prop2->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Unit 12B',
        'status' => UnitStatus::OCCUPIED,
    ]);
    $unit2->pricePlans()->create([
        'billing_period' => BillingPeriod::MONTHLY,
        'amount' => 3500000,
        'is_active' => true,
    ]);

    $this->searchService = new PropertySearchService();
});

test('keyword search finds properties by name, description, address, or location', function () {
    $res1 = $this->searchService->search(['q' => 'Pogung']);
    expect($res1->total())->toBe(1)
        ->and($res1->first()->id)->toBe($this->prop1->id);

    $res2 = $this->searchService->search(['q' => 'Dago']);
    expect($res2->total())->toBe(1)
        ->and($res2->first()->id)->toBe($this->prop2->id);

    $res3 = $this->searchService->search(['q' => 'TidakAda']);
    expect($res3->total())->toBe(0);
});

test('location filter filters properties by location id', function () {
    $res = $this->searchService->search(['location_id' => $this->locJogja->id]);
    expect($res->total())->toBe(1)
        ->and($res->first()->id)->toBe($this->prop1->id);

    $resBandung = $this->searchService->search(['location_id' => $this->locBandung->id]);
    expect($resBandung->total())->toBe(1)
        ->and($resBandung->first()->id)->toBe($this->prop2->id);
});

test('price range filter matches active unit price plan amounts', function () {
    // Under 2 million (should only match Prop 1: 1.5jt)
    $resBudget = $this->searchService->search(['max_price' => 2000000]);
    expect($resBudget->total())->toBe(1)
        ->and($resBudget->first()->id)->toBe($this->prop1->id);

    // Over 2 million (should only match Prop 2: 3.5jt)
    $resPremium = $this->searchService->search(['min_price' => 2000000]);
    expect($resPremium->total())->toBe(1)
        ->and($resPremium->first()->id)->toBe($this->prop2->id);

    // Range between 1jt and 4jt (should match both)
    $resAll = $this->searchService->search(['min_price' => 1000000, 'max_price' => 4000000]);
    expect($resAll->total())->toBe(2);
});

test('property type and gender rules filters match accurately', function () {
    $resType = $this->searchService->search(['type_id' => $this->typeApartemen->id]);
    expect($resType->total())->toBe(1)
        ->and($resType->first()->id)->toBe($this->prop2->id);

    $resGender = $this->searchService->search(['gender' => 'female_only']);
    // Prop 1 (female only) + Prop 2 (all) both accommodate female
    expect($resGender->total())->toBe(2);
});

test('facility filter requires properties to have selected facilities', function () {
    // Both have WiFi
    $resWifi = $this->searchService->search(['facilities' => [$this->facWifi->id]]);
    expect($resWifi->total())->toBe(2);

    // Only Prop 1 has AC
    $resAc = $this->searchService->search(['facilities' => [$this->facAc->id]]);
    expect($resAc->total())->toBe(1)
        ->and($resAc->first()->id)->toBe($this->prop1->id);
});

test('availability filter hides fully occupied properties', function () {
    $res = $this->searchService->search(['available_only' => '1']);
    expect($res->total())->toBe(1)
        ->and($res->first()->id)->toBe($this->prop1->id);
});

test('sorting options work properly', function () {
    // Price low to high: Prop 1 (1.5jt) then Prop 2 (3.5jt)
    $resPriceLow = $this->searchService->search(['sort' => 'price_low']);
    expect($resPriceLow->first()->id)->toBe($this->prop1->id);

    // Price high to low: Prop 2 (3.5jt) then Prop 1 (1.5jt)
    $resPriceHigh = $this->searchService->search(['sort' => 'price_high']);
    expect($resPriceHigh->first()->id)->toBe($this->prop2->id);
});
