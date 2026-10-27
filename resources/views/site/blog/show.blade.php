@php
    /** @var \App\Models\Article $article */

    $crumbs = array_values(array_filter([
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Blog', 'url' => route('blog.index')],
        $article->category
            ? ['label' => $article->category->name, 'url' => route('blog.category', $article->category)]
            : null,
        ['label' => \Illuminate\Support\Str::limit($article->title, 48, '')],
    ]));

    $whatsapp = site('whatsapp');
    $shareText = rawurlencode($article->title);
    $shareUrl = rawurlencode(route('blog.show', $article));
@endphp

<x-site.layout
    :title="$article->meta_title"
    :description="$article->meta_description"
    :image="$article->cover_image"
    type="article"
    :schema="$schema"
    :canonical="route('blog.show', $article)"
>
    <article>
        {{-- Header --}}
        <header class="relative overflow-hidden bg-night-950 text-white">
            <div class="panel-grid pointer-events-none absolute inset-0 opacity-50" aria-hidden="true"></div>

            <x-site.container class="relative">
                <div class="grid gap-8 py-12 sm:py-16 lg:py-20">
                    <x-site.breadcrumbs :items="$crumbs" tone="dark" />

                    <div class="max-w-4xl">
                        @if ($article->category)
                            <a
                                href="{{ route('blog.category', $article->category) }}"
                                class="inline-flex items-center gap-2 rounded-full bg-solar-500/15 px-3.5 py-1.5 text-xs font-semibold text-solar-200 ring-1 ring-solar-400/30 ring-inset transition-colors hover:bg-solar-500/25"
                            >
                                <x-site.icon name="folder" class="size-3.5" stroke="2" />
                                {{ $article->category->name }}
                            </a>
                        @endif

                        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-balance sm:text-4xl lg:text-5xl">
                            {{ $article->title }}
                        </h1>

                        @if (filled($article->excerpt))
                            <p class="mt-5 text-base leading-relaxed text-night-100/85 sm:text-lg">
                                {{ $article->excerpt }}
                            </p>
                        @endif

                        <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-night-200">
                            @if ($article->author)
                                <span class="flex items-center gap-3">
                                    <span class="grid size-10 place-items-center rounded-full bg-solar-400 text-sm font-bold text-night-950" aria-hidden="true">
                                        {{ \Illuminate\Support\Str::of($article->author->name)->substr(0, 1)->upper() }}
                                    </span>

                                    <span>
                                        <span class="block font-semibold text-white">Ditulis oleh {{ $article->author->name }}</span>
                                        @if (filled($article->author->bio))
                                            <span class="block text-xs">{{ \Illuminate\Support\Str::limit($article->author->bio, 60) }}</span>
                                        @endif
                                    </span>
                                </span>
                            @endif

                            @if ($article->published_at)
                                <time datetime="{{ $article->published_at->toIso8601String() }}" class="inline-flex items-center gap-2">
                                    <x-site.icon name="calendar" class="size-4" stroke="2" />
                                    {{ $article->published_at->translatedFormat('j F Y') }}
                                </time>
                            @endif

                            <span class="inline-flex items-center gap-2">
                                <x-site.icon name="clock" class="size-4" stroke="2" />
                                {{ $article->reading_time }} menit baca
                            </span>

                            <span class="inline-flex items-center gap-2">
                                <x-site.icon name="eye" class="size-4" stroke="2" />
                                {{ number_format($article->views_count, 0, ',', '.') }} dibaca
                            </span>
                        </div>
                    </div>
                </div>
            </x-site.container>
        </header>

        {{-- Cover --}}
        <x-site.container>
            <div class="-mt-1 overflow-hidden rounded-3xl ring-1 ring-night-900/10">
                <x-site.image
                    :src="$article->cover_image"
                    :alt="'Sampul artikel: '.$article->title"
                    variant="sunset"
                    ratio="aspect-[16/9]"
                    eager
                    width="1600"
                    height="900"
                />
            </div>
        </x-site.container>

        {{-- Body --}}
        <x-site.section tone="light" spacing="md">
            <x-site.container>
                <div class="grid gap-12 lg:grid-cols-12 lg:gap-14">
                    <div class="lg:col-span-8">
                        <div class="prose prose-solar max-w-none text-base leading-relaxed sm:text-lg">
                            {!! $article->body_html !!}
                        </div>

                        @if ($article->tags->isNotEmpty())
                            <footer class="mt-12 border-t border-night-900/10 pt-8">
                                <h2 class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">Topik terkait</h2>

                                <ul class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($article->tags as $tag)
                                        <li>
                                            <a
                                                href="{{ route('blog.tag', $tag) }}"
                                                class="inline-flex items-center gap-1.5 rounded-full bg-night-50 px-3 py-1.5 text-xs font-semibold text-night-800 ring-1 ring-night-900/10 ring-inset transition-colors hover:bg-solar-100 hover:text-solar-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                            >
                                                <x-site.icon name="tag" class="size-3.5" stroke="2" />
                                                {{ $tag->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Share --}}
                                <div class="mt-8 flex flex-wrap items-center gap-3">
                                    <span class="text-sm font-semibold text-night-950">Bagikan artikel ini:</span>

                                    <a
                                        href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-night-900 ring-1 ring-night-900/15 ring-inset transition-colors hover:bg-night-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                    >
                                        <x-site.icon name="whatsapp" class="size-4" stroke="2" />
                                        WhatsApp
                                    </a>

                                    <a
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-night-900 ring-1 ring-night-900/15 ring-inset transition-colors hover:bg-night-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                    >
                                        <x-site.icon name="facebook" class="size-4" stroke="2" />
                                        Facebook
                                    </a>

                                    <a
                                        href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-night-900 ring-1 ring-night-900/15 ring-inset transition-colors hover:bg-night-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                    >
                                        <x-site.icon name="linkedin" class="size-4" stroke="2" />
                                        LinkedIn
                                    </a>

                                    <a
                                        href="mailto:?subject={{ $shareText }}&amp;body={{ $shareUrl }}"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-night-900 ring-1 ring-night-900/15 ring-inset transition-colors hover:bg-night-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                    >
                                        <x-site.icon name="mail" class="size-4" stroke="2" />
                                        Email
                                    </a>
                                </div>
                            </footer>
                        @endif
                    </div>

                    <div class="lg:col-span-4">
                        <x-site.blog-sidebar :categories="$categories" :recent="$recent" />

                        <div class="mt-8 rounded-2xl bg-night-950 p-6 text-white">
                            <h2 class="text-base font-bold">Konsultasi gratis</h2>
                            <p class="mt-2 text-sm leading-relaxed text-night-200">
                                Kirim tagihan listrik Anda, kami hitungkan potensi penghematannya.
                            </p>

                            <x-site.button
                                :href="filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : route('contact')"
                                :external="filled($whatsapp)"
                                variant="primary"
                                icon="whatsapp"
                                class="mt-5 w-full"
                            >
                                Hubungi Kami
                            </x-site.button>
                        </div>
                    </div>
                </div>
            </x-site.container>
        </x-site.section>

        {{-- Related --}}
        @if ($related->isNotEmpty())
            <x-site.section tone="tint" labelled-by="artikel-terkait-heading" spacing="md">
                <x-site.container>
                    <x-site.section-heading
                        id="artikel-terkait-heading"
                        eyebrow="Baca Juga"
                        eyebrow-icon="book"
                        title="Artikel terkait"
                    />

                    <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($related as $item)
                            <x-site.article-card :article="$item" />
                        @endforeach
                    </div>
                </x-site.container>
            </x-site.section>
        @endif
    </article>

    <x-site.cta-banner />
</x-site.layout>
