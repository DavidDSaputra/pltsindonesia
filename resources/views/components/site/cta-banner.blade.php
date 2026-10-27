@props([
    'title' => null,
    'description' => null,
])

@php
    $title ??= site('cta_title', 'Siap beralih ke energi surya?');
    $description ??= site(
        'cta_description',
        'Konsultasikan kebutuhan listrik Anda secara gratis. Kami hitungkan potensi penghematan dan estimasi investasi untuk atap Anda.'
    );

    $whatsapp = site('whatsapp');
    $ctaUrl = filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : route('contact');
    $ctaExternal = filled($whatsapp);
@endphp

{{-- Reference-style closing band: a full-bleed parallax photo under a dark veil,
     with a centred display headline and the skewed light CTA. --}}
<section aria-labelledby="cta-heading" {{ $attributes->class('relative overflow-hidden bg-coal text-white') }}>
    <div class="parallax-frame absolute! inset-0" aria-hidden="true">
        <div data-parallax>
            <x-site.image
                :src="site('cta_image', '/images/photos/field.jpg')"
                alt=""
                ratio=""
                class="h-full w-full"
                width="1600"
                height="900"
            />
        </div>
    </div>

    <div class="absolute inset-0 bg-coal/80" aria-hidden="true"></div>

    <x-site.container class="relative">
        <div class="mx-auto max-w-3xl py-20 text-center sm:py-28">
            <x-site.eyebrow icon="bolt" tone="dark" class="reveal justify-center">
                {{ site('cta_eyebrow', 'Konsultasi Gratis') }}
            </x-site.eyebrow>

            <h2
                id="cta-heading"
                class="reveal mt-5 font-display text-4xl tracking-tight text-balance text-white sm:text-5xl lg:text-6xl"
                style="--reveal-delay: 80ms"
            >
                {{ $title }}
            </h2>

            <p class="reveal mx-auto mt-5 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg" style="--reveal-delay: 160ms">
                {{ $description }}
            </p>

            <div class="reveal mt-9 flex flex-wrap items-center justify-center gap-4" style="--reveal-delay: 240ms">
                <x-site.button
                    :href="$ctaUrl"
                    :external="$ctaExternal"
                    variant="skew-light"
                    size="lg"
                    icon="whatsapp"
                >
                    Hubungi Tim Kami
                </x-site.button>

                <x-site.button :href="route('contact')" variant="outline-light" size="lg" icon-right="arrow-right">
                    Minta Penawaran
                </x-site.button>
            </div>

            <ul class="reveal mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-sm text-white/70" style="--reveal-delay: 320ms">
                @foreach (explode('|', (string) site('cta_points', 'Survei lokasi gratis|Perhitungan ROI terperinci|Garansi pemasangan 2 tahun')) as $point)
                    @if (filled($point))
                        <li class="flex items-center gap-2.5">
                            <x-site.icon name="check-circle" class="size-4.5 shrink-0 text-solar-400" stroke="2" />
                            <span>{{ trim($point) }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </x-site.container>
</section>
