@php
    use App\Models\Testimonial;

    $company = site('company_name', config('app.name'));

    $testimonials = Testimonial::query()->active()->ordered()->limit(6)->get();

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Tentang Kami'],
    ];

    $defaultStory = <<<'MD'
    Perusahaan ini dimulai dari pertanyaan sederhana: mengapa atap yang setiap hari terkena matahari justru dibiarkan kosong?

    Sejak awal berdiri, kami fokus pada satu hal — membantu pemilik rumah dan pelaku usaha di Indonesia memahami energi surya tanpa istilah yang membingungkan. Setiap penawaran yang kami buat selalu dimulai dari data pemakaian listrik Anda, bukan dari katalog produk.

    Hari ini tim kami terdiri dari insinyur kelistrikan, perancang sistem, dan teknisi lapangan yang telah menangani ratusan instalasi di berbagai kondisi: atap rumah tinggal, gedung perkantoran, gudang industri, hingga lokasi terpencil yang belum terjangkau jaringan listrik.

    Kami percaya transisi energi tidak harus rumit. Cukup jujur pada perhitungan, rapi pada pengerjaan, dan hadir saat pelanggan membutuhkan bantuan.
    MD;

    $values = [
        ['icon' => 'chart', 'title' => 'Jujur pada angka', 'body' => 'Kami tampilkan perhitungan apa adanya, termasuk bila hasilnya menunjukkan atap Anda belum layak dipasang PLTS tahun ini.'],
        ['icon' => 'shield', 'title' => 'Keselamatan dulu', 'body' => 'Setiap instalasi mengikuti standar kelistrikan yang berlaku, lengkap dengan proteksi arus lebih, grounding, dan pemutus darurat.'],
        ['icon' => 'leaf', 'title' => 'Bertanggung jawab', 'body' => 'Kami mengelola limbah kemasan dan komponen bekas secara benar, serta memilih peralatan dengan umur pakai panjang.'],
        ['icon' => 'users', 'title' => 'Menemani jangka panjang', 'body' => 'Hubungan kami tidak berakhir saat serah terima. Tim purna jual memantau produksi dan siap dipanggil saat dibutuhkan.'],
    ];

    $steps = [
        ['title' => 'Konsultasi awal', 'body' => 'Kami minta data tagihan listrik dan foto atap Anda untuk memperkirakan kapasitas yang masuk akal.'],
        ['title' => 'Survei lokasi', 'body' => 'Teknisi datang mengukur luasan atap, memeriksa struktur, arah matahari, dan titik koneksi kelistrikan.'],
        ['title' => 'Proposal teknis', 'body' => 'Anda menerima gambar tata letak, daftar peralatan, simulasi produksi, dan rincian investasi.'],
        ['title' => 'Pemasangan', 'body' => 'Pekerjaan umumnya selesai dalam beberapa hari, dengan area kerja yang kami jaga tetap bersih dan aman.'],
        ['title' => 'Komisioning', 'body' => 'Sistem diuji, alat pemantauan disetting, dan Anda diajari membaca data produksinya.'],
        ['title' => 'Perawatan', 'body' => 'Pembersihan panel dan pemeriksaan berkala sesuai paket layanan yang Anda pilih.'],
    ];

    $milestones = collect(explode('|', (string) site('milestones', '')))->filter()->values();
@endphp

