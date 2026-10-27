<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Customer testimonials.
 *
 * These are illustrative sample entries for the demo install — replace them with
 * real, consented quotes before the site goes public.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->testimonials() as $index => $testimonial) {
            Testimonial::query()->updateOrCreate(
                ['name' => $testimonial['name'], 'company' => $testimonial['company']],
                $testimonial + ['sort_order' => ($index + 1) * 10, 'is_active' => true],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function testimonials(): array
    {
        return [
            [
                'name' => 'Andi Hartono',
                'role' => 'Pemilik Rumah',
                'company' => 'Bintaro, Tangerang Selatan',
                'quote' => 'Yang membuat saya yakin bukan harga penawarannya, tapi cara mereka menjelaskan. Ada hitungan per jam, ada denah bayangan di atap, dan mereka jujur bilang satu sisi atap tidak layak dipakai. Tagihan saya turun dari tiga juta jadi di bawah lima ratus ribu.',
                'rating' => 5,
            ],
            [
                'name' => 'Fitri Handayani',
                'role' => 'Manajer Operasional',
                'company' => 'PT Angkut Cepat Nusantara',
                'quote' => 'Proyek 248 kWp di gudang kami selesai sesuai jadwal meskipun sempat terkendala musim hujan. Yang paling saya hargai, mereka pakai klem tanpa pengeboran jadi tidak ada satu pun kebocoran atap sampai sekarang. Laporan produksi bulanan langsung kami pakai untuk laporan keberlanjutan.',
                'rating' => 5,
            ],
            [
                'name' => 'Made Sucipta',
                'role' => 'General Manager',
                'company' => 'Lovina Bay Resort',
                'quote' => 'Tamu kami tidak pernah lagi mengalami mati listrik sejak sistem hybrid dipasang. Genset yang dulu menyala berjam-jam sekarang hanya cadangan. Biaya operasional turun jauh dan itu terasa langsung pada laporan keuangan.',
                'rating' => 5,
            ],
            [
                'name' => 'Rusdi Marbun',
                'role' => 'Ketua Koperasi',
                'company' => 'Koperasi Tani Sejahtera Kampar',
                'quote' => 'Kami sempat ragu karena pabrik sawit itu berdebu dan panas. Tim mereka datang berkali-kali untuk mengukur, lalu menyusun jadwal pembersihan yang realistis dengan kemampuan kami. Konsumsi solar turun hampir enam puluh persen dan itu mengubah arus kas koperasi sepenuhnya.',
                'rating' => 5,
            ],
            [
                'name' => 'Suster Maria Goretti',
                'role' => 'Kepala Sekolah',
                'company' => 'SDN Wula Waijelu, Sumba Timur',
                'quote' => 'Laboratorium komputer kami akhirnya bisa dipakai setiap hari. Anak-anak tidak perlu lagi belajar di bawah cahaya lampu minyak. Dua warga desa kami dilatih merawat sistemnya, jadi kami tidak bergantung pada kunjungan dari luar.',
                'rating' => 5,
            ],
            [
                'name' => 'Hendra Wijaya',
                'role' => 'Direktur',
                'company' => 'CV Mitra Karya Bersama',
                'quote' => 'Yang cocok untuk kami adalah skema bertahap. Pasang separuh dulu, lihat hasilnya, baru lanjut. Semua jalur kabel dan tray sudah disiapkan untuk kapasitas penuh sejak awal jadi tahap kedua cuma tinggal tambah panel. Tidak ada yang dibongkar ulang.',
                'rating' => 4,
            ],
        ];
    }
}
