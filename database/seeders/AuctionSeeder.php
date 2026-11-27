<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product1 = Product::where('name', 'Bayam Hijau Segar (Ikat)')->first();
        $product2 = Product::where('name', 'Kangkung Hidroponik Segar (Ikat)')->first();
        $product3 = Product::where('name', 'Wortel Manis Berastagi Super (1 Kg)')->first();

        $member2 = User::firstWhere('username', 'member2');
        
        Auction::create([
            'product_id' => $product1->id,
            'active' => true,
            'endtime' => now()->addDays(1),
            'bid_start' => 25000,
            'bid_increment' => 5000,
            'rules' => 'Lelang paket panen bayam segar 10 ikat langsung dari kebun. Sayuran dipanen dan dikirim segera setelah lelang selesai.',
        ]);

        Auction::create([
            'product_id' => $product2->id,
            'active' => true,
            'endtime' => now()->addDays(2),
            'bid_start' => 40000,
            'bid_increment' => 10000,
            'rules' => 'Lelang keranjang panen kangkung hidroponik premium 5 kg. Higienis, bebas pestisida kimia.',
            'winner' => $member2?->id,
            'bid_winner' => 80000,
        ]);
        
        Auction::create([
            'product_id' => $product3->id,
            'active' => true,
            'endtime' => now()->addDays(3),
            'bid_start' => 60000,
            'bid_increment' => 10000,
            'rules' => 'Lelang krat peti wortel manis Berastagi grade super 10 kg. Pemenang harap konfirmasi alamat pengiriman pada admin.',
        ]);
    }
}
