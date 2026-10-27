<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Frequently asked questions, grouped by topic.
 *
 * Answers are rendered as a single paragraph, so keep each one free of markdown.
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->faqs() as $index => $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                $faq + ['sort_order' => ($index + 1) * 10, 'is_active' => true],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function faqs(): array
    {
        return [
            // Biaya & investasi
            [
                'group' => 'biaya',
                'question' => 'Berapa biaya pemasangan panel surya di Indonesia?',
                'answer' => 'Sebagai kisaran umum pada 2026, sistem PLTS atap on-grid untuk rumah tinggal berada di rentang Rp 12–18 juta per kWp tergantung merek panel, jenis inverter, dan kompleksitas pemasangan. Rumah dengan daya 2.200 VA biasanya cukup dengan sistem 2–3 kWp, sementara sistem komersial skala besar turun ke kisaran Rp 8–12 juta per kWp karena efek skala. Angka pasti baru bisa ditentukan setelah kami melihat tagihan listrik Anda dan melakukan survei atap, keduanya gratis.',
            ],
            [
                'group' => 'biaya',
                'question' => 'Berapa lama investasi panel surya kembali modal?',
                'answer' => 'Untuk rumah tinggal dengan pola pemakaian siang hari yang baik, balik modal umumnya tercapai dalam 5–7 tahun. Untuk bisnis dan pabrik yang bebannya memuncak pada siang hari, bisa lebih cepat, yaitu 3,5–5 tahun, karena hampir seluruh produksi panel langsung terpakai sendiri. Sistem off-grid yang tadinya bergantung pada genset berbahan bakar solar sering kali kembali modal dalam 3–4 tahun karena penghematan bahan bakarnya sangat besar.',
            ],
            [
                'group' => 'biaya',
                'question' => 'Apakah ada skema cicilan atau sewa untuk PLTS?',
                'answer' => 'Kami mendukung pemasangan bertahap, yaitu kapasitas dirancang penuh sejak awal tetapi dipasang sebagian dulu sesuai anggaran, lalu dilanjutkan tanpa membongkar pekerjaan yang sudah ada. Untuk pelanggan korporasi kami juga bekerja sama dengan beberapa lembaga pembiayaan yang menawarkan skema cicilan 3–5 tahun. Skema sewa atap masih terbatas ketersediaannya di Indonesia dan kami akan sampaikan terus terang bila lokasi Anda belum memenuhi syarat.',
            ],
            [
                'group' => 'biaya',
                'question' => 'Apakah harga sudah termasuk perizinan dan meteran ekspor-impor?',
                'answer' => 'Ya untuk sistem on-grid. Penawaran kami sudah mencakup pengurusan pendaftaran ke unit PLN setempat, penggantian atau penyesuaian meteran ekspor-impor, sampai sistem resmi beroperasi. Yang tidak termasuk adalah biaya peningkatan daya bila daya terpasang Anda ternyata kurang, dan itu akan kami sampaikan lebih dulu sebelum ada pekerjaan apa pun dimulai.',
            ],

            // Teknis
            [
                'group' => 'teknis',
                'question' => 'Apakah panel surya tetap menghasilkan listrik saat mendung?',
                'answer' => 'Ya, tetapi lebih sedikit. Panel surya bekerja pada cahaya, bukan panas, sehingga hari mendung tetap menghasilkan listrik meski biasanya hanya 20–40 persen dari kapasitas penuh. Yang benar-benar menghentikan produksi adalah kondisi gelap. Di Indonesia keuntungan kita adalah iradiasi tahunan yang tinggi dan relatif merata, sehingga produksi tahunan tetap dapat diandalkan meski ada musim hujan.',
            ],
            [
                'group' => 'teknis',
                'question' => 'Berapa luas atap yang dibutuhkan untuk sistem panel surya?',
                'answer' => 'Aturan praktisnya adalah 6–7 meter persegi area bebas bayangan untuk setiap kWp kapasitas. Jadi sistem rumah 3 kWp butuh sekitar 18–21 meter persegi, sementara sistem gudang 250 kWp butuh sekitar 1.500–1.750 meter persegi. Angka ini perlu ditambah ruang akses perawatan antar baris panel, biasanya 15–25 persen dari total luas, agar teknisi bisa bekerja dengan aman.',
            ],
            [
                'group' => 'teknis',
                'question' => 'Apa beda sistem on-grid, off-grid, dan hybrid?',
                'answer' => 'On-grid tersambung ke jaringan PLN tanpa baterai, paling murah dan paling efisien, tetapi ikut padam saat jaringan padam. Off-grid berdiri sendiri dengan bank baterai besar untuk lokasi yang belum terjangkau listrik, biayanya paling tinggi karena baterai mahal. Hybrid berada di tengah: tersambung ke PLN tetapi punya baterai cadangan, sehingga listrik tetap menyala saat pemadaman dan tagihan juga turun. Sebagian besar rumah di kota cocok dengan on-grid, sementara lokasi dengan pemadaman sering lebih cocok hybrid.',
            ],
            [
                'group' => 'teknis',
                'question' => 'Berapa lama umur panel surya dan inverter?',
                'answer' => 'Modul panel surya berkualitas umumnya punya garansi performa 25 tahun, artinya setelah 25 tahun masih menghasilkan minimal 80–85 persen dari daya awal. Panel tidak mati mendadak, melainkan menurun perlahan sekitar 0,4–0,55 persen per tahun. Inverter adalah komponen yang paling sering perlu diganti, dengan umur 10–15 tahun dan garansi pabrikan biasanya 5–10 tahun. Penggantian inverter jauh lebih murah dibanding penggantian panel.',
            ],
            [
                'group' => 'teknis',
                'question' => 'Apakah atap rumah saya cukup kuat menahan panel surya?',
                'answer' => 'Beban tambahan sistem panel surya berkisar 12–15 kilogram per meter persegi, termasuk rangka. Sebagian besar atap beton dan atap metal dengan rangka baja sudah memadai. Yang perlu diperiksa adalah atap dengan rangka kayu berusia tua, rangka baja ringan dengan bentang panjang tanpa sokongan tengah, dan atap yang sudah mendekati masa penggantian. Pemeriksaan struktur ini bagian dari survei gratis kami, dan bila perlu perkuatan akan kami sertakan biayanya secara terpisah agar Anda bisa memutuskan.',
            ],

            // Perizinan
            [
                'group' => 'perizinan',
                'question' => 'Apakah memasang PLTS atap perlu izin dari PLN?',
                'answer' => 'Perlu, berupa pendaftaran dan penggantian meteran menjadi meteran ekspor-impor. Proses ini diajukan ke unit PLN wilayah Anda dan umumnya memakan waktu dua sampai enam minggu tergantung kelengkapan dokumen dan kondisi jaringan setempat. Kami mengurus seluruh proses ini untuk Anda, termasuk menyiapkan gambar satu garis, spesifikasi peralatan, dan sertifikat pengujian instalasi yang menjadi syarat pengajuan.',
            ],
            [
                'group' => 'perizinan',
                'question' => 'Bagaimana sistem perhitungan ekspor-impor listrik ke PLN?',
                'answer' => 'Listrik yang Anda hasilkan melebihi pemakaian akan mengalir ke jaringan dan tercatat sebagai ekspor. Nilai ekspor itu menjadi pengurang tagihan pada periode berikutnya sesuai ketentuan tarif yang berlaku. Karena aturan besaran nilai ekspor pernah beberapa kali disesuaikan, kami selalu menghitung proyeksi penghematan berdasarkan ketentuan paling baru dan menyertakan asumsinya secara tertulis dalam dokumen kelayakan supaya Anda tahu persis dasar perhitungannya.',
            ],
            [
                'group' => 'perizinan',
                'question' => 'Apakah perlu izin lingkungan atau IMB tambahan?',
                'answer' => 'Untuk PLTS atap pada bangunan yang sudah berdiri, umumnya tidak diperlukan izin bangunan baru karena pemasangan tidak mengubah luas lantai. Pengecualian berlaku bila bangunan Anda berada di kawasan cagar budaya, atau bila sistem dipasang pada struktur tanah seperti carport surya yang berdiri sendiri. Untuk PLTS skala besar di atas tanah, diperlukan perizinan yang lebih lengkap dan kami akan dampingi prosesnya sejak tahap studi kelayakan.',
            ],

            // Perawatan
            [
                'group' => 'perawatan',
                'question' => 'Seberapa sering panel surya perlu dibersihkan?',
                'answer' => 'Untuk rumah di kawasan dengan curah hujan normal, pembersihan dua kali setahun sudah memadai karena hujan membantu membilas debu. Untuk lokasi berdebu, dekat pantai, dekat pabrik, atau di area dengan pohon rindang, kami sarankan setiap dua sampai tiga bulan. Yang penting dihindai adalah mencuci panel dengan deterjen atau sabun karena meninggalkan lapisan yang justru menahan debu, dan menyiram panel panas dengan air dingin karena perubahan suhu mendadak bisa menimbulkan retak halus.',
            ],
            [
                'group' => 'perawatan',
                'question' => 'Bagaimana saya tahu sistem sedang bermasalah?',
                'answer' => 'Setiap sistem yang kami pasang dilengkapi pemantauan produksi yang bisa Anda lihat dari aplikasi di ponsel. Anda akan melihat produksi harian dan bulanan, serta perbandingan dengan hari-hari sebelumnya. Tim kami juga memantau dari pusat dan akan menghubungi Anda lebih dulu bila produksi turun di luar batas wajar, misalnya lebih dari 25 persen dibanding hari dengan cuaca setara. Jadi Anda tidak perlu menunggu tagihan naik untuk menyadari ada masalah.',
            ],
            [
                'group' => 'perawatan',
                'question' => 'Garansi apa saja yang saya dapatkan?',
                'answer' => 'Ada tiga lapisan garansi. Pertama, garansi produk dari pabrikan untuk modul panel, biasanya 10–12 tahun terhadap cacat material. Kedua, garansi performa dari pabrikan, 25 tahun, yang menjamin panel tetap menghasilkan minimal persentase tertentu dari daya awal. Ketiga, garansi pemasangan dari kami selama 2 tahun yang mencakup kebocoran atap akibat pekerjaan kami, kelonggaran sambungan, dan kesalahan instalasi. Untuk inverter, garansi pabrikan umumnya 5–10 tahun dan bisa diperpanjang dengan biaya tambahan.',
            ],
            [
                'group' => 'perawatan',
                'question' => 'Apakah sistem panel surya perlu dimatikan saat pemadaman PLN?',
                'answer' => 'Sistem on-grid standar akan mati secara otomatis saat jaringan PLN padam, dan ini adalah fitur keselamatan yang wajib, bukan kerusakan. Alasannya, bila sistem tetap menyuntikkan listrik ke jaringan yang sedang diperbaiki, teknisi PLN bisa tersengat arus dari arah yang tidak mereka duga. Sistem hybrid berbeda: begitu jaringan padam, ia otomatis memisahkan diri dan tetap menyuplai panel beban kritis dari baterai, biasanya dalam waktu kurang dari 20 milidetik sehingga peralatan elektronik tidak sempat mati.',
            ],
        ];
    }
}
