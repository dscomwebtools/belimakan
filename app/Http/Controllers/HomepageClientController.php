<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Brand;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageClientController extends Controller
{
    function index(): View
    {
        // SETTINGS
        $hero = Setting::firstWhere('section_name', 'hero') ?? new Setting(['section_name' => 'hero', 'title' => 'Hero', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $promo = Setting::firstWhere('section_name', 'promo') ?? new Setting(['section_name' => 'promo', 'title' => 'Promo', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $auction = Setting::firstWhere('section_name', 'auction') ?? new Setting(['section_name' => 'auction', 'title' => 'Lelang', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $product = Setting::firstWhere('section_name', 'product') ?? new Setting(['section_name' => 'product', 'title' => 'Produk', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $testimonial = Setting::firstWhere('section_name', 'testimonial') ?? new Setting(['section_name' => 'testimonial', 'title' => 'Testimonial', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $faq = Setting::firstWhere('section_name', 'faq') ?? new Setting(['section_name' => 'faq', 'title' => 'FAQ', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $brand = Setting::firstWhere('section_name', 'brand') ?? new Setting(['section_name' => 'brand', 'title' => 'Brand', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        $link = Setting::firstWhere('section_name', 'link') ?? new Setting(['section_name' => 'link', 'title' => 'Link', 'description' => '', 'show_items' => 10, 'is_show' => true]);

        return view('admin.homepage-client.index', compact(
            'hero',
            'promo',
            'auction',
            'product',
            'testimonial',
            'faq',
            'brand',
            'link',
        ));
    }

    function update(Request $request, $section_name)
    {
        $request->validate([
            'title' => ['required'],
            'show_items' => ['numeric', 'min:1', 'max:100']
        ], [
            'title.required' => 'Judul tidak boleh kosong!'
        ], []);

        $data = Setting::firstOrCreate(['section_name' => $section_name], [
            'title' => $request->title,
            'description' => $request->description,
            'show_items' => $request->show_items ?? 10,
            'is_show' => $request->is_show ?? true,
        ]);
        $data->update($request->all());

        $section_name = $section_name === 'auction' ? 'lelang' : $section_name;
        return redirect()->back()->with('success', 'Data ' . $section_name . ' berhasil diupdate!');
    }
}
