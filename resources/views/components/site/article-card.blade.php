@props([
    'article',
    'variant' => 'default',
])

@php
    /** @var \App\Models\Article $article */
    $isFeatured = $variant === 'featured';
    $isCompact = $variant === 'compact';
@endphp

{{-- Editorial article card: sharp corners, hairline frame and a display headline. --}}
<article @class([
    'group relative flex border border-hairline bg-white transition-colors hover:border-ink/30',
    'flex-col' => ! $isCompact,
    'flex-col sm:flex-row' => $isCompact,
    'lg:flex-row' => $isFeatured,
])>
    <div @class([
        'relative shrink-0 overflow-hidden',
        'aspect-[16/10]' => ! $isCompact && ! $isFeatured,
        'aspect-[16/10] lg:aspect-auto lg:w-1/2' => $isFeatured,
        'aspect-[16/10] sm:aspect-auto sm:w-44' => $isCompact,
    ])>
        <x-site.image
            :src="$article->cover_image"
            :alt="'Sampul artikel: '.$article->title"
            :variant="$isFeatured ? 'sunset' : 'panels'"
            ratio="size-full"
        />

        @if ($article->category)
            <span class="absolute top-3 left-3 bg-coal/85 px-2.5 py-1 text-[11px] tracking-[0.18em] text-solar-300 uppercase">
                {{ $article->category->name }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5 sm:p-6">
        <h3 @class([
            'font-display tracking-tight text-ink text-balance',
            'text-2xl sm:text-3xl' => $isFeatured,
            'text-xl' => ! $isFeatured,
        ])>
            <a href="{{ route('blog.show', $article) }}" class="before:absolute before:inset-0 before:content-[''] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600">
                {{ $article->title }}
            </a>
        </h3>

        @if (! $isCompact)
            <p class="line-clamp-3 text-sm leading-relaxed text-ink/70">
                {{ $article->excerpt }}
            </p>
        @endif

        <div class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-hairline pt-3 text-xs text-ink/60">
            @if ($article->published_at)
                <time datetime="{{ $article->published_at->toIso8601String() }}" class="inline-flex items-center gap-1.5">
                    <x-site.icon name="calendar" class="size-3.5" stroke="2" />
                    {{ $article->published_at->translatedFormat('j F Y') }}
                </time>
            @endif

            <span class="inline-flex items-center gap-1.5">
                <x-site.icon name="clock" class="size-3.5" stroke="2" />
                {{ $article->reading_time }} menit baca
            </span>

            @if ($article->author)
                <span class="inline-flex items-center gap-1.5">
                    <x-site.icon name="users" class="size-3.5" stroke="2" />
                    {{ $article->author->name }}
                </span>
            @endif
        </div>
    </div>
</article>
