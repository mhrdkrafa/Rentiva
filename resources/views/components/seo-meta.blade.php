@props(['seo' => null])

@php
    $seoData = $seo ?? \App\Services\SeoService::default();
@endphp

<title>{{ $seoData->title }}</title>
<meta name="description" content="{{ $seoData->description }}" />
<meta name="robots" content="{{ $seoData->robots ?? 'index, follow' }}" />

@if($seoData->canonical)
    <link rel="canonical" href="{{ $seoData->canonical }}" />
@endif

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $seoData->type ?? 'website' }}" />
<meta property="og:url" content="{{ $seoData->canonical ?? url()->current() }}" />
<meta property="og:title" content="{{ $seoData->title }}" />
<meta property="og:description" content="{{ $seoData->description }}" />
@if($seoData->image)
    <meta property="og:image" content="{{ $seoData->image }}" />
@endif
<meta property="og:site_name" content="Rentiva" />

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seoData->title }}" />
<meta name="twitter:description" content="{{ $seoData->description }}" />
@if($seoData->image)
    <meta name="twitter:image" content="{{ $seoData->image }}" />
@endif

<!-- Schema.org JSON-LD Structured Data -->
@if(!empty($seoData->schema))
    <script type="application/ld+json">
        {!! json_encode($seoData->schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
