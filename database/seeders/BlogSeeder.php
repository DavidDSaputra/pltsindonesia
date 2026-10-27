<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Blog content: categories, tags and a set of original Indonesian articles.
 *
 * Every record is keyed on its slug, so re-running `db:seed` refreshes the
 * copy in place instead of duplicating it. Publish dates are offset from the
 * current day rather than hard-coded so the archive never looks abandoned.
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $tags = $this->seedTags();
        $authors = $this->authorIds();

        foreach ($this->articles() as $index => $article) {
            $published = $article['status'] === ArticleStatus::Published;

            $record = Article::query()->updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'user_id' => $authors === [] ? null : $authors[$index % count($authors)],
                    'category_id' => $categories[$article['category']] ?? null,
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'cover_image' => $article['cover_image'],
                    'status' => $article['status'],
                    'is_featured' => $article['is_featured'],
                    'published_at' => $published
                        ? now()->startOfDay()->subDays($article['days_ago'])->setTime(9, 30)
                        : null,
                    'views_count' => $article['views_count'],
                    'seo_title' => $article['seo_title'],
                    'seo_description' => $article['seo_description'],
                    'seo_keywords' => $article['seo_keywords'],
                ],
            );

            $record->tags()->sync(array_values(array_filter(
                array_map(fn (string $slug) => $tags[$slug] ?? null, $article['tags'])
            )));
        }

        $this->command?->info(sprintf(
            'Blog ready — %d kategori, %d tag, %d artikel.',
            count($categories),
            count($tags),
            count($this->articles()),
        ));
    }

    /**
     * @return array<string, int> Category slug => id
     */
    protected function seedCategories(): array
    {
        $categories = [
            ['name' => 'Panduan', 'slug' => 'panduan', 'description' => 'Penjelasan langkah demi langkah untuk pemilik rumah dan pengelola gedung.'],
            ['name' => 'Biaya & Investasi', 'slug' => 'biaya-investasi', 'description' => 'Rincian anggaran, skema pembiayaan, dan perhitungan balik modal.'],
            ['name' => 'Regulasi', 'slug' => 'regulasi', 'description' => 'Perizinan dan ketentuan ekspor listrik yang berlaku di Indonesia.'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Perkembangan modul, inverter, baterai, dan sistem pemantauan.'],
            ['name' => 'Studi Kasus', 'slug' => 'studi-kasus', 'description' => 'Laporan lapangan dari proyek yang sudah kami kerjakan.'],
        ];

        $ids = [];

        foreach ($categories as $index => $category) {
            $ids[$category['slug']] = Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['sort_order' => ($index + 1) * 10],
            )->id;
        }

        return $ids;
    }

    /**
     * @return array<string, int> Tag slug => id
     */
    protected function seedTags(): array
    {
        $tags = [
            'panel-surya' => 'Panel Surya',
            'on-grid' => 'On-Grid',
            'off-grid' => 'Off-Grid',
            'hybrid' => 'Hybrid',
            'net-metering' => 'Net Metering',
            'inverter' => 'Inverter',
            'baterai' => 'Baterai',
            'perawatan' => 'Perawatan',
            'monitoring' => 'Monitoring',
            'roi' => 'ROI',
            'biaya' => 'Biaya',
            'regulasi' => 'Regulasi',
            'atap-rumah' => 'Atap Rumah',
            'industri' => 'Industri',
            'teknis' => 'Teknis',
            'studi-kasus' => 'Studi Kasus',
            'ekspor-listrik' => 'Ekspor Listrik',
        ];

        $ids = [];

        foreach ($tags as $slug => $name) {
            $ids[$slug] = Tag::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            )->id;
        }

        return $ids;
    }

    /**
     * Authors are picked by role rather than by e-mail so the seeder keeps
     * working when the account addresses are overridden through the environment.
     *
     * @return array<int, int>
     */
    protected function authorIds(): array
    {
        return User::query()
            ->whereIn('role', [UserRole::Admin, UserRole::Editor])
            ->orderBy('id')
            ->pluck('id')
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function articles(): array
    {
        return [
            [
                'title' => 'Cara Kerja PLTS On-Grid dan Kenapa Tagihan Listrik Bisa Turun',
                'slug' => 'cara-kerja-plts-on-grid',
                'category' => 'panduan',
                'tags' => ['panel-surya', 'on-grid', 'net-metering'],
                'cover_image' => '/images/segment-residential.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => true,
                'days_ago' => 3,
                'views_count' => 2431,
                'excerpt' => 'Sistem on-grid tidak memakai baterai, tetapi tetap mampu memangkas tagihan. Begini alur energi dari atap sampai meteran dan apa yang menentukan besar penghematannya.',
                'seo_title' => 'Cara Kerja PLTS On-Grid: Skema, Komponen, dan Penghematan',
                'seo_description' => 'Penjelasan sederhana alur energi PLTS on-grid dari modul ke meteran, komponen yang dibutuhkan, serta faktor penentu besar penghematan tagihan listrik.',
                'seo_keywords' => 'cara kerja plts on grid, plts tanpa baterai, ekspor listrik pln, penghematan tagihan listrik',
                'body' => <<<'MD'
                    Sistem PLTS on-grid adalah konfigurasi paling umum untuk rumah tinggal dan gedung komersial di Indonesia. Alasannya sederhana: tanpa baterai, biaya investasinya paling rendah, sementara manfaatnya langsung terasa pada tagihan bulan berikutnya.

                    ## Alur energi dari atap ke meteran

                    Modul panel surya menghasilkan arus searah (DC) begitu terkena cahaya. Arus ini mengalir lewat kabel DC ke panel proteksi, kemudian masuk ke inverter yang mengubahnya menjadi arus bolak-balik (AC) 220 volt agar cocok dengan instalasi rumah.

                    Dari inverter, listrik masuk ke panel distribusi gedung. Di titik ini terjadi hal yang paling sering disalahpahami: **listrik tidak "disimpan" atau "dipilih"**. Beban rumah selalu mengambil daya dari sumber mana pun yang tersedia pada saat itu. Kalau produksi surya cukup, beban dipasok dari atap. Kalau kurang, kekurangannya otomatis ditarik dari PLN.

                    ## Peran meteran ekspor-impor

                    Meteran dua arah mencatat dua hal secara terpisah: energi yang Anda ambil dari PLN (impor) dan energi berlebih yang Anda kirim ke jaringan (ekspor). Ekspor terjadi ketika produksi surya melampaui konsumsi, biasanya sekitar tengah hari saat rumah kosong.

                    Inilah sumber penghematan kedua. Nilai ekspor diperhitungkan sebagai pengurang tagihan sesuai ketentuan yang berlaku. Besaran faktor pengalinya diatur regulasi dan dapat berubah, jadi kami selalu menghitung skenario konservatif agar proyeksi tidak bergantung pada angka terbaik.

                    ## Yang menentukan besar penghematan

                    Empat faktor berikut berpengaruh paling besar:

                    - **Profil beban siang hari.** Semakin banyak konsumsi antara pukul 09.00 sampai 15.00, semakin besar porsi produksi yang terpakai sendiri. Memakai listrik sendiri selalu lebih menguntungkan daripada mengekspornya.
                    - **Luas dan orientasi atap.** Atap menghadap utara dengan kemiringan 10 sampai 15 derajat menghasilkan energi tertinggi sepanjang tahun di Indonesia.
                    - **Bayangan.** Satu bayangan cerobong atau pohon bisa memangkas produksi satu string secara tidak proporsional.
                    - **Kapasitas terpasang.** Menambah kapasitas hanya berguna kalau masih ada ruang ekspor dan masih ada tagihan yang bisa dikurangi.

                    ## Kapan on-grid bukan pilihan tepat

                    Kalau lokasi Anda sering mengalami pemadaman, on-grid murni tidak membantu. Inverter on-grid wajib mati ketika jaringan PLN padam demi keselamatan petugas. Untuk kebutuhan seperti ini, sistem hybrid dengan baterai adalah solusi yang benar.

                    Kami membahas perbedaannya pada artikel tentang memilih jenis inverter.
                    MD,
            ],
            [
                'title' => 'Rincian Biaya Pasang Panel Surya Rumah 2.200 VA sampai 6.600 VA',
                'slug' => 'rincian-biaya-pasang-panel-surya-rumah',
                'category' => 'biaya-investasi',
                'tags' => ['biaya', 'roi', 'atap-rumah'],
                'cover_image' => '/images/gallery-atap-rumah.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => true,
                'days_ago' => 9,
                'views_count' => 3187,
                'excerpt' => 'Berapa sebenarnya anggaran yang perlu disiapkan untuk PLTS atap rumah? Kami bedah komponen biayanya, bukan sekadar menyebut satu angka total.',
                'seo_title' => 'Biaya Pasang Panel Surya Rumah 2026: Rincian per Komponen',
                'seo_description' => 'Rincian biaya pemasangan PLTS atap rumah dari modul, inverter, rangka, kabel, hingga jasa instalasi dan perizinan, lengkap dengan kisaran per kWp.',
                'seo_keywords' => 'biaya pasang panel surya rumah, harga plts atap, investasi panel surya, biaya per kwp',
                'body' => <<<'MD'
                    Pertanyaan pertama yang masuk ke tim kami hampir selalu sama: "habisnya berapa?" Jawaban jujur adalah "tergantung", tetapi itu tidak berguna. Artikel ini membedah komponen biaya supaya Anda bisa menilai penawaran mana pun secara mandiri.

                    ## Enam komponen biaya

                    **Modul panel surya** menyumbang porsi terbesar, biasanya sekitar sepertiga dari total. Selisih harga antar merek mencerminkan efisiensi, toleransi daya, dan panjang garansi. Modul bergaransi performa 25 tahun dengan degradasi rendah layak dibayar lebih mahal.

                    **Inverter** adalah komponen paling kritis karena paling sering menjadi titik kegagalan. Pilih inverter dengan layanan purna jual yang jelas di Indonesia, bukan hanya harga termurah.

                    **Rangka dan mounting** disesuaikan jenis atap. Atap metal standing seam paling murah karena memakai klem. Atap genteng butuh penyesuai khusus, dan atap beton datar memerlukan rangka ballast yang jauh lebih berat.

                    **Kabel, proteksi, dan panel** mencakup kabel DC tahan sinar ultraviolet, pemutus arus, serta proteksi lonjakan arus. Bagian ini sering dipangkas oleh penawar termurah, padahal di sinilah risiko kebakaran bermula.

                    **Jasa instalasi dan komisioning** mencakup survei, perhitungan struktur, pemasangan, pengujian, dan dokumentasi.

                    **Perizinan dan administrasi** mencakup pengajuan ke unit PLN setempat serta penyesuaian meteran.

                    ## Kisaran biaya per kapasitas

                    Untuk sistem on-grid tanpa baterai, biaya total umumnya berada di rentang berikut. Angka ini indikatif dan bergerak mengikuti harga modul serta kurs.

                    - Kapasitas 1,3 sampai 2,2 kWp: cocok untuk daya 2.200 VA, biasanya empat sampai enam modul.
                    - Kapasitas 3 sampai 4,4 kWp: cocok untuk daya 4.400 sampai 5.500 VA, delapan sampai sepuluh modul.
                    - Kapasitas 5 sampai 6,6 kWp: cocok untuk daya 6.600 VA ke atas, membutuhkan atap minimal 30 meter persegi yang bebas bayangan.

                    Semakin besar kapasitas, semakin rendah biaya per kWp karena komponen tetap seperti jasa dan panel proteksi tersebar ke lebih banyak modul.

                    ## Biaya tambahan untuk sistem baterai

                    Menambahkan baterai lithium meningkatkan investasi awal secara signifikan, sering kali dua sampai tiga kali lipat dibandingkan sistem on-grid dengan kapasitas modul yang sama. Baterai baru masuk akal kalau ada salah satu dari tiga kondisi ini: pemadaman sering terjadi, tarif listrik malam jauh lebih mahal, atau Anda ingin mandiri dari jaringan.

                    ## Cara menilai sebuah penawaran

                    Minta rincian per komponen, bukan satu angka gelondongan. Bandingkan tiga hal ini secara setara: merek dan tipe modul, merek dan tipe inverter, serta panjang garansi instalasi. Penawaran yang lebih murah 15 persen tetapi memakai inverter tanpa layanan lokal biasanya berujung lebih mahal pada tahun kelima.

                    Kami menyediakan perhitungan kelayakan tanpa biaya. Kirimkan tagihan listrik tiga bulan terakhir dan foto atap, hasilnya kami kirim dalam bentuk angka yang bisa Anda perdebatkan.
                    MD,
            ],
            [
                'title' => 'Memilih Inverter PLTS: String, Micro-Inverter, atau Hybrid?',
                'slug' => 'memilih-jenis-inverter-plts',
                'category' => 'teknologi',
                'tags' => ['inverter', 'teknis', 'panel-surya'],
                'cover_image' => '/images/gallery-inverter.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'days_ago' => 16,
                'views_count' => 1642,
                'excerpt' => 'Inverter menentukan seberapa banyak energi yang benar-benar Anda dapatkan. Tiga arsitektur ini punya keunggulan berbeda tergantung kondisi atap.',
                'seo_title' => 'Perbandingan Inverter String, Micro-Inverter, dan Hybrid',
                'seo_description' => 'Bandingkan inverter string, micro-inverter, dan inverter hybrid dari sisi harga, dampak bayangan, kemudahan perawatan, dan kecocokan untuk atap Indonesia.',
                'seo_keywords' => 'jenis inverter plts, inverter string vs micro inverter, inverter hybrid, memilih inverter panel surya',
                'body' => <<<'MD'
                    Inverter adalah otak sistem PLTS. Komponen ini mengubah arus DC dari modul menjadi AC yang bisa dipakai, sekaligus menjalankan pelacakan titik daya maksimum, proteksi kebocoran arus, dan pemantauan produksi. Memilih arsitektur yang salah bisa membuang 10 sampai 25 persen potensi energi atap Anda.

                    ## Inverter string

                    Seluruh modul disambung seri menjadi satu atau dua string, lalu satu inverter pusat mengubah dayanya. Ini konfigurasi paling umum di Indonesia karena harganya paling efisien dan perawatannya terpusat.

                    Kelemahannya ada pada bayangan. Karena modul tersambung seri, arus seluruh string dibatasi oleh modul terlemah. Satu bayangan pohon di pagi hari bisa menurunkan produksi sepanjang hari.

                    **Pilih string inverter kalau:** atap bebas bayangan, orientasi modul seragam, dan anggaran menjadi pertimbangan utama.

                    ## Optimizer daya

                    Jalan tengah antara string dan micro-inverter. Satu inverter pusat tetap dipakai, tetapi setiap modul mendapat optimizer yang melakukan pelacakan titik daya maksimum secara mandiri. Hasilnya, modul yang teduh tidak lagi menyeret seluruh string.

                    Biayanya naik sekitar seperempat dibanding sistem string murni, tetapi jauh di bawah micro-inverter penuh.

                    **Pilih optimizer kalau:** ada bayangan sebagian dari cerobong, dinding tetangga, atau pohon, tetapi Anda tetap ingin satu titik perawatan.

                    ## Micro-inverter

                    Setiap modul punya inverter sendiri. Tidak ada tegangan DC tinggi yang berjalan di atap, dan produksi tiap modul terpantau satu per satu.

                    Kelemahannya: perangkat elektronik kini tersebar di seluruh atap. Kalau satu rusak, teknisi harus naik ke atap untuk menggantinya, dan biaya penggantian per unit lebih tinggi.

                    **Pilih micro-inverter kalau:** atap punya banyak bidang dengan orientasi berbeda, ada bayangan kompleks, atau Anda ingin pemantauan tingkat modul.

                    ## Inverter hybrid

                    Hybrid adalah string inverter yang punya jalur pengisian baterai bawaan. Kelebihannya, Anda bisa mulai dengan sistem on-grid sekarang dan menambahkan baterai nanti tanpa mengganti inverter.

                    Untuk bangunan di area yang sering padam, hybrid adalah pilihan paling masuk akal secara ekonomi. Untuk area dengan pasokan stabil, biaya tambahan hybrid jarang kembali.

                    ## Yang sering diabaikan saat memilih

                    Tiga hal ini lebih penting daripada merek:

                    - **Ketersediaan suku cadang dan layanan di Indonesia.** Inverter bagus tanpa layanan lokal akan menjadi masalah pada tahun ketiga.
                    - **Rentang tegangan MPPT.** Menentukan berapa modul yang boleh disambung seri. Rentang sempit memaksa konfigurasi yang tidak efisien pada atap kecil.
                    - **Garansi dan cara klaimnya.** Tanyakan apakah klaim lewat distributor lokal atau harus kirim ke luar negeri.

                    Kalau Anda sudah punya penawaran, kirimkan spesifikasi inverternya. Kami berikan pendapat kedua tanpa biaya.
                    MD,
            ],
            [
                'title' => 'Baterai Lithium untuk PLTS: Kapan Benar-Benar Dibutuhkan?',
                'slug' => 'baterai-lithium-untuk-plts',
                'category' => 'teknologi',
                'tags' => ['baterai', 'hybrid', 'off-grid'],
                'cover_image' => '/images/gallery-baterai.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'days_ago' => 24,
                'views_count' => 1288,
                'excerpt' => 'Baterai menaikkan investasi awal secara besar. Kami jelaskan tiga kondisi yang membuatnya masuk akal, dan tiga kondisi yang membuatnya hanya memboroskan uang.',
                'seo_title' => 'Apakah PLTS Perlu Baterai? Tiga Kondisi Penentu',
                'seo_description' => 'Panduan menilai kebutuhan baterai lithium pada PLTS rumah dan bisnis, termasuk perhitungan sederhana, umur pakai, dan kondisi yang tidak memerlukan baterai.',
                'seo_keywords' => 'baterai plts, baterai lithium panel surya, plts hybrid, apakah perlu baterai plts',
                'body' => <<<'MD'
                    Baterai adalah komponen termahal kedua setelah modul, dan satu-satunya komponen yang umurnya terus berkurang walaupun tidak dipakai. Keputusan menambahkannya harus didasarkan pada perhitungan, bukan pada keinginan mandiri dari jaringan.

                    ## Tiga kondisi yang membuat baterai masuk akal

                    **Pertama, pasokan jaringan tidak andal.** Kalau pemadaman terjadi lebih dari beberapa kali sebulan dan Anda punya beban kritis seperti pompa air, kulkas vaksin, server, atau peralatan medis, baterai berfungsi sebagai cadangan. Nilainya bukan penghematan, melainkan menghindari kerugian akibat berhenti beroperasi.

                    **Kedua, selisih tarif yang besar antara siang dan malam.** Ketika tarif impor malam jauh lebih mahal daripada nilai energi yang diekspor siang, menyimpan kelebihan produksi lalu memakainya pada jam beban puncak menghasilkan keuntungan nyata. Ini paling relevan untuk pelanggan bisnis dengan tarif berbasis waktu pemakaian.

                    **Ketiga, tidak ada jaringan sama sekali.** Untuk lokasi terpencil, pembanding baterai bukanlah sistem on-grid, melainkan genset. Dalam hitungan biaya siklus hidup, baterai hampir selalu menang karena genset butuh bahan bakar, perawatan rutin, dan penggantian.

                    ## Tiga kondisi yang membuat baterai sia-sia

                    - Jaringan stabil dan tarif datar sepanjang hari. Uang yang sama lebih baik dipakai menambah kapasitas modul.
                    Rumah kosong sepanjang siang tetapi baterai dibeli tanpa menghitung bahwa energi malam bisa didapat murah dari jaringan.
                    - Kapasitas baterai jauh melebihi kebutuhan cadangan. Membeli 20 kWh untuk mencadangkan 2 kWh beban kritis adalah pemborosan.

                    ## Menghitung ukuran baterai

                    Mulai dari beban yang harus tetap hidup saat padam, bukan dari kapasitas modul. Jumlahkan daya perangkat kritis, kalikan dengan durasi cadangan yang diinginkan, lalu bagi dengan kedalaman pelepasan yang aman.

                    Contoh: pompa air 350 watt, kulkas 150 watt, dan penerangan 100 watt harus hidup selama 4 jam. Total 600 watt dikali 4 jam sama dengan 2,4 kWh. Dengan kedalaman pelepasan 90 persen, kapasitas terpasang yang dibutuhkan sekitar 2,7 kWh. Satu unit 5 kWh sudah memberi margin lebar.

                    ## Umur pakai dan cara membacanya

                    Baterai lithium besi fosfat umumnya diberi spesifikasi 6.000 siklus pada kedalaman pelepasan tertentu. Satu siklus penuh per hari berarti umur teoritis lebih dari 16 tahun, tetapi kapasitasnya menurun bertahap. Perhatikan dua angka pada lembar spesifikasi: jumlah siklus dan sisa kapasitas pada akhir masa garansi.

                    Suhu juga berpengaruh. Ruang baterai yang panas memperpendek umur secara nyata, jadi tempatkan di area teduh dengan sirkulasi udara.

                    ## Kesimpulan praktis

                    Untuk mayoritas rumah di kota besar dengan pasokan stabil, jawaban kami adalah mulai tanpa baterai. Pasang sistem on-grid, pantau produksinya selama enam bulan, lalu putuskan. Kalau Anda memilih inverter hybrid sejak awal, menambahkan baterai nanti hanya tinggal memasang unitnya tanpa membongkar sistem.
                    MD,
            ],
            [
                'title' => 'Aturan Ekspor Listrik PLTS Atap ke Jaringan PLN',
                'slug' => 'aturan-ekspor-listrik-plts-ke-pln',
                'category' => 'regulasi',
                'tags' => ['regulasi', 'net-metering', 'ekspor-listrik'],
                'cover_image' => '/images/segment-industrial.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'days_ago' => 31,
                'views_count' => 2054,
                'excerpt' => 'Sebelum menghitung penghematan, pahami dulu batas kapasitas yang boleh dipasang dan bagaimana kelebihan energi Anda diperhitungkan.',
                'seo_title' => 'Aturan PLTS Atap dan Ekspor Listrik ke PLN yang Berlaku',
                'seo_description' => 'Ringkasan ketentuan pemasangan PLTS atap, batas kapasitas, proses perizinan, dan cara perhitungan energi yang diekspor ke jaringan PLN.',
                'seo_keywords' => 'aturan plts atap, regulasi ekspor listrik pln, izin pasang plts, batas kapasitas plts',
                'body' => <<<'MD'
                    Ketentuan PLTS atap di Indonesia berubah beberapa kali dalam satu dekade terakhir. Karena itu artikel ini berfokus pada kerangka berpikirnya, bukan pada satu angka yang bisa kedaluwarsa. Kami selalu memverifikasi ketentuan terbaru ke unit PLN setempat sebelum mengajukan permohonan pelanggan.

                    ## Batas kapasitas yang boleh dipasang

                    Kapasitas sistem dibatasi berdasarkan daya tersambung pelanggan dan kemampuan jaringan setempat. Prinsipnya, sistem tidak boleh dirancang untuk menghasilkan energi jauh melebihi konsumsi sendiri, karena jaringan distribusi dirancang untuk aliran satu arah.

                    Sebelum menentukan kapasitas, kami meminta data pemakaian minimal 12 bulan. Kalau data tidak tersedia, kami mengukur beban dengan alat pencatat selama dua minggu untuk mendapatkan kurva konsumsi yang sebenarnya.

                    ## Meteran dua arah

                    Pelanggan PLTS atap memakai meteran ekspor-impor yang mencatat aliran ke dua arah secara terpisah. Penggantian meteran ini bagian dari proses pengajuan dan bukan sesuatu yang bisa dipasang sendiri.

                    Penting untuk dipahami: meteran dua arah bukan berarti energi yang diekspor dibayar tunai. Nilainya diperhitungkan sebagai pengurang tagihan dengan faktor pengali yang diatur ketentuan berlaku, dan biasanya ada periode perhitungan.

                    ## Alur pengajuan

                    Prosesnya secara umum berjalan seperti ini:

                    1. Survei teknis dan perhitungan kapasitas yang diizinkan.
                    2. Penyusunan dokumen permohonan beserta satu garis diagram sistem dan spesifikasi peralatan utama.
                    3. Pengajuan ke unit PLN wilayah pelanggan.
                    4. Evaluasi dan persetujuan, termasuk verifikasi kapasitas terhadap kondisi jaringan.
                    5. Pemasangan sistem oleh instalatir yang kompeten.
                    6. Pemeriksaan dan penggantian meteran.
                    7. Sertifikat laik operasi untuk sistem di atas kapasitas tertentu.
                    8. Komisioning bersama, lalu sistem resmi beroperasi.

                    Durasi total bervariasi. Untuk rumah tinggal biasanya lebih cepat daripada untuk pelanggan bisnis tegangan menengah yang memerlukan kajian jaringan lebih dalam.

                    ## Yang membuat permohonan ditolak

                    Dari pengalaman kami, penundaan paling sering berasal dari tiga hal: dokumen spesifikasi peralatan tidak lengkap, satu garis diagram tidak ditandatangani penanggung jawab teknik, atau kapasitas yang diajukan melampaui konsumsi tahunan pelanggan. Ketiganya mudah dihindari kalau disiapkan sejak awal.

                    ## Ketentuan keselamatan yang tidak bisa ditawar

                    Terlepas dari urusan administrasi, ada persyaratan teknis yang wajib dipenuhi karena menyangkut keselamatan petugas dan penghuni:

                    - Proteksi arus lebih dan proteksi lonjakan pada sisi DC maupun AC.
                    - Pembumian rangka dan sistem yang benar dengan nilai tahanan sesuai standar.
                    - Pemutus yang bisa diakses dan diberi label jelas.
                    - Inverter yang berhenti menyuntikkan daya ketika jaringan padam, atau yang dilengkapi pemutus yang mencegah islanding.

                    Poin terakhir inilah alasan sistem on-grid murni tidak bisa menjadi cadangan saat pemadaman. Kalau kebutuhan itu penting untuk Anda, solusinya adalah sistem hybrid dengan konfigurasi panel cadangan terpisah.

                    Kami mengurus seluruh proses administrasi sebagai bagian dari layanan pemasangan, termasuk pendampingan saat pemeriksaan.
                    MD,
            ],
            [
                'title' => 'Jadwal Perawatan Panel Surya agar Produksi Tidak Menurun',
                'slug' => 'jadwal-perawatan-panel-surya',
                'category' => 'panduan',
                'tags' => ['perawatan', 'monitoring', 'teknis'],
                'cover_image' => '/images/gallery-pembersihan.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'days_ago' => 45,
                'views_count' => 976,
                'excerpt' => 'Debu, kotoran burung, dan sambungan yang mengendur bisa memangkas produksi sampai belasan persen. Ini jadwal pemeriksaan yang kami terapkan sendiri.',
                'seo_title' => 'Jadwal Perawatan Panel Surya: Harian, Bulanan, Tahunan',
                'seo_description' => 'Daftar perawatan PLTS atap dari pemantauan harian, pembersihan modul, pemeriksaan sambungan dan inverter, sampai pengujian tahunan.',
                'seo_keywords' => 'perawatan panel surya, cara membersihkan panel surya, jadwal maintenance plts, produksi panel surya menurun',
                'body' => <<<'MD'
                    Panel surya sering dijual dengan janji "bebas perawatan". Itu separuh benar. Modulnya memang nyaris tidak butuh perhatian, tetapi sistem secara keseluruhan punya banyak titik yang perlu diperiksa. Berikut jadwal yang kami terapkan pada proyek sendiri.

                    ## Setiap hari: lihat angka, bukan atap

                    Aplikasi pemantauan memberi gambaran produksi harian. Yang perlu diperhatikan bukan angka absolutnya, melainkan pola. Bandingkan hari ini dengan hari-hari cerah sebelumnya pada bulan yang sama. Penurunan mendadak 20 persen atau lebih pada cuaca yang sama adalah tanda ada yang berubah.

                    Tiga penyebab paling umum: bayangan baru dari pertumbuhan pohon atau bangunan, kotoran yang menumpuk di satu area, atau satu string yang bermasalah.

                    ## Setiap bulan: periksa visual dan log inverter

                    Luangkan sepuluh menit untuk hal berikut:

                    - Lihat permukaan modul dari bawah. Kotoran burung, debu semen, dan daun yang menempel di tepi rangka paling sering muncul setelah musim kemarau panjang.
                    - Baca log peringatan pada inverter. Pesan yang muncul sesekali dan pulih sendiri biasanya tidak serius, tetapi pesan berulang pada jam yang sama layak ditelusuri.
                    - Periksa angka produksi per string kalau inverter Anda mendukungnya. Selisih besar antar string yang serupa menunjuk pada masalah spesifik.
                    - Pastikan tidak ada benda baru yang menghalangi, termasuk tangki air yang baru dipasang atau cabang pohon yang memanjang.

                    ## Setiap enam bulan: pembersihan

                    Frekuensi pembersihan tergantung lokasi. Rumah di tepi jalan berdebu atau dekat lahan pertanian butuh pembersihan lebih sering daripada rumah di perumahan rapat dengan sedikit debu.

                    Aturan yang kami pegang:

                    - Kerjakan pagi atau sore hari. Menyemprot air dingin ke modul yang panas berisiko menimbulkan retak halus pada sel.
                    - Pakai air bebas mineral kalau air Anda sadah. Endapan kapur meninggalkan bercak yang justru menghalangi cahaya.
                    - Gunakan sikat berbulu halus dengan gagang panjang. Jangan pernah memakai sabut atau bahan abrasif karena menggores lapisan anti reflektif.
                    - Jangan menyemprot sambungan dan kotak sambungan dengan tekanan tinggi.
                    - Gunakan tali pengaman untuk pekerjaan di atap curam. Ini bukan pekerjaan yang bisa dianggap sepele.

                    ## Setiap tahun: pemeriksaan teknis

                    Sekali setahun perlu dilakukan oleh teknisi yang kompeten:

                    - Kencangkan kembali sambungan kabel dan periksa perubahan warna pada terminal sebagai tanda panas berlebih.
                    - Ukur tahanan pembumian dan pastikan nilainya masih memenuhi standar.
                    - Periksa kondisi konektor, termasuk tanda retak akibat paparan sinar ultraviolet.
                    - Bersihkan kipas dan sirip pendingin inverter, serta pastikan sirkulasi udara di sekitarnya tidak terhalang.
                    - Periksa kekencangan baut rangka dan kondisi klem.
                    - Uji fungsi proteksi: pemutus arus, proteksi lonjakan, dan pemutus gangguan arus ke bumi.
                    - Bandingkan produksi tahunan dengan proyeksi awal. Selisih lebih dari 10 persen perlu diselidiki.

                    ## Setelah cuaca ekstrem

                    Hujan es, angin kencang, dan sambaran petir dekat lokasi adalah pemicu pemeriksaan tambahan. Yang dicari bukan kerusakan modul saja, tetapi juga sambungan yang tertarik, rangka yang bergeser, dan proteksi lonjakan yang sudah bekerja sehingga perlu diganti.

                    ## Layanan pemantauan jarak jauh

                    Kami menawarkan kontrak pemantauan untuk pelanggan bisnis. Sistem produksi dipantau dari pusat, penyimpangan terdeteksi dalam hitungan jam, dan tiket pekerjaan dibuat otomatis. Untuk instalasi di atas 100 kWp, deteksi dini satu string yang mati biasanya membayar biaya kontraknya dalam beberapa bulan.
                    MD,
            ],
            [
                'title' => 'Studi Kasus: Gudang Logistik 248 kWp Menekan Biaya Listrik Siang Hari',
                'slug' => 'studi-kasus-gudang-logistik-248-kwp',
                'category' => 'studi-kasus',
                'tags' => ['industri', 'studi-kasus', 'roi'],
                'cover_image' => '/images/gallery-gudang.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'days_ago' => 58,
                'views_count' => 1533,
                'excerpt' => 'Gudang dengan operasi dua shift adalah kandidat ideal PLTS. Ini angka sebenarnya dari proyek 248 kWp yang kami kerjakan, termasuk kendala di lapangan.',
                'seo_title' => 'Studi Kasus PLTS Gudang Logistik 248 kWp',
                'seo_description' => 'Laporan proyek PLTS atap gudang logistik 248 kWp: kondisi awal, konfigurasi sistem, kendala pemasangan, hasil produksi, dan perhitungan balik modal.',
                'seo_keywords' => 'plts gudang, plts industri, studi kasus panel surya, plts 248 kwp, penghematan listrik gudang',
                'body' => <<<'MD'
                    Gudang logistik adalah salah satu kandidat PLTS terbaik. Alasannya sederhana: konsumsi listriknya besar pada siang hari, atapnya luas dan biasanya datar, serta tidak ada penghuni yang terganggu selama pemasangan.

                    Berikut ringkasan proyek yang kami kerjakan untuk sebuah operator logistik di kawasan industri. Angka produksi adalah data pemantauan tahun pertama.

                    ## Kondisi awal

                    Gudang beroperasi dua shift dengan beban dasar yang relatif datar. Konsumsi terbesar datang dari penerangan area sortir, sistem konveyor, dan pendingin ruang kantor. Atap metal seluas sekitar 2.400 meter persegi dengan kemiringan rendah dan struktur rangka baja yang masih dalam kondisi baik.

                    Sebelum menawarkan apa pun, kami memasang alat pencatat daya selama tiga minggu untuk mendapatkan kurva beban yang sebenarnya. Hasilnya menunjukkan lebih dari 70 persen konsumsi terjadi antara pukul 08.00 dan 17.00, tepat pada jendela produksi surya.

                    ## Konfigurasi sistem

                    - Kapasitas terpasang 248,6 kWp memakai 425 modul monokristalin tipe bifacial.
                    - Delapan inverter string tiga fase, masing-masing dengan dua pelacak titik daya maksimum.
                    - Rangka aluminium dengan klem pada seam atap, tanpa pengeboran bidang atap sehingga lapisan kedap air tetap utuh.
                    - Panel proteksi utama dengan pemutus arus, proteksi lonjakan tipe 2, dan pembumian terpisah.
                    - Pemantauan produksi per string yang terhubung ke dasbor pusat.

                    Pembagian menjadi delapan inverter adalah keputusan penting. Kalau satu inverter bermasalah, hanya sekitar seperdelapan kapasitas yang berhenti, bukan seluruh sistem.

                    ## Kendala di lapangan

                    **Beban angin.** Atap datar luas sangat rentan terhadap angin angkat. Kami meminta perhitungan struktur ulang dan menambah titik pengikat pada area tepi serta sudut atap, yang justru paling jarang diperhitungkan.

                    **Debu industri.** Lokasi dekat kawasan pabrik membuat debu semen menempel lebih cepat daripada perkiraan. Interval pembersihan kami pendekkan dari enam bulan menjadi tiga bulan.

                    **Jadwal operasi.** Pemasangan tidak boleh menghentikan kegiatan sortir. Kami membagi atap menjadi empat zona dan mengerjakan satu zona per minggu dengan jalur kabel sementara yang aman dari lalu lintas forklift.

                    ## Hasil

                    Produksi tahun pertama berada sedikit di atas proyeksi kami, dengan rasio performa yang sehat untuk sistem di iklim tropis. Beban siang hari gudang tertutup sebagian besar oleh produksi surya, sehingga impor dari jaringan turun signifikan pada jam operasi.

                    Yang lebih penting dari angka penghematan: sistem hampir tidak pernah berhenti. Total waktu tidak tersedia sepanjang tahun pertama kurang dari satu hari, seluruhnya disebabkan satu inverter yang menunggu penggantian modul pendingin.

                    ## Pelajaran yang bisa dipakai

                    Tiga hal yang selalu kami bawa ke proyek berikutnya:

                    1. **Ukur dulu, baru rancang.** Kurva beban nyata tiga kali lebih berguna daripada asumsi berdasarkan tagihan bulanan.
                    2. **Pecah sistem menjadi beberapa inverter.** Kehilangan sebagian produksi jauh lebih baik daripada kehilangan semuanya.
                    3. **Rencanakan perawatan sejak awal.** Debu industri bukan pengecualian di Indonesia, dan interval pembersihan harus masuk ke perhitungan biaya operasi.

                    Kalau Anda mengelola fasilitas dengan atap di atas 1.000 meter persegi, kami bisa memberikan kajian awal tanpa biaya berdasarkan data tagihan dan foto atap.
                    MD,
            ],
            [
                'title' => 'Menghitung Balik Modal PLTS dengan Spreadsheet Sederhana',
                'slug' => 'menghitung-balik-modal-plts',
                'category' => 'biaya-investasi',
                'tags' => ['roi', 'biaya', 'monitoring'],
                'cover_image' => '/images/gallery-komisioning.svg',
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'days_ago' => 72,
                'views_count' => 1875,
                'excerpt' => 'Enam masukan, satu rumus, dan tiga jebakan yang membuat perhitungan balik modal terlihat lebih baik dari kenyataan.',
                'seo_title' => 'Cara Menghitung Balik Modal PLTS dengan Spreadsheet',
                'seo_description' => 'Langkah menghitung balik modal dan tingkat pengembalian investasi PLTS: data yang dibutuhkan, rumus produksi tahunan, degradasi, dan jebakan umum.',
                'seo_keywords' => 'hitung balik modal plts, roi panel surya, payback period plts, perhitungan investasi plts',
                'body' => <<<'MD'
                    Banyak penawaran menyertakan angka balik modal yang terlihat menggiurkan. Sebagian besar memakai asumsi yang terlalu optimis. Artikel ini menunjukkan cara menghitungnya sendiri dengan lembar kerja biasa.

                    ## Enam data yang Anda butuhkan

                    1. **Total biaya investasi**, termasuk pemasangan, perizinan, dan peralatan pendukung.
                    2. **Kapasitas terpasang** dalam satuan kilowatt peak.
                    3. **Iradiasi lokasi** dalam satuan kilowatt jam per meter persegi per hari.
                    4. **Tarif listrik** per kilowatt jam yang Anda bayar sekarang.
                    5. **Kenaikan tarif** per tahun sebagai asumsi.
                    6. **Biaya operasi** tahunan, terutama pembersihan dan cadangan penggantian inverter.

                    ## Menghitung produksi tahunan

                    Rumus dasarnya singkat:

                    Produksi tahunan = kapasitas terpasang × iradiasi harian rata-rata × 365 × rasio performa

                    Rasio performa memperhitungkan semua kerugian nyata: suhu modul yang panas, debu, ketidakcocokan antar modul, kerugian kabel, dan efisiensi inverter. Untuk iklim tropis Indonesia, nilai realistis berada di rentang 0,75 sampai 0,82. Kalau ada yang menghitung dengan 0,90, tanyakan asumsinya.

                    Sebagai contoh, sistem 5 kWp dengan iradiasi 4,6 kilowatt jam per meter persegi per hari dan rasio performa 0,78 menghasilkan sekitar 6.575 kilowatt jam per tahun.

                    ## Menghitung penghematan

                    Kalikan produksi dengan tarif listrik. Inilah penghematan kotor tahun pertama.

                    Kemudian kurangi dengan biaya operasi. Untuk sistem rumah, biasanya berupa satu kali pembersihan profesional per tahun ditambah cadangan untuk penggantian inverter pada tahun kesepuluh sampai kedua belas. Untuk sistem bisnis, tambahkan biaya pemantauan dan pemeriksaan tahunan.

                    ## Memperhitungkan degradasi

                    Modul kehilangan kapasitasnya secara bertahap. Modul berkualitas baik kehilangan sekitar 2 persen pada tahun pertama, lalu sekitar 0,4 sampai 0,55 persen setiap tahun berikutnya. Masukkan ini ke lembar kerja sebagai pengali yang menurun tiap tahun, bukan sebagai satu angka rata.

                    Setelah 25 tahun, sistem yang baik masih menghasilkan sekitar 85 sampai 87 persen dari kapasitas awalnya. Sistem yang memakai modul murah bisa tinggal 70 persen.

                    ## Tiga jebakan umum

                    **Menganggap seluruh produksi terpakai sendiri.** Pada rumah yang kosong sepanjang siang, sebagian besar produksi justru diekspor. Nilai ekspor biasanya lebih rendah daripada nilai listrik yang Anda pakai sendiri, jadi penghematan riil lebih kecil.

                    **Memakai tarif yang salah.** Periksa apakah tarif Anda sudah termasuk semua komponen tagihan. Selisih beberapa ratus rupiah per kilowatt jam mengubah hasil balik modal bertahun-tahun.

                    **Mengabaikan nilai waktu uang.** Balik modal sederhana tidak memperhitungkan bahwa uang hari ini bernilai lebih daripada uang tahun depan. Untuk keputusan investasi, hitung juga tingkat pengembalian internal dengan horizon 20 tahun.

                    ## Cara membaca hasilnya

                    Setelah semua dimasukkan, dua angka yang layak diperhatikan adalah periode balik modal dan total penghematan selama 20 tahun dikurangi investasi awal. Untuk sistem rumah on-grid tanpa baterai di Indonesia, periode balik modal yang wajar biasanya berada di kisaran lima sampai delapan tahun. Lebih cepat dari itu patut dicurigai asumsinya, lebih lambat dari sepuluh tahun perlu ditinjau ulang kapasitasnya.

                    Kami menyediakan lembar perhitungan ini dalam bentuk formulir. Isi data tagihan Anda dan hasilnya keluar otomatis, lengkap dengan daftar asumsi yang kami pakai supaya Anda bisa mengubahnya sendiri.
                    MD,
            ],
            [
                'title' => 'Membaca Kurva Beban Harian Sebelum Menentukan Kapasitas PLTS',
                'slug' => 'membaca-kurva-beban-harian',
                'category' => 'panduan',
                'tags' => ['teknis', 'monitoring'],
                'cover_image' => null,
                'status' => ArticleStatus::Draft,
                'is_featured' => false,
                'days_ago' => null,
                'views_count' => 0,
                'excerpt' => null,
                'seo_title' => null,
                'seo_description' => null,
                'seo_keywords' => null,
                'body' => <<<'MD'
                    ## Draf — sedang dikerjakan

                    Artikel ini akan menjelaskan cara membaca kurva beban harian hasil pengukuran, menentukan rasio pemakaian sendiri, dan menerjemahkannya menjadi kapasitas modul serta inverter yang tepat.

                    Kerangka yang sudah disiapkan:

                    - Kenapa tagihan bulanan tidak cukup untuk merancang sistem
                    - Memasang alat pencatat daya selama dua minggu
                    - Membentuk kurva beban rata-rata hari kerja dan akhir pekan
                    - Menumpangtindihkan kurva produksi surya dengan kurva beban
                    - Menghitung rasio pemakaian sendiri dan kebutuhan ekspor
                    - Menentukan kapasitas optimum, bukan kapasitas maksimum
                    MD,
            ],
            [
                'title' => 'Tren Teknologi Sel Surya dan Apa Artinya untuk Pasar Indonesia',
                'slug' => 'tren-teknologi-sel-surya',
                'category' => 'teknologi',
                'tags' => ['teknologi', 'panel-surya'],
                'cover_image' => null,
                'status' => ArticleStatus::Draft,
                'is_featured' => false,
                'days_ago' => null,
                'views_count' => 0,
                'excerpt' => null,
                'seo_title' => null,
                'seo_description' => null,
                'seo_keywords' => null,
                'body' => <<<'MD'
                    ## Draf — menunggu peninjauan data

                    Catatan kerangka untuk artikel tentang perkembangan teknologi sel surya dan relevansinya bagi pembeli di Indonesia.

                    Poin yang perlu diverifikasi sebelum diterbitkan:

                    - Efisiensi modul produksi massal terkini dan perbandingannya dengan lima tahun lalu
                    - Status komersial sel tandem perovskit serta catatan stabilitasnya di iklim lembap
                    - Tren harga modul dan pengaruhnya terhadap keputusan menunda pemasangan
                    - Ketersediaan modul dengan kandungan dalam negeri untuk proyek yang mewajibkannya
                    - Panduan praktis: kapan menunggu teknologi baru masuk akal dan kapan tidak
                    MD,
            ],
        ];
    }
}
