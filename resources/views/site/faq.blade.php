@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Faq> $faqs */

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'FAQ'],
    ];

    // Group questions so each topic gets its own accessible section.
    $grouped = $faqs->groupBy(fn ($faq) => filled($faq->group) ? $faq->group : 'Umum');

    $whatsapp = site('whatsapp');
@endphp

<x-site.layout
    :title="site('faq_meta_title', 'Pertanyaan yang Sering Diajukan')"
    :description="site('faq_meta_description', 'Jawaban atas pertanyaan umum seputar biaya, pemasangan, garansi, dan perawatan panel surya di Indonesia.')"
    :schema="$schema"
    :canonical="route('faq')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="FAQ"
        title="Pertanyaan yang sering diajukan"
        description="Kumpulan jawaban atas hal-hal yang paling sering ditanyakan calon pelanggan kami."
    />

    <x-site.section tone="light" labelled-by="faq-heading" spacing="md">
        <x-site.container>
            <div class="mx-auto max-w-4xl">
                @if ($faqs->isEmpty())
                    <p class="border border-hairline bg-tile p-10 text-center text-sm text-ink/70">
                        Daftar pertanyaan sedang kami susun. Silakan hubungi kami untuk bertanya langsung.
                    </p>
                @else
                    <h2 id="faq-heading" class="sr-only">Daftar pertanyaan</h2>

                    {{-- Jump links --}}
                    @if ($grouped->count() > 1)
                        <nav aria-label="Lompat ke topik" class="mb-8 flex flex-wrap gap-2">
                            @foreach ($grouped as $groupName => $items)
                                <a
                                    href="#topik-{{ \Illuminate\Support\Str::slug($groupName) }}"
                                    class="bg-tile px-3.5 py-1.5 text-xs font-semibold text-ink transition-colors hover:bg-coal hover:text-solar-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                >
                                    {{ \Illuminate\Support\Str::title($groupName) }}
                                </a>
                            @endforeach
                        </nav>
                    @endif

                    <div class="grid gap-10">
                        @foreach ($grouped as $groupName => $items)
                            <section aria-labelledby="topik-{{ \Illuminate\Support\Str::slug($groupName) }}">
                                <h3
                                    id="topik-{{ \Illuminate\Support\Str::slug($groupName) }}"
                                    class="reveal mb-4 font-display text-2xl tracking-tight text-ink scroll-mt-24"
                                >
                                    {{ \Illuminate\Support\Str::title($groupName) }}
                                </h3>

                                <x-site.faq-list :faqs="$items" :id="'daftar-faq-'.\Illuminate\Support\Str::slug($groupName)" />
                            </section>
                        @endforeach
                    </div>

                @endif

                {{-- Still need help --}}
                <div class="reveal mt-14 flex flex-col items-center gap-5 border border-white/10 bg-coal p-8 text-center text-white sm:flex-row sm:text-left">
                    <span class="grid size-12 shrink-0 place-items-center bg-solar-400 text-coal" aria-hidden="true">
                        <x-site.icon name="chat" class="size-6" stroke="1.9" />
                    </span>

                    <div class="flex-1">
                        <p class="font-display text-xl tracking-tight">Masih ada yang ingin ditanyakan?</p>
                        <p class="mt-1 text-sm text-white/65">
                            Tim kami siap menjelaskan dengan bahasa yang mudah dipahami, tanpa tekanan untuk membeli.
                        </p>
                    </div>

                    <x-site.button
                        :href="filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : route('contact')"
                        :external="filled($whatsapp)"
                        variant="skew-light"
                        icon="whatsapp"
                        class="shrink-0"
                    >
                        Tanya Sekarang
                    </x-site.button>
                </div>
            </div>
        </x-site.container>
    </x-site.section>

    <x-site.cta-banner />
</x-site.layout>
