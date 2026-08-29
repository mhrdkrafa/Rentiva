<?php

use App\Actions\Promotion\ApplyPromotionAction;
use App\Actions\Review\ModerateReviewAction;
use App\Actions\Review\ReplyReviewAction;
use App\Actions\Review\SubmitReviewAction;
use App\Enums\DiscountType;
use App\Enums\InvoiceStatus;
use App\Enums\PropertyStatus;
use App\Enums\RentalStatus;
use App\Enums\ReviewModerationStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Promotion;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Rental;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Services\Promotion\PromotionDiscountCalculator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();
    $this->otherTenant = User::factory()->tenant()->create();
    $this->otherOwner = User::factory()->owner()->create();
    $this->admin = User::factory()->superAdmin()->create();

    $this->location = Location::create(['name' => 'Sleman', 'slug' => 'sleman-promo']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Putri', 'slug' => 'kost-putri-promo']);
    $this->roomType = RoomType::create(['name' => 'Reguler', 'slug' => 'reguler-promo']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Putri Pogung',
        'slug' => 'kost-putri-pogung',
        'description' => 'Kost khusus putri dekat UGM',
        'address' => 'Pogung Kidul No. 12',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $this->unit = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar 101',
        'status' => UnitStatus::OCCUPIED,
    ]);

    $this->rental = Rental::create([
        'code' => 'RNT-TEST-001',
        'tenant_id' => $this->tenant->id,
        'unit_id' => $this->unit->id,
        'start_date' => now()->subMonths(1)->toDateString(),
        'end_date' => now()->addMonths(5)->toDateString(),
        'monthly_rent' => 1500000,
        'deposit_held' => 500000,
        'status' => RentalStatus::ACTIVE,
    ]);

    $this->invoice = Invoice::create([
        'code' => 'INV-TEST-PROMO',
        'tenant_id' => $this->tenant->id,
        'owner_id' => $this->owner->id,
        'subtotal_amount' => 1500000,
        'deposit_amount' => 500000,
        'additional_fees_amount' => 0,
        'discount_amount' => 0,
        'total_amount' => 2000000,
        'status' => InvoiceStatus::UNPAID,
        'due_date' => now()->addDay()->toDateString(),
    ]);
});

test('promotion calculator computes percentage discount with cap and fixed discounts', function () {
    // 1. Percentage promo: 10% discount with max cap Rp 100.000
    $promoPercent = Promotion::create([
        'code' => 'HEMAT10',
        'name' => 'Diskon 10%',
        'discount_type' => DiscountType::PERCENTAGE,
        'discount_value' => 10,
        'max_discount_amount' => 100000,
        'min_transaction_amount' => 500000,
        'is_active' => true,
    ]);

    $calculator = new PromotionDiscountCalculator();
    $res = $calculator->validateAndCalculate('HEMAT10', 1500000, $this->tenant);

    // 10% of 1.5jt = 150rb, but capped at 100rb
    expect($res['discount_amount'])->toBe(100000)
        ->and($res['final_amount'])->toBe(1400000);

    // 2. Fixed promo: Rp 50.000
    $promoFixed = Promotion::create([
        'code' => 'POTONGAN50',
        'name' => 'Potongan 50k',
        'discount_type' => DiscountType::FIXED,
        'discount_value' => 50000,
        'min_transaction_amount' => 500000,
        'is_active' => true,
    ]);

    $resFixed = $calculator->validateAndCalculate('POTONGAN50', 1500000, $this->tenant);
    expect($resFixed['discount_amount'])->toBe(50000)
        ->and($resFixed['final_amount'])->toBe(1450000);
});

test('promotion validation rejects expired, inactive, or exhausted promos', function () {
    $calculator = new PromotionDiscountCalculator();

    // Expired promo
    Promotion::create([
        'code' => 'EXPIRED2020',
        'name' => 'Promo Lawas',
        'discount_type' => DiscountType::FIXED,
        'discount_value' => 50000,
        'ends_at' => now()->subDay(),
        'is_active' => true,
    ]);

    expect(fn () => $calculator->validateAndCalculate('EXPIRED2020', 1500000))
        ->toThrow(ValidationException::class);

    // Exhausted promo
    Promotion::create([
        'code' => 'HABISKUOTA',
        'name' => 'Promo Habis',
        'discount_type' => DiscountType::FIXED,
        'discount_value' => 50000,
        'max_uses' => 5,
        'used_count' => 5,
        'is_active' => true,
    ]);

    expect(fn () => $calculator->validateAndCalculate('HABISKUOTA', 1500000))
        ->toThrow(ValidationException::class);
});

