<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::create([
            'name' => '100% Segar dari Kebun',
        ]);
        
        Feature::create([
            'name' => 'Bebas Pestisida Berbahaya',
        ]);
        
        Feature::create([
            'name' => 'Garansi Segar atau Ganti Baru',
        ]);
        
        Feature::create([
            'name' => 'Pengiriman Cepat Same Day',
        ]);
    }
}
