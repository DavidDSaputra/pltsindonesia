@php
    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => $title],
    ];

    // Editors can override the copy from the CMS settings screen.
    $customBody = site($bodyKey);
    $updatedAt = site($updatedKey);

    $sections = filled($customBody)
        ? collect(preg_split('/^##\s+/m', trim((string) $customBody)))
            ->filter()
            ->map(function (string $chunk) {
                $lines = explode("\n", trim($chunk), 2);

                return [
                    'heading' => trim($lines[0] ?? 'Ketentuan'),
                    'body' => trim($lines[1] ?? ''),
                ];
            })
            ->values()
            ->all()
        : $fallback;
@endphp

<x-site.layout
    :title="$title"
    :description="\Illuminate\Support\Str::limit(strip_tags($sections[0]['body'] ?? ''), 155)"
    :schema="[\App\Support\JsonLd::breadcrumbs($crumbs)]"
    :canonical="route(request()->route()->getName())"
    robots="index, follow"
>
    <x-site.page-hero :crumbs="$crumbs" eyebrow="Informasi Hukum" :title="$title" />

    <x-site.section tone="light" labelled-by="isi-heading" spacing="md">
        <x-site.container>
            <div class="mx-auto max-w-3xl">
                <p class="text-sm text-zinc-500">
                    Terakhir diperbarui:
                    <time datetime="{{ filled($updatedAt) ? \Illuminate\Support\Carbon::parse($updatedAt)->toDateString() : now()->toDateString() }}">
                        {{ filled($updatedAt) ? \Illuminate\Support\Carbon::parse($updatedAt)->translatedFormat('j F Y') : now()->translatedFormat('j F Y') }}
                    </time>
                </p>

                <h2 id="isi-heading" class="mt-6 text-xl font-bold tracking-tight text-night-950 sm:text-2xl">
                    Ringkasan
                </h2>

                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Dokumen ini menjelaskan {{ \Illuminate\Support\Str::lower($title) }} yang berlaku saat Anda menggunakan situs
                    dan layanan {{ site('company_name', config('app.name')) }}. Dengan mengakses situs ini, Anda dianggap telah
                    membaca dan menyetujuinya.
                </p>

                <nav aria-label="Daftar isi" class="mt-8 rounded-2xl bg-night-50/70 p-6 ring-1 ring-night-900/10">
                    <h2 class="text-sm font-bold tracking-[0.14em] text-solar-700 uppercase">Daftar isi</h2>

                    <ol class="mt-4 grid gap-2 text-sm">
                        @foreach ($sections as $index => $section)
                            <li>
                                <a href="#bagian-{{ $index + 1 }}" class="text-night-800 underline decoration-solar-300 decoration-2 underline-offset-2 hover:text-solar-700">
                                    {{ $index + 1 }}. {{ $section['heading'] }}
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </nav>

                <div class="mt-10 grid gap-9">
                    @foreach ($sections as $index => $section)
                        <section aria-labelledby="bagian-{{ $index + 1 }}">
                            <h3
                                id="bagian-{{ $index + 1 }}"
                                class="scroll-mt-24 text-lg font-bold tracking-tight text-night-950"
                            >
                                {{ $index + 1 }}. {{ $section['heading'] }}
                            </h3>

                            <div class="prose prose-solar mt-3 max-w-none text-base leading-relaxed">
                                {!! \Illuminate\Support\Str::markdown($section['body']) !!}
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="mt-12 rounded-2xl bg-night-950 p-6 text-white">
                    <p class="text-sm leading-relaxed text-night-100">
                        Ada bagian yang kurang jelas? Hubungi kami melalui
                        <a href="{{ route('contact') }}" class="font-semibold text-solar-300 underline decoration-solar-500 underline-offset-2">halaman kontak</a>
                        dan kami akan menjelaskannya.
                    </p>
                </div>
            </div>
        </x-site.container>
    </x-site.section>
</x-site.layout>
