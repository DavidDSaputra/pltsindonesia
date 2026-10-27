@props([
    'level' => 2,
    'size' => 'lg',
    'tone' => null,
    'align' => 'left',
])

@php
    $tag = 'h'.max(1, min(6, (int) $level));

    $sizes = [
        'xl' => 'text-4xl sm:text-5xl lg:text-6xl',
        'lg' => 'text-3xl sm:text-4xl lg:text-5xl',
        'md' => 'text-2xl sm:text-3xl',
        'sm' => 'text-xl sm:text-2xl',
    ];

    $aligns = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];

    // Inherit the section colour unless a tone is given explicitly.
    $tones = [
        'dark' => 'text-white',
        'light' => 'text-ink',
    ];
@endphp

<{{ $tag }} {{ $attributes->class([
    'font-display tracking-tight text-balance',
    $sizes[$size] ?? $sizes['lg'],
    $aligns[$align] ?? $aligns['left'],
    $tone ? ($tones[$tone] ?? '') : '',
]) }}>{{ $slot }}</{{ $tag }}>
