<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::create([
            'name' => 'Segar Farm Nusantara',
            'logo' => '/mocks/brands/apple.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Tani Makmur Organik',
            'logo' => '/mocks/brands/asus.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Kebun Hidroponik Lembang',
            'logo' => '/mocks/brands/lg.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Berastagi Fresh',
            'logo' => '/mocks/brands/dell.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Agro Lestari Malang',
            'logo' => '/mocks/brands/samsung.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Java Fresh Farm',
            'logo' => '/mocks/brands/toshiba.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Sayur Segar Alami',
            'logo' => '/mocks/brands/xiaomi.webp',
            'link' => '#'
        ]); 
        
        Brand::create([
            'name' => 'Bumi Organik Indonesia',
            'logo' => '/mocks/brands/hp.webp',
            'link' => '#'
        ]);
        
        Brand::create([
            'name' => 'Tani Bahagia',
            'logo' => '/mocks/brands/sony.webp',
            'link' => '#'
        ]);
    }
}
