<?php

namespace Database\Seeders;

use App\Models\Dimention;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DimentionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product1 = Product::where('name', 'Bayam Hijau Segar (Ikat)')->first();
        $product2 = Product::where('name', 'Kangkung Hidroponik Segar (Ikat)')->first();
        $product3 = Product::where('name', 'Wortel Manis Berastagi Super (1 Kg)')->first();
        $product4 = Product::where('name', 'Tomat Merah Segar Pilihan (500 gr)')->first();
        $product5 = Product::where('name', 'Kentang Granola Dieng (1 Kg)')->first();

        Dimention::create([
            'product_id' => $product1->id,
            'length' => 25,
            'width' => 15,
            'height' => 10
        ]);

        Dimention::create([
            'product_id' => $product2->id,
            'length' => 25,
            'width' => 15,
            'height' => 10
        ]);

        Dimention::create([
            'product_id' => $product3->id,
            'length' => 20,
            'width' => 15,
            'height' => 12
        ]);

        Dimention::create([
            'product_id' => $product4->id,
            'length' => 15,
            'width' => 15,
            'height' => 10
        ]);

        Dimention::create([
            'product_id' => $product5->id,
            'length' => 20,
            'width' => 15,
            'height' => 15
        ]);
    }
}
