@php
    use App\Models\Article;
    use App\Models\Service;

    $suggestions = Article::query()->published()->ordered()->limit(3)->get();
    $services = Service::query()->active()->ordered()->limit(3)->get();
@endphp

<x-site.layout
    title="Halaman tidak ditemukan"
    description="Halaman yang Anda cari tidak tersedia. Jelajahi layanan, proyek, dan artikel kami."
    noindex
    robots="noindex, follow"
>
    <section aria-labelledby="error-heading" class="relative overflow-hidden bg-night-950 text-white">
        <div class="panel-grid pointer-events-none absolute inset-0 opacity-50" aria-hidden="true"></div>

        <div class="pointer-events-none absolute -top-32 right-0 size-96 rounded-full bg-solar-500/20 blur-3xl" aria-hidden="true"></div>

        <x-site.container class="relative">
            <div class="grid gap-8 py-20 text-center sm:py-28">
                <div>
                    <p class="text-7xl font-extrabold tracking-tight text-solar-400 sm:text-8xl" aria-hidden="true">404</p>

                    <h1 id="error-heading" class="mt-4 text-2xl font-extrabold tracking-tight text-balance sm:text-4xl">
                        Halaman yang Anda cari tidak kami temukan
                    </h1>

                    <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-night-100/85">
                        Mungkin alamatnya berubah atau artikelnya sudah dihapus. Coba telusuri tautan di bawah ini,
                        atau gunakan pencarian di halaman blog.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <x-site.button :href="route('home')" variant="primary" size="lg" icon="home">
                            Kembali ke Beranda
                        </x-site.button>

                        <x-site.button :href="route('blog.index')" variant="outline-light" size="lg" icon="search">
                            Cari Artikel
                        </x-site.button>

                        <x-site.button :href="route('contact')" variant="ghost" size="lg" class="text-white hover:bg-white/10">
                            Hubungi Kami
                        </x-site.button>
                    </div>
                </div>
            </div>
        </x-site.container>
    </section>

    <x-site.section tone="light" labelled-by="saran-heading" spacing="md">
        <x-site.container>
            <x-site.section-heading
                id="saran-heading"
                align="center"
                eyebrow="Mungkin Ini yang Anda Cari"
                eyebrow-icon="spark"
                title="Halaman populer"
                class="mx-auto"
            />

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <x-site.service-card :service="$service" />
                @endforeach
            </div>

            @if ($suggestions->isNotEmpty())
                <h2 class="mt-16 text-lg font-bold tracking-tight text-night-950">Artikel terbaru</h2>

                <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($suggestions as $article)
                        <x-site.article-card :article="$article" />
                    @endforeach
                </div>
            @endif
        </x-site.container>
    </x-site.section>
</x-site.layout>
