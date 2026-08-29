<?php

use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Jobs\ProcessUploadedImageJob;
use App\Jobs\SendBookingNotificationJob;
use App\Models\Article;
use App\Models\BookingRequest;
use App\Models\Location;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Services\CmsService;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();

    $this->location = Location::create(['name' => 'Sleman', 'slug' => 'sleman-seo']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Putri', 'slug' => 'kost-putri-seo']);
    $this->roomType = RoomType::create(['name' => 'Standard', 'slug' => 'standard-seo']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Putri Asri Sleman',
        'slug' => 'kost-putri-asri-sleman',
        'description' => 'Kost putri asri dan nyaman dekat kampus',
        'address' => 'Jl. Kaliurang KM 6',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $this->unit = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar A1',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->pricePlan = PricePlan::create([
        'unit_id' => $this->unit->id,
        'duration_type' => 'monthly',
        'amount' => 1200000,
        'is_active' => true,
    ]);

    $this->article = Article::create([
        'author_id' => $this->owner->id,
        'title' => 'Tips Memilih Kost Aman di Sleman',
        'slug' => 'tips-memilih-kost-aman-di-sleman',
        'excerpt' => 'Panduan mencari kost aman dan nyaman di kawasan Sleman.',
        'body' => 'Isi artikel lengkap mengenai kost aman.',
        'category' => 'tips',
        'is_published' => true,
        'published_at' => now()->subDays(1),
    ]);
});

test('sitemap xml endpoint returns valid xml with properties and articles urls', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<urlset', false)
        ->assertSee(url('/'), false)
        ->assertSee(route('properties.index'), false)
        ->assertSee(route('properties.show', $this->property->slug), false)
        ->assertSee(route('articles.show', $this->article->slug), false);
});

test('robots txt endpoint returns valid crawler rules and points to sitemap', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('User-agent: *')
        ->assertSee('Disallow: /admin/')
        ->assertSee('Disallow: /tenant/')
        ->assertSee('Disallow: /owner/')
        ->assertSee('Allow: /properties/')
        ->assertSee('Sitemap: ' . url('/sitemap.xml'));
});

test('property page generates valid schema org lodging business structured data', function () {
    $response = $this->get(route('properties.show', $this->property->slug));

    $response->assertOk()
        ->assertSee('application/ld+json', false)
        ->assertSee('LodgingBusiness', false)
        ->assertSee('Kost Putri Asri Sleman')
        ->assertSee('og:title', false)
        ->assertSee('twitter:card', false);
});

test('article page generates valid schema org article structured data', function () {
    $response = $this->get(route('articles.show', $this->article->slug));

    $response->assertOk()
        ->assertSee('application/ld+json', false)
        ->assertSee('Article', false)
        ->assertSee('Tips Memilih Kost Aman di Sleman')
        ->assertSee('og:type', false);
});

test('background queued jobs can be dispatched for images and booking notifications', function () {
    Queue::fake();

    ProcessUploadedImageJob::dispatch('properties/test.jpg');
    Queue::assertPushed(ProcessUploadedImageJob::class);

    $booking = BookingRequest::create([
        'code' => 'BOOK-QUEUE-001',
        'unit_id' => $this->unit->id,
        'tenant_id' => $this->tenant->id,
        'price_plan_id' => $this->pricePlan->id,
        'check_in_date' => now()->addDays(2)->toDateString(),
        'check_out_date' => now()->addDays(2)->addMonths(1)->toDateString(),
        'duration_months' => 1,
        'base_amount' => 1200000,
        'deposit_amount' => 0,
        'additional_fees_amount' => 0,
        'total_amount' => 1200000,
        'status' => \App\Enums\BookingStatus::PENDING_APPROVAL,
    ]);

    SendBookingNotificationJob::dispatch($booking, 'created');
    Queue::assertPushed(SendBookingNotificationJob::class);
});

test('cms cache invalidation purges cached content', function () {
    $cmsService = app(CmsService::class);

    // Warm up cache
    $cmsService->homepageSections();
    $cmsService->featuredProperties();

    expect(Cache::has('cms_homepage_sections'))->toBeTrue();

    // Clear cache
    $cmsService->clearCache();
    expect(Cache::has('cms_homepage_sections'))->toBeFalse();
});
