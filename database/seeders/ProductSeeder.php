<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Bayam Hijau Segar (Ikat)',
            'slug' => 'bayam-hijau-segar-ikat',
            'description' => 'Sayur bayam hijau segar dipanen langsung setiap pagi dari kebun mitra petani lokal. Daun segar hijau cerah, kaya zat besi, dan sangat cocok untuk menu sayur bening keluarga.'
        ]);
        
        Product::create([
            'name' => 'Kangkung Hidroponik Segar (Ikat)',
            'slug' => 'kangkung-hidroponik-segar-ikat',
            'description' => 'Kangkung hidroponik berkualitas tinggi, bersih dari tanah dan bebas pestisida kimia berbahaya. Tekstur batang renyah gurih, lezat dimasak tumis terasi atau cah kangkung.'
        ]);
        
        Product::create([
            'name' => 'Wortel Manis Berastagi Super (1 Kg)',
            'slug' => 'wortel-manis-berastagi-super-1kg',
            'description' => 'Wortel pilihan dari dataran tinggi Berastagi. Warna oranye cerah, renyah, manis segar alami, dan kaya vitamin A. Sangat baik untuk sup maupun minuman jus kesehatan.'
        ]);
        
        Product::create([
            'name' => 'Tomat Merah Segar Pilihan (500 gr)',
            'slug' => 'tomat-merah-segar-pilihan-500gr',
            'description' => 'Tomat merah segar dengan tingkat kematangan pas di pohon. Tekstur kenyal padat dan kaya antioksidan likopen. Sempurna untuk masakan sambal, sup, dan jus segar harian.'
        ]);
        
        Product::create([
            'name' => 'Kentang Granola Dieng (1 Kg)',
            'slug' => 'kentang-granola-dieng-1kg',
            'description' => 'Kentang Granola super dari pegunungan Dieng. Bertekstur pulen lembut saat dimasak dan kulit mulus bersih. Pilihan terbaik untuk perkedel, rendang, dan kentang goreng.'
        ]);

        Product::create([
            'name' => 'Brokoli Hijau Segar Bebas Pestisida (500 gr)',
            'slug' => 'brokoli-hijau-segar-bebas-pestisida-500gr',
            'description' => 'Brokoli hijau segar berdaun padat tanpa pestisida kimia. Kaya akan serat tinggi, kalsium, dan vitamin C. Pilihan sehat tepat untuk menu diet sehat dan sayur kukus si kecil.'
        ]);

        Product::create([
            'name' => 'Bawang Merah Brebes Pilihan (500 gr)',
            'slug' => 'bawang-merah-brebes-pilihan-500gr',
            'description' => 'Bawang merah asli dari Brebes dengan aroma wangi yang kuat, umbi padat, kering alami dan rasa gurih khas. Bikin setiap masakan dan bumbu dapur Anda makin sedap.'
        ]);
        
        Product::create([
            'name' => 'Cabai Rawit Merah Segar Pedas (250 gr)',
            'slug' => 'cabai-rawit-merah-segar-pedas-250gr',
            'description' => 'Cabai rawit merah segar dengan tingkat kepedasan maksimal. Dipetik langsung dalam kondisi merah matang, bersih, segar tahan lama, cocok untuk pecinta pedas sejati.'
        ]);
        
        Product::create([
            'name' => 'Pakcoy Hidroponik Organik (250 gr)',
            'slug' => 'pakcoy-hidroponik-organik-250gr',
            'description' => 'Pakcoy segar berbatang putih renyah dan berdaun hijau segar. Dibudidayakan secara hidroponik higienis, sangat pas untuk tumisan bawang putih, shabu-shabu, dan mie kuah.'
        ]);

        Product::create([
            'name' => 'Jamur Tiram Putih Segar (250 gr)',
            'slug' => 'jamur-tiram-putih-segar-250gr',
            'description' => 'Jamur tiram putih segar hasil budidaya kumbung modern yang steril. Tekstur kenyal lembut menyerupai daging, kaya protein nabati, sedap dimasak tumis atau jamur krispi.'
        ]);
    }
}
