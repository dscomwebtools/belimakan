<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPicture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPictureSeeder extends Seeder
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

        ProductPicture::create([
            'product_id' => $product1->id,
            'path' => '/mocks/a.jpg',
        ]);
        
        ProductPicture::create([
            'product_id' => $product2->id,
            'path' => '/mocks/b.jpg',
        ]);
        
        ProductPicture::create([
            'product_id' => $product3->id,
            'path' => '/mocks/c.jpg',
        ]);
        
        ProductPicture::create([
            'product_id' => $product4->id,
            'path' => '/mocks/d.jpg',
        ]);
        
        ProductPicture::create([
            'product_id' => $product5->id,
            'path' => '/mocks/e.jpg',
        ]);

        ProductPicture::create([
            'product_id' => $product6->id,
            'path' => '/mocks/c.jpg',
        ]);

        ProductPicture::create([
            'product_id' => $product7->id,
            'path' => '/mocks/a.jpg',
        ]);
        ProductPicture::create([
            'product_id' => $product8->id,
            'path' => '/mocks/b.jpg',
        ]);
        ProductPicture::create([
            'product_id' => $product9->id,
            'path' => '/mocks/e.jpg',
        ]);
        ProductPicture::create([
            'product_id' => $product10->id,
            'path' => '/mocks/d.jpg',
        ]);
    }
}
