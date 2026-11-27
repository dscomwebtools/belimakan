<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimonial::create([
            'name' => 'Ibu Ratna',
            'img' => 'mocks/me.jpg',
            'message' => 'Sayurnya bener-bener segar seperti baru dipetik dari kebun! Bayam dan kangkungnya hijau mulus tanpa ulat, packing rapi dan bersih.',
            'show' => 1,
        ]); 

        Testimonial::create([
            'name' => 'Siti Rahma',
            'img' => 'mocks/me.jpg',
            'message' => 'Pengirimannya kilat banget, pesan pagi sebelum masak jam 8 sudah sampai di rumah. Masak jadi makin semangat karena sayurannya fresh.',
            'show' => 1,
        ]); 

        Testimonial::create([
            'name' => 'Dina Melinda',
            'img' => 'mocks/me.jpg',
            'message' => 'Sangat terbantu belanja sayur di BeliMakan. Gak perlu lagi subuh-subuh ke pasar becek, kualitas sayur hidroponik dan bumbu dapurnya super premium!',
            'show' => 1,
        ]); 

        Testimonial::create([
            'name' => 'Budi Santoso',
            'img' => 'mocks/me.jpg',
            'message' => 'Brokoli, wortel, dan kentangnya renyah dan manis. Anak-anak di rumah jadi suka makan sayur. Rekomendasi banget untuk keluarga sehat!',
            'show' => 1,
        ]); 
        
        Testimonial::create([
            'name' => 'Chef Hendra',
            'img' => 'mocks/me.jpg',
            'message' => 'Kualitas sayur organik di BeliMakan konsisten dan higienis. Sangat cocok untuk kebutuhan bahan baku restoran sehat saya.',
            'show' => 1,
        ]); 
    }
}
