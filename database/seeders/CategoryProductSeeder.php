<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product1 = Product::where('slug', 'bayam-hijau-segar-ikat')->first();
        $product2 = Product::where('slug', 'kangkung-hidroponik-segar-ikat')->first();
        $product3 = Product::where('slug', 'wortel-manis-berastagi-super-1kg')->first();
        $product4 = Product::where('slug', 'tomat-merah-segar-pilihan-500gr')->first();
        $product5 = Product::where('slug', 'kentang-granola-dieng-1kg')->first();
        $product6 = Product::where('slug', 'brokoli-hijau-segar-bebas-pestisida-500gr')->first();
        $product7 = Product::where('slug', 'bawang-merah-brebes-pilihan-500gr')->first();
        $product8 = Product::where('slug', 'cabai-rawit-merah-segar-pedas-250gr')->first();
        $product9 = Product::where('slug', 'pakcoy-hidroponik-organik-250gr')->first();
        $product10 = Product::where('slug', 'jamur-tiram-putih-segar-250gr')->first();

        // 1: Sayur Daun Segar
        CategoryProduct::create([
            'product_id' => $product1->id,
            'category_id' => 1,
        ]);
        
        // 1: Sayur Daun Segar & 4: Sayur Organik & Hidroponik
        CategoryProduct::create([
            'product_id' => $product2->id,
            'category_id' => 1,
        ]);
        
        // 3: Sayur Umbi & Rimpang
        CategoryProduct::create([
            'product_id' => $product3->id,
            'category_id' => 3,
        ]);
        
        // 2: Sayur Buah & Polong
        CategoryProduct::create([
            'product_id' => $product4->id,
            'category_id' => 2,
        ]);
        
        // 3: Sayur Umbi & Rimpang
        CategoryProduct::create([
            'product_id' => $product5->id,
            'category_id' => 3,
        ]);
        
        // 1: Sayur Daun Segar
        CategoryProduct::create([
            'product_id' => $product6->id,
            'category_id' => 1,
        ]);
        
        // 3: Sayur Umbi & Rimpang
        CategoryProduct::create([
            'product_id' => $product7->id,
            'category_id' => 3,
        ]);
        
        // 6: Cabai & Rempah Dapur
        CategoryProduct::create([
            'product_id' => $product8->id,
            'category_id' => 6,
        ]);
        
        // 4: Sayur Organik & Hidroponik
        CategoryProduct::create([
            'product_id' => $product9->id,
            'category_id' => 4,
        ]);
        
        // 5: Jamur Segar
        CategoryProduct::create([
            'product_id' => $product10->id,
            'category_id' => 5,
        ]);
    }
}
