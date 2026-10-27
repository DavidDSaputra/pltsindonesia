@php
    /** @var \App\Models\Project $project */

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Proyek', 'url' => route('projects.index')],
        ['label' => $project->title],
    ];

    $facts = collect([
        ['label' => 'Klien', 'value' => $project->client, 'icon' => 'building'],
        ['label' => 'Lokasi', 'value' => $project->location, 'icon' => 'pin'],
        ['label' => 'Kapasitas', 'value' => $project->capacity_label, 'icon' => 'bolt'],
        ['label' => 'Selesai', 'value' => $project->completed_on?->translatedFormat('F Y'), 'icon' => 'calendar'],
    ])->filter(fn (array $fact) => filled($fact['value']))->values();
@endphp

<x-site.layout
    :title="$project->title"
    :description="$project->meta_description"
    :image="$project->image"
    :schema="$schema"
    :canonical="route('projects.show', $project)"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Studi Kasus"
        :title="$project->title"
        :description="$project->summary"
    />

    <x-site.section tone="light" labelled-by="rincian-proyek-heading" spacing="md">
        <x-site.container>
            <div class="grid items-start gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-8">
                    <div class="overflow-hidden rounded-3xl ring-1 ring-night-900/10">
                        <x-site.image
                            :src="$project->image"
                            :alt="'Foto instalasi '.$project->title"
                            variant="sunset"
                            ratio="aspect-[16/10]"
                            eager
                            width="1600"
                            height="1000"
                        />
                    </div>

                    <h2 id="rincian-proyek-heading" class="mt-10 text-2xl font-bold tracking-tight text-night-950 sm:text-3xl">
                        Tentang proyek ini
                    </h2>

                    @if (filled($project->body))
                        <div class="prose prose-solar mt-5 max-w-none text-base leading-relaxed">
                            {!! $project->body_html !!}
                        </div>
                    @else
                        <p class="mt-5 text-base leading-relaxed text-zinc-600">{{ $project->summary }}</p>
                    @endif
                </div>

                <aside class="lg:col-span-4" aria-labelledby="fakta-proyek-heading">
                    <div class="rounded-2xl bg-night-50/70 p-6 ring-1 ring-night-900/10 lg:sticky lg:top-28">
                        <h2 id="fakta-proyek-heading" class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">
                            Data proyek
                        </h2>

                        @if ($facts->isNotEmpty())
                            <dl class="mt-5 grid gap-4">
                                @foreach ($facts as $fact)
                                    <div class="flex items-start gap-3">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-white text-solar-700 ring-1 ring-night-900/10" aria-hidden="true">
                                            <x-site.icon :name="$fact['icon']" class="size-4.5" stroke="2" />
                                        </span>

                                        <span class="min-w-0">
                                            <dt class="text-xs text-zinc-500">{{ $fact['label'] }}</dt>
                                            <dd class="text-sm font-semibold text-night-950">{{ $fact['value'] }}</dd>
                                        </span>
                                    </div>
                                @endforeach
                            </dl>
                        @endif

                        <x-site.button :href="route('contact')" variant="primary" class="mt-6 w-full">
                            Proyek Serupa untuk Saya
                        </x-site.button>
                    </div>
                </aside>
            </div>
        </x-site.container>
    </x-site.section>

    @if ($related->isNotEmpty())
        <x-site.section tone="tint" labelled-by="proyek-lain-heading" spacing="md">
            <x-site.container>
                <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <x-site.section-heading
                        id="proyek-lain-heading"
                        eyebrow="Proyek Lain"
                        eyebrow-icon="spark"
                        title="Lihat hasil kerja kami lainnya"
                    />

                    <x-site.button :href="route('projects.index')" variant="outline" icon-right="arrow-right" class="shrink-0">
                        Semua Proyek
                    </x-site.button>
                </div>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-site.project-card :project="$item" />
                    @endforeach
                </div>
            </x-site.container>
        </x-site.section>
    @endif

    <x-site.cta-banner />
</x-site.layout>
