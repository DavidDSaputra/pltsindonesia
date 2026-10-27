@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Service> $services */

    $crumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Layanan'],
    ];
@endphp

<x-site.layout
    :title="site('services_meta_title', 'Layanan Panel Surya & PLTS')"
    :description="site('services_meta_description', 'Survei, perancangan, pemasangan, hingga perawatan sistem panel surya untuk rumah, bisnis, dan industri di Indonesia.')"
    :schema="$schema"
    :canonical="route('services.index')"
>
    <x-site.page-hero
        :crumbs="$crumbs"
        eyebrow="Layanan"
        title="Layanan lengkap untuk sistem energi surya Anda"
        description="Satu tim menangani seluruh tahapan, sehingga Anda tidak perlu mengoordinasikan banyak vendor."
    />

    <x-site.section tone="light" labelled-by="daftar-layanan-heading">
        <x-site.container>
            <x-site.section-heading
                id="daftar-layanan-heading"
                eyebrow="Yang Kami Kerjakan"
                eyebrow-icon="panels"
                :title="$services->count().' layanan untuk berbagai kebutuhan'"
                description="Pilih layanan yang paling sesuai, atau bicarakan kebutuhan Anda langsung dengan tim kami."
            />

            @if ($services->isNotEmpty())
                <div class="mt-12 grid gap-6 lg:grid-cols-2">
                    @foreach ($services as $service)
                        <x-site.service-card :service="$service" variant="wide" />
                    @endforeach
                </div>
            @else
                <p class="mt-12 rounded-2xl bg-night-50 p-8 text-center text-sm text-zinc-600">
                    Daftar layanan sedang kami perbarui. Silakan hubungi kami untuk kebutuhan khusus.
                </p>
            @endif
        </x-site.container>
    </x-site.section>

    {{-- Recent work --}}
    @if ($projects->isNotEmpty())
        <x-site.section tone="tint" labelled-by="contoh-pekerjaan-heading">
            <x-site.container>
                <x-site.section-heading
                    id="contoh-pekerjaan-heading"
                    align="center"
                    eyebrow="Contoh Pekerjaan"
                    eyebrow-icon="spark"
                    title="Hasil pemasangan terbaru"
                    description="Beberapa proyek yang baru saja kami selesaikan."
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

    <x-site.cta-banner
        title="Butuh layanan yang tidak tercantum?"
        description="Ceritakan kebutuhan Anda. Kami sering menangani permintaan khusus mulai dari studi kelayakan sampai retrofit sistem lama."
    />
</x-site.layout>
