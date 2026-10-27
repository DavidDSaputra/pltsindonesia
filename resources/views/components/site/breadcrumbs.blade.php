@props([
    'items' => [],
    'tone' => 'light',
])

@php
    // Every entry except the last one is a link; the last one is the current page.
    $links = array_slice($items, 0, -1);
    $current = array_slice($items, -1)[0] ?? null;
@endphp

@if (count($items) > 0)
    <nav aria-label="Breadcrumb" {{ $attributes }}>
        <ol @class([
            'flex flex-wrap items-center gap-2 text-sm',
            'text-zinc-600' => $tone === 'light',
            'text-night-100/80' => $tone !== 'light',
        ])>
            <li>
                <a href="{{ route('home') }}" @class([
                    'inline-flex items-center gap-1.5 hover:underline',
                    'text-night-800' => $tone === 'light',
                    'text-white' => $tone !== 'light',
                ])>
                    <x-site.icon name="home" class="size-4" stroke="2" />
                    <span class="sr-only">Beranda</span>
                </a>
            </li>

            @foreach ($links as $crumb)
                <li class="flex items-center gap-2">
                    <x-site.icon name="chevron-right" class="size-3.5 opacity-50" stroke="2" />
                    <a href="{{ $crumb['url'] }}" class="hover:underline">{{ $crumb['label'] }}</a>
                </li>
            @endforeach

            @if ($current)
                <li class="flex items-center gap-2" aria-current="page">
                    <x-site.icon name="chevron-right" class="size-3.5 opacity-50" stroke="2" />
                    <span @class([
                        'font-medium',
                        'text-night-950' => $tone === 'light',
                        'text-white' => $tone !== 'light',
                    ])>{{ $current['label'] }}</span>
                </li>
            @endif
        </ol>
    </nav>
@endif
