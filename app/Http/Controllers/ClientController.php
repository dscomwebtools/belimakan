<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryLabel;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Notice;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\Sosmed;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClientController extends Controller
{
    private function getSetting(string $sectionName, string $defaultTitle = '', string $defaultDescription = '', int $defaultShowItems = 10): Setting
    {
        return Setting::where('section_name', $sectionName)->first() ?? new Setting([
            'section_name' => $sectionName,
            'is_show' => true,
            'title' => $defaultTitle,
            'description' => $defaultDescription,
            'show_items' => $defaultShowItems,
        ]);
    }

    function home()
    {
        $notice = Notice::latest()->first();
        $categories = Category::all();

        // SETTINGS
        $hero_data = $this->getSetting(
            'hero',
            'Sayur Segar Langsung dari Petani !',
            'Dapatkan aneka sayuran segar pilihan, sayur hidroponik, dan kebutuhan dapur organik terbaik yang dipetik langsung setiap pagi dari kebun petani lokal dengan jaminan kesegaran 100%.',
            10
        );
        $hero = [
            'data' => $hero_data,
            'product_pictures' => Product::with([
                'product_pictures' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
            ])->limit($hero_data->show_items)->latest()->get()
        ];

        $promo_data = $this->getSetting(
            'promo',
            'PROMO PANEN SEGAR HARI INI !!',
            'Belanja kebutuhan sayur dan bumbu dapur hemat setiap hari dengan diskon spesial panen raya. Pesan pagi, langsung diantar!',
            10
        );
        $promo = [
            'data' => $promo_data,
            'items' => Promo::where('active', 1)
                ->whereHas('product.product_variant', function ($q) {
                    $q->where('active', 1);
                })
                ->with([
                    'product' => function ($q) {
                        $q->with([
                            'product_variant' => function ($query) {
                                $query->where('active', 1)
                                    ->with(['product_detail.variant_value.variant']);
                            }
                        ]);
                    }
                ])
                ->limit($promo_data->show_items)
                ->latest()
                ->get()
        ];

        $auction_data = $this->getSetting(
            'auction',
            'LELANG PAKET SAYUR & HASIL PANEN !!',
            'Dapatkan paket sayur segar pilihan dan hasil panen terbaik langsung dari kebun mitra kami dengan harga penawaran terbaik sebelum lelang berakhir!',
            10
        );
        $auction = [
            'data' => $auction_data,
            'items' => Auction::where('active', 1)
                ->whereHas('product.product_variant', function ($q) {
                    $q->where('active', 1);
                })
                ->with([
                    'product' => function ($q) {
                        $q->with([
                            'product_variant' => function ($query) {
                                $query->where('active', 1)
                                    ->with(['product_detail.variant_value.variant']);
                            }
                        ]);
                    }
                ])
                ->limit($auction_data->show_items)
                ->latest()
                ->get()
        ];

        $product_data = $this->getSetting(
            'product',
            'SAYURAN TERBARU & TERLARIS !!',
            'Pilihan sayuran hijau, sayur buah, umbi-umbian, dan bumbu dapur paling segar untuk menu masakan sehat keluarga Anda.',
            10
        );
        $products = [
            'data' => $product_data,
            'items' => Product::with([
                'product_pictures' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
                'promo',
                'auction',
                'product_variant' => function ($query) {
                    $query->where('active', 1)
                        ->with(['product_detail.variant_value.variant', 'bulk_prices']);
                }
            ])
                ->whereHas('product_variant', function ($query) {
                    $query->where('active', 1);
                })
                ->limit($product_data->show_items)->latest()->get()
        ];
        $testimonial_data = $this->getSetting(
            'testimonial',
            'KATA PELANGGAN KAMI',
            'Pengalaman para ibu rumah tangga dan pecinta kuliner sehat yang berlangganan sayur segar di BeliMakan.',
            10
        );
        $testimonial = [
            'data' => $testimonial_data,
            'items' => Testimonial::where('show', true)
                ->limit($testimonial_data->show_items)->latest()->get()
        ];

        $faq_data = $this->getSetting(
            'faq',
            'PERTANYAAN SEPUTAR SAYUR SEGAR',
            'Informasi penting seputar pemesanan sayur, standar kesegaran produk, garansi, dan pengiriman kilat.',
            10
        );
        $faq = [
            'data' => $faq_data,
            'items' => Faq::limit($faq_data->show_items)->latest()->get()
        ];

        $brand_data = $this->getSetting(
            'brand',
            'MITRA KEBUN & PETANI LOKAL',
            'Bekerja sama erat dengan kelompok tani lokal dan perkebunan hidroponik terpercaya untuk menjaga kualitas sayur terbaik.',
            10
        );
        $brand = [
            'data' => $brand_data,
            'items' => Brand::limit($brand_data->show_items)->latest()->get()
        ];

        return view('client.index', compact(
            'notice',
            'categories',
            'hero',
            'promo',
            'auction',
            'products',
            'testimonial',
            'faq',
            'brand',
        ));
    }

    function promo(Request $request)
    {
        $perPage = 10;

        $promo_data = $this->getSetting(
            'promo',
            'PROMO PANEN SEGAR HARI INI !!',
            'Belanja kebutuhan sayur dan bumbu dapur hemat setiap hari dengan diskon spesial panen raya. Pesan pagi, langsung diantar!',
            10
        );
        $promo = Product::with([
            'product_variant' => function ($query) {
                $query->where('active', 1)
                    ->with(['product_detail.variant_value.variant']);
            }
        ])->whereHas('promo', function ($query) {
            $query->where('active', true);
        })->whereDoesntHave('auction', function ($query) {
            $query->where('active', true);
        })->whereHas('product_variant', function ($query) {
            $query->where('active', 1);
        })
            // ->limit( 0 // nanti buat disini load more nya dengan limit, sementara dikosongkan dulu agar ga limit)
            //LUPA KNP ADA KOMEN DIATAS NYURUH PAKE LIMIT
            ->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.client.product-items', ['products' => $promo])->render()
            ]);
        }

        return view('client.promo.index', compact('promo', 'promo_data'));
    }

    function lelang(Request $request)
    {
        $perPage = 10;
        $auction_data = $this->getSetting(
            'auction',
            'LELANG PAKET SAYUR & HASIL PANEN !!',
            'Dapatkan paket sayur segar pilihan dan hasil panen terbaik langsung dari kebun mitra kami dengan harga penawaran terbaik sebelum lelang berakhir!',
            10
        );
        $auction = Product::with([
            'product_variant' => function ($query) {
                $query->where('active', 1)
                    ->with(['product_detail.variant_value.variant']);
            }
        ])->whereHas('auction', function ($query) {
            $query->where('active', true);
        })->whereHas('product_variant', function ($query) {
            $query->where('active', 1);
        })
            // ->limit($auction_data->show_items) //sama seperti di promo
            ->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.client.product-items', ['products' => $auction])->render()
            ]);
        }
        return view('client.lelang.index', compact('auction', 'auction_data'));
    }
}
