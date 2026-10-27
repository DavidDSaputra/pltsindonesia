<?php

namespace Database\Seeders;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

/**
 * Sample inbox traffic and newsletter list for the demo install.
 *
 * These are illustrative enquiries written for demonstration only. Real
 * messages arrive through the contact form and the newsletter form; the
 * only source the live form records is `footer`.
 */
class LeadSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->messages() as $message) {
            $sentAt = now()->startOfDay()->subDays($message['days_ago'])->setTime(10, 15);

            $record = ContactMessage::query()->updateOrCreate(
                ['email' => $message['email'], 'subject' => $message['subject']],
                [
                    'name' => $message['name'],
                    'phone' => $message['phone'],
                    'message' => $message['message'],
                    'status' => $message['status'],
                    'ip_address' => $message['ip_address'],
                ],
            );

            $record->forceFill(['created_at' => $sentAt, 'updated_at' => $sentAt])->save();
        }

        foreach ($this->subscribers() as $index => $subscriber) {
            $subscribedAt = now()->startOfDay()->subDays(6 + ($index * 11))->setTime(20, 40);

            $record = Subscriber::subscribe($subscriber['email'], $subscriber['source']);

            if (! $subscriber['is_active']) {
                $record->fill(['is_active' => false]);
            }

            $record->forceFill([
                'subscribed_at' => $subscribedAt,
                'created_at' => $subscribedAt,
                'updated_at' => $subscribedAt,
            ])->save();
        }

        $this->command?->info(sprintf(
            'Leads ready — %d pesan (%d belum dibaca), %d pelanggan email.',
            count($this->messages()),
            collect($this->messages())->where('status', MessageStatus::New)->count(),
            count($this->subscribers()),
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function messages(): array
    {
        return [
            [
                'name' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya@example.com',
                'phone' => '0812-8877-4321',
                'subject' => 'Konsultasi PLTS atap rumah 5.500 VA di Tangerang',
                'message' => "Selamat siang. Rumah saya di Tangerang Selatan, daya 5.500 VA, tagihan rata-rata 1,9 juta per bulan. Atap genteng menghadap utara dan luasnya sekitar 40 meter persegi tanpa bayangan.\n\nBerapa kapasitas yang masuk akal untuk kondisi ini, dan apakah perlu baterai? Saya ingin tahu juga berapa lama proses perizinannya.",
                'status' => MessageStatus::New,
                'ip_address' => '114.10.64.21',
                'days_ago' => 1,
            ],
            [
                'name' => 'Nadia Puspita',
                'email' => 'nadia@kopisenja.example.id',
                'phone' => '0811-2233-9900',
                'subject' => 'Penawaran PLTS untuk ruko dua lantai di Bandung',
                'message' => "Kami mengelola dua ruko untuk kedai kopi dan roastery di Bandung. Total pemakaian listrik sekitar 4.200 kWh per bulan, sebagian besar pada siang hari untuk mesin roasting dan pendingin.\n\nBisa minta penawaran dan hitungan balik modalnya? Atap kami dak beton datar, luas sekitar 120 meter persegi.",
                'status' => MessageStatus::New,
                'ip_address' => '36.84.112.7',
                'days_ago' => 2,
            ],
            [
                'name' => 'Bagus Setiawan',
                'email' => 'bagus.setiawan@example.com',
                'phone' => null,
                'subject' => 'Pertanyaan seputar aturan ekspor listrik',
                'message' => "Saya membaca artikel di blog Anda tentang ekspor listrik. Kalau kapasitas sistem saya lebih kecil dari konsumsi tahunan, apakah tetap perlu pengajuan ke PLN dan penggantian meteran? Atau ada skema lain yang lebih sederhana?",
                'status' => MessageStatus::New,
                'ip_address' => '103.28.14.190',
                'days_ago' => 3,
            ],
            [
                'name' => 'PT Anugrah Tirta Mandiri',
                'email' => 'procurement@anugrahtirta.example.co.id',
                'phone' => '021-5099-7712',
                'subject' => 'Permintaan kajian awal PLTS atap gudang 3.000 m2',
                'message' => "Perusahaan kami memiliki gudang distribusi di Kawasan Industri Cikarang dengan atap metal seluas kurang lebih 3.000 meter persegi. Operasi berjalan dua shift, Senin sampai Sabtu.\n\nKami ingin mendapatkan kajian awal kapasitas, estimasi investasi, dan proyeksi penghematan. Data tagihan 12 bulan terakhir tersedia dan bisa kami kirimkan.\n\nMohon dijadwalkan panggilan dengan tim kami minggu ini.",
                'status' => MessageStatus::New,
                'ip_address' => '180.214.233.58',
                'days_ago' => 4,
            ],
            [
                'name' => 'Maria Gonzales',
                'email' => 'maria.g@resortlovina.example.com',
                'phone' => '0813-3812-4477',
                'subject' => 'Sistem off-grid untuk resort di Bali utara',
                'message' => "Resort kami di Lovina punya 14 vila dan pasokan jaringan sering tidak stabil pada musim hujan. Saat ini kami mengandalkan genset yang biaya bahan bakarnya semakin berat.\n\nApakah sistem hybrid dengan baterai cocok untuk kondisi ini? Berapa kapasitas yang perlu disiapkan supaya genset hanya menjadi cadangan?",
                'status' => MessageStatus::Read,
                'ip_address' => '112.215.176.44',
                'days_ago' => 7,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'phone' => '0856-4471-2290',
                'subject' => 'Jadwal perawatan untuk sistem terpasang 2024',
                'message' => "Sistem 4,4 kWp kami dipasang dua tahun lalu dan produksinya terasa menurun sejak musim kemarau kemarin. Apakah bisa dijadwalkan pemeriksaan dan pembersihan? Sekalian minta dicek log peringatan pada inverter.",
                'status' => MessageStatus::Replied,
                'ip_address' => '110.139.88.12',
                'days_ago' => 12,
            ],
            [
                'name' => 'Sekolah Dasar Negeri Waingapu',
                'email' => 'sdn.waingapu@example.sch.id',
                'phone' => null,
                'subject' => 'Permintaan informasi program PLTS untuk sekolah',
                'message' => "Kami ingin tahu apakah ada skema pembiayaan atau program yang memungkinkan sekolah di daerah kami memasang PLTS. Kebutuhan utama adalah penerangan ruang kelas, komputer, dan pompa air.\n\nKalau memungkinkan, kami mohon informasi persyaratan pengajuannya.",
                'status' => MessageStatus::Replied,
                'ip_address' => '36.80.144.203',
                'days_ago' => 21,
            ],
            [
                'name' => 'Toko Bangunan Sejahtera',
                'email' => 'info@sejahterabangunan.example.com',
                'phone' => '031-7788-1200',
                'subject' => 'Spam — penawaran pinjaman tanpa agunan',
                'message' => "Kami menawarkan pinjaman modal usaha cepat cair tanpa jaminan dengan bunga rendah. Hubungi nomor berikut untuk pengajuan hari ini juga.",
                'status' => MessageStatus::Archived,
                'ip_address' => '45.132.192.77',
                'days_ago' => 30,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function subscribers(): array
    {
        return [
            ['email' => 'hendra.wijaya@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'nadia@kopisenja.example.id', 'source' => 'footer', 'is_active' => true],
            ['email' => 'sinta.rahmawati@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'yohanes.pratama@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'dewi.kartika@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'agus.sulaiman@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'ratna.permata@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'bambang.hartono@example.com', 'source' => 'footer', 'is_active' => false],
            ['email' => 'fitri.handayani@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'eko.susilo@example.com', 'source' => 'footer', 'is_active' => true],
            ['email' => 'lilik.maryani@example.com', 'source' => 'footer', 'is_active' => false],
            ['email' => 'reza.mahendra@example.com', 'source' => 'footer', 'is_active' => true],
        ];
    }
}
