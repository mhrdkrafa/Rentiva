<?php

namespace App\Services;

use App\Support\SeoData;

class SeoService
{
    /**
     * Generate default marketplace SEO metadata.
     */
    public function default(): SeoData
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
     * Generate SEO metadata for a property listing.
     */
    public function forProperty(string $title, string $description, ?string $imageUrl = null, ?string $price = null, ?string $city = null): SeoData
    {
        $formattedTitle = $title . ' — Sewa Properti di ' . ($city ?? 'Rentiva');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Accommodation',
            'name' => $title,
            'description' => $description,
            'url' => url()->current(),
        ];

        if ($imageUrl) {
            $schema['image'] = $imageUrl;
        }

        if ($price) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $price,
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
}
