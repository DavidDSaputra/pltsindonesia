<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Adapted PLTS catalogue based on the public product and service information
 * found on jarwinn.com and jarwinn.co.id. Copy is rewritten for PLTSINDONESIA.
 */
class JarwinnContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedServices();
        $this->seedProjects();
        $this->seedFaqs();
        $this->seedArticles();
    }

    protected function seedSettings(): void
    {
        Setting::setMany([
            'tagline' => 'Solusi PLTS terpercaya untuk rumah, bisnis, dan industri',
            'company_description' => 'PLTSINDONESIA menyediakan solusi energi surya untuk rumah, bisnis, dan industri, mulai dari perencanaan, instalasi, hingga perawatan.',
            'phone' => '+62 812 5888 5595',
            'whatsapp' => '6281258885595',
            'email' => 'jarwinnofficial@gmail.com',
            'address' => 'Jl. Raya Taman Tekhno Lt. 2 Blok AA No. 7, Ciater',
            'city' => 'Tangerang Selatan',
            'region' => 'Banten',
            'postal_code' => null,
            'country' => 'Indonesia',
            'service_area_name' => 'Indonesia',
            'service_area' => 'Seluruh Indonesia',
            'social' => [
                'whatsapp' => 'https://wa.me/6281258885595',
            ],
            'hero_title' => 'Mulai transisi ke energi surya yang lebih hemat dan mandiri',
            'hero_subtitle' => 'Kami membantu Anda memilih, memasang, dan merawat sistem PLTS yang sesuai dengan kebutuhan listrik, kondisi lokasi, dan target penghematan.',
            'cta_eyebrow' => 'Konsultasi Gratis',
            'cta_title' => 'Siap mulai menggunakan energi surya?',
            'cta_description' => 'Kirimkan kebutuhan listrik dan lokasi Anda. Tim kami akan membantu menentukan sistem PLTS yang paling sesuai.',
            'cta_points' => 'Konsultasi kebutuhan energi|Rekomendasi sistem yang sesuai|Dukungan instalasi dan purna jual',
            'meta_description' => 'PLTSINDONESIA menyediakan panel surya on-grid, off-grid, hybrid, baterai, PJU tenaga surya, dan pompa air tenaga surya untuk rumah, bisnis, dan industri di seluruh Indonesia.',
            'seo_keywords' => 'PLTS on-grid, PLTS off-grid, PLTS hybrid, baterai solar panel, PJU tenaga surya, pompa air tenaga surya, panel surya Indonesia',
            'services_meta_title' => 'Layanan PLTS, Panel Surya, Baterai, dan Energi Surya',
            'services_meta_description' => 'Temukan solusi PLTS on-grid, off-grid, hybrid, baterai, PJU tenaga surya, dan pompa air surya untuk kebutuhan rumah, bisnis, industri, dan fasilitas publik.',
            'projects_meta_title' => 'Portfolio Proyek PLTS dan Panel Surya',
            'projects_meta_description' => 'Lihat portfolio instalasi PLTS rumah, komersial, industri, sistem off-grid, hybrid, dan solusi energi surya di berbagai wilayah Indonesia.',
            'faq_meta_title' => 'FAQ Panel Surya dan Sistem PLTS',
            'faq_meta_description' => 'Jawaban atas pertanyaan umum tentang PLTS on-grid, off-grid, hybrid, baterai, biaya, perawatan, PJU tenaga surya, dan pompa air surya.',
        ]);
    }

    protected function seedServices(): void
    {
        $services = [
            ['title' => 'PLTS On-Grid', 'slug' => 'plts-on-grid', 'icon' => 'sun', 'image' => '/images/segment-residential.svg', 'summary' => 'Sistem panel surya yang terhubung dengan jaringan PLN untuk membantu mengurangi pemakaian listrik dari jaringan pada siang hari.', 'body' => "PLTS on-grid menghubungkan panel surya dan inverter ke instalasi listrik bangunan yang tetap terhubung ke PLN. Sistem ini cocok untuk rumah, kantor, toko, dan fasilitas yang memiliki konsumsi listrik pada siang hari.\n\n### Keunggulan\n- Investasi awal lebih ringan karena tidak membutuhkan baterai.\n- Energi surya dapat langsung digunakan oleh beban di dalam bangunan.\n- Sistem dapat dipantau melalui aplikasi monitoring.\n- Cocok untuk instalasi rumah dan komersial yang sudah memiliki jaringan PLN.", 'features' => ['Terhubung ke jaringan PLN', 'Tanpa baterai untuk kebutuhan standar', 'Monitoring produksi energi', 'Desain sesuai profil beban']],
            ['title' => 'PLTS Off-Grid', 'slug' => 'plts-off-grid', 'icon' => 'bolt', 'image' => '/images/segment-industrial.svg', 'summary' => 'Sistem tenaga surya mandiri dengan baterai untuk lokasi yang belum terjangkau PLN atau membutuhkan kemandirian energi.', 'body' => "PLTS off-grid bekerja tanpa ketergantungan langsung pada jaringan PLN. Panel surya mengisi baterai pada siang hari, kemudian energi disalurkan ke beban sesuai kebutuhan.\n\nSolusi ini dapat digunakan untuk rumah di daerah terpencil, fasilitas perkebunan, pos keamanan, telekomunikasi, dan lokasi yang jaringan listriknya tidak stabil. Kapasitas panel dan baterai dirancang berdasarkan beban harian serta cadangan energi yang diperlukan.", 'features' => ['Tidak bergantung pada jaringan PLN', 'Bank baterai sesuai kebutuhan beban', 'Cocok untuk daerah terpencil', 'Dapat dipadukan dengan genset']],
            ['title' => 'PLTS Hybrid', 'slug' => 'plts-hybrid', 'icon' => 'battery', 'image' => '/images/segment-business.svg', 'summary' => 'Kombinasi panel surya, baterai, dan PLN untuk menghemat listrik sekaligus menyediakan cadangan saat jaringan padam.', 'body' => "Sistem hybrid menggabungkan manfaat PLTS on-grid dan off-grid. Panel surya melayani beban dan mengisi baterai, sementara PLN menjadi sumber pendukung ketika produksi surya dan cadangan baterai tidak mencukupi.\n\nDengan panel beban kritis, sistem dapat menjaga peralatan penting tetap menyala saat pemadaman, seperti server, pompa, perangkat jaringan, sistem keamanan, dan penerangan utama.", 'features' => ['Panel surya dan baterai terintegrasi', 'Cadangan listrik saat pemadaman', 'Prioritas beban kritis', 'Pengaturan energi otomatis']],
            ['title' => 'Baterai & Energy Storage', 'slug' => 'baterai-energy-storage', 'icon' => 'battery', 'image' => '/images/gallery-baterai.svg', 'summary' => 'Penyimpanan energi untuk meningkatkan pemakaian listrik surya dan menyediakan cadangan daya ketika dibutuhkan.', 'body' => "Baterai energy storage menyimpan energi yang dihasilkan panel surya agar dapat digunakan pada malam hari atau ketika jaringan listrik padam. Kapasitas baterai ditentukan dari beban kritis, durasi cadangan, dan pola produksi sistem.\n\nKami membantu memilih konfigurasi baterai, inverter, proteksi, dan sistem monitoring yang aman untuk kebutuhan rumah maupun bisnis.", 'features' => ['Baterai lithium untuk sistem surya', 'Backup untuk beban penting', 'Monitoring status dan kapasitas', 'Dapat diintegrasikan ke sistem hybrid']],
            ['title' => 'PJU Tenaga Surya', 'slug' => 'pju-tenaga-surya', 'icon' => 'sun', 'image' => '/images/showcase/solar-streetlight.png', 'summary' => 'Lampu penerangan jalan mandiri dengan panel surya dan baterai untuk area publik, perumahan, kawasan industri, dan jalan lingkungan.', 'body' => "PJU tenaga surya menggunakan panel surya untuk mengisi baterai pada siang hari dan menyalakan lampu secara otomatis pada malam hari. Solusi ini membantu mengurangi pekerjaan penarikan kabel dan biaya listrik untuk lokasi outdoor.\n\nSpesifikasi lampu, panel, baterai, tiang, dan durasi pencahayaan disesuaikan dengan lebar jalan, tinggi pemasangan, tingkat cahaya yang dibutuhkan, serta kondisi lokasi.", 'features' => ['Menyala otomatis saat malam', 'Tidak membutuhkan kabel listrik panjang', 'Baterai dan panel terintegrasi', 'Cocok untuk area publik dan industri']],
            ['title' => 'Pompa Air Tenaga Surya', 'slug' => 'pompa-air-tenaga-surya', 'icon' => 'bolt', 'image' => '/images/showcase/solar-panel.png', 'summary' => 'Sistem pompa air bertenaga matahari untuk irigasi, peternakan, perkebunan, dan penyediaan air bersih.', 'body' => "Pompa air tenaga surya mengubah energi matahari menjadi daya untuk mengalirkan air tanpa bergantung penuh pada listrik jaringan atau bahan bakar diesel. Sistem dapat dipakai untuk sumur, tandon, irigasi, peternakan, dan fasilitas air bersih.\n\nKami menghitung kebutuhan debit, tinggi angkat, jarak pipa, pola pemakaian, serta kapasitas panel dan pompa sebelum menentukan konfigurasi.", 'features' => ['Untuk irigasi dan air bersih', 'Mengurangi biaya bahan bakar', 'Kapasitas disesuaikan debit air', 'Dapat memakai tandon sebagai penyimpanan']],
        ];

        foreach ($services as $index => $service) {
            Service::query()->updateOrCreate(['slug' => $service['slug']], $service + ['sort_order' => ($index + 1) * 10, 'is_active' => true]);
        }
    }

    protected function seedProjects(): void
    {
        $projects = [
            ['title' => 'PLTS Off-Grid PT KAI Palembang', 'slug' => 'plts-off-grid-kai-palembang', 'client' => 'PT Kereta Api Indonesia', 'location' => 'Palembang, Sumatera Selatan', 'capacity_kwp' => 1.60, 'completed_on' => '2025-11-01', 'summary' => 'Sistem off-grid dengan panel 1,6 kWp dan inverter 1,5 kW untuk kebutuhan listrik mandiri.', 'body' => 'Proyek ini menggunakan panel surya dan penyimpanan energi untuk menyediakan listrik mandiri pada lokasi operasional. Sistem dirancang dengan mempertimbangkan beban harian, kebutuhan cadangan, dan kemudahan pemantauan.', 'image' => '/images/photos/panels.jpg', 'is_featured' => true],
            ['title' => 'PLTS On-Grid ISUZU Warung Buncit', 'slug' => 'plts-on-grid-isuzu-warung-buncit', 'client' => 'ISUZU', 'location' => 'Warung Buncit, Jakarta Selatan', 'capacity_kwp' => 12.00, 'completed_on' => '2025-10-01', 'summary' => 'Instalasi panel surya 12 kWp dengan inverter on-grid 10 kW untuk mendukung efisiensi energi fasilitas.', 'body' => 'Sistem PLTS on-grid dipasang untuk memanfaatkan produksi listrik surya pada jam operasional. Penempatan modul dan jalur kabel dirancang agar tetap rapi, aman, dan mudah dirawat.', 'image' => '/images/photos/field.jpg', 'is_featured' => true],
            ['title' => 'PLTS On-Grid ISUZU Pramuka', 'slug' => 'plts-on-grid-isuzu-pramuka', 'client' => 'ISUZU', 'location' => 'Pramuka, Jakarta Timur', 'capacity_kwp' => 12.00, 'completed_on' => '2025-09-01', 'summary' => 'Sistem PLTS on-grid 12 kWp untuk membantu menurunkan penggunaan listrik jaringan pada siang hari.', 'body' => 'Proyek ini memanfaatkan area atap yang tersedia untuk menghasilkan listrik secara langsung bagi beban fasilitas. Sistem dilengkapi inverter on-grid dan monitoring produksi.', 'image' => '/images/photos/hero.jpg', 'is_featured' => false],
            ['title' => 'PLTS Hybrid Kembangan', 'slug' => 'plts-hybrid-kembangan', 'client' => 'Pelanggan Komersial', 'location' => 'Kembangan, Jakarta Barat', 'capacity_kwp' => 11.60, 'completed_on' => '2025-08-01', 'summary' => 'Sistem hybrid 11,6 kWp untuk menggabungkan penghematan energi surya dan cadangan baterai.', 'body' => 'Konfigurasi hybrid dipilih untuk menjaga beban penting tetap beroperasi ketika pasokan listrik membutuhkan cadangan. Sistem panel, inverter, dan baterai disusun untuk mendukung penggunaan energi yang lebih stabil.', 'image' => '/images/photos/installer.jpg', 'is_featured' => false],
            ['title' => 'PLTS Rumah Tinggal BSD City', 'slug' => 'plts-rumah-tinggal-bsd-city', 'client' => 'Pelanggan Residensial', 'location' => 'BSD City, Tangerang Selatan', 'capacity_kwp' => 3.00, 'completed_on' => '2025-07-01', 'summary' => 'Sistem PLTS rumah tinggal 3 kWp untuk membantu mengurangi pemakaian listrik dari PLN pada siang hari.', 'body' => 'Instalasi residensial dirancang berdasarkan luas atap, arah matahari, dan kebiasaan penggunaan listrik keluarga. Panel ditempatkan pada area dengan bayangan minimal dan dihubungkan ke inverter on-grid.', 'image' => '/images/photos/grass.jpg', 'is_featured' => false],
            ['title' => 'PLTS On-Grid Tuban Bali', 'slug' => 'plts-on-grid-tuban-bali', 'client' => 'Pelanggan Residensial', 'location' => 'Tuban, Bali', 'capacity_kwp' => 2.00, 'completed_on' => '2025-06-01', 'summary' => 'Instalasi PLTS on-grid 2 kWp untuk kebutuhan listrik rumah tinggal di kawasan Bali.', 'body' => 'Sistem dirancang untuk memaksimalkan pemakaian langsung energi surya pada siang hari. Tim melakukan pemeriksaan atap, penataan kabel, pemasangan proteksi, dan pengujian produksi setelah instalasi.', 'image' => '/images/photos/aerial.jpg', 'is_featured' => false],
        ];

        foreach ($projects as $index => $project) {
            Project::query()->updateOrCreate(['slug' => $project['slug']], $project + ['sort_order' => ($index + 1) * 10, 'is_active' => true]);
        }
    }

    protected function seedFaqs(): void
    {
        $faqs = [
            ['group' => 'Sistem PLTS', 'question' => 'Apa perbedaan PLTS on-grid, off-grid, dan hybrid?', 'answer' => 'PLTS on-grid terhubung ke PLN dan biasanya tidak memakai baterai. PLTS off-grid bekerja mandiri dengan baterai untuk lokasi tanpa jaringan PLN. PLTS hybrid menggabungkan panel surya, baterai, dan PLN sehingga dapat menghemat energi sekaligus menyediakan cadangan saat listrik padam.'],
            ['group' => 'Sistem PLTS', 'question' => 'Apakah panel surya tetap menghasilkan listrik saat mendung?', 'answer' => 'Ya. Panel surya tetap menghasilkan listrik dari cahaya yang tersedia, tetapi produksinya lebih rendah dibandingkan saat cuaca cerah. Kapasitas sistem sebaiknya dihitung berdasarkan pola cuaca tahunan dan kebutuhan energi lokasi.'],
            ['group' => 'Baterai', 'question' => 'Kapan sistem PLTS membutuhkan baterai?', 'answer' => 'Baterai biasanya diperlukan untuk sistem off-grid, kebutuhan backup saat pemadaman, atau lokasi yang ingin memakai energi surya pada malam hari. Untuk PLTS on-grid standar, baterai tidak selalu diperlukan.'],
            ['group' => 'Perawatan', 'question' => 'Seberapa sering panel surya perlu dibersihkan?', 'answer' => 'Frekuensinya tergantung debu, polusi, pepohonan, dan kondisi lokasi. Umumnya pembersihan dilakukan setiap tiga sampai enam bulan, lalu disesuaikan berdasarkan hasil monitoring produksi.'],
            ['group' => 'PJU & Pompa', 'question' => 'Di mana PJU tenaga surya dapat dipasang?', 'answer' => 'PJU tenaga surya dapat dipasang di jalan lingkungan, area perumahan, taman, kawasan industri, fasilitas publik, area parkir, dan lokasi yang sulit ditarik kabel listrik.'],
            ['group' => 'PJU & Pompa', 'question' => 'Apakah pompa air tenaga surya bisa dipakai untuk irigasi?', 'answer' => 'Bisa. Sistem pompa dirancang berdasarkan debit air, tinggi angkat, jarak distribusi, dan jam operasional. Air dapat dipompa langsung saat matahari tersedia atau disimpan dalam tandon untuk digunakan sesuai kebutuhan.'],
            ['group' => 'Investasi', 'question' => 'Bagaimana cara menghitung kapasitas PLTS yang tepat?', 'answer' => 'Kapasitas ditentukan dari tagihan dan profil beban listrik, luas atap, arah serta bayangan, target penghematan, dan anggaran. Kami menyarankan survei dan simulasi sebelum menentukan jumlah panel.'],
            ['group' => 'Investasi', 'question' => 'Apakah tersedia konsultasi dan survei lokasi?', 'answer' => 'Ya. Konsultasi awal membantu memetakan kebutuhan energi, kondisi lokasi, pilihan sistem, dan estimasi kapasitas sebelum Anda menerima rekomendasi teknis.'],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::query()->updateOrCreate(['question' => $faq['question']], $faq + ['sort_order' => ($index + 1) * 10, 'is_active' => true]);
        }
    }

    protected function seedArticles(): void
    {
        $categories = [
            'panduan' => Category::query()->updateOrCreate(['slug' => 'panduan'], ['name' => 'Panduan', 'description' => 'Panduan praktis seputar energi surya dan PLTS.', 'sort_order' => 10])->id,
            'teknologi' => Category::query()->updateOrCreate(['slug' => 'teknologi'], ['name' => 'Teknologi', 'description' => 'Informasi teknologi panel, inverter, baterai, dan sistem surya.', 'sort_order' => 20])->id,
            'solusi' => Category::query()->updateOrCreate(['slug' => 'solusi'], ['name' => 'Solusi Energi', 'description' => 'Penerapan PLTS untuk rumah, bisnis, fasilitas publik, dan industri.', 'sort_order' => 30])->id,
        ];
        $tags = [];
        foreach (['plts-on-grid' => 'PLTS On-Grid', 'plts-off-grid' => 'PLTS Off-Grid', 'plts-hybrid' => 'PLTS Hybrid', 'baterai' => 'Baterai', 'pju' => 'PJU Tenaga Surya', 'pompa-air' => 'Pompa Air Surya'] as $slug => $name) {
            $tags[$slug] = Tag::query()->updateOrCreate(['slug' => $slug], ['name' => $name])->id;
        }
        $author = User::query()->whereIn('role', [UserRole::Admin, UserRole::Editor])->orderBy('id')->value('id');
        $articles = [
            ['title' => 'PLTS On-Grid, Off-Grid, atau Hybrid: Mana yang Tepat?', 'slug' => 'memilih-plts-on-grid-off-grid-hybrid', 'category' => 'panduan', 'tags' => ['plts-on-grid', 'plts-off-grid', 'plts-hybrid'], 'cover_image' => '/images/segment-residential.svg', 'excerpt' => 'Kenali perbedaan tiga konfigurasi PLTS dan pilih sistem yang sesuai dengan jaringan listrik, kebutuhan backup, dan kondisi lokasi Anda.', 'body' => "Memilih sistem PLTS tidak cukup hanya berdasarkan jumlah panel. Hal pertama yang perlu dipahami adalah hubungan sistem dengan jaringan PLN dan kebutuhan penyimpanan energi.\n\n## PLTS on-grid\n\nSistem on-grid bekerja bersama jaringan PLN tanpa baterai. Energi surya dipakai langsung oleh beban di siang hari, sementara PLN membantu ketika produksi surya tidak cukup. Konfigurasi ini cocok untuk rumah, toko, kantor, dan bisnis yang sudah memiliki jaringan PLN.\n\n## PLTS off-grid\n\nSistem off-grid tidak bergantung pada PLN. Panel mengisi baterai yang kemudian menyuplai beban sesuai kebutuhan. Sistem ini cocok untuk lokasi terpencil atau fasilitas dengan jaringan yang tidak tersedia.\n\n## PLTS hybrid\n\nSistem hybrid menggabungkan panel, baterai, dan PLN. Konfigurasi ini cocok untuk pengguna yang ingin mengurangi tagihan sekaligus memiliki cadangan listrik saat pemadaman.\n\nKapasitas terbaik ditentukan melalui audit beban, survei atap, dan simulasi produksi—bukan dari ukuran standar yang sama untuk semua bangunan.", 'seo_title' => 'Perbedaan PLTS On-Grid, Off-Grid, dan Hybrid', 'seo_description' => 'Pelajari perbedaan PLTS on-grid, off-grid, dan hybrid serta cara memilih sistem panel surya yang sesuai kebutuhan energi Anda.', 'seo_keywords' => 'plts on grid, plts off grid, plts hybrid, memilih sistem plts'],
            ['title' => 'Baterai PLTS: Fungsi, Kapasitas, dan Kapan Dibutuhkan', 'slug' => 'baterai-plts-fungsi-dan-kebutuhan', 'category' => 'teknologi', 'tags' => ['baterai', 'plts-hybrid'], 'cover_image' => '/images/gallery-baterai.svg', 'excerpt' => 'Baterai menyimpan energi surya untuk digunakan kemudian. Pahami kapan baterai layak dipasang dan bagaimana menentukan kapasitasnya.', 'body' => "Baterai energy storage menyimpan listrik yang dihasilkan panel pada siang hari agar dapat digunakan pada malam hari atau ketika jaringan PLN padam.\n\n## Kapan baterai diperlukan?\n\nBaterai menjadi bagian penting pada sistem off-grid, sistem hybrid, serta lokasi yang membutuhkan suplai untuk beban kritis. Pada sistem on-grid biasa, baterai belum tentu ekonomis jika tujuan utama hanya mengurangi tagihan siang hari.\n\n## Menentukan kapasitas\n\nKapasitas baterai dihitung dari daya beban penting dan berapa lama beban tersebut harus bertahan. Server, pompa, jaringan, penerangan, dan peralatan keselamatan biasanya diprioritaskan sebelum beban non-esensial.\n\nSelain kapasitas, perhatikan jenis baterai, kedalaman pemakaian, sistem manajemen baterai, ventilasi, proteksi, garansi, dan layanan purna jual.", 'seo_title' => 'Baterai PLTS: Fungsi dan Cara Menentukan Kapasitas', 'seo_description' => 'Panduan memilih baterai untuk PLTS hybrid dan off-grid, termasuk fungsi, kebutuhan backup, kapasitas, dan faktor keselamatan.', 'seo_keywords' => 'baterai plts, energy storage, baterai solar panel, plts hybrid'],
            ['title' => 'PJU Tenaga Surya untuk Jalan dan Area Publik', 'slug' => 'pju-tenaga-surya-untuk-jalan-dan-area-publik', 'category' => 'solusi', 'tags' => ['pju'], 'cover_image' => '/images/showcase/solar-streetlight.png', 'excerpt' => 'PJU tenaga surya membantu menerangi jalan dan area publik tanpa penarikan kabel listrik yang panjang. Ini hal yang perlu direncanakan.', 'body' => "PJU tenaga surya menggabungkan panel, baterai, lampu LED, kontroler, dan tiang dalam satu sistem penerangan mandiri. Pada siang hari panel mengisi baterai, lalu lampu menyala otomatis ketika cahaya lingkungan menurun.\n\nSebelum memilih produk, lakukan survei terhadap lebar jalan, jarak antar tiang, tinggi pemasangan, tingkat pencahayaan, durasi malam, dan potensi bayangan. Kapasitas baterai juga harus memperhitungkan beberapa hari dengan cuaca mendung.\n\nPJU surya dapat diterapkan pada jalan perumahan, area parkir, taman, kawasan industri, fasilitas publik, dan lokasi yang biaya penarikan kabelnya terlalu tinggi.", 'seo_title' => 'PJU Tenaga Surya: Manfaat dan Perencanaan Sistem', 'seo_description' => 'Panduan memilih PJU tenaga surya untuk jalan, area publik, kawasan industri, dan fasilitas outdoor.', 'seo_keywords' => 'pju tenaga surya, lampu jalan solar panel, solar street light'],
            ['title' => 'Pompa Air Tenaga Surya untuk Irigasi dan Air Bersih', 'slug' => 'pompa-air-tenaga-surya-untuk-irigasi', 'category' => 'solusi', 'tags' => ['pompa-air'], 'cover_image' => '/images/showcase/solar-panel.png', 'excerpt' => 'Pompa air surya dapat membantu irigasi dan penyediaan air bersih dengan mengurangi ketergantungan pada listrik jaringan atau bahan bakar.', 'body' => "Pompa air tenaga surya memanfaatkan energi matahari untuk memindahkan air dari sumur, sungai, atau sumber lain menuju tandon dan lahan yang membutuhkan. Sistem ini dapat menjadi alternatif untuk area pertanian, perkebunan, peternakan, dan fasilitas air bersih.\n\nPerancangan dimulai dari kebutuhan debit, kedalaman sumber air, tinggi angkat, panjang pipa, jadwal penggunaan, dan luas area layanan. Penyimpanan air dalam tandon sering kali lebih sederhana dan lebih ekonomis daripada memasang baterai besar.\n\nDengan perhitungan yang tepat, pompa dapat bekerja otomatis saat energi surya tersedia dan mengisi tandon untuk digunakan sesuai kebutuhan.", 'seo_title' => 'Pompa Air Tenaga Surya untuk Irigasi dan Air Bersih', 'seo_description' => 'Ketahui cara kerja pompa air tenaga surya, kebutuhan desain, dan penerapannya untuk irigasi, perkebunan, peternakan, dan air bersih.', 'seo_keywords' => 'pompa air tenaga surya, solar water pump, pompa irigasi surya'],
        ];

        foreach ($articles as $index => $article) {
            $record = Article::query()->updateOrCreate(['slug' => $article['slug']], [
                'user_id' => $author,
                'category_id' => $categories[$article['category']],
                'title' => $article['title'], 'excerpt' => $article['excerpt'], 'body' => $article['body'],
                'cover_image' => $article['cover_image'], 'status' => ArticleStatus::Published, 'is_featured' => $index === 0,
                'published_at' => now()->startOfDay()->subDays($index + 1)->setTime(9, 30), 'views_count' => 0,
                'seo_title' => $article['seo_title'], 'seo_description' => $article['seo_description'], 'seo_keywords' => $article['seo_keywords'],
            ]);
            $record->tags()->sync(array_map(fn (string $slug) => $tags[$slug], $article['tags']));
        }
    }
}
