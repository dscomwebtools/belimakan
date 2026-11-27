<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::create([
            'question' => 'Apa saja yang dijual di BeliMakan?',
            'answer' => 'BeliMakan menyediakan aneka sayuran segar (sayur daun, sayur buah, sayur umbi), sayur hidroponik dan organik, jamur segar, serta rempah bumbu dapur pilihan langsung dari petani lokal.'
        ]);

        Faq::create([
            'question' => 'Bagaimana BeliMakan menjaga kesegaran sayuran?',
            'answer' => 'Sayuran dipanen setiap dini hari oleh petani mitra kami, melewati proses sortir higienis dan pengemasan rapi, kemudian dikirim menggunakan kurir cepat agar tiba di dapur Anda dalam kondisi tetap segar dan renyah.'
        ]);

        Faq::create([
            'question' => 'Bagaimana jika sayuran yang saya terima layu atau rusak?',
            'answer' => 'Kami memberikan Garansi Segar 100%. Cukup foto produk sayur yang bermasalah dan hubungi Customer Service kami dalam waktu 1x24 jam setelah pesanan diterima untuk penggantian baru atau pengembalian dana.'
        ]);

        Faq::create([
            'question' => 'Apakah melayani pengiriman di hari yang sama (Same-Day)?',
            'answer' => 'Ya, kami melayani pengiriman Instant dan Same-Day untuk menjaga mutu dan kesegaran sayuran sampai di tangan Anda.'
        ]);
    }
}
