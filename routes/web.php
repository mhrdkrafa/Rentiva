<?php

use App\Http\Controllers\Marketplace\PropertyController as PublicPropertyController;
use App\Http\Controllers\Owner\AvailabilityController as OwnerAvailabilityController;
use App\Http\Controllers\Owner\BookingController as OwnerBookingController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\IssueController as OwnerIssueController;
use App\Http\Controllers\Owner\PricingController as OwnerPricingController;
use App\Http\Controllers\Owner\ProfileController as OwnerProfileController;
use App\Http\Controllers\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Owner\PropertyManagerController;
use App\Http\Controllers\Owner\StatisticsController as OwnerStatisticsController;
use App\Http\Controllers\Owner\TenantDirectoryController as OwnerTenantDirectoryController;
use App\Http\Controllers\Owner\UnitController as OwnerUnitController;
use App\Http\Controllers\Tenant\BookingController as TenantBookingController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\FavoriteController as TenantFavoriteController;
use App\Http\Controllers\Tenant\IssueController as TenantIssueController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;
use App\Http\Controllers\Tenant\RentalController as TenantRentalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Marketplace Catalog Routes
Route::get('/properties', [PublicPropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{slug}', [PublicPropertyController::class, 'show'])->name('properties.show');
Route::get('/search', [PublicPropertyController::class, 'index'])->name('search');

// Tenant Routes
Route::prefix('tenant')->name('tenant.')->group(function () {
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

    // Rental Tenancies & Digital Receipts
    Route::get('/rentals', [TenantRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental}', [TenantRentalController::class, 'show'])->name('rentals.show');
    Route::get('/rentals/{rental}/receipt', [TenantRentalController::class, 'receipt'])->name('rentals.receipt');

    // Maintenance / Issue Reporting
    Route::get('/issues', [TenantIssueController::class, 'index'])->name('issues.index');
    Route::get('/issues/create', [TenantIssueController::class, 'create'])->name('issues.create');
    Route::post('/issues', [TenantIssueController::class, 'store'])->name('issues.store');
    Route::get('/issues/{issue}', [TenantIssueController::class, 'show'])->name('issues.show');
});

// Owner Routes
Route::prefix('owner')->name('owner.')->group(function () {
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

    // Statistics & Performance
    Route::get('/statistics', [OwnerStatisticsController::class, 'index'])->name('statistics');
});
