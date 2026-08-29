<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Marketplace\PropertyController as PublicPropertyController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\Owner\AvailabilityController as OwnerAvailabilityController;
use App\Http\Controllers\Owner\BookingController as OwnerBookingController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\FinanceController as OwnerFinanceController;
use App\Http\Controllers\Owner\IssueController as OwnerIssueController;
use App\Http\Controllers\Owner\PricingController as OwnerPricingController;
use App\Http\Controllers\Owner\ProfileController as OwnerProfileController;
use App\Http\Controllers\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Owner\PropertyManagerController;
use App\Http\Controllers\Owner\ReviewController as OwnerReviewController;
use App\Http\Controllers\Owner\StatisticsController as OwnerStatisticsController;
use App\Http\Controllers\Owner\TenantDirectoryController as OwnerTenantDirectoryController;
use App\Http\Controllers\Owner\UnitController as OwnerUnitController;
use App\Http\Controllers\Tenant\BookingController as TenantBookingController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\FavoriteController as TenantFavoriteController;
use App\Http\Controllers\Tenant\InvoiceController as TenantInvoiceController;
use App\Http\Controllers\Tenant\IssueController as TenantIssueController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;
use App\Http\Controllers\Tenant\RentalController as TenantRentalController;
use App\Http\Controllers\Tenant\ReviewController as TenantReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Dynamic XML Sitemap & Robots.txt
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

// Public Marketplace Catalog Routes
Route::get('/properties', [PublicPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{slug}', [PublicPropertyController::class, 'show'])->name('properties.show');
Route::get('/search', [PublicPropertyController::class, 'index'])->name('search');

// Public Promotions & Deals
Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');

// Informational & Policy Pages
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Educational Articles & Guides
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// In-Platform Messaging Routes
Route::prefix('messages')->name('messages.')->middleware('auth')->group(function () {
    Route::get('/', [MessagingController::class, 'index'])->name('index');
    Route::post('/start', [MessagingController::class, 'start'])->name('start');
    Route::get('/{conversation}', [MessagingController::class, 'show'])->name('show');
    Route::post('/{conversation}/send', [MessagingController::class, 'send'])->name('send');
});

// Tenant Routes (Protected for Tenant Role)
Route::prefix('tenant')->name('tenant.')->middleware(['auth', 'role.tenant'])->group(function () {
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [TenantProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [TenantProfileController::class, 'update'])->name('profile.update');

    // Favorites Wishlist
    Route::get('/favorites', [TenantFavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/{property}/toggle', [TenantFavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Booking requests
    Route::get('/bookings', [TenantBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [TenantBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [TenantBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [TenantBookingController::class, 'cancel'])->name('bookings.cancel');

    // Invoices & Payments
    Route::get('/invoices', [TenantInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [TenantInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/checkout', [TenantInvoiceController::class, 'checkout'])->name('invoices.checkout');
    Route::post('/invoices/{invoice}/checkout', [TenantInvoiceController::class, 'processCheckout'])->name('invoices.process-checkout');

    // Rental Tenancies & Digital Receipts
    Route::get('/rentals', [TenantRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental}', [TenantRentalController::class, 'show'])->name('rentals.show');
    Route::get('/rentals/{rental}/receipt', [TenantRentalController::class, 'receipt'])->name('rentals.receipt');
    Route::get('/rentals/{rental}/review', [TenantReviewController::class, 'create'])->name('reviews.create');
    Route::post('/rentals/{rental}/review', [TenantReviewController::class, 'store'])->name('reviews.store');

    // Maintenance / Issue Reporting
    Route::get('/issues', [TenantIssueController::class, 'index'])->name('issues.index');
    Route::get('/issues/create', [TenantIssueController::class, 'create'])->name('issues.create');
    Route::post('/issues', [TenantIssueController::class, 'store'])->name('issues.store');
    Route::get('/issues/{issue}', [TenantIssueController::class, 'show'])->name('issues.show');

    // Messaging Shortcut
    Route::get('/messages', [MessagingController::class, 'index'])->name('messages');
});

// Owner Routes (Protected for Owner & Property Manager Roles)
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role.owner'])->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [OwnerProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [OwnerProfileController::class, 'update'])->name('profile.update');

    Route::get('/managers', [PropertyManagerController::class, 'index'])->name('managers');
    Route::post('/managers', [PropertyManagerController::class, 'store'])->name('managers.store');
    Route::delete('/managers/{assignment}', [PropertyManagerController::class, 'destroy'])->name('managers.destroy');

    // Properties Management
    Route::get('/properties', [OwnerPropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [OwnerPropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [OwnerPropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [OwnerPropertyController::class, 'show'])->name('properties.show');
    Route::post('/properties/{property}/submit-verification', [OwnerPropertyController::class, 'submitVerification'])->name('properties.submit-verification');

    // Units & Pricing Management
    Route::get('/properties/{property}/units/create', [OwnerUnitController::class, 'create'])->name('units.create');
    Route::post('/properties/{property}/units', [OwnerUnitController::class, 'store'])->name('units.store');
    Route::delete('/units/{unit}', [OwnerUnitController::class, 'destroy'])->name('units.destroy');
    Route::get('/units/{unit}/pricing', [OwnerPricingController::class, 'edit'])->name('units.pricing.edit');
    Route::post('/units/{unit}/pricing', [OwnerPricingController::class, 'update'])->name('units.pricing.update');

    // Incoming Booking Requests & Approvals
    Route::get('/bookings', [OwnerBookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/{booking}', [OwnerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/approve', [OwnerBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [OwnerBookingController::class, 'reject'])->name('bookings.reject');

    // Calendar Availability & Hold Blocks
    Route::get('/availability', [OwnerAvailabilityController::class, 'index'])->name('availability');
    Route::post('/availability', [OwnerAvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{block}', [OwnerAvailabilityController::class, 'destroy'])->name('availability.destroy');

    // Tenant Directory
    Route::get('/tenants', [OwnerTenantDirectoryController::class, 'index'])->name('tenants');
    Route::post('/tenants/{rental}/complete', [OwnerTenantDirectoryController::class, 'complete'])->name('tenants.complete');

    // Issues & Maintenance
    Route::get('/issues', [OwnerIssueController::class, 'index'])->name('issues.index');
    Route::get('/issues/{issue}', [OwnerIssueController::class, 'show'])->name('issues.show');
    Route::put('/issues/{issue}', [OwnerIssueController::class, 'update'])->name('issues.update');

    // Finance & Income Ledger
    Route::get('/finance', [OwnerFinanceController::class, 'index'])->name('finance');

    // Reviews & Ratings
    Route::get('/reviews', [OwnerReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/reply', [OwnerReviewController::class, 'reply'])->name('reviews.reply');

    // Statistics & Performance
    Route::get('/statistics', [OwnerStatisticsController::class, 'index'])->name('statistics');

    // Messaging Shortcut
    Route::get('/messages', [MessagingController::class, 'index'])->name('messages');
});
