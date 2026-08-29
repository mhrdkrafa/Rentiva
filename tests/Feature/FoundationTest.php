<?php

use App\Models\User;

test('homepage renders public marketplace layout successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Rentiva');
    $response->assertSee('Temukan Hunian Nyaman');
});

test('tenant dashboard shell renders successfully', function () {
    $response = $this->get('/tenant/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Portal Hunian Terpadu');
    $response->assertSee('Ringkasan (Overview)');
});

test('owner dashboard shell renders successfully', function () {
    $response = $this->get('/owner/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Dashboard Manajemen Properti');
    $response->assertSee('Total Properti');
});

test('404 error page renders custom branded view', function () {
    $response = $this->get('/non-existent-page-url-12345');

    $response->assertStatus(404);
    $response->assertSee('Halaman Tidak Ditemukan');
    $response->assertSee('Kembali ke Beranda');
});
