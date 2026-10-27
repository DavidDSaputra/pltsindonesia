<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Original service catalogue for the solar contractor site.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->services() as $index => $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                $service + ['sort_order' => ($index + 1) * 10, 'is_active' => true],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function services(): array
    {
        return [
            [
                'title' => 'PLTS Atap On-Grid untuk Rumah',
                'slug' => 'plts-atap-on-grid-rumah',
                'icon' => 'home',
                'summary' => 'Sistem panel surya yang tersambung jaringan PLN untuk memangkas tagihan listrik rumah tangga tanpa perlu baterai.',
                'body' => <<<'MD'
                    Sistem **on-grid** adalah pilihan paling masuk akal untuk kebanyakan rumah di Indonesia: sederhana, paling murah per kWh, dan langsung terasa penghematannya pada bulan pertama.

                    Panel menghasilkan listrik searah (DC) yang diubah inverter menjadi arus bolak-balik (AC) sefase dengan jaringan PLN. Saat produksi melebihi pemakaian rumah, kelebihan itu mengalir ke jaringan dan tercatat pada meteran ekspor-impor. Malam hari atau saat mendung, rumah kembali mengambil listrik dari PLN seperti biasa.

                    ## Tahapan pekerjaan

                    1. **Audit tagihan 12 bulan** — kami minta salinan tagihan untuk melihat pola pemakaian, bukan sekadar angka rata-rata.
                    2. **Survei atap** — pengukuran luas efektif, kemiringan, arah hadap, dan pemetaan sumber bayangan (pohon, tower, bangunan tetangga).
                    3. **Desain susunan panel** — penempatan string, pemilihan inverter, dan jalur kabel.
                    4. **Pengurusan perizinan** — pendaftaran ke unit PLN setempat sampai meteran ekspor-impor terpasang.
                    5. **Pemasangan dan komisioning** — uji isolasi, uji pentanahan, dan verifikasi produksi awal.

                    ## Yang perlu Anda siapkan

                    Daya terpasang minimal 1.300 VA umumnya sudah bisa dipasangi sistem 1 kWp. Yang lebih menentukan adalah apakah atap Anda punya area bebas bayangan sekitar 6–7 m² per kWp, dan apakah struktur rangkanya mampu menahan tambahan beban 12–15 kg per panel.

                    Bila atap Anda sudah lama atau menggunakan rangka ringan dengan bentang panjang, kami sarankan pemeriksaan struktur sebelum pemasangan. Itu bagian dari survei, tanpa biaya tambahan.
                    MD,
                'features' => [
                    'Audit tagihan listrik 12 bulan',
                    'Survei atap dan pemetaan bayangan',
                    'Pengurusan izin ke PLN',
                    'Pemasangan rangka aluminium anti karat',
                    'Uji pentanahan dan uji isolasi',
                    'Garansi pemasangan 2 tahun',
                ],
            ],
            [
                'title' => 'Sistem Hybrid dengan Baterai',
                'slug' => 'sistem-hybrid-baterai',
                'icon' => 'battery',
                'summary' => 'Gabungan panel surya, baterai penyimpan, dan jaringan PLN agar listrik tetap menyala saat pemadaman.',
                'body' => <<<'MD'
                    Sistem **hybrid** menjawab dua kebutuhan sekaligus: menekan tagihan dan punya cadangan saat jaringan padam. Ini konfigurasi yang paling banyak kami pasang di daerah dengan frekuensi pemadaman tinggi, juga di rumah yang punya peralatan vital seperti akuarium besar, server rumahan, atau alat medis.

                    Inverter hybrid mengatur tiga sumber — panel, baterai, dan PLN — secara otomatis. Saat matahari cukup, panel melayani beban rumah sambil mengisi baterai. Saat produksi turun, baterai mengambil alih. Bila baterai habis, PLN menyambung kembali tanpa jeda yang terasa.

                    ## Memilih kapasitas baterai

                    Kesalahan paling umum adalah membeli baterai terlalu besar. Baterai hanya perlu menutup beban kritis selama durasi pemadaman yang wajar, bukan seluruh rumah semalaman.

                    Sebagai gambaran, beban kritis 800 watt selama enam jam membutuhkan sekitar 5 kWh energi terpakai. Dengan kedalaman pelepasan 90 persen pada baterai LiFePO4, kapasitas terpasang sekitar 5,5 kWh sudah cukup.

                    ## Kenapa LiFePO4

                    Kami memakai baterai **lithium iron phosphate** karena siklus hidupnya 4.000–6.000 kali, jauh di atas lead-acid yang berhenti di 500–800 siklus. Secara harga awal memang lebih mahal, tetapi biaya per siklusnya jauh lebih rendah dan tidak memerlukan perawatan rutin.

                    Setiap baterai yang kami pasang dilengkapi BMS yang memutus arus bila suhu, tegangan, atau arus melewati batas aman, dan kami sambungkan ke pemantauan jarak jauh agar Anda bisa melihat statusnya dari ponsel.
                    MD,
                'features' => [
                    'Baterai LiFePO4 5–20 kWh',
                    'Inverter hybrid dengan transfer otomatis',
                    'Panel beban kritis terpisah',
                    'Pemantauan produksi dan status baterai',
                    'Garansi baterai hingga 10 tahun',
                    'Pelatihan penggunaan untuk penghuni',
                ],
            ],
            [
                'title' => 'PLTS Komersial & Industri',
                'slug' => 'plts-komersial-industri',
                'icon' => 'building',
                'summary' => 'Pembangkit surya skala puluhan sampai ratusan kWp untuk pabrik, gudang, hotel, dan gedung perkantoran.',
                'body' => <<<'MD'
                    Beban listrik bisnis hampir selalu memuncak pada siang hari — pendingin ruangan, mesin produksi, penerangan, dan lift bekerja bersamaan. Kurva itu persis sejalan dengan kurva produksi panel surya, sehingga rasio pemakaian langsung (self-consumption) biasanya mencapai 80–95 persen tanpa perlu baterai sama sekali.

                    ## Studi kelayakan

                    Sebelum bicara harga, kami susun dokumen kelayakan berisi:

                    - Profil beban per jam dari data meteran atau logger yang kami pasang selama dua minggu.
                    - Kapasitas atap yang benar-benar bisa dipakai setelah dikurangi area perawatan, skylight, dan peralatan rooftop.
                    - Simulasi produksi tahunan dengan data iradiasi lokasi dan derating realistis (suhu, kotoran, rugi kabel, efisiensi inverter).
                    - Proyeksi arus kas, titik balik modal, dan sensitivitas terhadap kenaikan tarif listrik.

                    ## Pemasangan bertahap

                    Banyak klien kami tidak ingin mengeluarkan belanja modal penuh di awal. Kami mendukung pemasangan bertahap — misalnya 40 persen kapasitas di tahun pertama, sisanya saat arus kas mengizinkan — dengan desain awal yang sudah memperhitungkan perluasan sehingga tidak ada pekerjaan yang harus dibongkar ulang.

                    ## Struktur dan keselamatan

                    Untuk atap metal standing seam kami pakai klem penjepit tanpa pengeboran. Untuk atap beton, pondasi ballast dengan perhitungan beban angin. Setiap sistem dilengkapi proteksi surge DC dan AC, pemutus arus per string, dan jalur kabel yang terpisah dari instalasi eksisting.

                    Setelah serah terima, kami berikan laporan produksi bulanan yang bisa langsung dipakai untuk pelaporan keberlanjutan perusahaan.
                    MD,
                'features' => [
                    'Studi kelayakan dan proyeksi arus kas',
                    'Logger profil beban dua minggu',
                    'Kapasitas 50 kWp sampai 1 MWp',
                    'Pemasangan bertahap sesuai anggaran',
                    'Proteksi surge DC dan AC',
                    'Laporan produksi bulanan untuk ESG',
                ],
            ],
            [
                'title' => 'Sistem Off-Grid untuk Lokasi Terpencil',
                'slug' => 'sistem-off-grid-lokasi-terpencil',
                'icon' => 'bolt',
                'summary' => 'Pembangkit mandiri dengan bank baterai besar untuk lokasi yang belum terjangkau atau tidak stabil jaringan listriknya.',
                'body' => <<<'MD'
                    Off-grid adalah disiplin yang berbeda. Karena tidak ada jaringan yang menutup kekurangan, seluruh keseimbangan antara produksi, penyimpanan, dan beban harus dihitung sendiri — dan marginnya harus cukup untuk musim hujan.

                    ## Cara kami merancang

                    **Langkah pertama adalah memisahkan beban.** Kami kelompokkan peralatan menjadi beban esensial (penerangan, komunikasi, pendingin vaksin atau hasil panen), beban normal, dan beban berat yang opsional (pompa besar, pemanas). Desain sistem mengikuti beban esensial plus normal, sementara beban berat dijadwalkan menyala saat baterai penuh.

                    **Langkah kedua menentukan hari otonomi.** Berapa hari sistem harus bertahan tanpa matahari sama sekali? Untuk lokasi di Kalimantan dan Sumatra kami pakai tiga hari, karena pola cuaca di sana bisa mendung berkepanjangan.

                    **Langkah ketiga memilih sumber cadangan.** Hampir semua sistem off-grid yang kami bangun tetap punya genset kecil. Bukan tanda kegagalan desain, melainkan pengaman agar baterai tidak pernah terkuras terlalu dalam — kondisi yang paling merusak umur baterai.

                    ## Pembangkit hibrida surya-diesel

                    Untuk lokasi dengan genset yang sudah berjalan 24 jam, kami pasangkan sistem hibrida: panel surya melayani beban siang, genset turun ke putaran minimum untuk mengisi baterai, dan kontroler mematikan genset begitu baterai penuh. Penghematan solar biasanya 40–65 persen, dan itu sering kali membuat investasi kembali dalam tiga sampai empat tahun.

                    ## Perawatan jarak jauh

                    Lokasi terpencil berarti kunjungan teknisi mahal. Karena itu setiap sistem off-grid kami lengkapi telemetri yang mengirim data produksi, status baterai, dan alarm ke pusat pemantauan. Bila ada penyimpangan, tim kami bisa mendiagnosis dari jauh dan menjadwalkan kunjungan hanya bila benar-benar perlu.
                    MD,
                'features' => [
                    'Bank baterai 10–200 kWh',
                    'Desain dengan tiga hari otonomi',
                    'Kontroler hibrida surya-diesel',
                    'Telemetri dan alarm jarak jauh',
                    'Pelatihan operator lokal',
                    'Kontrak perawatan jangka panjang',
                ],
            ],
            [
                'title' => 'Perawatan & Pemantauan Sistem',
                'slug' => 'perawatan-pemantauan-sistem',
                'icon' => 'wrench',
                'summary' => 'Pembersihan panel, kalibrasi inverter, dan pemantauan produksi berkala agar hasil tetap sesuai rancangan.',
                'body' => <<<'MD'
                    Panel surya memang minim perawatan, tetapi "minim" bukan berarti "nol". Sistem yang tidak pernah dibersihkan di daerah berdebu bisa kehilangan 8–15 persen produksi dalam setahun. Inverter punya kipas dan kapasitor yang umurnya bergantung pada suhu ruangan. Dan sambungan kabel bisa melonggar karena siklus panas-dingin.

                    ## Paket perawatan kami

                    **Kunjungan dua kali setahun** mencakup pembersihan modul dengan air bebas mineral dan sikat lunak (tanpa deterjen yang meninggalkan lapisan), pemeriksaan visual terhadap micro-crack dan delaminasi, pengukuran tegangan per string, pengencangan terminal, pembersihan filter dan kipas inverter, serta verifikasi fungsi pemutus arus dan proteksi surge.

                    Setiap kunjungan ditutup laporan berisi perbandingan produksi aktual versus simulasi, daftar temuan, dan tindakan yang sudah atau perlu dilakukan.

                    ## Pemantauan berkelanjutan

                    Di antara kunjungan, sistem Anda terpantau 24 jam. Kami menetapkan ambang anomali per lokasi — misalnya produksi turun lebih dari 25 persen dibanding hari dengan iradiasi setara — dan tim kami akan menghubungi Anda lebih dulu, bukan menunggu Anda menyadari tagihan naik.

                    ## Untuk sistem yang bukan kami pasang

                    Kami juga menerima kontrak perawatan sistem PLTS yang dibangun kontraktor lain, dengan catatan kami melakukan audit awal lebih dulu. Audit itu memetakan kondisi eksisting, menguji proteksi, dan mencatat apa yang perlu diperbaiki sebelum sistem layak dirawat secara rutin. Bila ditemukan masalah keselamatan serius, kami sampaikan apa adanya beserta perkiraan biaya perbaikannya — Anda bebas memutuskan.
                    MD,
                'features' => [
                    'Pembersihan modul dua kali setahun',
                    'Pengukuran tegangan per string',
                    'Pembersihan dan kalibrasi inverter',
                    'Pemantauan produksi 24 jam',
                    'Laporan perbandingan produksi aktual',
                    'Menerima sistem dari kontraktor lain',
                ],
            ],
            [
                'title' => 'Konsultasi & Studi Kelayakan',
                'slug' => 'konsultasi-studi-kelayakan',
                'icon' => 'chart',
                'summary' => 'Analisis teknis dan finansial mandiri untuk Anda yang ingin menilai sendiri sebelum memutuskan berinvestasi.',
                'body' => <<<'MD'
                    Tidak semua orang siap membeli sistem hari ini, dan itu wajar. Layanan ini kami buat untuk Anda yang ingin angka-angkanya lebih dulu, tanpa tekanan untuk langsung memesan pemasangan.

                    ## Isi dokumen kelayakan

                    Anda menerima satu laporan tertulis yang bisa dibawa ke manajemen, bank, atau pembanding kontraktor:

                    - **Profil beban** berdasarkan tagihan atau data logger, diurai per jam untuk melihat kapan listrik paling banyak dipakai.
                    - **Potensi atap** dengan denah penempatan panel, lengkap dengan area yang dikecualikan karena bayangan atau kebutuhan akses perawatan.
                    - **Simulasi produksi** per bulan selama satu tahun, memakai data iradiasi lokasi dan asumsi derating yang kami tuliskan terbuka.
                    - **Analisis finansial** meliputi belanja modal, penghematan tahunan, titik balik modal, nilai bersih sekarang (NPV), dan tingkat pengembalian internal (IRR).
                    - **Analisis sensitivitas** terhadap tiga variabel: kenaikan tarif listrik, penurunan performa panel, dan perubahan pola pemakaian.
                    - **Rekomendasi konfigurasi** dengan dua atau tiga opsi kapasitas beserta konsekuensi biayanya.

                    ## Berapa lama

                    Untuk rumah tinggal, laporan selesai dalam lima hari kerja setelah survei. Untuk fasilitas komersial yang membutuhkan pemasangan logger, tambahkan dua minggu untuk periode pengambilan data.

                    ## Biaya studi jadi kredit

                    Bila dalam enam bulan Anda memutuskan memasang sistem bersama kami, seluruh biaya studi kelayakan kami potong dari nilai kontrak. Bila tidak, laporannya tetap milik Anda sepenuhnya.
                    MD,
                'features' => [
                    'Laporan tertulis lengkap',
                    'Simulasi produksi 12 bulan',
                    'Analisis NPV, IRR, dan balik modal',
                    'Uji sensitivitas tiga variabel',
                    'Dua sampai tiga opsi konfigurasi',
                    'Biaya studi dipotong dari nilai kontrak',
                ],
            ],
        ];
    }
}
