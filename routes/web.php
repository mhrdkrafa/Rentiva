<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tenant/dashboard', function () {
    return view('tenant.dashboard');
})->name('tenant.dashboard');

Route::get('/owner/dashboard', function () {
    return view('owner.dashboard');
})->name('owner.dashboard');

Route::get('/search', function () {
    return view('welcome');
})->name('search');
