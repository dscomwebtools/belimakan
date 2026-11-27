<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notice::create([
            'message' => 'Sayur Segar Panen Hari Ini Sudah Tersedia! Dapatkan Diskon & Gratis Ongkir Sekarang!',
            'link' => '/promo'
        ]);
    }
}
