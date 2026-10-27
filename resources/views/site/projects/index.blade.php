@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $projects */

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Proyek'],
    ];

    $activeFilter = request()->string('filter')->toString();
@endphp

<x-site.layout
    :title="site('projects_meta_title', 'Portofolio Proyek PLTS')"
    :description="site('projects_meta_description', 'Dokumentasi proyek pemasangan panel surya untuk rumah, bisnis, dan industri di berbagai kota di Indonesia.')"
    :schema="$schema"
    :canonical="route('projects.index')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Portofolio"
        title="Proyek yang sudah kami kerjakan"
        description="Setiap proyek punya tantangan atap, beban, dan anggaran yang berbeda. Berikut sebagian hasilnya."
    />

    <x-site.section tone="light" labelled-by="daftar-proyek-heading">
        <x-site.container>
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <x-site.section-heading
                    id="daftar-proyek-heading"
                    eyebrow="Galeri Proyek"
                    eyebrow-icon="grid"
                    :title="$projects->total().' proyek terdokumentasi'"
                />

                @if ($locations->isNotEmpty())
                    <form method="GET" action="{{ route('projects.index') }}" class="flex items-end gap-3">
                        <div>
                            <label for="filter-lokasi" class="mb-1.5 block text-xs font-semibold tracking-wide text-zinc-600 uppercase">
                                Saring lokasi
                            </label>

                            <select
                                id="filter-lokasi"
                                name="filter"
                                onchange="this.form.submit()"
                                class="rounded-xl border-0 bg-white py-2.5 pr-9 pl-3.5 text-sm text-night-950 ring-1 ring-night-900/15 ring-inset focus:ring-2 focus:ring-solar-500 focus:outline-none"
                            >
                                <option value="">Semua lokasi</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location }}" @selected($activeFilter === $location)>{{ $location }}</option>
                                @endforeach
                            </select>
                        </div>

                        <noscript>
                            <button type="submit" class="rounded-xl bg-night-950 px-4 py-2.5 text-sm font-semibold text-white">Terapkan</button>
                        </noscript>

                        @if (filled($activeFilter))
                            <a href="{{ route('projects.index') }}" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-zinc-600 ring-1 ring-night-900/15 ring-inset transition-colors hover:bg-night-50">
                                Bersihkan
                            </a>
                        @endif
                    </form>
                @endif
            </div>

            @if ($projects->isNotEmpty())
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-site.project-card :project="$project" />
                    @endforeach
                </div>

                <nav class="mt-14" aria-label="Navigasi halaman proyek">
                    {{ $projects->links() }}
                </nav>
            @else
                <p class="mt-12 rounded-2xl bg-night-50 p-10 text-center text-sm text-zinc-600">
                    Belum ada proyek pada penyaringan ini.
                    @if (filled($activeFilter))
                        <a href="{{ route('projects.index') }}" class="font-semibold text-solar-700 underline">Lihat semua proyek</a>.
                    @endif
                </p>
            @endif
        </x-site.container>
    </x-site.section>

    <x-site.cta-banner
        title="Proyek Anda bisa jadi yang berikutnya"
        description="Kirimkan lokasi dan kebutuhan daya Anda. Kami siapkan kajian awal beserta perkiraan kapasitas yang sesuai."
    />
</x-site.layout>
