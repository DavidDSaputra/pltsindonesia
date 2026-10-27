<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seed the site identity and copy the public pages read through site().
 *
 * Anything left out here still renders, because every call site carries its own
 * Indonesian fallback string. Values seeded below simply make the demo coherent.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setMany([
            // Identity
            'company_name' => 'PLTSINDONESIA',
            'company_short_name' => 'PLTSINDONESIA',
            'company_legal_name' => 'PLTSINDONESIA',
            'tagline' => 'Kontraktor Panel Surya & PLTS Terpercaya',
            'company_description' => 'Kami merancang, memasang, dan merawat pembangkit listrik tenaga surya untuk rumah, bisnis, dan industri di seluruh Indonesia.',
            'founded_year' => 2017,
            'experience_years' => 9,

            // Contact & location
            'phone' => '+62 21 5099 4120',
            'whatsapp' => '62811904578',
            'email' => 'info@pltsindonesia.com',
            'address' => 'Gedung Graha Energi Lantai 5, Jl. Letjen S. Parman Kav. 12',
            'city' => 'Jakarta Barat',
            'region' => 'DKI Jakarta',
            'postal_code' => '11480',
            'country' => 'Indonesia',
            'service_area_name' => 'Jangkauan layanan',
            'service_area' => 'Jabodetabek, Jawa Barat, Jawa Tengah, Jawa Timur, Bali, Sumatra Utara, Sumatra Barat, Riau, Lampung, Kalimantan Timur, Kalimantan Selatan, Sulawesi Selatan, Nusa Tenggara Barat, dan Nusa Tenggara Timur.',
            'business_hours' => 'Senin – Sabtu, 08.00 – 17.00 WIB',
            'geo_lat' => '-6.196135',
            'geo_lng' => '106.795532',

            'social' => [
                'instagram' => 'https://instagram.com/pltsindonesia',
                'facebook' => 'https://facebook.com/pltsindonesia',
                'linkedin' => 'https://linkedin.com/company/pltsindonesia',
                'youtube' => 'https://youtube.com/@pltsindonesia',
                'whatsapp' => 'https://wa.me/62811904578',
            ],

            // Imagery — original artwork shipped in public/images. Leading slashes
            // matter: image_url() returns them verbatim instead of resolving them
            // through the public storage disk.
            'og_image' => '/images/og-cover.svg',
            'hero_image' => '/images/photos/hero.jpg',
            'about_image' => '/images/photos/installer.jpg',
            'segment_home_image' => '/images/photos/grass.jpg',
            'segment_business_image' => '/images/photos/panels.jpg',
            'segment_industry_image' => '/images/photos/hero.jpg',

            // Home page hero
            'hero_title' => 'Ubah sinar matahari menjadi tagihan listrik yang lebih ringan',
            'hero_subtitle' => 'PLTSINDONESIA menghadirkan, memasang, dan merawat pembangkit listrik tenaga surya untuk rumah, bisnis, dan industri di seluruh Indonesia — dengan perhitungan penghematan yang transparan sejak awal.',
            'hero_badge_value' => 'hingga 70%',
            'hero_badge_note' => 'dari tagihan listrik bulanan Anda',
            'rating_value' => '4.9',
            'rating_count' => '184',
            'stat_1_value' => '260+',
            'stat_2_value' => '12,4 MWp',
            'stat_3_value' => '34',
            'stat_4_value' => '9',

            // Home page CTA block
            'cta_eyebrow' => 'Konsultasi Gratis',
            'cta_title' => 'Siap beralih ke energi surya?',
            'cta_description' => 'Konsultasikan kebutuhan listrik Anda secara gratis. Kami hitungkan potensi penghematan dan estimasi investasi untuk atap Anda.',
            'cta_points' => 'Survei lokasi gratis|Perhitungan ROI terperinci|Garansi pemasangan 2 tahun',

            // About page
            'about_title' => 'Kami mengerjakan transisi energi satu atap pada satu waktu',
            'about_subtitle' => 'Bukan sekadar menjual panel. Kami menghitung, merancang, memasang, lalu merawat — supaya sistem Anda benar-benar menghasilkan selama puluhan tahun.',
            'about_story_title' => 'Berangkat dari rasa penasaran pada tagihan listrik',
            'about_body' => <<<'MD'
                PLTSINDONESIA lahir pada 2017 dari sebuah pertanyaan sederhana: kenapa banyak pemilik rumah di Indonesia membayar listrik begitu mahal padahal atap mereka mendapat sinar matahari sepanjang tahun?

                Awalnya kami hanya berempat — dua insinyur listrik, satu drafter, dan satu orang yang mengurus perizinan. Proyek pertama kami adalah PLTS atap 2,2 kWp untuk sebuah rumah di Bintaro. Dari situ kami belajar bahwa kesalahan paling mahal bukan pada harga panel, melainkan pada perhitungan beban yang asal-asalan.

                ## Cara kami bekerja

                Kami memulai setiap proyek dari data, bukan dari katalog produk:

                - **Audit tagihan** dua belas bulan terakhir untuk melihat pola pemakaian sebenarnya.
                - **Survei atap** mengukur luas efektif, kemiringan, arah hadap, dan potensi bayangan.
                - **Simulasi produksi** memakai data iradiasi lokasi agar estimasi tidak mengawang.
                - **Rancangan listrik** yang memenuhi standar keselamatan dan siap diajukan perizinannya.

                Setelah sistem menyala, pekerjaan kami belum selesai. Setiap instalasi dipantau produksinya dan dirawat berkala, karena panel yang kotor atau inverter yang tidak dikalibrasi bisa memangkas hasil sampai belasan persen.

                ## Hari ini

                Tim kami kini berjumlah empat puluh orang dengan teknisi bersertifikat di lima kota. Total kapasitas terpasang yang kami tangani melewati 12 MWp, tersebar dari rumah tinggal sampai pabrik pengolahan hasil perkebunan.

                Yang tidak berubah sejak proyek pertama: kami tetap menghitung lebih dulu, menjual kemudian.
                MD,
            'milestones' => '2017 — Perusahaan berdiri dengan tim beranggotakan empat orang|2019 — Proyek komersial pertama berkapasitas 120 kWp di Cikarang|2021 — Membuka cabang Surabaya dan menambah tim komisioning|2023 — Menembus total 8 MWp kapasitas terpasang|2026 — Melayani 34 kota dengan 260+ proyek selesai',

            // SEO defaults
            'meta_description' => 'Kontraktor panel surya dan PLTS atap terpercaya di Indonesia. Survei gratis, perhitungan penghematan transparan, pemasangan oleh teknisi bersertifikat, dan garansi resmi.',
            'seo_keywords' => 'panel surya, PLTS atap, instalasi panel surya, harga panel surya, kontraktor PLTS, energi surya Indonesia, solar panel Indonesia',
            'feed_description' => 'Artikel edukasi seputar panel surya, PLTS atap, perhitungan penghematan listrik, dan perkembangan regulasi energi terbarukan di Indonesia.',

            'services_meta_title' => 'Layanan Instalasi Panel Surya & PLTS',
            'services_meta_description' => 'Layanan PLTS lengkap: survei dan studi kelayakan, desain sistem, pemasangan, pengurusan perizinan, hingga perawatan dan pemantauan produksi.',
            'projects_meta_title' => 'Portofolio Proyek PLTS',
            'projects_meta_description' => 'Lihat proyek panel surya yang sudah kami selesaikan untuk rumah tinggal, gedung komersial, pabrik, dan lokasi terpencil di berbagai daerah Indonesia.',
            'gallery_meta_title' => 'Galeri Dokumentasi Pekerjaan',
            'gallery_meta_description' => 'Dokumentasi pemasangan panel surya, inverter, struktur rangka, dan komisioning sistem PLTS oleh tim PLTSINDONESIA.',
            'faq_meta_title' => 'Pertanyaan Umum Seputar Panel Surya',
            'faq_meta_description' => 'Jawaban atas pertanyaan yang paling sering diajukan seputar biaya, cara kerja, perizinan, garansi, dan perawatan PLTS atap di Indonesia.',
            'about_meta_title' => 'Tentang PLTSINDONESIA',
            'about_meta_description' => 'Mengenal tim, cara kerja, dan rekam jejak PLTSINDONESIA sebagai kontraktor panel surya dan PLTS di Indonesia sejak 2017.',
            'contact_meta_title' => 'Kontak & Konsultasi Gratis',
            'contact_meta_description' => 'Hubungi tim kami untuk konsultasi gratis seputar pemasangan panel surya dan PLTS di seluruh Indonesia.',

            'rss_enabled' => true,
        ]);

        Setting::flush();
    }
}
