<?php

use App\Models\Media;
use App\Models\Setting;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Pengaturan Situs')] class extends Component {
    use WithFileUploads;

    /**
     * Image settings rendered with an uploader.
     *
     * @var array<string, string>
     */
    public const array IMAGES = [
        'hero_image' => 'Gambar utama beranda',
        'og_image' => 'Gambar berbagi media sosial (disarankan 1200×630)',
        'about_image' => 'Gambar halaman Tentang Kami',
        'segment_home_image' => 'Foto segmen rumah tinggal',
        'segment_business_image' => 'Foto segmen bisnis',
        'segment_industry_image' => 'Foto segmen industri',
    ];

    /**
     * Social networks whose key doubles as the footer icon name.
     *
     * @var array<int, string>
     */
    public const array NETWORKS = ['instagram', 'facebook', 'linkedin', 'youtube', 'whatsapp'];

    /**
     * Every editable setting, keyed exactly as the public site reads it.
     *
     * @var array<string, mixed>
     */
    public array $settings = [];

    /**
     * Pending image uploads, keyed by setting name.
     *
     * @var array<string, TemporaryUploadedFile|null>
     */
    public array $uploads = [];

    public function mount(): void
    {
        $stored = Setting::cached();

        $this->settings = [
            'company_name' => (string) ($stored['company_name'] ?? config('app.name')),
            'company_short_name' => (string) ($stored['company_short_name'] ?? ''),
            'company_legal_name' => (string) ($stored['company_legal_name'] ?? ''),
            'tagline' => (string) ($stored['tagline'] ?? ''),
            'company_description' => (string) ($stored['company_description'] ?? ''),
            'founded_year' => (string) ($stored['founded_year'] ?? ''),
            'experience_years' => (string) ($stored['experience_years'] ?? ''),

            'phone' => (string) ($stored['phone'] ?? ''),
            'whatsapp' => (string) ($stored['whatsapp'] ?? ''),
            'email' => (string) ($stored['email'] ?? ''),
            'address' => (string) ($stored['address'] ?? ''),
            'city' => (string) ($stored['city'] ?? ''),
            'region' => (string) ($stored['region'] ?? ''),
            'postal_code' => (string) ($stored['postal_code'] ?? ''),
            'country' => (string) ($stored['country'] ?? 'Indonesia'),
            'service_area_name' => (string) ($stored['service_area_name'] ?? ''),
            'service_area' => (string) ($stored['service_area'] ?? ''),
            'business_hours' => (string) ($stored['business_hours'] ?? ''),
            'geo_lat' => (string) ($stored['geo_lat'] ?? ''),
            'geo_lng' => (string) ($stored['geo_lng'] ?? ''),
            'map_embed_url' => (string) ($stored['map_embed_url'] ?? ''),

            'social' => array_merge(
                array_fill_keys(self::NETWORKS, ''),
                array_map(fn ($url) => (string) $url, (array) ($stored['social'] ?? [])),
            ),

            'hero_title' => (string) ($stored['hero_title'] ?? ''),
            'hero_subtitle' => (string) ($stored['hero_subtitle'] ?? ''),
            'hero_badge_value' => (string) ($stored['hero_badge_value'] ?? ''),
            'hero_badge_note' => (string) ($stored['hero_badge_note'] ?? ''),
            'rating_value' => (string) ($stored['rating_value'] ?? '4.9'),
            'rating_count' => (string) ($stored['rating_count'] ?? ''),
            'stat_1_value' => (string) ($stored['stat_1_value'] ?? ''),
            'stat_2_value' => (string) ($stored['stat_2_value'] ?? ''),
            'stat_3_value' => (string) ($stored['stat_3_value'] ?? ''),
            'stat_4_value' => (string) ($stored['stat_4_value'] ?? ''),
            'cta_eyebrow' => (string) ($stored['cta_eyebrow'] ?? ''),
            'cta_title' => (string) ($stored['cta_title'] ?? ''),
            'cta_description' => (string) ($stored['cta_description'] ?? ''),
            'cta_points' => (string) ($stored['cta_points'] ?? ''),

            'about_title' => (string) ($stored['about_title'] ?? ''),
            'about_subtitle' => (string) ($stored['about_subtitle'] ?? ''),
            'about_story_title' => (string) ($stored['about_story_title'] ?? ''),
            'about_body' => (string) ($stored['about_body'] ?? ''),
            'milestones' => (string) ($stored['milestones'] ?? ''),

            'meta_description' => (string) ($stored['meta_description'] ?? ''),
            'seo_keywords' => (string) ($stored['seo_keywords'] ?? ''),
            'services_meta_title' => (string) ($stored['services_meta_title'] ?? ''),
            'services_meta_description' => (string) ($stored['services_meta_description'] ?? ''),
            'projects_meta_title' => (string) ($stored['projects_meta_title'] ?? ''),
            'projects_meta_description' => (string) ($stored['projects_meta_description'] ?? ''),
            'gallery_meta_title' => (string) ($stored['gallery_meta_title'] ?? ''),
            'gallery_meta_description' => (string) ($stored['gallery_meta_description'] ?? ''),
            'faq_meta_title' => (string) ($stored['faq_meta_title'] ?? ''),
            'faq_meta_description' => (string) ($stored['faq_meta_description'] ?? ''),
            'about_meta_title' => (string) ($stored['about_meta_title'] ?? ''),
            'about_meta_description' => (string) ($stored['about_meta_description'] ?? ''),
            'contact_meta_title' => (string) ($stored['contact_meta_title'] ?? ''),
            'contact_meta_description' => (string) ($stored['contact_meta_description'] ?? ''),
            'rss_enabled' => (bool) ($stored['rss_enabled'] ?? true),

            'hero_image' => (string) ($stored['hero_image'] ?? ''),
            'og_image' => (string) ($stored['og_image'] ?? ''),
            'about_image' => (string) ($stored['about_image'] ?? ''),
            'segment_home_image' => (string) ($stored['segment_home_image'] ?? ''),
            'segment_business_image' => (string) ($stored['segment_business_image'] ?? ''),
            'segment_industry_image' => (string) ($stored['segment_industry_image'] ?? ''),
        ];

        $this->uploads = array_fill_keys(array_keys(self::IMAGES), null);
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function imageFields(): array
    {
        return array_keys(self::IMAGES);
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function networks(): array
    {
        return self::NETWORKS;
    }

    /**
     * Label for an image setting.
     */
    public function imageLabel(string $key): string
    {
        return self::IMAGES[$key] ?? $key;
    }

    /**
     * Label for a social network.
     */
    public function networkLabel(string $network): string
    {
        return \Illuminate\Support\Str::title($network);
    }

    /**
     * Store an uploaded image as soon as it arrives and write its path back.
     */
    public function updated(string $property, mixed $value): void
    {
        if (! str_starts_with($property, 'uploads.')) {
            return;
        }

        $key = substr($property, strlen('uploads.'));

        if (! array_key_exists($key, self::IMAGES)) {
            return;
        }

        if (! $value instanceof TemporaryUploadedFile) {
            return;
        }

        $this->validate(
            ["uploads.$key" => ['image', 'max:4096']],
            ["uploads.$key.image" => 'Berkas harus berupa gambar.', "uploads.$key.max" => 'Ukuran gambar maksimal 4 MB.'],
            ["uploads.$key" => $this->imageLabel($key)],
        );

        $this->settings[$key] = Media::storeUpload($value, Auth::id(), 'settings')->path;
        $this->uploads[$key] = null;

        Flux::toast(variant: 'success', text: $this->imageLabel($key).' berhasil diunggah.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesFor(): array
    {
        return [
            'settings.company_name' => ['required', 'string', 'max:120'],
            'settings.company_short_name' => ['nullable', 'string', 'max:60'],
            'settings.company_legal_name' => ['nullable', 'string', 'max:160'],
            'settings.tagline' => ['nullable', 'string', 'max:160'],
            'settings.company_description' => ['nullable', 'string', 'max:600'],
            'settings.founded_year' => ['nullable', 'integer', 'min:1900', 'max:2200'],
            'settings.experience_years' => ['nullable', 'integer', 'min:0', 'max:99'],

            'settings.phone' => ['nullable', 'string', 'max:40'],
            'settings.whatsapp' => ['nullable', 'string', 'max:40'],
            'settings.email' => ['nullable', 'email:filter', 'max:160'],
            'settings.address' => ['nullable', 'string', 'max:400'],
            'settings.city' => ['nullable', 'string', 'max:120'],
            'settings.region' => ['nullable', 'string', 'max:120'],
            'settings.postal_code' => ['nullable', 'string', 'max:12'],
            'settings.country' => ['nullable', 'string', 'max:80'],
            'settings.service_area_name' => ['nullable', 'string', 'max:120'],
            'settings.service_area' => ['nullable', 'string', 'max:600'],
            'settings.business_hours' => ['nullable', 'string', 'max:400'],
            'settings.geo_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'settings.geo_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'settings.map_embed_url' => ['nullable', 'string', 'max:1500'],

            'settings.rating_value' => ['nullable', 'numeric', 'between:0,5'],
            'settings.rating_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'settings.hero_title' => ['nullable', 'string', 'max:190'],
            'settings.hero_subtitle' => ['nullable', 'string', 'max:400'],
            'settings.hero_badge_value' => ['nullable', 'string', 'max:60'],
            'settings.hero_badge_note' => ['nullable', 'string', 'max:120'],
            'settings.cta_eyebrow' => ['nullable', 'string', 'max:60'],
            'settings.cta_title' => ['nullable', 'string', 'max:190'],
            'settings.cta_description' => ['nullable', 'string', 'max:400'],
            'settings.cta_points' => ['nullable', 'string', 'max:400'],

            'settings.about_title' => ['nullable', 'string', 'max:190'],
            'settings.about_subtitle' => ['nullable', 'string', 'max:400'],
            'settings.about_story_title' => ['nullable', 'string', 'max:190'],
            'settings.about_body' => ['nullable', 'string', 'max:20000'],
            'settings.milestones' => ['nullable', 'string', 'max:1000'],

            'settings.meta_description' => ['nullable', 'string', 'max:170'],
            'settings.seo_keywords' => ['nullable', 'string', 'max:400'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        $labels = [
            'company_name' => 'nama perusahaan',
            'company_short_name' => 'nama singkat',
            'company_legal_name' => 'nama badan hukum',
            'tagline' => 'tagline',
            'company_description' => 'deskripsi perusahaan',
            'founded_year' => 'tahun berdiri',
            'experience_years' => 'tahun pengalaman',
            'phone' => 'telepon',
            'whatsapp' => 'WhatsApp',
            'email' => 'email',
            'address' => 'alamat',
            'city' => 'kota',
            'region' => 'provinsi',
            'postal_code' => 'kode pos',
            'country' => 'negara',
            'service_area_name' => 'nama area layanan',
            'service_area' => 'area layanan',
            'business_hours' => 'jam operasional',
            'geo_lat' => 'lintang',
            'geo_lng' => 'bujur',
            'map_embed_url' => 'URL embed peta',
            'rating_value' => 'nilai penilaian',
            'rating_count' => 'jumlah penilaian',
            'hero_title' => 'judul hero',
            'hero_subtitle' => 'subjudul hero',
            'hero_badge_value' => 'nilai lencana hero',
            'hero_badge_note' => 'keterangan lencana hero',
            'cta_eyebrow' => 'eyebrow CTA',
            'cta_title' => 'judul CTA',
            'cta_description' => 'deskripsi CTA',
            'cta_points' => 'poin CTA',
            'about_title' => 'judul Tentang Kami',
            'about_subtitle' => 'subjudul Tentang Kami',
            'about_story_title' => 'judul cerita',
            'about_body' => 'isi Tentang Kami',
            'milestones' => 'pencapaian',
            'meta_description' => 'deskripsi meta',
            'seo_keywords' => 'kata kunci SEO',
        ];

        return collect($labels)
            ->mapWithKeys(fn (string $label, string $key) => ["settings.$key" => $label])
            ->all();
    }

    public function save(): void
    {
        $this->validate($this->rulesFor(), attributes: $this->validationAttributes());

        $values = array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            $this->settings,
        );

        $values['social'] = array_filter(
            array_map(
                fn ($url) => trim((string) $url),
                (array) ($values['social'] ?? []),
            ),
        );

        // Blank values are removed entirely so the public site falls back to its own copy.
        $values = array_filter(
            $values,
            fn ($value) => ! ($value === null || $value === '' || $value === []),
        );

        Setting::query()
            ->whereNotIn('key', array_keys($values))
            ->delete();

        Setting::setMany($values);

        Flux::toast(variant: 'success', text: 'Pengaturan situs disimpan.');
    }
}; ?>

<div class="space-y-6">
    <x-admin.page-header
        title="Pengaturan Situs"
        description="Satu tempat untuk identitas, kontak, konten beranda, dan metadata SEO. Kolom yang dikosongkan memakai teks bawaan."
    />

    <form wire:submit="save" class="space-y-6">
        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">Identitas Perusahaan</flux:heading>

                <flux:text size="sm">Dipakai di header, footer, structured data Organization, dan halaman legal.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="settings.company_name" label="Nama perusahaan" type="text" required placeholder="Surya Nusa Energi" />

                <flux:input wire:model.blur="settings.company_short_name" label="Nama singkat" type="text" placeholder="SuryaNusa" />

                <flux:input wire:model.blur="settings.company_legal_name" label="Nama badan hukum" type="text" placeholder="PT PLTSINDONESIA" />

                <flux:input wire:model.blur="settings.tagline" label="Tagline" type="text" placeholder="Energi surya untuk Indonesia" />

                <flux:input wire:model.blur="settings.founded_year" label="Tahun berdiri" type="number" min="1900" max="2200" placeholder="2016" />

                <flux:input wire:model.blur="settings.experience_years" label="Tahun pengalaman" type="number" min="0" max="99" placeholder="9" />
            </div>

            <flux:textarea
                wire:model.blur="settings.company_description"
                label="Deskripsi singkat"
                description="Muncul di footer dan dipakai sebagai deskripsi Organization."
                rows="3"
                placeholder="Kami merancang, memasang, dan merawat pembangkit listrik tenaga surya untuk rumah, bisnis, dan industri di seluruh Indonesia."
            />
        </flux:card>

        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">Kontak &amp; Lokasi</flux:heading>

                <flux:text size="sm">Dipakai di halaman kontak, footer, tombol WhatsApp, dan structured data LocalBusiness.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input wire:model.blur="settings.phone" label="Telepon" type="text" placeholder="+62 21 5000 1234" />

                <flux:input wire:model.blur="settings.whatsapp" label="WhatsApp" description="Format internasional, contoh 6281234567890." type="text" placeholder="6281234567890" />

                <flux:input wire:model.blur="settings.email" label="Email" type="email" placeholder="info@pltsindonesia.com" />
            </div>

            <flux:textarea wire:model.blur="settings.address" label="Alamat" rows="2" placeholder="Jl. Energi Surya No. 12, Kebon Jeruk" />

            <div class="grid gap-4 sm:grid-cols-4">
                <flux:input wire:model.blur="settings.city" label="Kota" type="text" placeholder="Jakarta Barat" />

                <flux:input wire:model.blur="settings.region" label="Provinsi" type="text" placeholder="DKI Jakarta" />

                <flux:input wire:model.blur="settings.postal_code" label="Kode pos" type="text" placeholder="11530" />

                <flux:input wire:model.blur="settings.country" label="Negara" type="text" placeholder="Indonesia" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="settings.service_area_name" label="Nama area layanan" type="text" placeholder="Jabodetabek dan seluruh Jawa" />

                <flux:input wire:model.blur="settings.geo_lat" label="Lintang (latitude)" type="number" step="0.000001" placeholder="-6.200000" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="settings.geo_lng" label="Bujur (longitude)" type="number" step="0.000001" placeholder="106.816666" />

                <flux:input wire:model.blur="settings.map_embed_url" label="URL embed peta" type="text" placeholder="https://www.google.com/maps/embed?pb=…" />
            </div>

            <flux:textarea
                wire:model.blur="settings.service_area"
                label="Daftar area layanan"
                description="Pisahkan dengan tanda pipa (|). Ditampilkan sebagai daftar kota."
                rows="2"
                placeholder="Jakarta|Bogor|Depok|Tangerang|Bekasi|Bandung|Surabaya"
            />

            <flux:textarea
                wire:model.blur="settings.business_hours"
                label="Jam operasional"
                description="Satu baris per keterangan waktu."
                rows="3"
                placeholder="Senin–Jumat: 08.00–17.00 WIB&#10;Sabtu: 09.00–14.00 WIB&#10;Minggu dan libur nasional: tutup"
            />
        </flux:card>

        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">Media Sosial</flux:heading>

                <flux:text size="sm">Isi dengan URL lengkap. Jaringan yang dikosongkan tidak ditampilkan di footer.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($this->networks as $network)
                    <flux:input
                        wire:model.blur="settings.social.{{ $network }}"
                        :label="$this->networkLabel($network)"
                        type="url"
                        :placeholder="'https://'.$network.'.com/…'"
                    />
                @endforeach
            </div>
        </flux:card>

        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">Beranda</flux:heading>

                <flux:text size="sm">Judul besar, lencana, statistik, dan blok ajakan bertindak di halaman utama.</flux:text>
            </div>

            <flux:textarea
                wire:model.blur="settings.hero_title"
                label="Judul hero"
                description="Idealnya 40–70 karakter agar mudah dibaca dan memuat kata kunci utama."
                rows="2"
                placeholder="Ubah sinar matahari menjadi tagihan listrik yang lebih ringan"
            />

            <flux:textarea
                wire:model.blur="settings.hero_subtitle"
                label="Subjudul hero"
                rows="3"
                placeholder="Kami merancang, memasang, dan merawat PLTS untuk rumah, bisnis, dan industri di seluruh Indonesia."
            />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="settings.hero_badge_value" label="Nilai lencana penghematan" type="text" placeholder="hingga 70%" />

                <flux:input wire:model.blur="settings.hero_badge_note" label="Keterangan lencana" type="text" placeholder="dari tagihan listrik bulanan Anda" />
            </div>

            <div class="grid gap-4 sm:grid-cols-4">
                <flux:input wire:model.blur="settings.stat_1_value" label="Statistik 1" type="text" description="Proyek selesai" placeholder="250+" />

                <flux:input wire:model.blur="settings.stat_2_value" label="Statistik 2" type="text" description="Kapasitas terpasang" placeholder="12 MWp" />

                <flux:input wire:model.blur="settings.stat_3_value" label="Statistik 3" type="text" description="Kota terlayani" placeholder="34" />

                <flux:input wire:model.blur="settings.stat_4_value" label="Statistik 4" type="text" description="Tahun pengalaman" placeholder="9" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model.blur="settings.rating_value"
                    label="Nilai penilaian pelanggan"
                    description="0–5, dipakai pada structured data AggregateRating."
                    type="number"
                    step="0.1"
                    min="0"
                    max="5"
                    placeholder="4.9"
                />

                <flux:input
                    wire:model.blur="settings.rating_count"
                    label="Jumlah penilaian"
                    type="number"
                    min="0"
                    placeholder="180"
                />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="settings.cta_eyebrow" label="Eyebrow blok CTA" type="text" placeholder="Mulai Sekarang" />

                <flux:input wire:model.blur="settings.cta_title" label="Judul blok CTA" type="text" placeholder="Siap menekan tagihan listrik bulan depan?" />
            </div>

            <flux:textarea
                wire:model.blur="settings.cta_description"
                label="Deskripsi blok CTA"
                rows="2"
                placeholder="Konsultasikan kebutuhan listrik Anda secara gratis. Kami hitungkan potensi penghematan dan estimasi investasi untuk atap Anda."
            />

            <flux:input
                wire:model.blur="settings.cta_points"
                label="Poin blok CTA"
                description="Pisahkan dengan tanda pipa (|)."
                type="text"
                placeholder="Survei lokasi gratis|Perhitungan ROI terperinci|Garansi pemasangan 2 tahun"
            />
        </flux:card>

        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">Halaman Tentang Kami</flux:heading>

                <flux:text size="sm">Isi ditulis dalam Markdown. Judul bagian memakai format <code>## Judul</code>.</flux:text>
            </div>

            <flux:input wire:model.blur="settings.about_title" label="Judul halaman" type="text" placeholder="Kami mengerjakan transisi energi satu atap pada satu waktu" />

            <flux:textarea wire:model.blur="settings.about_subtitle" label="Subjudul" rows="2" placeholder="Bukan sekadar menjual panel…" />

            <flux:input wire:model.blur="settings.about_story_title" label="Judul cerita perusahaan" type="text" placeholder="Dari bengkel kecil menuju 12 MWp terpasang" />

            <flux:textarea
                wire:model.blur="settings.about_body"
                label="Cerita perusahaan (Markdown)"
                rows="8"
                placeholder="## Awal mula&#10;&#10;Tulis sejarah dan nilai yang dipegang perusahaan."
            />

            <flux:input
                wire:model.blur="settings.milestones"
                label="Pencapaian"
                description="Pisahkan dengan tanda pipa (|). Ditampilkan sebagai daftar tonggak."
                type="text"
                placeholder="2016: berdiri di Jakarta|2019: 50 proyek residensial|2023: 10 MWp terpasang"
            />
        </flux:card>

        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">Gambar</flux:heading>

                <flux:text size="sm">
                    Unggah langsung, atau tempel path dari pustaka media. Gambar yang dikosongkan diganti ilustrasi bawaan.
                </flux:text>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                @foreach ($this->imageFields as $key)
                    <x-admin.image-field
                        :label="$this->imageLabel($key)"
                        :model="'uploads.'.$key"
                        :path-model="'settings.'.$key"
                        :preview="image_url((string) ($settings[$key] ?? ''))"
                    />
                @endforeach
            </div>
        </flux:card>

        <flux:card class="space-y-5">
            <div>
                <flux:heading level="2" size="lg">SEO</flux:heading>

                <flux:text size="sm">Metadata bawaan untuk halaman yang tidak memiliki artikel tersendiri.</flux:text>
            </div>

            <flux:textarea
                wire:model.blur="settings.meta_description"
                label="Deskripsi meta beranda"
                description="Maksimal 170 karakter."
                rows="2"
                placeholder="Kontraktor panel surya untuk rumah, bisnis, dan industri di Indonesia…"
            />

            <flux:input
                wire:model.blur="settings.seo_keywords"
                label="Kata kunci"
                description="Pisahkan dengan koma."
                type="text"
                placeholder="panel surya, PLTS atap, kontraktor solar panel"
            />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.blur="settings.services_meta_title" label="Judul meta /layanan" type="text" placeholder="Layanan Panel Surya" />

                <flux:textarea wire:model.blur="settings.services_meta_description" label="Deskripsi meta /layanan" rows="2" />

                <flux:input wire:model.blur="settings.projects_meta_title" label="Judul meta /proyek" type="text" placeholder="Portofolio Proyek PLTS" />

                <flux:textarea wire:model.blur="settings.projects_meta_description" label="Deskripsi meta /proyek" rows="2" />

                <flux:input wire:model.blur="settings.gallery_meta_title" label="Judul meta /galeri" type="text" placeholder="Galeri Pemasangan" />

                <flux:textarea wire:model.blur="settings.gallery_meta_description" label="Deskripsi meta /galeri" rows="2" />

                <flux:input wire:model.blur="settings.faq_meta_title" label="Judul meta /faq" type="text" placeholder="Pertanyaan yang Sering Diajukan" />

                <flux:textarea wire:model.blur="settings.faq_meta_description" label="Deskripsi meta /faq" rows="2" />

                <flux:input wire:model.blur="settings.about_meta_title" label="Judul meta /tentang-kami" type="text" placeholder="Tentang Kami" />

                <flux:textarea wire:model.blur="settings.about_meta_description" label="Deskripsi meta /tentang-kami" rows="2" />

                <flux:input wire:model.blur="settings.contact_meta_title" label="Judul meta /kontak" type="text" placeholder="Hubungi Kami" />

                <flux:textarea wire:model.blur="settings.contact_meta_description" label="Deskripsi meta /kontak" rows="2" />
            </div>

            <flux:switch
                wire:model="settings.rss_enabled"
                label="Aktifkan RSS feed (/feed.xml)"
                description="Matikan bila Anda tidak ingin berlangganan melalui pembaca feed."
            />
        </flux:card>

        <div class="sticky bottom-4 flex flex-wrap items-center gap-2 rounded-xl border border-zinc-200 bg-white/95 p-3 backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/95">
            <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled">
                Simpan Pengaturan
            </flux:button>

            <flux:button type="button" variant="ghost" :href="route('home')" target="_blank" icon="arrow-top-right-on-square">
                Lihat Situs
            </flux:button>

            <flux:text size="sm" class="ms-auto" wire:loading wire:target="save">Menyimpan…</flux:text>
        </div>
    </form>
</div>
