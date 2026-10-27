<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

/**
 * Gallery entries pointing at the original artwork shipped in public/images.
 *
 * Replace the image paths through the CMS media library once real project
 * photography is available.
 */
class GallerySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->images() as $index => $image) {
            Gallery::query()->updateOrCreate(
                ['image' => $image['image']],
                $image + ['sort_order' => ($index + 1) * 10, 'is_active' => true],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function images(): array
    {
        return [
            [
                'title' => 'Susunan modul di atap rumah tinggal',
                'image' => '/images/gallery-atap-rumah.svg',
                'alt' => 'Deretan modul panel surya tersusun rapi di atap rumah tinggal menghadap barat daya',
                'group' => 'Rumah Tinggal',
            ],
            [
                'title' => 'Rangka aluminium sebelum pemasangan modul',
                'image' => '/images/gallery-rangka-atap.svg',
                'alt' => 'Rangka aluminium dan rel penopang yang sudah terpasang di atap sebelum modul dipasang',
                'group' => 'Proses Pemasangan',
            ],
            [
                'title' => 'Inverter string pada dinding garasi',
                'image' => '/images/gallery-inverter.svg',
                'alt' => 'Inverter string dengan layar pemantauan produksi terpasang pada dinding yang teduh',
                'group' => 'Proses Pemasangan',
            ],
            [
                'title' => 'Atap gudang logistik 248 kWp',
                'image' => '/images/gallery-gudang.svg',
                'alt' => 'Atap gudang logistik yang hampir seluruhnya tertutup susunan panel surya',
                'group' => 'Komersial & Industri',
            ],
            [
                'title' => 'Bank baterai lithium sistem hybrid',
                'image' => '/images/gallery-baterai.svg',
                'alt' => 'Empat unit baterai lithium berjejer di dalam ruang dengan indikator status pengisian',
                'group' => 'Komersial & Industri',
            ],
            [
                'title' => 'Komisioning dan uji produksi',
                'image' => '/images/gallery-komisioning.svg',
                'alt' => 'Teknisi membaca grafik produksi pada layar pemantauan saat komisioning sistem',
                'group' => 'Proses Pemasangan',
            ],
            [
                'title' => 'Pembersihan modul berkala',
                'image' => '/images/gallery-pembersihan.svg',
                'alt' => 'Permukaan modul disemprot air bebas mineral saat pembersihan berkala',
                'group' => 'Perawatan',
            ],
            [
                'title' => 'Kanopi parkir bertenaga surya',
                'image' => '/images/gallery-carport.svg',
                'alt' => 'Kanopi parkir dengan atap panel surya yang menaungi dua kendaraan',
                'group' => 'Komersial & Industri',
            ],
            [
                'title' => 'Sistem mandiri di lokasi terpencil',
                'image' => '/images/gallery-off-grid.svg',
                'alt' => 'Susunan panel surya sistem off-grid dengan latar perbukitan di lokasi terpencil',
                'group' => 'Rumah Tinggal',
            ],
            [
                'title' => 'Penataan kabel dan proteksi DC',
                'image' => '/images/gallery-kabel.svg',
                'alt' => 'Kabel DC tersusun pada tray beserta panel pemutus arus dan proteksi surge',
                'group' => 'Proses Pemasangan',
            ],
            [
                'title' => 'Rangka pada atap beton datar',
                'image' => '/images/gallery-atap-beton.svg',
                'alt' => 'Susunan panel surya dengan rangka ballast di atas atap beton datar',
                'group' => 'Komersial & Industri',
            ],
            [
                'title' => 'Tim teknisi sebelum memulai pekerjaan',
                'image' => '/images/gallery-tim.svg',
                'alt' => 'Empat teknisi mengenakan helm dan rompi keselamatan bersiap memulai pekerjaan pemasangan',
                'group' => 'Perawatan',
            ],
        ];
    }
}
