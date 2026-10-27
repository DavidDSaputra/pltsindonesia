@props([
    'src' => null,
    'alt' => '',
    'variant' => 'panels',
    'ratio' => 'aspect-[4/3]',
    'imgClass' => '',
    'eager' => false,
    'width' => null,
    'height' => null,
])

@php
    use Illuminate\Support\Str;

    $url = image_url($src);

    // Remote URLs are trusted as-is; local files are only rendered when they really exist.
    $exists = filled($url) && (
        Str::startsWith($url, ['http://', 'https://', '//'])
        || file_exists(public_path(ltrim((string) parse_url($url, PHP_URL_PATH), '/')))
    );

    $uid = 'art-'.Str::lower(Str::random(8));

    $palette = match ($variant) {
        'sunset' => ['#1a4331', '#12372a', '#e7b83c'],
        'leaf' => ['#2f6b4f', '#12372a', '#e7b83c'],
        'slate' => ['#0f172a', '#1e293b', '#38bdf8'],
        default => ['#12372a', '#1a4331', '#e7b83c'],
    };

    [$from, $to, $glow] = $palette;
@endphp

<div {{ $attributes->class(['relative isolate overflow-hidden bg-night-950', $ratio]) }}>
    @if ($exists)
        <img
            src="{{ $url }}"
            alt="{{ $alt }}"
            class="size-full object-cover {{ $imgClass }}"
            @if ($width) width="{{ $width }}" @endif
            @if ($height) height="{{ $height }}" @endif
            @unless ($eager) loading="lazy" @endunless
            decoding="async"
        />
    @else
        {{-- Original solar artwork: shown until a real photo is uploaded for this slot. --}}
        <svg
            class="size-full"
            viewBox="0 0 800 600"
            role="img"
            aria-label="{{ filled($alt) ? $alt : 'Ilustrasi instalasi panel surya' }}"
            preserveAspectRatio="xMidYMid slice"
        >
            <defs>
                <linearGradient id="{{ $uid }}-sky" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="{{ $from }}" />
                    <stop offset="100%" stop-color="{{ $to }}" />
                </linearGradient>
                <radialGradient id="{{ $uid }}-sun" cx="78%" cy="20%" r="45%">
                    <stop offset="0%" stop-color="{{ $glow }}" stop-opacity="0.85" />
                    <stop offset="100%" stop-color="{{ $glow }}" stop-opacity="0" />
                </radialGradient>
                <pattern id="{{ $uid }}-cells" width="60" height="34" patternUnits="userSpaceOnUse">
                    <rect width="60" height="34" fill="none" />
                    <path d="M0 0H60M0 0V34" stroke="#ffffff" stroke-opacity="0.18" stroke-width="1.5" />
                </pattern>
            </defs>

            <rect width="800" height="600" fill="url(#{{ $uid }}-sky)" />
            <rect width="800" height="600" fill="url(#{{ $uid }}-sun)" />

            {{-- Panel field --}}
            <g transform="translate(60 330) skewY(-9)">
                <rect width="680" height="240" fill="#0b1220" fill-opacity="0.55" />
                <rect width="680" height="240" fill="url(#{{ $uid }}-cells)" />
            </g>

            {{-- Horizon --}}
            <path d="M0 470h800v130H0z" fill="#12372a" fill-opacity="0.75" />
            <circle cx="624" cy="120" r="46" fill="{{ $glow }}" fill-opacity="0.9" />
        </svg>
    @endif

    {{ $slot }}
</div>
