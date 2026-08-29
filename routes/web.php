<?php

use App\Http\Controllers\Owner\ProfileController as OwnerProfileController;
use App\Http\Controllers\Owner\PropertyManagerController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/search', function () {
    return view('welcome');
})->name('search');

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
});
