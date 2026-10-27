@props([
    'eyebrow' => null,
    'eyebrowIcon' => 'sun',
    'title' => null,
    'level' => 2,
    'description' => null,
    'align' => 'left',
    'tone' => 'light',
    'id' => null,
])

@php
    $wrapper = $align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl';
@endphp

<div {{ $attributes->class($wrapper) }}>
    @if ($eyebrow)
        <x-site.eyebrow :icon="$eyebrowIcon" :tone="$tone === 'light' ? 'light' : 'dark'">{{ $eyebrow }}</x-site.eyebrow>
    @endif

    @if ($title)
        <x-site.heading :level="$level" :id="$id" class="mt-4">{{ $title }}</x-site.heading>
    @endif

    @if ($description)
        <p @class([
            'mt-4 text-base leading-relaxed sm:text-lg',
            'text-ink/70' => $tone === 'light',
            'text-white/70' => $tone !== 'light',
        ])>{{ $description }}</p>
    @endif

    @if (trim($slot) !== '')
        <div @class(['mt-6', 'flex flex-wrap items-center gap-3' => $align !== 'center', 'flex flex-wrap items-center justify-center gap-3' => $align === 'center'])>
            {{ $slot }}
        </div>
    @endif
</div>
