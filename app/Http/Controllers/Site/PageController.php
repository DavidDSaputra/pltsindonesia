<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Gallery;
use App\Support\JsonLd;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Company profile: story, values, milestones and the team promise.
     */
    public function about(): View
    {
        return view('site.about');
    }

    /**
     * Installation gallery grouped by album.
     */
    public function gallery(): View
    {
        $images = Gallery::query()->active()->ordered()->get();

        if ($images->isEmpty()) {
            return view('site.empty', ['title' => 'Galeri']);
        }

        return view('site.gallery', [
            'images' => $images,
            'groups' => Gallery::groups(),
        ]);
    }

    /**
     * Frequently asked questions, grouped, with FAQPage structured data.
     */
    public function faq(): View
    {
        $faqs = Faq::query()->active()->ordered()->get();

        if ($faqs->isEmpty()) {
            return view('site.empty', ['title' => 'FAQ']);
        }

        return view('site.faq', [
            'faqs' => $faqs,
            'groups' => Faq::groups(),
            'schema' => $faqs->isNotEmpty() ? [JsonLd::faqPage($faqs)] : [],
        ]);
    }

    /**
     * Contact details plus the Livewire enquiry form.
     */
    public function contact(): View
    {
        if (blank(site('email')) && blank(site('phone')) && blank(site('address'))) {
            return view('site.empty', ['title' => 'Kontak']);
        }

        return view('site.contact');
    }

    public function privacy(): View
    {
        return view('site.legal', [
            'title' => 'Kebijakan Privasi',
            'bodyKey' => 'privacy_body',
            'updatedKey' => 'privacy_updated_at',
            'fallback' => $this->privacyFallback(),
        ]);
    }

    public function terms(): View
    {
        return view('site.legal', [
            'title' => 'Syarat & Ketentuan',
            'bodyKey' => 'terms_body',
            'updatedKey' => 'terms_updated_at',
            'fallback' => $this->termsFallback(),
        ]);
    }

    /**
     * Default privacy copy used until an editor writes their own in the CMS.
     *
     * @return array<int, array{heading: string, body: string}>
     */
    private function privacyFallback(): array
    {
        return [
            [
                'heading' => 'Data yang kami kumpulkan',
                'body' => 'Kami hanya mengumpulkan data yang Anda kirimkan secara sukarela melalui formulir kontak dan formulir langganan, yaitu nama, alamat email, nomor telepon, serta isi pesan Anda. Data teknis seperti alamat IP dan jenis peramban disimpan untuk keperluan keamanan dan statistik agregat.',
            ],
            [
                'heading' => 'Tujuan penggunaan data',
                'body' => 'Data tersebut digunakan untuk menindaklanjuti permintaan penawaran, memberikan konsultasi teknis, mengirim buletin bila Anda berlangganan, serta meningkatkan kualitas layanan kami. Kami tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga.',
            ],
            [
                'heading' => 'Cookie',
                'body' => 'Situs ini menggunakan cookie fungsional untuk menjaga sesi masuk Anda tetap aktif. Cookie analitik hanya dipakai dalam bentuk agregat dan tidak mengidentifikasi Anda secara pribadi.',
            ],
            [
                'heading' => 'Penyimpanan dan keamanan',
                'body' => 'Data disimpan pada server yang kami kelola dengan akses terbatas. Kami menerapkan enkripsi TLS pada seluruh transmisi data dan meninjau izin akses secara berkala.',
            ],
            [
                'heading' => 'Hak Anda',
                'body' => 'Anda berhak meminta salinan, perbaikan, atau penghapusan data pribadi Anda kapan saja. Untuk berhenti berlangganan, gunakan tautan berhenti di setiap email atau hubungi kami langsung.',
            ],
            [
                'heading' => 'Kontak',
                'body' => 'Permintaan terkait privasi dapat dikirim melalui halaman kontak atau email resmi yang tercantum di bagian bawah situs ini.',
            ],
        ];
    }

    /**
     * Default terms copy used until an editor writes their own in the CMS.
     *
     * @return array<int, array{heading: string, body: string}>
     */
    private function termsFallback(): array
    {
        return [
            [
                'heading' => 'Ruang lingkup layanan',
                'body' => 'Kami menyediakan jasa survei, perancangan, pengadaan, pemasangan, hingga perawatan sistem pembangkit listrik tenaga surya. Setiap pekerjaan dijalankan berdasarkan kesepakatan tertulis yang memuat lingkup, jadwal, dan biaya.',
            ],
            [
                'heading' => 'Permintaan penawaran',
                'body' => 'Penawaran yang kami terbitkan berlaku selama 30 hari kalender sejak tanggal penerbitan, kecuali dinyatakan lain. Harga dapat menyesuaikan hasil survei lapangan dan kondisi teknis lokasi pemasangan.',
            ],
            [
                'heading' => 'Pemasangan dan garansi',
                'body' => 'Pemasangan dilakukan oleh teknisi bersertifikat sesuai standar keselamatan kelistrikan yang berlaku. Garansi pekerjaan pemasangan diberikan selama 2 tahun, sementara garansi produk mengikuti ketentuan masing-masing pabrikan.',
            ],
            [
                'heading' => 'Konten situs',
                'body' => 'Seluruh artikel, gambar, dan materi edukasi pada situs ini disusun untuk tujuan informasi umum dan tidak menggantikan asesmen teknis di lokasi. Kami berupaya menjaga keakuratan informasi namun tidak menjamin kesesuaiannya untuk setiap kondisi khusus.',
            ],
            [
                'heading' => 'Hak kekayaan intelektual',
                'body' => 'Merek, logo, dan seluruh konten situs ini dilindungi undang-undang. Penggunaan ulang untuk kepentingan komersial memerlukan izin tertulis dari kami.',
            ],
            [
                'heading' => 'Penyelesaian sengketa',
                'body' => 'Setiap perselisihan akan diselesaikan terlebih dahulu melalui musyawarah. Apabila tidak tercapai kesepakatan, para pihak memilih domisili hukum pada pengadilan negeri setempat.',
            ],
        ];
    }
}
