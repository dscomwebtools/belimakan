<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml file';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // ✅ Tambahkan halaman statis
        $sitemap->add(Url::create('/'));
        $sitemap->add(Url::create('/about-us'));
        $sitemap->add(Url::create('/contact'));

        // ✅ Tambahkan produk dari database
        foreach (\App\Models\Product::all() as $product) {
            $sitemap->add(Url::create(route('client.product', $product->slug)));
        }

        // Simpan ke public/
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ Sitemap generated successfully!');
    }
}
