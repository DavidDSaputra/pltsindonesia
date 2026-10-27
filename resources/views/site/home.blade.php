@php
    $company = site('company_name', config('app.name'));
    $whatsapp = site('whatsapp');
    $ctaUrl = filled($whatsapp) ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : route('contact');
    $ctaExternal = filled($whatsapp);

    $heroStats = [
        ['value' => 500, 'suffix' => '+', 'label' => 'Proyek PLTS'],
        ['value' => 10, 'suffix' => '+', 'label' => 'Tahun Pengalaman'],
    ];

    $values = [
        ['icon' => 'chart', 'title' => 'Efisiensi Energi', 'text' => 'Sistem dihitung dari profil beban Anda, bukan ukuran standar.'],
        ['icon' => 'shield', 'title' => 'Teknologi Terpercaya', 'text' => 'Modul, inverter, dan proteksi dari produsen dengan rekam jejak teruji.'],
        ['icon' => 'wrench', 'title' => 'Instalasi Profesional', 'text' => 'Dikerjakan tim teknis bersertifikat dengan prosedur K3.'],
        ['icon' => 'bolt', 'title' => 'Monitoring Berkelanjutan', 'text' => 'Produksi energi dipantau agar performa tetap sesuai desain.'],
        ['icon' => 'users', 'title' => 'Dukungan Purna Jual', 'text' => 'Garansi, pemeliharaan, dan tim yang tetap bisa dihubungi setelah serah terima.'],
    ];

    $services = [
        ['no' => '01', 'title' => 'PLTS Residensial', 'text' => 'Solusi tenaga surya untuk kebutuhan listrik rumah dengan sistem yang dirancang sesuai konsumsi energi.', 'image' => '/images/photos/grass.jpg', 'span' => 'lg:col-span-7', 'ratio' => 'aspect-[4/3] lg:aspect-[16/10]'],
        ['no' => '02', 'title' => 'PLTS Komersial', 'text' => 'Optimalkan biaya operasional bisnis melalui sistem energi surya yang efisien.', 'image' => '/images/photos/panels.jpg', 'span' => 'lg:col-span-5', 'ratio' => 'aspect-[4/3] lg:aspect-[4/4.4]'],
        ['no' => '03', 'title' => 'PLTS Industri', 'text' => 'Solusi PLTS berskala besar untuk fasilitas industri dan kebutuhan energi berkapasitas tinggi.', 'image' => '/images/photos/hero.jpg', 'span' => 'lg:col-span-5', 'ratio' => 'aspect-[4/3] lg:aspect-[4/4.4]'],
        ['no' => '04', 'title' => 'Engineering & Installation', 'text' => 'Perencanaan teknis dan instalasi sistem PLTS oleh tenaga profesional.', 'image' => '/images/photos/hands.jpg', 'span' => 'lg:col-span-7', 'ratio' => 'aspect-[4/3] lg:aspect-[16/10]'],
        ['no' => '05', 'title' => 'Maintenance & Monitoring', 'text' => 'Pemeliharaan dan pemantauan performa sistem untuk menjaga efisiensi jangka panjang.', 'image' => '/images/photos/detail.jpg', 'span' => 'lg:col-span-7', 'ratio' => 'aspect-[4/3] lg:aspect-[16/10]'],
        ['no' => '06', 'title' => 'Energy Consultation', 'text' => 'Konsultasi kebutuhan energi untuk menentukan sistem PLTS yang paling sesuai.', 'image' => '/images/photos/grid.jpg', 'span' => 'lg:col-span-5', 'ratio' => 'aspect-[4/3] lg:aspect-[4/3.1]'],
    ];

    $benefits = [
        'Penghematan Biaya Listrik',
        'Mengurangi Ketergantungan Energi Konvensional',
        'Investasi Jangka Panjang',
        'Energi Bersih dan Berkelanjutan',
        'Meningkatkan Efisiensi Operasional',
    ];

    $benefitStats = [
        ['value' => 15, 'suffix' => '+', 'label' => 'MW Kapasitas Terpasang'],
        ['value' => 500, 'suffix' => '+', 'label' => 'Proyek'],
        ['value' => 25, 'suffix' => '+', 'label' => 'Tahun Potensi Masa Pakai Sistem'],
        ['value' => 30, 'suffix' => '+', 'label' => 'Kota Terlayani'],
    ];

    $steps = [
        ['no' => '01', 'title' => 'Konsultasi', 'text' => 'Kami memahami kebutuhan energi dan kondisi lokasi Anda.', 'image' => '/images/photos/trackers.jpg'],
        ['no' => '02', 'title' => 'Survey & Perencanaan', 'text' => 'Tim melakukan analisis lokasi, kebutuhan daya, dan potensi energi matahari.', 'image' => '/images/photos/aerial.jpg'],
        ['no' => '03', 'title' => 'Instalasi', 'text' => 'Sistem PLTS dipasang oleh tim teknis profesional dengan standar keselamatan yang sesuai.', 'image' => '/images/photos/installer.jpg'],
        ['no' => '04', 'title' => 'Monitoring & Support', 'text' => 'Kami memastikan sistem tetap bekerja optimal melalui monitoring dan dukungan purna jual.', 'image' => '/images/photos/grid.jpg'],
    ];

    $projects = [
        ['title' => 'PLTS Pabrik Manufaktur', 'place' => 'Karawang, Jawa Barat', 'size' => '500 kWp', 'image' => '/images/photos/field.jpg', 'class' => 'lg:col-span-8', 'ratio' => 'aspect-[4/3] lg:aspect-[16/10]'],
        ['title' => 'PLTS Gedung Komersial', 'place' => 'Jakarta', 'size' => '250 kWp', 'image' => '/images/photos/tall.jpg', 'class' => 'lg:col-span-4 lg:mt-24', 'ratio' => 'aspect-[4/3] lg:aspect-[3/4]'],
        ['title' => 'PLTS Gudang Logistik', 'place' => 'Bekasi', 'size' => '350 kWp', 'image' => '/images/photos/hero.jpg', 'class' => 'lg:col-span-5', 'ratio' => 'aspect-[4/3] lg:aspect-[4/3.4]'],
        ['title' => 'PLTS Residensial', 'place' => 'Tangerang', 'size' => '10 kWp', 'image' => '/images/photos/installer.jpg', 'class' => 'lg:col-span-7 lg:-mt-10', 'ratio' => 'aspect-[4/3] lg:aspect-[16/10]'],
    ];

    $testimonials = [
        ['quote' => 'PLTS membantu kami mengurangi biaya energi sekaligus meningkatkan efisiensi operasional fasilitas.', 'by' => 'Operations Manager', 'org' => 'Manufacturing Company'],
        ['quote' => 'Proses dari survey hingga instalasi berjalan sangat profesional dan komunikatif.', 'by' => 'Property Owner', 'org' => 'Jakarta'],
    ];

    $articles = [
        ['title' => 'Berapa Potensi Penghematan dari PLTS Atap?', 'category' => 'Energi Surya', 'image' => '/images/photos/grass.jpg'],
        ['title' => 'Panduan Memilih Sistem PLTS untuk Bisnis', 'category' => 'PLTS', 'image' => '/images/photos/panels.jpg'],
        ['title' => 'PLTS untuk Industri: Investasi atau Penghematan?', 'category' => 'Industri', 'image' => '/images/photos/hero.jpg'],
    ];

