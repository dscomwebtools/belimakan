<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'section_name' => 'hero',
            'is_show' => true,
            'title' => 'Sayur Segar Langsung dari Petani !',
            'description' => 'Dapatkan aneka sayuran segar pilihan, sayur hidroponik, dan kebutuhan dapur organik terbaik yang dipetik langsung setiap pagi dari kebun petani lokal dengan jaminan kesegaran 100%.',
            'show_items' => 10
        ]);
        
        Setting::create([
            'section_name' => 'promo',
            'is_show' => true,
            'title' => 'PROMO PANEN SEGAR HARI INI !!',
            'description' => 'Belanja kebutuhan sayur dan bumbu dapur hemat setiap hari dengan diskon spesial panen raya. Pesan pagi, langsung diantar!',
            'show_items' => 10
        ]);
        
        Setting::create([
            'section_name' => 'auction',
            'is_show' => true,
            'title' => 'LELANG PAKET SAYUR & HASIL PANEN !!',
            'description' => 'Dapatkan paket sayur segar pilihan dan hasil panen terbaik langsung dari kebun mitra kami dengan harga penawaran terbaik sebelum lelang berakhir!',
            'show_items' => 10
        ]);
        
        Setting::create([
            'section_name' => 'product',
            'is_show' => true,
            'title' => 'SAYURAN TERBARU & TERLARIS !!',
            'description' => 'Pilihan sayuran hijau, sayur buah, umbi-umbian, dan bumbu dapur paling segar untuk menu masakan sehat keluarga Anda.',
            'show_items' => 10
        ]);
        
        Setting::create([
            'section_name' => 'testimonial',
            'is_show' => true,
            'title' => 'KATA PELANGGAN KAMI',
            'description' => 'Pengalaman para ibu rumah tangga dan pecinta kuliner sehat yang berlangganan sayur segar di BeliMakan.',
            'show_items' => 10
        ]);
        
        Setting::create([
            'section_name' => 'faq',
            'is_show' => true,
            'title' => 'PERTANYAAN SEPUTAR SAYUR SEGAR',
            'description' => 'Informasi penting seputar pemesanan sayur, standar kesegaran produk, garansi, dan pengiriman kilat.',
            'show_items' => 10
        ]);
        
        Setting::create([
            'section_name' => 'brand',
            'is_show' => true,
            'title' => 'MITRA KEBUN & PETANI LOKAL',
            'description' => 'Bekerja sama erat dengan kelompok tani lokal dan perkebunan hidroponik terpercaya untuk menjaga kualitas sayur terbaik.',
            'show_items' => 10
        ]);

        Setting::create([
            'section_name' => 'link',
            'is_show' => true,
            'title' => 'BeliMakan.id',
            'description' => 'Belanja Sayur Segar Langsung dari Petani Lokal',
            'show_items' => 10
        ]);
    }
}
