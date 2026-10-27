@props([
    'categories' => [],
    'tags' => [],
    'recent' => [],
])

@php
    use Illuminate\Support\Collection;

    /** @var Collection<int, \App\Models\Category> $categories */
    /** @var Collection<int, \App\Models\Tag> $tags */

    // Callers may pass plain arrays; normalise so the isEmpty() checks below are safe.
    $categories = collect($categories);
    $tags = collect($tags);
    $recent = collect($recent);
@endphp

<aside class="grid gap-8 lg:sticky lg:top-28 lg:self-start" aria-label="Sidebar blog">
    {{-- Search --}}
    <section aria-labelledby="cari-artikel-heading" class="rounded-2xl bg-night-50/70 p-6 ring-1 ring-night-900/10">
        <h2 id="cari-artikel-heading" class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">
            Cari artikel
        </h2>

        <form method="GET" action="{{ route('blog.index') }}" role="search" class="mt-4">
            <label for="cari-blog" class="sr-only">Kata kunci pencarian</label>

            <div class="relative">
                <x-site.icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4.5 -translate-y-1/2 text-zinc-400" stroke="2" />

                <input
                    id="cari-blog"
                    type="search"
                    name="cari"
                    value="{{ request('cari') }}"
                    placeholder="Misalnya: biaya PLTS atap"
                    class="w-full rounded-xl border-0 bg-white py-2.5 pr-3.5 pl-10 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset transition placeholder:text-zinc-400 focus:ring-2 focus:ring-solar-500 focus:outline-none"
                />
            </div>

            <button type="submit" class="mt-3 w-full rounded-full bg-night-950 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-night-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-500">
                Cari
            </button>
        </form>
    </section>

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <nav aria-labelledby="kategori-heading" class="rounded-2xl bg-white p-6 ring-1 ring-night-900/10">
            <h2 id="kategori-heading" class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">
                Kategori
            </h2>

            <ul class="mt-4 grid gap-1">
                @foreach ($categories as $category)
                    <li>
                        <a
                            href="{{ route('blog.category', $category) }}"
                            @if (request()->routeIs('blog.category') && request()->route('category')?->is($category)) aria-current="page" @endif
                            @class([
                                'flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm transition-colors',
                                'bg-solar-50 font-semibold text-solar-900' => request()->routeIs('blog.category') && request()->route('category')?->is($category),
                                'text-zinc-700 hover:bg-night-50' => ! (request()->routeIs('blog.category') && request()->route('category')?->is($category)),
                            ])
                        >
                            <span class="truncate">{{ $category->name }}</span>
                            <span class="shrink-0 rounded-full bg-night-50 px-2 py-0.5 text-xs font-semibold text-zinc-600">{{ $category->articles_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif

    {{-- Recent --}}
    @if ($recent->isNotEmpty())
        <section aria-labelledby="terbaru-heading" class="rounded-2xl bg-white p-6 ring-1 ring-night-900/10">
            <h2 id="terbaru-heading" class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">
                Artikel terbaru
            </h2>

            <ul class="mt-4 grid gap-4">
                @foreach ($recent as $item)
                    <li>
                        <a href="{{ route('blog.show', $item) }}" class="group flex gap-3 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600">
                            <span class="size-16 shrink-0 overflow-hidden rounded-lg">
                                <x-site.image
                                    :src="$item->cover_image"
                                    :alt="''"
                                    variant="panels"
                                    ratio="size-full"
                                />
                            </span>

                            <span class="min-w-0">
                                <span class="line-clamp-2 text-sm font-semibold text-night-950 group-hover:text-solar-700">{{ $item->title }}</span>

                                @if ($item->published_at)
                                    <time datetime="{{ $item->published_at->toIso8601String() }}" class="mt-1 block text-xs text-zinc-500">
                                        {{ $item->published_at->translatedFormat('j M Y') }}
                                    </time>
                                @endif
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Tags --}}
    @if ($tags->isNotEmpty())
        <section aria-labelledby="tag-heading" class="rounded-2xl bg-white p-6 ring-1 ring-night-900/10">
            <h2 id="tag-heading" class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">
                Topik populer
            </h2>

            <ul class="mt-4 flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <li>
                        <a
                            href="{{ route('blog.tag', $tag) }}"
                            @if (request()->routeIs('blog.tag') && request()->route('tag')?->is($tag)) aria-current="page" @endif
                            class="inline-flex items-center gap-1.5 rounded-full bg-night-50 px-3 py-1.5 text-xs font-semibold text-night-800 ring-1 ring-night-900/10 ring-inset transition-colors hover:bg-solar-100 hover:text-solar-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                        >
                            <x-site.icon name="tag" class="size-3.5" stroke="2" />
                            {{ $tag->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</aside>
