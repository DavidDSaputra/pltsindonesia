<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Original project portfolio entries.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $index => $project) {
            Project::query()->updateOrCreate(
                ['slug' => $project['slug']],
                $project + ['sort_order' => ($index + 1) * 10, 'is_active' => true],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function projects(): array
    {
        return [
            [
                'title' => 'PLTS Atap Perumahan Bintaro Sektor 9',
                'slug' => 'plts-atap-bintaro-sektor-9',
                'client' => 'Keluarga Hartono',
                'location' => 'Tangerang Selatan, Banten',
                'capacity_kwp' => 5.28,
                'completed_on' => '2026-03-18',
                'is_featured' => true,
                'summary' => 'Sistem on-grid 5,28 kWp yang menurunkan tagihan listrik rumah dua lantai dari rata-rata Rp 3,1 juta menjadi sekitar Rp 480 ribu per bulan.',
                'body' => <<<'MD'
                    Rumah dua lantai dengan daya terpasang 6.600 VA ini memakai pendingin ruangan hampir sepanjang hari. Tagihan bulanannya berkisar Rp 2,8–3,4 juta, dan sekitar 78 persen pemakaian terjadi antara pukul 09.00 dan 17.00 — persis jendela produksi panel surya terbaik.

                    ## Konfigurasi

                    Kami memasang 12 modul monokristalin 440 Wp pada atap sisi barat daya dengan kemiringan 12 derajat, memakai rangka aluminium dengan klem tengah. Inverter string 5 kW dipasang di dinding garasi yang teduh, sekitar empat meter dari panel untuk menekan rugi kabel DC.

                    Jalur kabel DC memakai jenis tahan sinar ultraungu dengan konduit terpisah, sementara sisi AC masuk ke panel distribusi utama melalui pemutus arus khusus 32 A.

                    ## Kendala di lapangan

                    Ada dua hal yang perlu diselesaikan. Pertama, cerobong air panas tenaga surya eksisting menimbulkan bayangan selebar 1,2 meter pada pukul 07.00–09.00. Kami geser satu string menjauh dari area itu dan mengorbankan satu posisi panel agar tidak ada modul yang terpangkas produksinya oleh mismatch.

                    Kedua, rangka atap di sisi barat menggunakan baja ringan dengan bentang 1,4 meter tanpa sokongan tengah. Setelah perhitungan beban, kami tambahkan dua titik perkuatan sebelum rangka dipasang, karena total beban sistem sekitar 180 kg.

                    ## Hasil

                    Dalam tiga bulan pertama, produksi rata-rata 21,4 kWh per hari atau 642 kWh per bulan. Penghematan tagihan stabil di angka 84 persen, dan estimasi balik modal berada pada tahun keenam dengan asumsi tarif listrik naik 4 persen per tahun.
                    MD,
            ],
            [
                'title' => 'Atap Gudang Logistik Cikarang',
                'slug' => 'atap-gudang-logistik-cikarang',
                'client' => 'PT Angkut Cepat Nusantara',
                'location' => 'Cikarang, Jawa Barat',
                'capacity_kwp' => 248.6,
                'completed_on' => '2025-11-04',
                'is_featured' => true,
                'summary' => 'Sistem komersial 248,6 kWp di atap gudang seluas 2.400 m² yang memasok hampir seluruh kebutuhan listrik operasional siang hari.',
                'body' => <<<'MD'
                    Gudang distribusi ini beroperasi dua shift dengan beban dasar sekitar 180 kW pada siang hari, didominasi chiller dan conveyor. Sebelum proyek, pengelola membayar rata-rata Rp 210 juta per bulan untuk listrik.

                    ## Perancangan

                    Atap metal standing seam memungkinkan pemasangan tanpa pengeboran sama sekali. Kami memakai klem penjepit yang menggigit lipatan atap, sehingga tidak ada satu pun lubang baru yang berpotensi bocor.

                    Sebanyak 536 modul 465 Wp disusun dalam 14 string, dihubungkan ke empat inverter string 60 kW yang dipasang berjajar di sisi utara bangunan. Penempatan ini sengaja dipilih agar inverter tidak terpapar matahari sore langsung, yang bisa menurunkan efisiensi konversi pada suhu di atas 45 derajat Celsius.

                    ## Struktur dan drainase

                    Sudut kemiringan 10 derajat sudah cukup untuk membersihkan permukaan panel secara alami oleh hujan, sekaligus menjaga tambahan beban angin tetap rendah. Kami periksa juga kemiringan atap eksisting dan menambahkan talang pembuangan di dua titik karena susunan panel mengubah pola aliran air hujan.

                    ## Keselamatan

                    Setiap inverter dilengkapi pemutus DC dengan kemampuan memutus beban penuh, plus perangkat proteksi surge Tipe II di sisi DC dan AC. Jalur kabel di atas atap ditopang tray galvanis yang diangkat 15 cm dari permukaan agar air tetap mengalir bebas di bawahnya.

                    ## Hasil

                    Produksi tahun pertama diproyeksikan 348 MWh, menutup sekitar 62 persen konsumsi listrik total fasilitas. Titik balik modal berada pada 4,8 tahun, dengan penghematan tahun pertama sekitar Rp 520 juta.
                    MD,
            ],
            [
                'title' => 'Resor Pantai Lovina dengan Sistem Hybrid',
                'slug' => 'resor-pantai-lovina-hybrid',
                'client' => 'Lovina Bay Resort',
                'location' => 'Buleleng, Bali',
                'capacity_kwp' => 86.4,
                'completed_on' => '2025-08-22',
                'is_featured' => true,
                'summary' => 'Kombinasi PLTS 86,4 kWp dan baterai 120 kWh yang menjaga kenyamanan tamu sekaligus menurunkan pemakaian genset hingga 70 persen.',
                'body' => <<<'MD'
                    Resor dengan 32 kamar ini menghadapi dua masalah: tarif listrik yang tinggi dan pemadaman jaringan yang bisa berlangsung dua sampai tiga jam. Bagi akomodasi wisata, pemadaman berarti keluhan tamu langsung.

                    ## Desain hybrid

                    Kami memasang 180 modul 480 Wp di atas bangunan vila dan area parkir, dihubungkan ke dua inverter hybrid 50 kW. Bank baterai LiFePO4 120 kWh ditempatkan di ruang ber-AC terpisah, karena suhu operasi di atas 35 derajat Celsius memangkas umur baterai secara signifikan.

                    Panel beban kritis — pompa air, penerangan koridor, sistem kunci kamar, jaringan internet, dan dapur utama — dipisahkan dari beban non-esensial seperti pemanas air kolam. Saat baterai menipis, beban non-esensial diputus otomatis lebih dulu.

                    ## Korosi lingkungan pantai

                    Lokasi berjarak kurang dari 300 meter dari garis pantai, jadi seluruh perangkat keras kami pilih dari baja tahan karat kelas 316, bukan galvanis biasa. Rangka aluminium diberi lapisan anodisasi tambahan, dan semua konektor memakai tipe tertutup dengan tingkat proteksi IP67.

                    Perawatan dijadwalkan empat kali setahun, lebih rapat dari standar, karena garam yang menempel di permukaan modul mempercepat penurunan transmisi cahaya.

                    ## Hasil

                    Sistem menutup sekitar 71 persen kebutuhan listrik resor. Jam operasi genset turun dari 340 jam per bulan menjadi sekitar 95 jam, dan tamu tidak lagi mengalami pemadaman sama sekali selama sembilan bulan pengamatan pertama.
                    MD,
            ],
            [
                'title' => 'Pabrik Pengolahan Kelapa Sawit Kampar',
                'slug' => 'pabrik-pengolahan-sawit-kampar',
                'client' => 'Koperasi Tani Sejahtera Kampar',
                'location' => 'Kampar, Riau',
                'capacity_kwp' => 412.0,
                'completed_on' => '2025-05-30',
                'is_featured' => false,
                'summary' => 'Pembangkit hibrida surya-diesel 412 kWp yang memangkas konsumsi solar pabrik hingga 58 persen.',
                'body' => <<<'MD'
                    Pabrik pengolahan ini berada di luar jangkauan jaringan tegangan menengah yang stabil, sehingga bergantung pada tiga genset diesel 350 kVA yang berjalan bergantian sepanjang hari. Biaya solarnya mencapai Rp 380 juta per bulan.

                    ## Pendekatan hibrida

                    Tujuan proyek bukan mengganti genset sepenuhnya, melainkan menurunkannya ke titik operasi paling efisien. Genset diesel paling boros saat dibebani di bawah 40 persen kapasitas, dan itulah persis kondisi yang terjadi pada siang hari saat beban pabrik sedang moderat.

                    Dengan 800 modul 515 Wp yang berproduksi pada siang hari, kontroler hibrida menurunkan beban genset sehingga mesin tetap bekerja di atas 65 persen kapasitas — zona efisien — sementara kekurangannya ditutup panel surya. Saat baterai penuh, genset dimatikan dan beban dilayani sepenuhnya oleh panel.

                    ## Debu dan pembersihan

                    Lingkungan pabrik kelapa sawit berdebu dengan serat-serat halus yang mudah menempel pada permukaan modul, apalagi saat lembap. Kami jadwalkan pembersihan setiap enam minggu, dan memasang stasiun air bertekanan rendah di tiga titik agar pekerjaan itu tidak memakan waktu lama.

                    Tanpa pembersihan rutin, pengukuran awal kami menunjukkan penurunan produksi hingga 11 persen hanya dalam lima minggu.

                    ## Hasil

                    Konsumsi solar turun dari 78.000 liter menjadi sekitar 33.000 liter per bulan. Jam operasi genset berkurang 41 persen, yang juga memperpanjang interval servis besar. Investasi kembali pada tahun ke-3,7.
                    MD,
            ],
            [
                'title' => 'Sekolah Dasar Off-Grid di Sumba Timur',
                'slug' => 'sekolah-dasar-off-grid-sumba-timur',
                'client' => 'Yayasan Terang Nusantara',
                'location' => 'Sumba Timur, Nusa Tenggara Timur',
                'capacity_kwp' => 12.5,
                'completed_on' => '2026-01-15',
                'is_featured' => false,
                'summary' => 'Sistem mandiri 12,5 kWp dengan baterai 30 kWh yang memberi listrik stabil untuk enam ruang kelas dan laboratorium komputer.',
                'body' => <<<'MD'
                    Sekolah ini sebelumnya memakai genset bensin empat jam sehari, hanya untuk penerangan dan beberapa kipas. Laboratorium komputer berisi 18 unit tidak pernah bisa dinyalakan karena genset tidak mampu menanggung beban puncaknya.

                    ## Perancangan untuk keterbatasan

                    Karena tidak ada jaringan listrik, seluruh keseimbangan harus dihitung mandiri. Kami petakan beban esensial: penerangan 12 titik, 18 komputer, satu proyektor, pompa air 250 watt, dan sistem komunikasi. Total beban terpasang 4,2 kW dengan pemakaian harian sekitar 14 kWh.

                    Bank baterai 30 kWh memberi otonomi lebih dari dua hari untuk beban esensial saja. Inverter hybrid 6 kW dipilih agar masih punya ruang pertumbuhan bila sekolah menambah peralatan di kemudian hari.

                    ## Logistik dan pengerjaan

                    Seluruh material diangkut dengan truk dari Waingapu lalu dilanjutkan kendaraan gardan ganda sejauh 22 kilometer jalan berbatu. Kami susun jadwal pengiriman dalam tiga tahap agar tidak ada penumpukan material di lokasi yang tidak punya gudang aman.

                    Pemasangan dikerjakan bersama enam warga desa yang kami latih selama pengerjaan. Dua di antaranya kini memegang peran perawatan rutin, dan kami sediakan daftar periksa bergambar agar pekerjaan pembersihan dan pemeriksaan visual bisa dilakukan tanpa latar belakang teknis.

                    ## Pemantauan

                    Telemetri dikirim melalui modem seluler ke pusat pemantauan kami. Bila produksi turun di luar ambang normal, tim bisa menghubungi koordinator sekolah lebih dulu dan mendiagnosis masalah dari data, bukan dengan mengirim orang melintasi 22 kilometer jalan berbatu.

                    ## Hasil

                    Laboratorium komputer kini menyala setiap hari sekolah. Genset bensin hanya dipakai sebagai cadangan darurat dan konsumsi bahan bakarnya turun dari 120 liter menjadi sekitar 8 liter per bulan.
                    MD,
            ],
            [
                'title' => 'Ruko dan Kantor Tiga Lantai di Surabaya',
                'slug' => 'ruko-kantor-surabaya',
                'client' => 'CV Mitra Karya Bersama',
                'location' => 'Surabaya, Jawa Timur',
                'capacity_kwp' => 32.7,
                'completed_on' => '2026-06-09',
                'is_featured' => false,
                'summary' => 'Sistem 32,7 kWp yang dipasang bertahap dalam dua tahap, menutup sekitar 68 persen pemakaian listrik jam kerja.',
                'body' => <<<'MD'
                    Bangunan tiga lantai ini menggabungkan toko di lantai dasar, kantor di lantai dua, dan ruang rapat di lantai tiga. Pemilik ingin beralih ke energi surya tetapi tidak bisa mengeluarkan belanja modal penuh sekaligus.

                    ## Desain untuk perluasan

                    Kami merancang sistem dengan kapasitas akhir 32,7 kWp namun membagi pemasangannya menjadi dua tahap. Tahap pertama 19,8 kWp dipasang pada atap sisi timur dan selatan, dilengkapi inverter string 20 kW yang masih punya ruang untuk tambahan string.

                    Kabel tray, jalur konduit, dan pemutus arus di panel distribusi sudah kami pasang dengan kapasitas penuh sejak awal. Dengan begitu, tahap kedua enam bulan kemudian hanya menambahkan modul, satu inverter lagi, dan penyambungan — tanpa membongkar pekerjaan yang sudah ada.

                    ## Beban dan bayangan

                    Lantai tiga memakai pendingin ruangan hampir sepanjang hari kerja, dan inilah penyumbang beban terbesar. Karena ruko berjejer, bangunan tetangga yang lebih tinggi menimbulkan bayangan pada sisi barat setelah pukul 15.30. Kami hindari area itu sepenuhnya dan mengoptimalkan penempatan di sisi timur yang justru mendapat matahari pagi lebih bersih.

                    ## Optimizer per modul

                    Mengingat ada beberapa titik bayangan parsial dari antena dan toren air, kami pasang optimizer daya pada setiap modul. Perangkat ini memastikan satu modul yang terbayangi tidak menyeret turun produksi seluruh string, yang pada konfigurasi konvensional bisa memangkas hasil hingga 30 persen.

                    ## Hasil

                    Setelah tahap kedua selesai, produksi rata-rata 128 kWh per hari. Pemakaian dari jaringan PLN pada jam kerja turun 68 persen, dan investasi tahap pertama sudah kembali dalam 4,2 tahun.
                    MD,
            ],
        ];
    }
}
