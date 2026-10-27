@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
    'schema' => [],
    'noindex' => false,
])

@php
    $company = site('company_name', config('app.name'));
    $tagline = site('tagline', 'Kontraktor Panel Surya & PLTS Terpercaya');

    $pageTitle = filled($title)
        ? \Illuminate\Support\Str::limit($title.' · '.$company, 65, '')
        : $company.' — '.$tagline;

    $pageDescription = filled($description)
        ? \Illuminate\Support\Str::limit($description, 160, '')
        : \Illuminate\Support\Str::limit((string) site('meta_description', $tagline.'. Konsultasi, perencanaan, pemasangan hingga perawatan PLTS untuk rumah, bisnis, dan industri di seluruh Indonesia.'), 160, '');

    $canonicalUrl = $canonical ?? url()->current();
    // Always advertise a share image: page override, then the site setting, then bundled artwork.
    $ogImage = image_url($image) ?? image_url(site('og_image')) ?? '/images/og-cover.svg';
    $resolvedRobots = $noindex ? 'noindex, nofollow' : $robots;

    $socials = array_filter((array) site('social', []), fn ($url) => filled($url));

    $blocks = array_values(array_filter([
        \App\Support\JsonLd::organization(),
        $type === 'website' ? \App\Support\JsonLd::website() : null,
        ...(array) $schema,
    ]));
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="theme-color" content="#12372a" />
        <meta name="author" content="{{ $company }}" />
        <meta name="application-name" content="{{ $company }}" />

        <title>{{ $pageTitle }}</title>

        <meta name="description" content="{{ $pageDescription }}" />
        <meta name="robots" content="{{ $resolvedRobots }}" />
        @if (filled(site('seo_keywords')))
            <meta name="keywords" content="{{ site('seo_keywords') }}" />
        @endif
        <link rel="canonical" href="{{ $canonicalUrl }}" />
        <link rel="alternate" hreflang="id" href="{{ $canonicalUrl }}" />
        <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}" />

        {{-- Open Graph --}}
        <meta property="og:site_name" content="{{ $company }}" />
        <meta property="og:type" content="{{ $type }}" />
        <meta property="og:locale" content="id_ID" />
        <meta property="og:title" content="{{ $pageTitle }}" />
        <meta property="og:description" content="{{ $pageDescription }}" />
        <meta property="og:url" content="{{ $canonicalUrl }}" />
        @if ($ogImage)
            <meta property="og:image" content="{{ url($ogImage) }}" />
            <meta property="og:image:alt" content="{{ $pageTitle }}" />
        @endif

        {{-- Twitter / X --}}
        <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}" />
        <meta name="twitter:title" content="{{ $pageTitle }}" />
        <meta name="twitter:description" content="{{ $pageDescription }}" />
        @if ($ogImage)
            <meta name="twitter:image" content="{{ url($ogImage) }}" />
            <meta name="twitter:image:alt" content="{{ $pageTitle }}" />
        @endif

        {{-- Icons --}}
        <link rel="icon" href="/images/logo-navbar.png" type="image/png" />
        <link rel="apple-touch-icon" href="/images/logo-navbar.png" />
        @if (filled(site('rss_enabled', true)))
            <link rel="alternate" type="application/rss+xml" title="Artikel terbaru {{ $company }}" href="{{ route('feed') }}" />
        @endif

        {{-- Structured data --}}
        @foreach ($blocks as $block)
            <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>

    <body class="min-h-screen bg-paper font-sans text-ink antialiased">
        <a href="#konten" class="skip-link">Lewati ke konten utama</a>

        @include('partials.site.header', ['socials' => $socials])

        <main id="konten" @class(['pt-24 sm:pt-28' => ! request()->routeIs('home')])>
            {{ $slot }}
        </main>

        @include('partials.site.footer', ['socials' => $socials])

        @fluxScripts
    </body>
</html>