<x-site.layout
    :title="site('about_meta_title', 'Tentang Kami')"
    :description="site('about_meta_description', 'Kenali tim di balik '.$company.' — nilai yang kami pegang, cara kami bekerja, dan komitmen kami pada energi bersih di Indonesia.')"
    :schema="[\App\Support\JsonLd::breadcrumbs($crumbs)]"
    :canonical="route('about')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Tentang Kami"
        :title="site('about_title', 'Kami mengerjakan transisi energi satu atap pada satu waktu')"
        :description="site('about_subtitle', 'Bukan sekadar menjual panel. Kami menghitung, merancang, memasang, lalu merawat — supaya sistem Anda benar-benar menghasilkan selama puluhan tahun.')"
    />

    {{-- Story --}}
    <x-site.section tone="light" labelled-by="cerita-heading">
        <x-site.container>
            <div class="grid items-start gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-7">
                    <x-site.section-heading
                        id="cerita-heading"
                        class="reveal"
                        eyebrow="Cerita Kami"
                        eyebrow-icon="sun"
                        :title="site('about_story_title', 'Berangkat dari rasa penasaran pada tagihan listrik')"
                        level="2"
                    />

                    <div class="prose prose-solar mt-6 max-w-none text-base leading-relaxed">
                        {!! \Illuminate\Support\Str::markdown((string) site('about_body', $defaultStory)) !!}
                    </div>
                </div>

                <aside class="lg:col-span-5">
                    <div class="reveal-media parallax-frame aspect-[4/5]">
                        <div data-parallax>
                            <x-site.image
                                :src="site('about_image')"
                                alt="Tim teknisi sedang berdiskusi di depan instalasi panel surya"
                                variant="leaf"
                                ratio=""
                                class="h-full w-full"
                                width="900"
                                height="1125"
                            />
                        </div>
                    </div>

                    @if ($milestones->isNotEmpty())
                        <dl class="mt-6 grid gap-4 border border-hairline bg-tile p-6">
                            <dt class="text-xs font-semibold tracking-[0.22em] text-ink/60 uppercase">Perjalanan kami</dt>
                            <dd>
                                <ol class="grid gap-3">
                                    @foreach ($milestones as $milestone)
                                        <li class="flex gap-3 text-sm text-ink">
                                            <x-site.icon name="check-circle" class="mt-0.5 size-4.5 shrink-0 text-solar-600" stroke="2" />
                                            <span>{{ $milestone }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </dd>
                        </dl>
                    @endif
                </aside>
            </div>
        </x-site.container>
    </x-site.section>

    {{-- Values --}}
    <x-site.section tone="tint" labelled-by="nilai-heading">
        <x-site.container>
            <x-site.section-heading
                id="nilai-heading"
                align="center"
                class="reveal mx-auto"
                eyebrow="Nilai Kami"
                eyebrow-icon="shield"
                title="Empat hal yang tidak kami tawar"
                description="Prinsip ini yang menentukan cara kami menghitung, memasang, dan melayani."
            />

            <ul class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($values as $value)
                    <li class="reveal border border-hairline bg-white p-6" style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <span class="grid size-12 place-items-center bg-coal text-solar-400" aria-hidden="true">
                            <x-site.icon :name="$value['icon']" class="size-6" stroke="1.8" />
                        </span>

                        <h3 class="mt-5 font-display text-lg tracking-tight text-ink">{{ $value['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink/70">{{ $value['body'] }}</p>
                    </li>
                @endforeach
            </ul>
        </x-site.container>
    </x-site.section>

    {{-- Process --}}
    <x-site.section tone="light" labelled-by="proses-heading">
        <x-site.container>
            <x-site.section-heading
                id="proses-heading"
                class="reveal"
                eyebrow="Cara Kerja"
                eyebrow-icon="list"
                title="Dari obrolan pertama sampai sistem menyala"
                description="Enam tahap yang kami jalankan pada setiap proyek, besar maupun kecil."
            />

            <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($steps as $index => $step)
                    <li class="reveal border-t-2 border-solar-400 bg-tile p-6" style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                        <span class="font-display text-3xl text-ink/40" aria-hidden="true">
                            {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <h3 class="mt-2 font-display text-lg tracking-tight text-ink">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink/70">{{ $step['body'] }}</p>
                    </li>
                @endforeach
            </ol>
        </x-site.container>
    </x-site.section>

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <x-site.section tone="dark" labelled-by="ulasan-tentang-heading">
            <x-site.container>
                <x-site.section-heading
                    id="ulasan-tentang-heading"
                    tone="dark"
                    class="reveal mb-10"
                    eyebrow="Testimoni"
                    eyebrow-icon="quote"
                    title="Cerita dari pelanggan kami"
                    description="Mereka yang sudah lebih dulu memindahkan sebagian tagihannya ke matahari."
                />

                <x-site.testimonials :testimonials="$testimonials" tone="dark" />
            </x-site.container>
        </x-site.section>
    @endif

    <x-site.cta-banner
        title="Mari mulai dari obrolan singkat"
        description="Kirimkan tagihan listrik terakhir Anda, kami hitungkan potensi penghematannya tanpa biaya dan tanpa kewajiban berlanjut."
    />
</x-site.layout>
