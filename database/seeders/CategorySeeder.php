<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Sayur Daun Segar',
        ]);
        
        Category::create([
            'name' => 'Sayur Buah & Polong',
        ]);
        
        Category::create([
            'name' => 'Sayur Umbi & Rimpang',
        ]);

        Category::create([
            'name' => 'Sayur Organik & Hidroponik',
        ]);

        Category::create([
            'name' => 'Jamur Segar',
        ]);

        Category::create([
            'name' => 'Cabai & Rempah Dapur',
        ]);
    }
}
