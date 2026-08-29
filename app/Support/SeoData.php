<?php

namespace App\Support;

class SeoData
{
    public function __construct(
        public string $title = 'Rentiva — Marketplace Sewa Kamar, Kost & Properti Terpercaya',
        public string $description = 'Temukan ribuan kamar sewa, kost eksklusif, apartemen, dan rumah sewa terbaik dengan harga transparan, fasilitas lengkap, dan pemesanan mudah di Rentiva.',
        public ?string $canonical = null,
        public ?string $image = null,
        public string $type = 'website',
        public string $robots = 'index, follow',
        public array $schema = []
    ) {
        $this->canonical = $canonical ?? url()->current();
        $this->image = $image ?? asset('images/og-rentiva.png');
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'image' => $this->image,
            'type' => $this->type,
            'robots' => $this->robots,
            'schema' => $this->schema,
        ];
    }
}