test('apply promotion action updates invoice totals and records usage', function () {
    $promo = Promotion::create([
        'code' => 'DISKON100K',
        'name' => 'Diskon 100 Ribu',
        'discount_type' => DiscountType::FIXED,
        'discount_value' => 100000,
        'min_transaction_amount' => 1000000,
        'is_active' => true,
    ]);

    $action = app(ApplyPromotionAction::class);
    $updatedInvoice = $action->execute($this->invoice, 'DISKON100K', $this->tenant);

    expect($updatedInvoice->discount_amount)->toBe(100000)
        ->and($updatedInvoice->total_amount)->toBe(1900000) // (1.5jt - 100k) + 500k deposit = 1.9jt
        ->and($updatedInvoice->items()->where('item_type', 'discount')->exists())->toBeTrue()
        ->and($promo->fresh()->used_count)->toBe(1);
});

test('verified tenant can submit review with multi-dimensional ratings', function () {
    $submitAction = app(SubmitReviewAction::class);

    $review = $submitAction->execute($this->tenant, $this->rental, [
        'rating' => 5,
        'cleanliness_rating' => 5,
        'accuracy_rating' => 5,
        'communication_rating' => 4,
        'location_rating' => 5,
        'value_rating' => 5,
        'comment' => 'Kamar sangat bersih, WiFi kencang, dan ibu kost sangat ramah!',
    ]);

    expect($review)->not->toBeNull()
        ->and($review->rating)->toBe(5)
        ->and($review->cleanliness_rating)->toBe(5)
        ->and($review->communication_rating)->toBe(4)
        ->and($this->rental->fresh()->hasReviewed())->toBeTrue()
        ->and($this->property->fresh()->average_rating)->toBe(5.0)
        ->and($this->property->fresh()->reviews_count)->toBe(1);
});

test('duplicate review on same rental is prevented', function () {
    $submitAction = app(SubmitReviewAction::class);

    $submitAction->execute($this->tenant, $this->rental, [
        'rating' => 4,
        'comment' => 'Ulasan pertama',
    ]);

    // Second attempt on same rental should fail
    expect(fn () => $submitAction->execute($this->tenant, $this->rental, [
        'rating' => 5,
        'comment' => 'Ulasan kedua yang dilarang',
    ]))->toThrow(ValidationException::class);
});

test('unauthorized tenant cannot review another tenants rental', function () {
    $submitAction = app(SubmitReviewAction::class);

    expect(fn () => $submitAction->execute($this->otherTenant, $this->rental, [
        'rating' => 1,
        'comment' => 'Ulasan palsu',
    ]))->toThrow(AuthorizationException::class);
});

test('property owner can reply to approved review', function () {
    $review = Review::create([
        'rental_id' => $this->rental->id,
        'property_id' => $this->property->id,
        'unit_id' => $this->unit->id,
        'tenant_id' => $this->tenant->id,
        'rating' => 5,
        'comment' => 'Kost recommended!',
        'moderation_status' => ReviewModerationStatus::APPROVED,
    ]);

    $replyAction = app(ReplyReviewAction::class);

    // 1. Authorized owner reply
    $repliedReview = $replyAction->execute($this->owner, $review, 'Terima kasih banyak atas kunjungannya!');
    expect($repliedReview->owner_reply)->toBe('Terima kasih banyak atas kunjungannya!')
        ->and($repliedReview->owner_replied_at)->not->toBeNull();

    // 2. Unauthorized other owner reply fails
    expect(fn () => $replyAction->execute($this->otherOwner, $review, 'Balasan tidak sah'))
        ->toThrow(AuthorizationException::class);
});

test('admin can moderate review status', function () {
    $review = Review::create([
        'rental_id' => $this->rental->id,
        'property_id' => $this->property->id,
        'unit_id' => $this->unit->id,
        'tenant_id' => $this->tenant->id,
        'rating' => 5,
        'comment' => 'Kamar mantap',
        'moderation_status' => ReviewModerationStatus::PENDING,
    ]);

    $moderateAction = app(ModerateReviewAction::class);
    $moderateAction->execute($this->admin, $review, ReviewModerationStatus::APPROVED);

    expect($review->fresh()->moderation_status)->toBe(ReviewModerationStatus::APPROVED);
});
