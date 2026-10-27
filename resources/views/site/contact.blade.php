@php
    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Kontak'],
    ];

    $whatsapp = site('whatsapp');
    $mapEmbed = site('map_embed_url');

    $channels = collect([
        ['icon' => 'whatsapp', 'label' => 'WhatsApp', 'value' => $whatsapp, 'href' => filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : null, 'note' => 'Respons tercepat, jam kerja maupun di luarnya'],
        ['icon' => 'mail', 'label' => 'Email', 'value' => site('email'), 'href' => filled(site('email')) ? 'mailto:'.site('email') : null, 'note' => 'Untuk dokumen, gambar atap, dan permintaan penawaran'],
        ['icon' => 'phone', 'label' => 'Telepon', 'value' => site('phone'), 'href' => filled(site('phone')) ? 'tel:'.preg_replace('/\s+/', '', site('phone')) : null, 'note' => site('business_hours', 'Senin – Sabtu, 08.00 – 17.00 WIB')],
        ['icon' => 'pin', 'label' => 'Kantor', 'value' => trim(implode(', ', array_filter([site('address'), site('city'), site('region'), site('postal_code')]))), 'href' => null, 'note' => 'Kunjungan disarankan dengan janji temu'],
    ])->filter(fn (array $channel) => filled($channel['value']))->values();
@endphp

<x-site.layout
    :title="site('contact_meta_title', 'Kontak & Konsultasi Gratis')"
    :description="site('contact_meta_description', 'Hubungi tim kami untuk konsultasi gratis seputar pemasangan panel surya dan PLTS di seluruh Indonesia.')"
    :schema="[
        \App\Support\JsonLd::breadcrumbs($crumbs),
        \App\Support\JsonLd::organization(),
    ]"
    :canonical="route('contact')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Kontak"
        title="Mari bicarakan kebutuhan energi surya Anda"
        description="Ceritakan kondisi lokasi dan pemakaian listrik Anda. Kami balas dengan perhitungan awal yang bisa Anda jadikan bahan pertimbangan."
    />

    <x-site.section tone="light" labelled-by="formulir-heading" spacing="md">
        <x-site.container>
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">
                {{-- Form --}}
                <div class="lg:col-span-7">
                    <h2 id="formulir-heading" class="sr-only">Formulir kontak</h2>

                    <livewire:pages::site.contact-form />
                </div>

                {{-- Channels --}}
                <div class="lg:col-span-5">
                    <div class="grid gap-5">
                        @foreach ($channels as $channel)
                            <div class="reveal flex gap-4 border border-hairline bg-white p-5" style="--reveal-delay: {{ $loop->index * 70 }}ms">
                                <span class="grid size-11 shrink-0 place-items-center bg-coal text-solar-400" aria-hidden="true">
                                    <x-site.icon :name="$channel['icon']" class="size-5" stroke="1.9" />
                                </span>

                                <div class="min-w-0">
                                    <h3 class="text-xs font-semibold tracking-[0.2em] text-ink/55 uppercase">
                                        {{ $channel['label'] }}
                                    </h3>

                                    @if ($channel['href'])
                                        <a
                                            href="{{ $channel['href'] }}"
                                            @if (\Illuminate\Support\Str::startsWith($channel['href'], 'http')) target="_blank" rel="noopener noreferrer" @endif
                                            class="mt-1 block truncate text-sm font-semibold text-ink underline decoration-solar-400 decoration-2 underline-offset-2 hover:text-solar-700"
                                        >
                                            {{ $channel['value'] }}
                                        </a>
                                    @else
                                        <p class="mt-1 text-sm leading-relaxed font-semibold text-ink">{{ $channel['value'] }}</p>
                                    @endif

                                    @if (filled($channel['note']))
                                        <p class="mt-1.5 text-xs leading-relaxed text-ink/65">{{ $channel['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="reveal border border-white/10 bg-coal p-6 text-white">
                            <h3 class="font-display text-lg tracking-tight">Apa yang perlu disiapkan?</h3>

                            <ul class="mt-4 grid gap-2.5 text-sm text-white/70">
                                @foreach ([
                                    'Foto atau tagihan listrik 1–3 bulan terakhir',
                                    'Perkiraan luas dan jenis atap yang tersedia',
                                    'Lokasi lengkap untuk memperkirakan intensitas matahari',
                                    'Target penghematan atau kebutuhan cadangan listrik',
                                ] as $item)
                                    <li class="flex items-start gap-2.5">
                                        <x-site.icon name="check-circle" class="mt-0.5 size-4.5 shrink-0 text-solar-400" stroke="2" />
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </x-site.container>
    </x-site.section>

    {{-- Map --}}
    @if (filled($mapEmbed))
        <x-site.section tone="tint" labelled-by="peta-heading" spacing="sm">
            <x-site.container>
                <h2 id="peta-heading" class="font-display text-xl tracking-tight text-ink">Lokasi kantor kami</h2>

                <div class="mt-5 overflow-hidden border border-hairline">
                    <iframe
                        src="{{ $mapEmbed }}"
                        title="Peta lokasi kantor {{ site('company_name', config('app.name')) }}"
                        class="h-80 w-full border-0 sm:h-96"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
            </x-site.container>
        </x-site.section>
    @endif
</x-site.layout>
