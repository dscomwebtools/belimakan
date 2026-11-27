<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
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
        
        // Product 1 - Bayam Hijau
        ProductVariant::create([
            'product_id' => $product1->id,
            'product_variant_name' => null,
            'sku' => 'bayam-segar-ori',
            'price' => 5000,
            'stock' => 100,
            'weight' => 250, //gram
            'active' => true
        ]);

        // Product 2 - Kangkung Hidroponik (with variants)
        ProductVariant::create([
            'product_id' => $product2->id,
            'product_variant_name' => '500g Organik Super',
            'sku' => 'kangkung-500g-org',
            'price' => 9000,
            'stock' => 100,
            'weight' => 500, //gram
            'active' => true
        ]);
        
        ProductVariant::create([
            'product_id' => $product2->id,
            'product_variant_name' => '1kg Organik Super',
            'sku' => 'kangkung-1kg-org',
            'price' => 16000,
            'stock' => 100,
            'weight' => 1000, //gram
            'active' => true
        ]);
        
        ProductVariant::create([
            'product_id' => $product2->id,
            'product_variant_name' => '1kg Standar Segar',
            'sku' => 'kangkung-1kg-std',
            'price' => 13000,
            'stock' => 100,
            'weight' => 1000, //gram
            'active' => true
        ]);

        ProductVariant::create([
            'product_id' => $product2->id,
            'product_variant_name' => '500g Standar Segar',
            'sku' => 'kangkung-500g-std',
            'price' => 7500,
            'stock' => 100,
            'weight' => 500, //gram
            'active' => true
        ]);
        
        // Product 3 - Wortel
        ProductVariant::create([
            'product_id' => $product3->id,
            'product_variant_name' => null,
            'sku' => 'wortel-berastagi-1kg',
            'price' => 18000,
            'stock' => 150,
            'weight' => 1000, //gram
            'active' => true
        ]);
        
        // Product 4 - Tomat Merah
        ProductVariant::create([
            'product_id' => $product4->id,
            'product_variant_name' => null,
            'sku' => 'tomat-merah-500g',
            'price' => 12000,
            'stock' => 200,
            'weight' => 500, //gram
            'active' => true
        ]);
        
        // Product 5 - Kentang
        ProductVariant::create([
            'product_id' => $product5->id,
            'product_variant_name' => null,
            'sku' => 'kentang-dieng-1kg',
            'price' => 22000,
            'stock' => 150,
            'weight' => 1000, //gram
            'active' => true
        ]);

        // Product 6 - Brokoli
        ProductVariant::create([
            'product_id' => $product6->id,
            'product_variant_name' => null,
            'sku' => 'brokoli-hijau-500g',
            'price' => 16000,
            'stock' => 100,
            'weight' => 500, //gram
            'active' => true
        ]);

        // Product 7 - Bawang Merah
        ProductVariant::create([
            'product_id' => $product7->id,
            'product_variant_name' => null,
            'sku' => 'bawang-merah-500g',
            'price' => 26000,
            'stock' => 180,
            'weight' => 500, //gram
            'active' => true
        ]);

        // Product 8 - Cabai Rawit
        ProductVariant::create([
            'product_id' => $product8->id,
            'product_variant_name' => null,
            'sku' => 'cabai-rawit-250g',
            'price' => 19000,
            'stock' => 120,
            'weight' => 250, //gram
            'active' => true
        ]);

        // Product 9 - Pakcoy
        ProductVariant::create([
            'product_id' => $product9->id,
            'product_variant_name' => null,
            'sku' => 'pakcoy-hidro-250g',
            'price' => 8000,
            'stock' => 150,
            'weight' => 250, //gram
            'active' => true
        ]);

        // Product 10 - Jamur Tiram
        ProductVariant::create([
            'product_id' => $product10->id,
            'product_variant_name' => null,
            'sku' => 'jamur-tiram-250g',
            'price' => 10000,
            'stock' => 140,
            'weight' => 250, //gram
            'active' => true
        ]);
    }
}
