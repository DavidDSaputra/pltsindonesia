@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $articles */

    $heading ??= 'Blog & Edukasi Energi Surya';
    $description ??= 'Panduan praktis, hitung-hitungan biaya, dan perkembangan regulasi PLTS di Indonesia.';

    $crumbs = array_values(array_filter([
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Blog', 'url' => isset($crumb) ? route('blog.index') : null],
        $crumb ?? null,
    ]));

    $pageTitle = isset($crumb) ? $crumb['label'] : 'Blog & Edukasi';

    // The sidebar "recent" list is only needed on the plain index.
    $recent = isset($crumb) || filled($term)
        ? collect()
        : \App\Models\Article::query()->published()->ordered()->limit(4)->get();
@endphp

<x-site.layout
    :title="$pageTitle"
    :description="$description"
    :schema="$schema"
    :canonical="route('blog.index')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Blog"
        :title="$heading"
        :description="$description"
    />

    <x-site.section tone="light" labelled-by="daftar-artikel-heading" spacing="md">
        <x-site.container>
            {{-- Featured article only shows on the unfiltered index --}}
            @if ($featured && ! isset($crumb) && ! filled($term))
                <div class="mb-12">
                    <x-site.article-card :article="$featured" variant="featured" />
                </div>
            @endif

            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-8">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 id="daftar-artikel-heading" class="text-lg font-bold tracking-tight text-night-950">
                            @if (filled($term))
                                Hasil pencarian untuk &ldquo;{{ $term }}&rdquo;
                            @else
                                Semua artikel
                            @endif
                        </h2>

                        <p class="text-sm text-zinc-500" role="status">
                            {{ $articles->total() }} artikel ditemukan
                        </p>
                    </div>

                    @if ($articles->isNotEmpty())
                        <div class="mt-7 grid gap-6 sm:grid-cols-2">
                            @foreach ($articles as $article)
                                <x-site.article-card :article="$article" />
                            @endforeach
                        </div>

                        <nav class="mt-12" aria-label="Navigasi halaman artikel">
                            {{ $articles->links() }}
                        </nav>
                    @else
                        <div class="mt-7 rounded-2xl bg-night-50 p-10 text-center">
                            <p class="text-sm font-semibold text-night-950">Belum ada artikel yang cocok.</p>
                            <p class="mt-2 text-sm text-zinc-600">
                                Coba kata kunci lain atau jelajahi kategori yang tersedia di samping.
                            </p>

                            <x-site.button :href="route('blog.index')" variant="outline" class="mt-6">
                                Lihat Semua Artikel
                            </x-site.button>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-4">
                    <x-site.blog-sidebar :categories="$categories" :tags="$tags" :recent="$recent" />
                </div>
            </div>
        </x-site.container>
    </x-site.section>

    <x-site.cta-banner
        title="Punya pertanyaan yang belum terjawab?"
        description="Tim teknis kami senang menjelaskan hal-hal seperti perhitungan kapasitas, pilihan inverter, atau pengurusan perizinan."
    />
</x-site.layout>
