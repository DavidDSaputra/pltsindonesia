@props([
    'as' => 'section',
    'tone' => 'light',
    'spacing' => 'lg',
    'labelledBy' => null,
])

@php
    // Alternating editorial banding: warm paper vs near-black coal blocks.
    $tones = [
        'light' => 'bg-paper text-ink',
        'tint' => 'bg-tile text-ink',
        'cream' => 'bg-solar-50 text-night-950',
        'dark' => 'bg-coal text-white',
        'brand' => 'bg-night-900 text-white',
    ];

    $spacings = [
        'sm' => 'py-10 sm:py-12',
        'md' => 'py-14 sm:py-20',
        'lg' => 'py-20 sm:py-28',
    ];
@endphp

<{{ $as }}
    {{ $attributes
        ->class(['relative scroll-mt-24', $tones[$tone] ?? $tones['light'], $spacings[$spacing] ?? $spacings['lg']])
        ->when($labelledBy, fn ($bag) => $bag->merge(['aria-labelledby' => $labelledBy])) }}
>
    {{ $slot }}
</{{ $as }}>
