@php
    /** @var \App\Models\Service $service */

    $features = collect((array) ($service->features ?? []))->filter()->values();

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Layanan', 'url' => route('services.index')],
        ['label' => $service->title],
    ];

    $whatsapp = site('whatsapp');
    $ctaUrl = filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : route('contact');
@endphp

<x-site.layout
    :title="$service->title"
    :description="$service->meta_description"
    :image="$service->image"
    :schema="$schema"
    :canonical="route('services.show', $service)"
>
    {{-- Hero --}}
    <section aria-labelledby="layanan-heading" class="relative overflow-hidden bg-night-950 text-white">
        <div class="panel-grid pointer-events-none absolute inset-0 opacity-50" aria-hidden="true"></div>

        <div class="pointer-events-none absolute -top-32 -left-24 size-96 rounded-full bg-solar-500/20 blur-3xl" aria-hidden="true"></div>

        <x-site.container class="relative">
            <div class="grid items-center gap-12 py-14 lg:grid-cols-2 lg:gap-16 lg:py-20">
                <div>
                    <x-site.breadcrumbs :items="$crumbs" tone="dark" />

                    <x-site.eyebrow icon="panels" tone="dark" class="mt-6">Layanan</x-site.eyebrow>

                    <h1 id="layanan-heading" class="mt-4 text-3xl font-extrabold tracking-tight text-balance sm:text-4xl lg:text-5xl">
                        {{ $service->title }}
                    </h1>

                    <p class="mt-5 max-w-xl text-base leading-relaxed text-night-100/85 sm:text-lg">
                        {{ $service->summary }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <x-site.button
                            :href="$ctaUrl"
                            :external="filled($whatsapp)"
                            variant="primary"
                            size="lg"
                            icon="whatsapp"
                        >
                            Konsultasikan Sekarang
                        </x-site.button>

                        <x-site.button :href="route('contact')" variant="outline-light" size="lg" icon-right="arrow-right">
                            Minta Penawaran
                        </x-site.button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl ring-1 ring-white/15">
                    <x-site.image
                        :src="$service->image"
                        :alt="'Dokumentasi layanan '.$service->title"
                        variant="sunset"
                        ratio="aspect-[4/3]"
                        eager
                        width="1200"
                        height="900"
                    />
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- Body + features --}}
    <x-site.section tone="light" labelled-by="rincian-heading">
        <x-site.container>
            <div class="grid items-start gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-8">
                    <h2 id="rincian-heading" class="text-2xl font-bold tracking-tight text-night-950 sm:text-3xl">
                        Rincian layanan
                    </h2>

                    @if (filled($service->body))
                        <div class="prose prose-solar mt-6 max-w-none text-base leading-relaxed">
                            {!! $service->body_html !!}
                        </div>
                    @else
                        <p class="mt-6 text-base leading-relaxed text-zinc-600">
                            {{ $service->summary }}
                        </p>
                    @endif
                </div>

                @if ($features->isNotEmpty())
                    <aside class="lg:col-span-4" aria-labelledby="cakupan-heading">
                        <div class="rounded-2xl bg-night-50/70 p-6 ring-1 ring-night-900/10 lg:sticky lg:top-28">
                            <h2 id="cakupan-heading" class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">
                                Cakupan pekerjaan
                            </h2>

                            <ul class="mt-5 grid gap-3">
                                @foreach ($features as $feature)
                                    <li class="flex items-start gap-3 text-sm text-night-900">
                                        <x-site.icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-solar-600" stroke="2" />
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <x-site.button :href="$ctaUrl" :external="filled($whatsapp)" variant="primary" class="mt-6 w-full">
                                Bicarakan Kebutuhan Anda
                            </x-site.button>
                        </div>
                    </aside>
                @endif
            </div>
        </x-site.container>
    </x-site.section>

    {{-- Related projects --}}
    @if ($projects->isNotEmpty())
        <x-site.section tone="tint" labelled-by="proyek-terkait-heading">
            <x-site.container>
                <x-site.section-heading
                    id="proyek-terkait-heading"
                    align="center"
                    eyebrow="Hasil Kerja"
                    eyebrow-icon="spark"
                    title="Proyek terkait layanan ini"
                    class="mx-auto"
                />

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-site.project-card :project="$project" />
                    @endforeach
                </div>
            </x-site.container>
        </x-site.section>
    @endif

    {{-- FAQ --}}
    @if ($faqs->isNotEmpty())
        <x-site.section tone="light" labelled-by="faq-layanan-heading">
            <x-site.container>
                <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
                    <div class="lg:col-span-5">
                        <x-site.section-heading
                            id="faq-layanan-heading"
                            eyebrow="FAQ"
                            eyebrow-icon="chat"
                            title="Pertanyaan seputar layanan ini"
                            description="Masih ragu? Tanyakan langsung pada tim teknis kami."
                        >
                            <x-site.button :href="route('faq')" variant="ghost" icon-right="arrow-right">
                                Semua Pertanyaan
                            </x-site.button>
                        </x-site.section-heading>
                    </div>

                    <div class="lg:col-span-7">
                        <x-site.faq-list :faqs="$faqs" id="daftar-faq-layanan" />
                    </div>
                </div>
            </x-site.container>
        </x-site.section>
    @endif

    {{-- Other services --}}
    @if ($others->isNotEmpty())
        <x-site.section tone="tint" labelled-by="layanan-lain-heading">
            <x-site.container>
                <x-site.section-heading
                    id="layanan-lain-heading"
                    eyebrow="Layanan Lain"
                    eyebrow-icon="layers"
                    title="Mungkin Anda juga membutuhkan"
                />

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($others as $other)
                        <x-site.service-card :service="$other" />
                    @endforeach
                </div>
            </x-site.container>
        </x-site.section>
    @endif

    <x-site.cta-banner />
</x-site.layout>
