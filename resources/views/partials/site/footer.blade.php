@php
    $company = site('company_name', config('app.name'));
    $whatsapp = site('whatsapp');
    $phone = site('phone');

    $companyLinks = [
        ['label' => 'Tentang Kami', 'route' => 'about'],
        ['label' => 'Layanan', 'route' => 'services.index'],
        ['label' => 'Proyek', 'route' => 'projects.index'],
        ['label' => 'Blog', 'route' => 'blog.index'],
        ['label' => 'Kontak', 'route' => 'contact'],
    ];

    $socialLabels = ['instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'facebook' => 'Facebook'];
@endphp

<footer class="bg-night-950 text-white">
    <x-site.container>
        <div class="grid gap-14 py-20 lg:grid-cols-12 lg:gap-10 lg:py-24">
            <div class="lg:col-span-5">
                <a
                    href="{{ route('home') }}"
                    class="text-4xl font-extrabold tracking-tight text-white focus-visible:outline-2 focus-visible:outline-offset-8 focus-visible:outline-solar-400 sm:text-5xl"
                >
                    {{ $company }}
                </a>

                <p class="mt-6 max-w-sm text-base leading-relaxed text-white/70">
                    Solusi energi surya untuk masa depan Indonesia yang lebih mandiri dan berkelanjutan.
                </p>

                <div class="mt-8 max-w-sm">
                    <h2 class="text-xs font-semibold tracking-[0.22em] text-white/50 uppercase">Kabar &amp; Tips Energi</h2>
                    <div class="mt-4">
                        <livewire:pages::site.newsletter-form />
                    </div>
                </div>
            </div>

            <nav aria-labelledby="footer-perusahaan" class="lg:col-span-3 lg:col-start-7">
                <h2 id="footer-perusahaan" class="text-xs font-semibold tracking-[0.22em] text-white/50 uppercase">Perusahaan</h2>

                <ul class="mt-6 grid gap-3 text-base">
                    @foreach ($companyLinks as $link)
                        <li>
                            <a href="{{ route($link['route']) }}" class="text-white/80 transition-colors hover:text-solar-400">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <h2 class="text-xs font-semibold tracking-[0.22em] text-white/50 uppercase">Kontak</h2>

                <address class="mt-6 grid gap-3 text-base not-italic text-white/80">
                    <span>BSD Ruko Boulevard, Jalan Raya Taman Tekno Serpong Blok AA No.7, Ciater, Serpong Sub-District, South Tangerang City, Banten 15323</span>

                    @if (filled(site('email')))
                        <a href="mailto:{{ site('email') }}" class="transition-colors hover:text-solar-400">{{ site('email') }}</a>
                    @endif

                    @if (filled($phone))
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="transition-colors hover:text-solar-400">{{ $phone }}</a>
                    @endif
                </address>

                <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    @foreach ($socials ?? [] as $network => $url)
                        @if (isset($socialLabels[$network]))
                            <li>
                                <a
                                    href="{{ $url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-semibold text-white/80 underline decoration-white/30 underline-offset-4 transition-colors hover:text-solar-400 hover:decoration-solar-400"
                                >
                                    {{ $socialLabels[$network] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="flex flex-col justify-between gap-4 border-t border-white/10 py-6 text-xs text-white/50 sm:flex-row sm:items-center">
            <p>&copy; {{ now()->year }} {{ site('company_legal_name', $company) }}. Seluruh hak cipta dilindungi.</p>

            <ul class="flex flex-wrap gap-x-6 gap-y-2">
                <li><a href="{{ route('privacy') }}" class="transition-colors hover:text-white">Kebijakan Privasi</a></li>
                <li><a href="{{ route('terms') }}" class="transition-colors hover:text-white">Syarat &amp; Ketentuan</a></li>
                <li><a href="{{ route('sitemap') }}" class="transition-colors hover:text-white">Peta Situs</a></li>
            </ul>
        </div>
    </x-site.container>
</footer>
