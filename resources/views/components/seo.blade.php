@props([
    'data' => null,
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'robots' => 'index, follow',
    'schema' => null,
])

@php
    if ($data instanceof \App\Support\SeoData) {
        $title = $title ?? $data->title;
        $description = $description ?? $data->description;
        $canonical = $canonical ?? $data->canonical;
        $image = $image ?? $data->image;
        $robots = $robots ?? ($data->robots ?? 'index, follow');
        $schema = $schema ?? $data->schema;
    }

    $title = $title ?? 'Rentiva — Marketplace Sewa Kamar, Kost & Properti Terpercaya';
    $description = $description ?? 'Temukan ribuan kost, apartemen, dan rumah sewa terbaik dengan harga transparan dan fasilitas lengkap di Rentiva.';
    $canonical = $canonical ?? url()->current();
    $image = $image ?? asset('images/og-rentiva.png');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="Rentiva">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonical }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

@if($schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