@endphp

<x-site.layout :schema="$schema ?? []">
    {{-- 1. Hero: sky photo, PLTS wordmark sitting behind the hills, equipment standing
         in front of them, copy and glass stat cards on the ground. Entrance order:
         wordmark -> equipment -> copy -> stats (rolling digits) -> progress bar. --}}
    <section id="home-hero" aria-labelledby="hero-heading" class="home-hero home-rail-target relative isolate flex min-h-[max(44rem,100svh)] flex-col justify-end overflow-hidden bg-[#2d89e6]">
        <img src="/images/hero/sky.jpg" alt="" class="absolute inset-0 size-full object-cover object-top" fetchpriority="high" width="1672" height="941" />

        {{-- Faint vertical guide lines across the sky. --}}
        <div class="hero-lines absolute inset-x-0 top-0 h-[39svh]" aria-hidden="true"></div>

        {{-- Stage: sky is full-bleed, but wordmark, hills and panels share one box on desktop
             so the metal feet always land on the same patch of grass. --}}
        <div class="home-hero-stage absolute inset-x-0 top-0 bottom-0 lg:top-auto lg:left-1/2 lg:right-auto lg:aspect-[2172/724] lg:w-[108vw] lg:-translate-x-1/2" aria-hidden="true">
            {{-- Wordmark (behind the hills) --}}
            <div class="home-hero-wordmark hero-wordmark pointer-events-none absolute inset-x-0 top-[17svh] z-[1] flex justify-center sm:top-[19svh] lg:top-auto lg:bottom-[57%]" data-drift>
                <span class="flex font-extrabold leading-[0.82] tracking-[-0.04em] text-white text-[clamp(6.5rem,17vw,17rem)]">
                    @foreach (str_split('PLTS') as $i => $letter)
                        <span class="hero-letter" style="--i: {{ $i }}">{{ $letter }}</span>
                    @endforeach
                </span>
            </div>

            {{-- Hills --}}
            <img
                src="/images/hero/hills.png"
                alt=""
                class="absolute inset-x-0 bottom-0 z-[2] h-[105%] w-full object-cover object-top sm:h-[72svh] lg:h-full"
                width="2172"
                height="724"
            />
            <div class="absolute inset-x-0 bottom-0 z-[3] h-[42svh] bg-linear-to-b from-transparent to-black/35 lg:h-1/2" aria-hidden="true"></div>

            {{-- One centered solar panel placed over the grass. --}}
            <div class="home-hero-panel pointer-events-none absolute z-[4]">
                <img
                    src="/images/hero/panel-center.png"
                    alt=""
                    class="fx block h-auto w-full max-w-none"
                    style="--fx-delay: 400ms; --fx-dur: 1.6s"
                    width="816"
                    height="451"
                />
            </div>
        </div>

        {{-- Copy + stats --}}
        <x-site.container class="home-hero-content relative z-[6] pb-8 pt-[28rem] sm:pb-12 sm:pt-[30rem] lg:pt-40">
            <div class="grid items-end gap-8 lg:grid-cols-2 lg:gap-10">
                <div class="home-hero-copy fx" style="--fx-delay: 3300ms; --fx-dur: 1.4s">
                    <h1 id="hero-heading" class="max-w-lg font-display text-[clamp(1.9rem,2.7vw,2.5rem)] text-white [text-shadow:0_2px_20px_rgb(0_0_0/0.5)]">
                        PLTSINDONESIA menghadirkan solusi pembangkit listrik tenaga surya yang efisien, terpercaya, dan dirancang untuk kebutuhan rumah, bisnis, hingga industri.
                    </h1>

                    <div class="mt-6 flex flex-wrap items-center gap-3 max-sm:flex-col max-sm:items-stretch">
                        <a href="{{ $ctaUrl }}" @if ($ctaExternal) target="_blank" rel="noopener noreferrer" @endif class="nav-cta nav-cta-white inline-flex max-sm:justify-center">
                            <span class="nav-cta-text">Konsultasi Sekarang</span>
                            <span class="nav-cta-overlay" aria-hidden="true"></span>
                        </a>
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center rounded-full border border-white/40 bg-black/10 px-6 py-2 text-base font-medium text-white backdrop-blur-sm transition-colors duration-300 hover:bg-white hover:text-night-950 max-sm:justify-center">
                            Lihat Proyek
                        </a>
                    </div>
                </div>

                <dl class="home-hero-stats grid grid-cols-2 gap-2 lg:justify-self-end">
                    @foreach ($heroStats as $stat)
                        <div class="fx hero-stat flex min-w-0 flex-col gap-0.5 rounded-lg border border-white/12 bg-white/16 p-3.5 text-white backdrop-blur-[24px] sm:min-w-[10.5rem] sm:p-5" style="--fx-delay: {{ 3700 + $loop->index * 180 }}ms; --fx-dur: 1.3s">
                            <dt class="text-[0.8rem] font-medium tracking-[0.08em] uppercase">{{ $stat['label'] }}</dt>
                            <dd class="mt-1 flex items-end gap-0.5 text-3xl font-bold tracking-tight tabular-nums sm:text-4xl" aria-label="{{ $stat['value'] }}{{ $stat['suffix'] }}">
                                <span class="flex" aria-hidden="true">
                                    @foreach (str_split((string) $stat['value']) as $d => $digit)
                                        <span class="odo" style="--d: {{ $digit }}; --n: {{ $loop->parent->index * 2 + $d }}">
                                            <span class="odo-col">@for ($n = 0; $n <= 9; $n++)<span>{{ $n }}</span>@endfor</span>
                                        </span>
                                    @endforeach
                                </span>
                                <span class="text-solar-400" aria-hidden="true">{{ $stat['suffix'] }}</span>
                            </dd>
                            @if ($loop->index === 2)
                                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-solar-500/30" aria-hidden="true">
                                    <div class="hero-progress h-full rounded-full bg-solar-500"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </dl>
            </div>
        </x-site.container>
    </section>

    {{-- 2. Solar showcase: a draggable, infinitely looping image marquee. --}}
    <section id="home-showcase" aria-labelledby="solar-showcase-heading" class="home-rail-target solar-showcase-section">
        <x-site.container>
            <div class="solar-showcase-intro">
                <div>
                    <x-site.eyebrow>Energi yang terlihat nyata</x-site.eyebrow>
                    <h2 id="solar-showcase-heading" class="solar-showcase-title">
                        Dari cahaya matahari menjadi energi untuk setiap aktivitas.
                    </h2>
                </div>
                <p class="solar-showcase-copy">
                    Kami merancang sistem PLTS yang rapi, efisien, dan siap bekerja untuk rumah, bisnis, hingga industri.
                </p>
            </div>

            <div class="solar-showcase">
                <div
                    class="solar-showcase-viewport"
                    data-solar-showcase
                    data-draggable-marquee
                    tabindex="0"
                    role="region"
                    aria-label="Showcase gambar PLTS. Geser atau gunakan tombol panah kiri dan kanan."
                >
                    <div class="solar-showcase-track" data-marquee-track>
                        <a href="{{ route('gallery') }}" class="solar-showcase-card solar-showcase-card-left" data-marquee-item>
                            <img src="/images/showcase/solar-panel.png" alt="Panel surya untuk sistem PLTS" loading="lazy" draggable="false" />
                            <span class="solar-showcase-card-label">Panel surya</span>
                        </a>
                        <a href="{{ route('projects.index') }}" class="solar-showcase-card solar-showcase-card-center" data-marquee-item>
                            <img src="/images/showcase/skyworth-inverter.png" alt="Inverter Skyworth untuk sistem energi surya" loading="lazy" draggable="false" />
                            <span class="solar-showcase-card-label">Inverter pintar</span>
                        </a>
                        <a href="{{ route('services.index') }}" class="solar-showcase-card solar-showcase-card-right" data-marquee-item>
                            <img src="/images/showcase/pytes-battery.png" alt="Baterai penyimpanan energi Pytes" loading="lazy" draggable="false" />
                            <span class="solar-showcase-card-label">Baterai energi</span>
                        </a>
                        <a href="{{ route('contact') }}" class="solar-showcase-card solar-showcase-card-right" data-marquee-item>
                            <img src="/images/showcase/solar-streetlight.png" alt="Lampu jalan tenaga surya" loading="lazy" draggable="false" />
                            <span class="solar-showcase-card-label">Penerangan surya</span>
                        </a>
                    </div>
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- 3. PLTS estimator: a transparent first-look calculation before a survey. --}}
    <section id="home-calculator" aria-labelledby="calculator-heading" class="bg-night-950 py-24 text-white lg:py-32" x-data="pltsCalculator()">
        <x-site.container>
            <div class="grid gap-12 lg:grid-cols-12 lg:items-start lg:gap-16">
                <div class="lg:col-span-5">
                    <x-site.eyebrow tone="dark">Simulasi Awal</x-site.eyebrow>
                    <h2 id="calculator-heading" class="mt-6 font-display text-4xl sm:text-5xl lg:text-6xl">
                        Hitung perkiraan kebutuhan PLTS Anda.
                    </h2>
                    <p class="mt-6 max-w-lg text-lg leading-relaxed text-white/70">
                        Masukkan tagihan listrik bulanan untuk mendapatkan gambaran kapasitas panel, kebutuhan area atap, potensi penghematan, dan kisaran investasi.
                    </p>
                    <p class="mt-5 max-w-lg text-sm leading-relaxed text-white/50">
                        Hasil ini adalah estimasi awal berbasis asumsi umum di Indonesia. Kapasitas final dipengaruhi profil beban, luas atap, arah panel, bayangan, dan hasil survei teknis.
                    </p>
                </div>

                <div class="lg:col-span-7">
                    <div class="border border-white/15 bg-white/7 p-6 sm:p-8">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="calculator-bill" class="block text-sm font-semibold text-white/85">Rata-rata tagihan listrik per bulan</label>
                                <div class="relative mt-2">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-white/45">Rp</span>
                                    <input id="calculator-bill" type="number" min="0" step="50000" x-model.number="monthlyBill" class="w-full rounded-none border border-white/20 bg-white/8 py-3 pl-11 pr-4 text-white placeholder:text-white/35 focus:border-solar-400 focus:ring-solar-400" inputmode="numeric" />
                                </div>
                            </div>

                            <div>
                                <label for="calculator-system" class="block text-sm font-semibold text-white/85">Jenis sistem</label>
                                <select id="calculator-system" x-model="system" class="mt-2 w-full rounded-none border border-white/20 bg-night-900 py-3 px-4 text-white focus:border-solar-400 focus:ring-solar-400">
                                    <option value="on-grid">PLTS On-Grid</option>
                                    <option value="hybrid">PLTS Hybrid + Baterai</option>
                                    <option value="off-grid">PLTS Off-Grid</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-2 gap-px overflow-hidden border border-white/10 bg-white/10 sm:grid-cols-4" aria-live="polite">
                            <div class="bg-night-950 p-4 sm:p-5">
                                <p class="text-xs uppercase tracking-[0.14em] text-white/45">Kapasitas</p>
                                <p class="mt-2 text-2xl font-bold tracking-tight text-solar-300"><span x-text="number(capacity, 1)"></span> kWp</p>
                            </div>
                            <div class="bg-night-950 p-4 sm:p-5">
                                <p class="text-xs uppercase tracking-[0.14em] text-white/45">Panel</p>
                                <p class="mt-2 text-2xl font-bold tracking-tight"><span x-text="number(panelCount, 0)"></span> unit</p>
                            </div>
                            <div class="bg-night-950 p-4 sm:p-5">
                                <p class="text-xs uppercase tracking-[0.14em] text-white/45">Atap minimum</p>
                                <p class="mt-2 text-2xl font-bold tracking-tight"><span x-text="number(roofArea, 0)"></span> m²</p>
                            </div>
                            <div class="bg-night-950 p-4 sm:p-5">
                                <p class="text-xs uppercase tracking-[0.14em] text-white/45">Hemat / bulan</p>
                                <p class="mt-2 text-2xl font-bold tracking-tight text-solar-300" x-text="currency(monthlySavings)"></p>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col gap-5 border-t border-white/12 pt-6 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm text-white/55">Perkiraan investasi</p>
                                <p class="mt-1 text-2xl font-bold" x-text="currency(investment)"></p>
                                <p class="mt-1 text-xs text-white/45">Estimasi balik modal ± <span x-text="number(paybackYears, 1)"></span> tahun</p>
                            </div>
                            <a href="{{ route('contact') }}" class="inline-flex shrink-0 items-center justify-center rounded-full bg-solar-400 px-6 py-3 text-sm font-bold text-night-950 transition hover:bg-solar-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-300">
                                Konsultasikan hasilnya
                                <span class="ml-2" aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- 4. About: asymmetric text column with two overlapping photographs. --}}
    <section id="home-about" aria-labelledby="about-heading" class="home-rail-target bg-paper py-24 lg:py-32">
        <x-site.container>
            <div class="grid items-start gap-14 lg:grid-cols-12 lg:gap-10">
                <div class="lg:col-span-6 lg:pt-8">
                    <x-site.eyebrow class="reveal">About PLTSINDONESIA</x-site.eyebrow>

                    <h2 id="about-heading" class="reveal mt-6 text-4xl text-night-900 sm:text-5xl lg:text-6xl font-display" style="--reveal-delay: 80ms">
                        Energi Surya Bukan Sekadar Pilihan Hijau
                    </h2>

                    <p class="reveal mt-8 max-w-lg text-lg leading-relaxed text-ink/75" style="--reveal-delay: 160ms">
                        PLTSINDONESIA membantu pelanggan mengubah energi matahari menjadi sumber listrik yang lebih efisien, ekonomis, dan berkelanjutan.
                    </p>

                    <ul class="reveal mt-10 grid max-w-lg gap-4 border-t border-hairline pt-8 text-ink/80 sm:grid-cols-2" style="--reveal-delay: 220ms">
                        @foreach (['Inspeksi teknis di lokasi', 'Instalasi atap & ground-mounted', 'Desain sesuai profil beban', 'Monitoring performa sistem'] as $point)
                            <li class="flex items-start gap-3 text-sm font-medium">
                                <x-site.icon name="check" class="mt-0.5 size-4 shrink-0 text-night-600" stroke="2.4" />
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="reveal mt-10" style="--reveal-delay: 280ms">
                        <x-site.button :href="route('about')" variant="skew" icon-right="arrow-right">Tentang Kami</x-site.button>
                    </div>
                </div>

                <div class="relative lg:col-span-6">
                    <div class="reveal-media group overflow-hidden lg:ml-auto lg:w-[88%]">
                        <x-site.image
                            src="/images/photos/installer.jpg"
                            alt="Teknisi memasang modul surya di atap rumah"
                            ratio="aspect-[4/5]"
                            img-class="transition-transform duration-700 ease-out group-hover:scale-105"
                            width="1200"
                            height="1500"
                        />
                    </div>

                    <div class="reveal-media absolute -bottom-10 left-0 hidden w-[46%] overflow-hidden border-8 border-paper sm:block" style="--reveal-delay: 160ms">
                        <x-site.image
                            src="/images/photos/hands.jpg"
                            alt="Sambungan kabel modul surya saat inspeksi teknis"
                            ratio="aspect-[4/3]"
                            width="800"
                            height="600"
                        />
                    </div>
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- 3. Marquee --}}
    <section aria-label="Semboyan PLTSINDONESIA" class="overflow-hidden border-y border-hairline bg-paper py-6 sm:py-8">
        <div class="marquee" aria-hidden="true">
            @foreach (range(1, 2) as $copy)
                <ul class="marquee-track">
                    @foreach (['Energi Bersih', 'Masa Depan Cerah', 'Solar Power', 'Indonesia Energy', 'Power Your Future'] as $word)
                        <li class="flex items-center gap-8 pr-8 font-display text-4xl uppercase text-night-900 sm:text-6xl">
                            {{ $word }}
                            <span class="size-3 shrink-0 rounded-full bg-solar-500"></span>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </section>

    {{-- 4. Promise: sticky heading beside a ruled list rather than cards. --}}
    <section aria-labelledby="promise-heading" class="bg-paper py-24 lg:py-32">
        <x-site.container>
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-10">
                <div class="lg:col-span-5">
                    <div class="lg:sticky lg:top-28">
                        <x-site.eyebrow class="reveal">Komitmen Kami</x-site.eyebrow>
                        <h2 id="promise-heading" class="reveal mt-6 font-display text-4xl text-night-900 sm:text-5xl" style="--reveal-delay: 80ms">
                            Solusi Energi yang Dibangun untuk Jangka Panjang
                        </h2>
                    </div>
                </div>

                <ol class="lg:col-span-7">
                    @foreach ($values as $value)
                        <li class="reveal group grid grid-cols-[3rem_1fr_auto] items-start gap-4 border-t border-night-900/15 py-7 last:border-b sm:grid-cols-[4rem_1fr_auto] sm:gap-6" style="--reveal-delay: {{ $loop->index * 60 }}ms">
                            <span class="pt-1 text-sm font-bold tabular-nums text-night-600">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-night-900 sm:text-2xl">{{ $value['title'] }}</h3>
                                <p class="mt-2 max-w-md text-sm leading-relaxed text-ink/70 sm:text-base">{{ $value['text'] }}</p>
                            </div>
                            <x-site.icon :name="$value['icon']" class="size-7 text-night-700 transition-transform duration-300 group-hover:-translate-y-0.5" stroke="1.6" />
                        </li>
                    @endforeach
                </ol>
            </div>
        </x-site.container>
    </section>

    {{-- 5. Services: image-driven tiles on a dark band, varied widths per row. --}}
    <section id="home-services" aria-labelledby="services-heading" class="home-rail-target bg-night-900 py-24 text-white lg:py-32">
        <x-site.container>
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <x-site.eyebrow tone="dark" class="reveal">Keahlian Kami</x-site.eyebrow>
                    <h2 id="services-heading" class="reveal mt-6 font-display text-4xl text-white sm:text-5xl lg:text-6xl" style="--reveal-delay: 80ms">
                        Solusi PLTS untuk Berbagai Kebutuhan
                    </h2>
                </div>
                <div class="reveal">
                    <x-site.button :href="route('services.index')" variant="skew-light" icon-right="arrow-right">Semua Layanan</x-site.button>
                </div>
            </div>

            <div class="mt-14 grid gap-x-8 gap-y-14 lg:grid-cols-12">
                @foreach ($services as $service)
                    <article class="reveal group {{ $service['span'] }}">
                        <a href="{{ route('services.index') }}" class="block focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-solar-400">
                            <div class="overflow-hidden">
                                <x-site.image
                                    :src="$service['image']"
                                    :alt="$service['title']"
                                    :ratio="$service['ratio']"
                                    img-class="transition-transform duration-700 ease-out group-hover:scale-105"
                                    width="1000"
                                    height="750"
                                />
                            </div>

                            <div class="mt-5 flex items-start gap-5">
                                <span class="pt-1.5 text-sm font-bold tabular-nums text-solar-400">{{ $service['no'] }}</span>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $service['title'] }}</h3>
                                    <p class="mt-2 max-w-md text-sm leading-relaxed text-white/70 sm:text-base">{{ $service['text'] }}</p>
                                </div>
                                <x-site.icon name="arrow-up-right" class="mt-2 size-6 shrink-0 text-white/50 transition-all duration-300 group-hover:translate-x-1 group-hover:-translate-y-1 group-hover:text-solar-400" stroke="2" />
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- 6. Benefits --}}
    <section id="home-benefits" aria-labelledby="benefits-heading" class="home-rail-target bg-paper pt-24 lg:pt-32">
        <x-site.container>
            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="reveal-media overflow-hidden lg:col-span-7">
                    <x-site.image
                        src="/images/photos/aerial.jpg"
                        alt="Tampak atas deretan panel surya di lahan terbuka"
                        ratio="aspect-[4/3] lg:aspect-[5/4]"
                        width="1400"
                        height="1050"
                    />
                </div>

                <div class="lg:col-span-5">
                    <x-site.eyebrow class="reveal">Manfaat</x-site.eyebrow>
                    <h2 id="benefits-heading" class="reveal mt-6 font-display text-4xl text-night-900 sm:text-5xl" style="--reveal-delay: 80ms">
                        Kenapa Beralih ke Energi Surya?
                    </h2>

                    <ul class="mt-8">
                        @foreach ($benefits as $benefit)
                            <li class="reveal flex items-center gap-4 border-t border-night-900/15 py-4 text-base font-semibold text-night-900 last:border-b sm:text-lg" style="--reveal-delay: {{ $loop->index * 50 }}ms">
                                <span class="grid size-6 shrink-0 place-items-center bg-solar-500 text-night-950">
                                    <x-site.icon name="check" class="size-3.5" stroke="3" />
                                </span>
                                {{ $benefit }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </x-site.container>

        <div class="mt-24 bg-night-950 py-14 text-white lg:mt-32 lg:py-20">
            <x-site.container>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4">
                    @foreach ($benefitStats as $stat)
                        <div @class(['lg:border-l lg:border-white/15 lg:pl-8' => ! $loop->first])>
                            <dd class="text-5xl font-extrabold tracking-tight tabular-nums sm:text-6xl">
                                <span data-count="{{ $stat['value'] }}">{{ $stat['value'] }}</span><span class="text-solar-400">{{ $stat['suffix'] }}</span>
                            </dd>
                            <dt class="mt-3 max-w-[14rem] text-sm leading-snug text-white/70">{{ $stat['label'] }}</dt>
                        </div>
                    @endforeach
                </dl>
            </x-site.container>
        </div>
    </section>

    {{-- 7. How it works: step list drives a crossfading image + copy panel. --}}
    <section
        id="home-process"
        aria-labelledby="process-heading"
        class="home-rail-target bg-tile py-24 lg:py-32"
        x-data="{ step: 0 }"
    >
        <x-site.container>
            <div class="max-w-3xl">
                <x-site.eyebrow class="reveal">Proses Kerja</x-site.eyebrow>
                <h2 id="process-heading" class="reveal mt-6 font-display text-4xl text-night-900 sm:text-5xl lg:text-6xl" style="--reveal-delay: 80ms">
                    Bagaimana PLTSINDONESIA Bekerja
                </h2>
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="relative order-1 overflow-hidden bg-night-950 lg:order-2 lg:col-span-7">
                    <div class="relative aspect-[4/3] lg:aspect-[5/4]">
                        @foreach ($steps as $i => $s)
                            <div
                                class="absolute inset-0 transition-all duration-700 ease-out"
                                :class="step === {{ $i }} ? 'opacity-100 scale-100' : 'pointer-events-none opacity-0 scale-105'"
                                @if ($i > 0) x-cloak @endif
                            >
                                <x-site.image :src="$s['image']" :alt="$s['title']" ratio="" class="h-full w-full" img-class="object-bottom" width="1200" height="960" />
                            </div>
                        @endforeach
                        <div class="absolute inset-x-0 bottom-0 h-1/3 bg-linear-to-t from-night-950/70 to-transparent" aria-hidden="true"></div>
                        <p class="absolute bottom-5 left-5 text-6xl font-extrabold tabular-nums text-white sm:text-7xl" aria-hidden="true">
                            @foreach ($steps as $i => $s)
                                <span x-show="step === {{ $i }}" @if ($i > 0) x-cloak @endif x-transition.opacity.duration.400ms>{{ $s['no'] }}</span>
                            @endforeach
                        </p>
                    </div>
                </div>

                <ol class="order-2 lg:order-1 lg:col-span-5">
                    @foreach ($steps as $i => $s)
                        <li class="border-t border-night-900/20 last:border-b">
                            <button
                                type="button"
                                class="group flex w-full items-start gap-5 py-6 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-solar-600"
                                @mouseenter="step = {{ $i }}"
                                @focus="step = {{ $i }}"
                                @click="step = {{ $i }}"
                                :aria-expanded="step === {{ $i }} ? 'true' : 'false'"
                                aria-controls="step-{{ $i }}"
                            >
                                <span class="pt-1.5 text-sm font-bold tabular-nums transition-colors" :class="step === {{ $i }} ? 'text-night-900' : 'text-night-900/40'">{{ $s['no'] }}</span>
                                <span class="flex-1">
                                    <span class="block text-2xl font-bold tracking-tight transition-colors sm:text-3xl" :class="step === {{ $i }} ? 'text-night-900' : 'text-night-900/40 group-hover:text-night-900/70'">
                                        {{ $s['title'] }}
                                    </span>
                                    <span
                                        id="step-{{ $i }}"
                                        class="grid transition-all duration-500 ease-out"
                                        :class="step === {{ $i }} ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                                    >
                                        <span class="overflow-hidden">
                                            <span class="mt-3 block max-w-sm text-base leading-relaxed text-ink/75">{{ $s['text'] }}</span>
                                        </span>
                                    </span>
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ol>
            </div>
        </x-site.container>
    </section>

    {{-- 8. Projects: editorial arrangement with staggered offsets. --}}
    <section id="home-projects" aria-labelledby="projects-heading" class="home-rail-target bg-paper py-24 lg:py-32">
        <x-site.container>
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <x-site.eyebrow class="reveal">Studi Kasus</x-site.eyebrow>
                    <h2 id="projects-heading" class="reveal mt-6 font-display text-4xl text-night-900 sm:text-5xl lg:text-6xl" style="--reveal-delay: 80ms">
                        Proyek yang Menghasilkan Dampak Nyata
                    </h2>
                </div>
                <div class="reveal">
                    <x-site.button :href="route('projects.index')" variant="skew" icon-right="arrow-right">Semua Proyek</x-site.button>
                </div>
            </div>

            <div class="mt-14 grid gap-x-8 gap-y-14 lg:grid-cols-12">
                @foreach ($projects as $project)
                    <article class="reveal group {{ $project['class'] }}">
                        <a href="{{ route('projects.index') }}" class="block focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-solar-600">
                            <div class="overflow-hidden">
                                <x-site.image
                                    :src="$project['image']"
                                    :alt="$project['title'].', '.$project['place']"
                                    :ratio="$project['ratio']"
                                    img-class="transition-transform duration-700 ease-out group-hover:scale-105"
                                    width="1200"
                                    height="900"
                                />
                            </div>

                            <div class="mt-5 flex items-start justify-between gap-6 border-b border-night-900/15 pb-5">
                                <div>
                                    <h3 class="text-xl font-bold tracking-tight text-night-900 sm:text-2xl">{{ $project['title'] }}</h3>
                                    <p class="mt-1 flex items-center gap-2 text-sm text-ink/65">
                                        <x-site.icon name="pin" class="size-4" stroke="2" />
                                        {{ $project['place'] }}
                                    </p>
                                </div>
                                <p class="shrink-0 text-right text-2xl font-extrabold tracking-tight tabular-nums text-night-700 sm:text-3xl">{{ $project['size'] }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </x-site.container>
    </section>

    {{-- 9. Testimonials --}}
    <section aria-labelledby="testimonials-heading" class="bg-night-900 py-24 text-white lg:py-32">
        <x-site.container>
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-10">
                <div class="lg:col-span-4">
                    <x-site.eyebrow tone="dark" class="reveal">Testimoni</x-site.eyebrow>
                    <h2 id="testimonials-heading" class="reveal mt-6 font-display text-4xl text-white sm:text-5xl" style="--reveal-delay: 80ms">
                        Apa Kata Mereka
                    </h2>
                </div>

                <div class="grid gap-14 lg:col-span-8">
                    @foreach ($testimonials as $t)
                        <figure @class(['reveal border-t border-white/20 pt-8', 'lg:mr-12' => $loop->odd, 'lg:ml-12' => $loop->even])>
                            <blockquote class="text-2xl font-semibold leading-snug tracking-tight text-balance sm:text-3xl">
                                “{{ $t['quote'] }}”
                            </blockquote>
                            <figcaption class="mt-6 flex items-center gap-3 text-sm text-white/70">
                                <span class="h-px w-8 bg-solar-400" aria-hidden="true"></span>
                                {{ $t['by'] }}, {{ $t['org'] }}
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- 10. Insights: magazine layout, lead story + stacked list. --}}
    <section id="home-insights" aria-labelledby="insights-heading" class="home-rail-target bg-paper py-24 lg:py-32">
        <x-site.container>
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <x-site.eyebrow class="reveal">Blog</x-site.eyebrow>
                    <h2 id="insights-heading" class="reveal mt-6 font-display text-4xl text-night-900 sm:text-5xl lg:text-6xl" style="--reveal-delay: 80ms">
                        Insight Energi Surya
                    </h2>
                </div>
                <div class="reveal">
                    <x-site.button :href="route('blog.index')" variant="skew" icon-right="arrow-right">Semua Artikel</x-site.button>
                </div>
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-12 lg:gap-14">
                @php $lead = $articles[0]; @endphp
                <article class="reveal group lg:col-span-7">
                    <a href="{{ route('blog.index') }}" class="block focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-solar-600">
                        <div class="overflow-hidden">
                            <x-site.image :src="$lead['image']" :alt="$lead['title']" ratio="aspect-[4/3]" img-class="transition-transform duration-700 ease-out group-hover:scale-105" width="1200" height="900" />
                        </div>
                        <p class="mt-6 text-xs font-bold tracking-[0.2em] text-night-600 uppercase">{{ $lead['category'] }}</p>
                        <h3 class="mt-3 max-w-xl font-display text-3xl text-night-900 text-balance sm:text-4xl">{{ $lead['title'] }}</h3>
                    </a>
                </article>

                <div class="grid content-start gap-10 lg:col-span-5">
                    @foreach (array_slice($articles, 1) as $article)
                        <article class="reveal group border-t border-night-900/15 pt-8" style="--reveal-delay: {{ $loop->iteration * 80 }}ms">
                            <a href="{{ route('blog.index') }}" class="block focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-solar-600">
                                <div class="overflow-hidden">
                                    <x-site.image :src="$article['image']" :alt="$article['title']" ratio="aspect-[16/9]" img-class="transition-transform duration-700 ease-out group-hover:scale-105" width="800" height="450" />
                                </div>
                                <p class="mt-5 text-xs font-bold tracking-[0.2em] text-night-600 uppercase">{{ $article['category'] }}</p>
                                <h3 class="mt-2 text-xl font-bold tracking-tight text-night-900 text-balance sm:text-2xl">{{ $article['title'] }}</h3>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </x-site.container>
    </section>

    {{-- 11. Final CTA --}}
    <section aria-labelledby="cta-heading" class="relative isolate overflow-hidden bg-night-950 text-white">
        <div class="parallax-frame absolute! inset-0" aria-hidden="true">
            <div data-parallax>
                <x-site.image src="/images/photos/trackers.jpg" alt="" ratio="" class="h-full w-full" width="1600" height="1066" />
            </div>
        </div>
        <div class="absolute inset-0 bg-night-950/70" aria-hidden="true"></div>

        <x-site.container class="relative">
            <div class="max-w-4xl py-28 sm:py-36">
                <x-site.eyebrow tone="dark" class="reveal">Mulai dari Sini</x-site.eyebrow>
                <h2 id="cta-heading" class="reveal mt-6 font-display text-[clamp(2.25rem,6vw,5rem)] text-white" style="--reveal-delay: 80ms">
                    Siap Mengubah Energi Matahari Menjadi Nilai?
                </h2>
                <p class="reveal mt-6 max-w-xl text-lg leading-relaxed text-white/80" style="--reveal-delay: 160ms">
                    Konsultasikan kebutuhan PLTS Anda bersama tim PLTSINDONESIA.
                </p>
                <div class="reveal mt-10 flex flex-wrap items-center gap-3" style="--reveal-delay: 240ms">
                    <x-site.button :href="$ctaUrl" :external="$ctaExternal" variant="primary" size="lg" icon-right="arrow-right">Konsultasi Sekarang</x-site.button>
                    <x-site.button :href="route('contact')" variant="outline-light" size="lg">Hubungi Kami</x-site.button>
                </div>
            </div>
        </x-site.container>
    </section>
</x-site.layout>
