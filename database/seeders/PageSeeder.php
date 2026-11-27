<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::create([
            'name' => 'how_to_order', 
            'title' => 'Cara Belanja Sayur Segar',
            'content' => '<p>Belanja sayur segar di <strong>BeliMakan.id</strong> sangat praktis dan mudah:</p>
            <ol class="list-decimal ml-6 space-y-2 mt-2">
                <li>Pilih aneka sayuran segar, sayur organik, atau bumbu dapur yang Anda butuhkan.</li>
                <li>Tentukan jumlah atau varian berat kemasan yang diinginkan, lalu klik <strong>Tambah ke Keranjang</strong>.</li>
                <li>Buka halaman keranjang belanja, periksa daftar pesanan, lalu klik <strong>Checkout</strong>.</li>
                <li>Tentukan alamat pengiriman Anda dan pilih layanan kurir (Instant / Same Day disarankan untuk menjaga kesegaran).</li>
                <li>Pilih metode pembayaran yang tersedia dan lakukan konfirmasi pembayaran.</li>
                <li>Pesanan sayur Anda akan dipersiapkan, disortir, dan dikirim langsung ke rumah Anda!</li>
            </ol>'
        ]);
        
        Page::create([
            'name' => 'how_to_return', 
            'title' => 'Garansi & Pengembalian Sayur',
            'content' => '<p>Kepuasan dan kesehatan Anda adalah prioritas kami. BeliMakan memberikan <strong>Garansi Segar 100%</strong>:</p>
            <ul class="list-disc ml-6 space-y-2 mt-2">
                <li>Jika sayuran yang Anda terima dalam kondisi busuk, layu parah, atau tidak layak konsumsi, Anda berhak mendapatkan penggantian produk baru atau pengembalian dana penuh.</li>
                <li>Silakan ambil foto atau video sayuran yang bermasalah saat paket tiba.</li>
                <li>Kirim bukti foto/video ke layanan pelanggan kami maksimal 1x24 jam sejak pesanan diterima.</li>
                <li>Tim kami akan segera memproses penggantian sayur atau refund dana Anda tanpa proses berbelit-belit.</li>
            </ul>'
        ]);
        
        Page::create([
            'name' => 'payment_method', 
            'title' => 'Metode Pembayaran',
            'content' => '<p>Untuk memudahkan transaksi belanja sayuran segar harian, BeliMakan mendukung berbagai metode pembayaran yang aman dan terpercaya:</p>
            <ul class="list-disc ml-6 space-y-2 mt-2">
                <li><strong>Transfer Bank:</strong> BCA, Mandiri, BNI, BRI, dan Bank Syariah Indonesia (BSI).</li>
                <li><strong>QRIS & E-Wallet:</strong> GoPay, OVO, DANA, ShopeePay, dan LinkAja.</li>
                <li><strong>Virtual Account:</strong> Pembayaran otomatis dengan verifikasi instan.</li>
            </ul>'
        ]);
        
        Page::create([
            'name' => 'pricelist', 
            'title' => 'Pricelist Sayur Segar Harian',
            'content' => '<p>Harga sayuran segar di BeliMakan selalu diperbarui setiap hari sesuai hasil panen petani mitra kami untuk memberikan harga terbaik dan paling terjangkau bagi keluarga Anda. Anda dapat mengunduh daftar harga sayur harian melalui tautan atau langsung mengecek halaman katalog produk kami.</p>'
        ]);
        
        Page::create([
            'name' => 'about_us', 
            'title' => 'Tentang BeliMakan',
            'content' => '<p><strong>BeliMakan.id</strong> adalah platform belanja sayur segar, sayur hidroponik, sayur organik, buah-buahan, jamur, dan bumbu dapur langsung dari petani lokal ke dapur rumah Anda.</p>
            <p class="mt-2">Kami hadir untuk memotong rantai distribusi panjang produk pertanian sehingga petani mendapatkan harga yang adil dan konsumen menikmati sayuran dengan tingkat kesegaran tertinggi, higienis, serta harga yang hemat. Setiap sayuran melewati proses sortir mutu ketat sebelum dikemas rapi dan dikirimkan ke rumah Anda.</p>'
        ]);
        
        Page::create([
            'name' => 'contact', 
            'title' => 'Kontak Layanan Pelanggan',
            'content' => '<p>Punya pertanyaan seputar pesanan, ketersediaan pasokan sayur segar, atau kerjasama mitra kebun tani? Hubungi tim BeliMakan melalui:</p>
            <ul class="list-disc ml-6 space-y-2 mt-2">
                <li><strong>WhatsApp CS:</strong> +62 812-3456-7890 (Senin - Minggu, 06.00 - 18.00 WIB)</li>
                <li><strong>Email:</strong> halo@belimakan.id</li>
                <li><strong>Pusat Distribusi:</strong> Sentra Distribusi Sayur Segar BeliMakan, Jl. Raya Pasar Minggu No. 45, Jakarta Selatan</li>
            </ul>'
        ]);
    }
}
