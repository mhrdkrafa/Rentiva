<?php

use App\Services\SeoService;
use App\Support\SeoData;

test('seo service generates default metadata', function () {
    $seoService = new SeoService();
    $seo = $seoService->default();

    expect($seo)->toBeInstanceOf(SeoData::class)
        ->and($seo->title)->toContain('Rentiva')
        ->and($seo->description)->not->toBeEmpty()
        ->and($seo->schema)->toHaveKey('@context');
});

test('seo service generates property specific metadata', function () {
    $seoService = new SeoService();
    $seo = $seoService->forProperty(
        title: 'Kost Putri Melati Exclusive',
        description: 'Kost nyaman dekat kampus dengan kamar mandi dalam.',
        imageUrl: 'https://rentiva.test/storage/properties/photo1.jpg',
        price: '1500000',
        city: 'Bandung'
    );

    expect($seo->title)->toContain('Kost Putri Melati Exclusive')
        ->and($seo->title)->toContain('Bandung')
        ->and($seo->image)->toBe('https://rentiva.test/storage/properties/photo1.jpg')
        ->and($seo->schema['offers']['price'])->toBe('1500000');
});
