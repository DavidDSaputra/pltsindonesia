@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'external' => false,
])

@php
    $variants = [
        'primary' => 'rounded-full bg-solar-500 text-night-950 hover:bg-solar-400 focus-visible:outline-solar-600 shadow-sm',
        'dark' => 'rounded-full bg-night-950 text-white hover:bg-night-900 focus-visible:outline-night-700 shadow-sm',
        'outline' => 'rounded-full border-2 border-night-900/15 bg-transparent text-night-900 hover:border-night-900/35 hover:bg-night-50',
        'outline-light' => 'rounded-full border-2 border-white/30 bg-transparent text-white hover:border-white/70 hover:bg-white/10',
        'ghost' => 'rounded-full bg-transparent text-night-800 hover:bg-night-50',
        'white' => 'rounded-full bg-white text-night-950 hover:bg-solar-50 shadow-sm',
        // Reference-style CTA: sharp corners on a skewed slab that un-skews on hover.
        'skew' => 'btn-skew rounded-none bg-transparent text-white focus-visible:outline-solar-400',
        'skew-light' => 'btn-skew btn-skew-light rounded-none bg-transparent text-night-950 focus-visible:outline-night-700',
    ];

    $sizes = [
        'sm' => 'px-3.5 py-2 text-sm gap-1.5',
        'md' => 'px-5 py-2.5 text-sm gap-2',
        'lg' => 'px-6 py-3.5 text-base gap-2.5',
    ];

    $classes = [
        'inline-flex items-center justify-center font-semibold',
        'transition-colors duration-150',
        'focus-visible:outline-2 focus-visible:outline-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-60',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ];
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->class($classes) }}
        @if ($external) target="_blank" rel="noopener noreferrer" @endif
    >
        @if ($icon)
            <x-site.icon :name="$icon" class="size-4" />
        @endif

        <span>{{ $slot }}</span>

        @if ($iconRight)
            <x-site.icon :name="$iconRight" class="size-4" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-site.icon :name="$icon" class="size-4" />
        @endif

        <span>{{ $slot }}</span>

        @if ($iconRight)
            <x-site.icon :name="$iconRight" class="size-4" />
        @endif
    </button>
@endif
