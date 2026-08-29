<?php

use App\Http\Controllers\Marketplace\PropertyController as PublicPropertyController;
use App\Http\Controllers\Owner\ProfileController as OwnerProfileController;
use App\Http\Controllers\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Owner\PropertyManagerController;
use App\Http\Controllers\Owner\UnitController as OwnerUnitController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;
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
    Route::get('/dashboard', function () {
        return view('tenant.dashboard');
    })->name('dashboard');

    Route::get('/profile', [TenantProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [TenantProfileController::class, 'update'])->name('profile.update');
});

// Owner Routes
Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner.dashboard');
    })->name('dashboard');

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

    // Units Management
    Route::get('/properties/{property}/units/create', [OwnerUnitController::class, 'create'])->name('units.create');
    Route::post('/properties/{property}/units', [OwnerUnitController::class, 'store'])->name('units.store');
    Route::delete('/units/{unit}', [OwnerUnitController::class, 'destroy'])->name('units.destroy');
});
