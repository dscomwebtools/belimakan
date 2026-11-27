<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product5 = Product::where('slug', 'kentang-granola-dieng-1kg')->first();
        $product6 = Product::where('slug', 'brokoli-hijau-segar-bebas-pestisida-500gr')->first();
        $product7 = Product::where('slug', 'bawang-merah-brebes-pilihan-500gr')->first();
        $product8 = Product::where('slug', 'cabai-rawit-merah-segar-pedas-250gr')->first();
        $product9 = Product::where('slug', 'pakcoy-hidroponik-organik-250gr')->first();
        $product10 = Product::where('slug', 'jamur-tiram-putih-segar-250gr')->first();

        Promo::create([
            'product_id' => $product5->id,
            'discount' => 15,
            'active' => true,
        ]);

        Promo::create([
            'product_id' => $product6->id,
            'discount' => 20,
            'active' => true,
        ]);

        Promo::create([
            'product_id' => $product7->id,
            'discount' => 10,
            'active' => true,
        ]);

        Promo::create([
            'product_id' => $product8->id,
            'discount' => 25,
            'active' => true,
        ]);

        Promo::create([
            'product_id' => $product9->id,
            'discount' => 30,
            'active' => true,
        ]);

        Promo::create([
            'product_id' => $product10->id,
            'discount' => 15,
            'active' => true,
        ]);
    }
}
