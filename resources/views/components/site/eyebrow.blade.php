@props([
    'icon' => null,
    'tone' => 'light',
    'as' => 'p',
])

@php
    // Reference-style eyebrow: a small solar dot plus letterspaced uppercase,
    // no pill background so it reads editorial rather than badge-like.
    $tones = [
        'light' => 'text-ink/60',
        'dark' => 'text-white/60',
        'neutral' => 'text-ink/60',
    ];
@endphp

<{{ $as }} {{ $attributes->class([
    'inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.22em]',
    $tones[$tone] ?? $tones['light'],
]) }}>
    <span class="size-1.5 shrink-0 rounded-full bg-solar-500" aria-hidden="true"></span>

    @if ($icon)
        <x-site.icon :name="$icon" class="size-3.5" stroke="2" />
    @endif

    <span>{{ $slot }}</span>
</{{ $as }}>
