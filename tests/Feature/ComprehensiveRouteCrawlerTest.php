<?php

use App\Enums\InvoiceStatus;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\PropertyStatus;
use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\Article;
use App\Models\BookingRequest;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Rental;
use App\Models\RentalIssue;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create(['name' => 'Pemilik Kost Utama']);
    $this->tenant = User::factory()->tenant()->create(['name' => 'Penyewa Utama']);

    $this->location = Location::create(['name' => 'Yogyakarta', 'slug' => 'yogyakarta-crawler', 'is_active' => true]);
    $this->propertyType = PropertyType::create(['name' => 'Kost Putri', 'slug' => 'kost-putri-crawler', 'is_active' => true]);
    $this->roomType = RoomType::create(['name' => 'Deluxe', 'slug' => 'deluxe-crawler', 'is_active' => true]);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Putri Cantika',
        'slug' => 'kost-putri-cantika',
        'description' => 'Kost eksklusif dekat kampus',
        'address' => 'Jl. Kaliurang KM 5',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'featured' => true,
        'published_at' => now(),
    ]);

    $this->unit = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar 01',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->pricePlan = PricePlan::create([
        'unit_id' => $this->unit->id,
        'duration_type' => 'monthly',
        'amount' => 1500000,
        'is_active' => true,
    ]);

    $this->booking = BookingRequest::create([
        'code' => 'BOOK-CRAWL-001',
        'unit_id' => $this->unit->id,
        'tenant_id' => $this->tenant->id,
        'price_plan_id' => $this->pricePlan->id,
        'check_in_date' => now()->addDays(2)->toDateString(),
        'check_out_date' => now()->addDays(2)->addMonths(1)->toDateString(),
        'duration_months' => 1,
        'base_amount' => 1500000,
        'deposit_amount' => 500000,
        'additional_fees_amount' => 0,
        'total_amount' => 2000000,
        'status' => \App\Enums\BookingStatus::PENDING_APPROVAL,
    ]);

    $this->invoice = Invoice::create([
        'code' => 'INV-CRAWL-001',
        'booking_request_id' => $this->booking->id,
        'tenant_id' => $this->tenant->id,
        'owner_id' => $this->owner->id,
        'subtotal_amount' => 1500000,
        'deposit_amount' => 500000,
        'additional_fees_amount' => 0,
        'total_amount' => 2000000,
        'status' => InvoiceStatus::UNPAID,
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    $this->rental = Rental::create([
        'code' => 'RNT-CRAWL-001',
        'booking_request_id' => $this->booking->id,
        'tenant_id' => $this->tenant->id,
        'unit_id' => $this->unit->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(6)->toDateString(),
        'monthly_rent' => 1500000,
        'deposit_held' => 500000,
        'status' => RentalStatus::ACTIVE,
    ]);

    $this->issue = RentalIssue::create([
        'rental_id' => $this->rental->id,
        'tenant_id' => $this->tenant->id,
        'title' => 'Lampu Kamar Mandi Redup',
        'description' => 'Perlu diganti dengan lampu LED baru',
        'priority' => IssuePriority::MEDIUM,
        'status' => IssueStatus::REPORTED,
    ]);

    $this->article = Article::create([
        'author_id' => $this->owner->id,
        'title' => 'Panduan Memilih Kost Mahasiswa',
        'slug' => 'panduan-memilih-kost-mahasiswa',
        'excerpt' => 'Tips mencari hunian sewa nyaman.',
        'body' => 'Isi panduan lengkap.',
        'category' => 'tips',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->conversation = Conversation::create([
        'property_id' => $this->property->id,
    ]);
    $this->conversation->participants()->attach([$this->tenant->id, $this->owner->id]);
});

test('public routes render successfully without 500 errors', function () {
    $publicUrls = [
        '/',
        '/properties',
        '/properties/' . $this->property->slug,
        '/search',
        '/search?q=Cantika',
        '/search?city=yogyakarta-crawler',
        '/articles',
        '/articles/' . $this->article->slug,
        '/promotions',
        '/faq',
        '/terms',
        '/privacy',
        '/contact',
        '/login',
        '/register',
        '/sitemap.xml',
        '/robots.txt',
    ];

    foreach ($publicUrls as $url) {
        $response = $this->get($url);
        $response->assertOk();
    }
});

test('guest can register as tenant and login successfully', function () {
    // 1. Register as tenant
    $registerResponse = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@example.com',
        'phone' => '081298765432',
        'role' => 'tenant',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $registerResponse->assertRedirect(route('tenant.dashboard'));
    $this->assertAuthenticated();

    // 2. Logout
    $logoutResponse = $this->post('/logout');
    $logoutResponse->assertRedirect('/');
    $this->assertGuest();

    // 3. Login
    $loginResponse = $this->post('/login', [
        'email' => 'budi.santoso@example.com',
        'password' => 'Password123!',
    ]);
    $loginResponse->assertRedirect(route('tenant.dashboard'));
    $this->assertAuthenticated();
});

test('tenant routes render successfully for authenticated tenant', function () {
    $tenantUrls = [
        '/tenant/dashboard',
        '/tenant/profile',
        '/tenant/favorites',
        '/tenant/bookings',
        '/tenant/bookings/' . $this->booking->id,
        '/tenant/invoices',
        '/tenant/invoices/' . $this->invoice->id,
        '/tenant/invoices/' . $this->invoice->id . '/checkout',
        '/tenant/rentals',
        '/tenant/rentals/' . $this->rental->id,
        '/tenant/rentals/' . $this->rental->id . '/receipt',
        '/tenant/rentals/' . $this->rental->id . '/review',
        '/tenant/issues',
        '/tenant/issues/create',
        '/tenant/issues/' . $this->issue->id,
        '/tenant/messages',
    ];

    foreach ($tenantUrls as $url) {
        $response = $this->actingAs($this->tenant)->get($url);
        $response->assertOk();
    }
});

test('owner routes render successfully for authenticated property owner', function () {
    $ownerUrls = [
        '/owner/dashboard',
        '/owner/profile',
        '/owner/managers',
        '/owner/properties',
        '/owner/properties/create',
        '/owner/properties/' . $this->property->id,
        '/owner/properties/' . $this->property->id . '/units/create',
        '/owner/units/' . $this->unit->id . '/pricing',
        '/owner/bookings',
        '/owner/bookings/' . $this->booking->id,
        '/owner/availability',
        '/owner/tenants',
        '/owner/issues',
        '/owner/issues/' . $this->issue->id,
        '/owner/finance',
        '/owner/reviews',
        '/owner/statistics',
        '/owner/messages',
    ];

    foreach ($ownerUrls as $url) {
        $response = $this->actingAs($this->owner)->get($url);
        $response->assertOk();
    }
});

test('messaging routes render stream for participant', function () {
    $response = $this->actingAs($this->tenant)->get('/messages/' . $this->conversation->id);
    $response->assertRedirect(route('messages.index', ['conversation' => $this->conversation->id]));

    $streamResponse = $this->actingAs($this->tenant)->get('/messages?conversation=' . $this->conversation->id);
    $streamResponse->assertOk();
});

test('role based middleware enforces strict access isolation', function () {
    // 1. Guest blocked from tenant & owner dashboards
    $this->get('/tenant/dashboard')->assertRedirect(route('login'));
    $this->get('/owner/dashboard')->assertRedirect(route('login'));

    // 2. Tenant blocked from owner portal and redirected to tenant dashboard
    $this->actingAs($this->tenant)
        ->get('/owner/dashboard')
        ->assertRedirect(route('tenant.dashboard'));

    // 3. Owner redirected when accessing tenant dashboard
    $this->actingAs($this->owner)
        ->get('/tenant/dashboard')
        ->assertRedirect(route('owner.dashboard'));
});
