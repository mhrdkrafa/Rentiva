<?php

namespace App\Services;

use App\Support\SeoData;

class SeoService
{
    /**
     * Generate default marketplace SEO metadata.
     */
    public static function default(): SeoData
    {
        return new SeoData(
            title: 'Rentiva — Marketplace Sewa Kamar, Kost & Properti Terpercaya',
            description: 'Temukan ribuan kost putri, putra, pasutri, apartemen, dan kontrakan terbaik dengan foto terverifikasi, harga transparan, dan booking instan.',
            schema: [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'Rentiva',
                'url' => config('app.url'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => config('app.url') . '/search?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ]
        );
    }

    /**
     * Generate SEO metadata for property listing catalog page.
     */
    public static function propertyList(int $totalCount = 0): SeoData
    {
        return new SeoData(
            title: 'Cari Kost & Sewa Kamar Terverifikasi — Rentiva',
            description: "Jelajahi {$totalCount} pilihan kost, apartemen, dan hunian sewa terverifikasi dengan informasi harga transparan dan fasilitas lengkap.",
            schema: [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Katalog Kost & Properti Sewa Rentiva',
                'numberOfItems' => $totalCount,
            ]
        );
    }

    /**
     * Generate SEO metadata for a property detail page.
     */
    public static function propertyDetail(
        string $title,
        string $description,
        ?string $imageUrl = null,
        ?string $url = null,
        ?int $minPrice = null,
        ?string $city = null,
        ?string $address = null
    ): SeoData {
        $formattedTitle = $title . ' — Sewa Properti di ' . ($city ?? 'Rentiva');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Accommodation',
            'name' => $title,
            'description' => $description,
            'url' => $url ?? url()->current(),
        ];

        if ($imageUrl) {
            $schema['image'] = $imageUrl;
        }

        if ($address) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressLocality' => $city ?? 'Indonesia',
                'addressCountry' => 'ID',
            ];
        }

        if ($minPrice) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => (string) $minPrice,
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
            ];
        }

        return new SeoData(
            title: $formattedTitle,
            description: $description,
            image: $imageUrl,
            schema: $schema
        );
    }

    /**
     * Instance wrapper for backward compatibility.
     */
    public function forProperty(string $title, string $description, ?string $imageUrl = null, ?string $price = null, ?string $city = null): SeoData
    {
        return self::propertyDetail(
            title: $title,
            description: $description,
            imageUrl: $imageUrl,
            minPrice: (int) $price,
            city: $city
        );
    }
}
