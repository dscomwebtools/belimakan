<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CategoryProduct;
use App\Models\Detail;
use App\Models\DetailValue;
use App\Models\Dimention;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductPicture;
use App\Models\ProductVariant;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\Variant;
use App\Models\VariantValue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public $picture_folder = 'product-pictures/';

    function index(Request $request): View
    {
        $queryParams = [
            'nama' => ['string', 'nullable'],
        ];

        $validated = $request->validate($queryParams);

        $data = Product::with([
            'product_pictures',
            'promo',
            'auction',
            'product_variant' => function ($query) {
                $query->with(['product_detail.variant_value.variant']);
            }
        ])->latest();

        if (isset($validated["nama"])) {
            $data = $data->where('name', 'like', '%' . $validated["nama"] . '%');
        }
        // NUMBER OF ROWS PER PAGE
        $numbRows = $request['numbRows'] ?? 100;

        $data = $data->paginate($numbRows)->appends(array_merge($validated, ['numbRows' => $numbRows]));
        $indexNumber = (request()->input('page', 1) - 1) * $numbRows;

        // PERHATIKAN URUTAN $headers dan $rows, INI AKAN MENJADI URUTAN PADA TABEL
        $headers = [
            'No',
            'Nama',
            'Stok',
            'Harga',
            'Aktif',
            'Promo',
            'Lelang',
            'Aksi'
        ];

        $rows = $data->map(function ($item) {
            $variantData = $item->product_variant->map(function ($pv) {
                $variantName = optional(optional($pv->product_detail->first())->variant_value)->value ?? 'Tanpa Varian';
                return [
                    'id' => $pv->id,
                    'name' => $variantName,
                    'price' => $pv->price ?? 0,
                    'active' => $pv->active ?? false,
                    'stock' => $pv->stock ?? 0,
                ];
            });

            $auction = $item->auction
                ? array_merge($item->auction->toArray(), ['product_name' => $item->name])
                : ['product_name' => $item->name, 'product_id' => $item->id];

            return [
                'name' => $item->name,
                'stock' => $variantData,
                'price' => $variantData,
                'active' => $variantData,
                'promo' => $item->promo->active ?? null,
                'auction' => $auction,
                'id' => $item->id,
            ];
        });

        $routeName = 'product';

        return view('admin.product.index', compact('data', 'indexNumber', 'validated', 'numbRows', 'headers', 'rows', 'routeName', 'queryParams'));
    }

    public function extractYoutubeId($url)
    {
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        preg_match(
            '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S*?v=|.*v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $url,
            $matches
        );

        return $matches[1] ?? null;
    }

    function create(): View
    {
        $variants = Variant::limit(2)->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();

        return view('admin.product.form', compact('variants', 'categories', 'brands'));
    }

    function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $new_product = new Product();
        $new_product->name = $validated["name"];
        $new_product->slug = Str::of($validated["name"])->slug('-')->value;
        $new_product->description = $validated["description"];
        $new_product->brand_id = $validated["brand_id"] ?? null;
        $new_product->youtube = $this->extractYoutubeId($validated["youtube"]) ?? null;

        DB::transaction(function () use ($new_product, $validated) {
            $new_product->save();

            if (isset($validated["variantData"])) {
                // JIKA ADA VARIANT
                $variants = json_decode($validated['variantData'], true);
                $variantCombinations = json_decode($validated['variantCombinationsData'], true);
                foreach ($variants as $variantName => $values) {
                    $variant = Variant::firstOrCreate(['variant' => $variantName]);

                    foreach ($values as $value) {
                        VariantValue::firstOrCreate([
                            'variant_id' => $variant->id,
                            'value' => $value,
                        ]);
                    }
                }

                // Simpan kombinasi varian ke tabel product_variants dan product_details
                // Simpan kombinasi varian ke tabel product_variants dan product_details
                $variantIdMap = []; // 🔹 buat mapping untuk koneksi id varian ke index array
                foreach ($variantCombinations as $i => $combination) {
                    $productVariant = new ProductVariant([
                        'product_id' => $new_product->id,
                        'stock' => $validated['stock_variant'][$i],
                        'price' => $validated['price_variant'][$i],
                        'weight' => $validated['weight_variant'][$i],
                        'sku' => $validated['sku_variant'][$i],
                        'active' => $validated['active_variant'][$i] ?? false,
                    ]);
                    $productVariant->save();

                    // simpan id mapping biar nanti bisa pakai saat upload gambar
                    $variantIdMap[$i] = $productVariant->id;

                    foreach ($combination as $value) {
                        [$variantName, $variantValue] = explode(': ', $value);
                        $variant = Variant::where('variant', $variantName)->first();
                        $variantValueObj = VariantValue::firstOrCreate([
                            'variant_id' => $variant->id,
                            'value' => $variantValue,
                        ]);

                        ProductDetail::create([
                            'product_variant_id' => $productVariant->id,
                            'variant_value_id' => $variantValueObj->id,
                            'isMain' => false,
                        ]);
                    }
                }

                // 🔹 Variant Pictures
                if (isset($validated['variant_pictures'])) {
                    $lastPosition = 0;
                    foreach ($validated['variant_pictures'] as $index => $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $variantId = $variantIdMap[$index] ?? null;
                            if (!$variantId) continue;

                            $filename = $new_product->slug . '-variant-' . $variantId . '-' . uniqid() . '.webp';
                            $path = $this->picture_folder . $filename;

                            Storage::put($path, file_get_contents($file));

                            $newPicture = new ProductPicture();
                            $newPicture->product_id = $new_product->id;
                            $newPicture->variant_id = $variantId;
                            $newPicture->path = $path;
                            $newPicture->position = ++$lastPosition;
                            $newPicture->save();
                        }
                    }
                }
            } else {
                // JIKA TIDAK ADA VARIANT MAKA SIMPAN SATU SAJA PADA PRODUCT VARIANT JIKA ADA VARIANT MAKA DI HANDLE DIBAWAH DIBAGIAN VARIANTS
                $newProductVariant = new ProductVariant();
                $newProductVariant->product_id = $new_product->id;
                $newProductVariant->stock = $validated["stock"];
                $newProductVariant->price = $validated["price"];
                $newProductVariant->weight = $validated["weight"];
                $newProductVariant->sku = $validated["sku"];
                $newProductVariant->active = $validated["active"] ?? false;
                $newProductVariant->save();
            }

            // CATEGORIES (many to many)
            if (isset($validated["categories"])) {
                $categoryIds = [];

                foreach ($validated["categories"] as $categoryId) {
                    $category = Category::find($categoryId);

                    if (!$category) {
                        // If the category does not exist, create it with the ID as the name
                        $category = Category::create(['name' => $categoryId]);
                    }

                    $categoryIds[] = $category->id;
                }

                $new_product->category()->attach($categoryIds);
            }

            // PICTURES
            if (isset($validated["product_pictures"])) {
                foreach ($validated["product_pictures"] as $product_picture) {
                    $filename = $new_product->slug . '-' . uniqid() . '.webp';
                    $path = $this->picture_folder . $filename;
                    Storage::put($path, file_get_contents($product_picture));

                    $newPicture = new ProductPicture();
                    $newPicture->product_id = $new_product->id;
                    $newPicture->path = $path;
                    $newPicture->save();
                }
            }

            // DISCOUNT (PROMO)
            $discount = $validated["discount"];
            if ($discount && $discount > 0) {
                $promo = new Promo();
                $promo->product_id = $new_product->id;
                $promo->discount = $discount;
                $promo->save();
            }

            // DIMENTION
            $dimention = new Dimention();
            $dimention->product_id = $new_product->id;
            $dimention->length = $validated["length"];
            $dimention->width = $validated["width"];
            $dimention->height = $validated["height"];
            $dimention->save();

            // DETAILS
            if (isset($validated["detail"])) {
                foreach ($validated["detail"] as $index => $detail_input) {

                    if (isset($detail_input)) { //hanya simpan detail yang tidak null
                        $detail = Detail::firstOrCreate(['detail' => $detail_input]);

                        $detail_value = new DetailValue();
                        $detail_value->detail_id = $detail->id;
                        $detail_value->product_id = $new_product->id;
                        $detail_value->value = $validated["detail-value"][$index];

                        $detail_value->save();
                    }
                }
            }
        });
        // $variants = json_decode($request->input('variantData'), true);
        // $variantCombinations = json_decode($request->input('variantCombinationsData'), true);

        // // Simpan varian dan nilai varian
        // foreach ($variants as $variantName => $values) {
        //     $variant = Variant::firstOrCreate(['variant' => $variantName]);

        //     foreach ($values as $value) {
        //         $variantValue = VariantValue::firstOrCreate([
        //             'variant_id' => $variant->id,
        //             'value' => $value,
        //         ]);
        //     }
        // }

        // // Simpan kombinasi varian ke tabel product_variants
        // foreach ($variantCombinations as $variantData) {
        //     $productVariant = new ProductVariant([
        //         'stock' => $variantData['stock'],
        //         'price' => $variantData['price'],
        //         'weight' => $variantData['weight'],
        //         'sku' => $variantData['sku'],
        //         'active' => $variantData['active'],
        //     ]);

        //     // $product->productVariants()->save($productVariant);
        // }

        return redirect()->route('product.index')
            ->with('success', 'Berhasil ditambahkan!');
    }

    function show(Product $product)
    {
        $data = $product;

        return view('admin.product.show', compact('data'));
    }

    public function edit(Product $product)
    {
        $data = $product->load([
            'detail_value.detail',
            'product_pictures' => function ($query) {
                $query->whereNull('variant_id')->orderBy('position', 'asc');
            },
            // tambahkan relasi ini supaya bisa ambil gambar tiap varian
            'product_variant.product_pictures',
            'product_variant.product_detail.variant_value.variant',
        ]);

        $variants = Variant::limit(2)->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();

        // ==============================
        // 🔹 Existing Variants (Warna, Ukuran, dst)
        // ==============================
        $existingVariants = [];
        foreach ($product->product_variant as $variant) {
            foreach ($variant->product_detail as $detail) {
                $variantName = $detail->variant_value->variant->variant;
                $variantValue = $detail->variant_value->value;

                if (!isset($existingVariants[$variantName])) {
                    $existingVariants[$variantName] = [];
                }

                if (!in_array($variantValue, $existingVariants[$variantName])) {
                    $existingVariants[$variantName][] = $variantValue;
                }
            }
        }

        // ==============================
        // 🔹 Variant Combinations + Pictures
        // ==============================
        $productVariants = $product->product_variant->map(function ($variant) {
            return [
                'id' => $variant->id,
                'stock' => $variant->stock,
                'price' => $variant->price,
                'weight' => $variant->weight,
                'sku' => $variant->sku,
                'active' => $variant->active,
                'picture' => $variant->product_pictures->first()
                    ? Storage::url($variant->product_pictures->first()->path)
                    : null,
                'details' => $variant->product_detail->map(function ($detail) {
                    return [
                        'variant' => $detail->variant_value->variant->variant,
                        'value' => $detail->variant_value->value,
                    ];
                }),
            ];
        });

        // dd($productVariants);

        // ==============================
        // 🔹 Build picture map keyed by combination string
        // (so frontend can show existing variant pictures)
        // ==============================
        $variantPictureMap = [];
        foreach ($productVariants as $v) {
            $comb = collect($v['details'])
                ->map(fn($d) => $d['variant'] . ': ' . $d['value'])
                ->implode('-');
            $variantPictureMap[$comb] = $v['picture'];
        }

        // 👉 Optional: Debugging
        // dd($variants, $existingVariants, $productVariants, $variantPictureMap);

        $productVariantsJson = $productVariants->toJson();

        return view('admin.product.form', compact(
            'data',
            'variants',
            'categories',
            'brands',
            'existingVariants',
            'productVariants',
            'variantPictureMap',
            'productVariantsJson',
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        // dd($validated);

        $product->name = $validated["name"];
        $product->slug = Str::of($validated["name"])->slug('-')->value;
        $product->description = $validated["description"];
        $product->brand_id = $validated["brand_id"] ?? null;
        $product->youtube = $this->extractYoutubeId($validated["youtube"]) ?? null;

        DB::transaction(function () use ($product, $validated) {
            $product->save();
            $variantIdMap = [];

            $oldVariantPictures = ProductPicture::where('product_id', $product->id)
                ->whereNotNull('variant_id')
                ->get()
                ->keyBy('variant_id');

            if (isset($validated["variantData"])) {
                $oldVariantMap = [];
                foreach ($product->product_variant as $variant) {
                    $valuesKey = $variant->product_detail
                        ->map(fn($detail) => $detail->variant_value->variant->variant . ':' . $detail->variant_value->value)
                        ->sort()
                        ->implode('|'); // e.g. "Warna:Merah|Ukuran:L"

                    $oldVariantMap[$valuesKey] = $variant->id;
                }

                $oldVariantPictures = ProductPicture::where('product_id', $product->id)
                    ->whereNotNull('variant_id')
                    ->get()
                    ->groupBy('variant_id');

                $variants_id = [];
                $variant_values_id = [];

                foreach ($product->product_variant as $variant) {
                    foreach ($variant->product_detail as $detail) {
                        $variant_values_id[] = $detail->variant_value->id;
                        $variants_id[] = $detail->variant_value->variant_id;
                    }
                }

                foreach ($product->product_variant as $variant) {
                    $variant->delete();
                }

                $variant_values = array_unique($variant_values_id);
                foreach ($variant_values as $value_id) {
                    if (!ProductDetail::where('variant_value_id', $value_id)->exists()) {
                        VariantValue::destroy($value_id);
                    }
                }

                foreach ($variants_id as $variant_id) {
                    if (!VariantValue::where('variant_id', $variant_id)->exists()) {
                        Variant::destroy($variant_id);
                    }
                }

                $variants = json_decode($validated['variantData'], true);
                $variantCombinations = json_decode($validated['variantCombinationsData'], true);

                foreach ($variants as $variantName => $values) {
                    $variant = Variant::firstOrCreate(['variant' => $variantName]);
                    foreach ($values as $value) {
                        VariantValue::firstOrCreate([
                            'variant_id' => $variant->id,
                            'value' => $value,
                        ]);
                    }
                }

                $variantIdMap = []; // mapping oldID -> newID
                foreach ($variantCombinations as $i => $combination) {
                    $productVariant = new ProductVariant([
                        'product_id' => $product->id,
                        'stock' => $validated['stock_variant'][$i],
                        'price' => $validated['price_variant'][$i],
                        'weight' => $validated['weight_variant'][$i],
                        'sku' => $validated['sku_variant'][$i],
                        'active' => $validated['active_variant'][$i] ?? false,
                    ]);
                    $productVariant->save();

                    $valuesForKey = [];
                    foreach ($combination as $value) {
                        [$variantName, $variantValue] = explode(': ', $value);
                        $variant = Variant::where('variant', $variantName)->first();

                        $variantValueObj = VariantValue::firstOrCreate([
                            'variant_id' => $variant->id,
                            'value' => $variantValue,
                        ]);

                        ProductDetail::create([
                            'product_variant_id' => $productVariant->id,
                            'variant_value_id' => $variantValueObj->id,
                            'isMain' => false,
                        ]);

                        $valuesForKey[] = $variantName . ':' . $variantValue;
                    }

                    $valuesKey = collect($valuesForKey)->sort()->implode('|');

                    if (isset($oldVariantMap[$valuesKey])) {
                        $oldId = $oldVariantMap[$valuesKey];
                        $newId = $productVariant->id;
                        $variantIdMap[$oldId] = $newId;

                        Cart::where('product_variant_id', $oldId)
                            ->update(['product_variant_id' => $newId]);
                    } else {
                        $variantIdMap[$i] = $productVariant->id;
                    }
                }

                $lastPosition = ProductPicture::where('product_id', $product->id)->max('position') ?? 0;

                foreach ($oldVariantPictures as $oldVariantId => $pictures) {
                    $newVariantId = $variantIdMap[$oldVariantId] ?? null;
                    if ($newVariantId) {
                        foreach ($pictures as $picture) {
                            $newPic = $picture->replicate();
                            $newPic->variant_id = $newVariantId;
                            $newPic->position = ++$lastPosition;
                            $newPic->save();
                        }
                    }
                }

                if (isset($validated['variant_pictures'])) {
                    foreach ($validated['variant_pictures'] as $variantId => $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $newVariantId = $variantIdMap[$variantId] ?? $variantId;

                            $filename = $product->slug . '-variant-' . $newVariantId . '-' . uniqid() . '.webp';
                            $path = $this->picture_folder . $filename;
                            Storage::put($path, file_get_contents($file));

                            $newPicture = new ProductPicture();
                            $newPicture->product_id = $product->id;
                            $newPicture->variant_id = $newVariantId;
                            $newPicture->path = $path;
                            $newPicture->position = ++$lastPosition;
                            $newPicture->save();
                        }
                    }
                }

                if (!empty($validated['deleted_variant_pictures'])) {
                    $deletedPics = $validated['deleted_variant_pictures'];
                    if (is_string($deletedPics)) {
                        $decoded = json_decode($deletedPics, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $deletedPics = $decoded;
                        } else {
                            $deletedPics = [];
                        }
                    }

                    foreach ($deletedPics as $deletedItem) {
                        $picture = is_numeric($deletedItem)
                            ? ProductPicture::find($deletedItem)
                            : ProductPicture::where('path', 'like', '%' . basename($deletedItem) . '%')->first();

                        if ($picture) {
                            Storage::delete($picture->path);
                            $picture->delete();
                        }
                    }
                }
            } else {
                // JIKA TIDAK ADA VARIANT

                $variants_id = [];
                $variant_values_id = [];
                foreach ($product->product_variant as $variant) {
                    foreach ($variant->product_detail as $detail) {
                        $variant_values_id[] = $detail->variant_value->id;
                        $variants_id[] = $detail->variant_value->variant_id;
                    }
                }
                foreach ($product->product_variant as $key => $variant) {
                    if ($key === 0) continue;
                    $variant->delete();
                }

                $variant_values = array_unique($variant_values_id);
                foreach ($variant_values as $value_id) {
                    $is_exists_in_product_detail = ProductDetail::where('variant_value_id', $value_id)->exists();
                    if (!$is_exists_in_product_detail) {
                        VariantValue::destroy($value_id);
                    }
                }

                foreach ($variants_id as $variant_id) {
                    $is_exists_in_variant_value = VariantValue::where('variant_id', $variant_id)->exists();
                    if (!$is_exists_in_variant_value) {
                        Variant::destroy($variant_id);
                    }
                }

                $newProductVariant = ProductVariant::firstWhere('product_id', $product->id);
                $newProductVariant->product_id = $product->id;
                $newProductVariant->stock = $validated["stock"];
                $newProductVariant->price = $validated["price"];
                $newProductVariant->weight = $validated["weight"];
                $newProductVariant->sku = $validated["sku"];
                $newProductVariant->active = $validated["active"] ?? false;
                $newProductVariant->save();
            }

            $lastMainPos = ProductPicture::where('product_id', $product->id)
                ->whereNull('variant_id')
                ->orderBy('position', 'desc')
                ->value('position') ?? 0;

            $pos = $lastMainPos + 1;

            $pictures = ProductPicture::where('product_id', $product->id)
                ->whereNotNull('variant_id')
                ->orderBy('variant_id', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($pictures as $pic) {
                if ($pic->position != $pos) {
                    $pic->position = $pos;
                    $pic->save();
                }
                $pos++;
            }

            // CATEGORIES
            if (isset($validated["categories"])) {
                $categoryIds = [];
                foreach ($validated["categories"] as $categoryId) {
                    $category = Category::find($categoryId);
                    if (!$category) {
                        $category = Category::create(['name' => $categoryId]);
                    }
                    $categoryIds[] = $category->id;
                }
                $product->category()->sync($categoryIds);
            }

            // PICTURES
            if (isset($validated["product_pictures"])) {
                foreach ($validated["product_pictures"] as $pos => $product_picture) {
                    $filename = $product->slug . '-' . uniqid() . '.webp';
                    $path = $this->picture_folder . $filename;
                    Storage::put($path, file_get_contents($product_picture));

                    $newPicture = new ProductPicture();
                    $newPicture->product_id = $product->id;
                    $newPicture->path = $path;
                    $newPicture->position = $pos + 1; // ✅ keep consistent ordering
                    $newPicture->save();
                }
            }

            if (isset($validated["deleted_pictures"])) {
                $deletedPictures = json_decode($validated['deleted_pictures'], true);
                if (!empty($deletedPictures)) {
                    ProductPicture::destroy($deletedPictures);
                }
            }

            // ✅ Update existing picture positions
            if (!empty($validated['picture_positions'])) {
                $positions = json_decode($validated['picture_positions'], true);

                foreach ($positions as $index => $id) {
                    ProductPicture::where('id', $id)->update(['position' => $index + 1]);
                }
            }

            // UPDATE DISCOUNT
            $discount = $validated["discount"];
            if ($discount) {
                $promo = Promo::firstOrNew(['product_id' => $product->id]);
                $promo->discount = $discount;
                $promo->active = $discount > 0;
                $promo->save();
            } else {
                $promo = Promo::firstWhere(['product_id' => $product->id]);
                if ($promo) {
                    $promo->discount = 0;
                    $promo->active = false;
                    $promo->save();
                }
            }

            // DIMENTION
            $dimention = Dimention::firstWhere('product_id', $product->id);
            $dimention->length = $validated["length"];
            $dimention->width = $validated["width"];
            $dimention->height = $validated["height"];
            $dimention->save();

            // DETAILS
            if (isset($validated["detail"])) {
                $currentDetailValue = DetailValue::where('product_id', $product->id)->get();
                $currentDetailIds = $currentDetailValue->pluck('detail_id')->toArray();

                DetailValue::destroy($currentDetailValue);
                foreach ($validated["detail"] as $index => $detail_input) {
                    if (isset($detail_input)) {
                        $detail = Detail::firstOrCreate(['detail' => $detail_input]);

                        $detail_value = new DetailValue();
                        $detail_value->detail_id = $detail->id;
                        $detail_value->product_id = $product->id;
                        $detail_value->value = $validated["detail-value"][$index];
                        $detail_value->save();
                    }
                }

                Detail::whereIn('id', $currentDetailIds)
                    ->whereNotIn('id', function ($q) {
                        $q->select('detail_id')->from((new DetailValue())->getTable());
                    })->delete();
            } else {
                $currentDetailValue = DetailValue::where('product_id', $product->id)->get();
                DetailValue::destroy($currentDetailValue);
            }
        });

        return redirect()->route('product.index')
            ->with('success', 'Berhasil diubah!');
    }

    function destroy(Product $product)
    {
        $productPictures = ProductPicture::where('product_id', $product->id)->get();

        if ($productPictures) {
            foreach ($productPictures as $picture) {
                Storage::delete($picture->path);
            }
        }

        $product->delete();

        return redirect()->route('product.index')
            ->with('success', 'Berhasil dihapus !!');
    }

    function allProducts(Request $request)
    {
        $keyword = $request->query('q');
        $perPage = 10;

        if ($keyword) {
            $products = Product::with([
                'product_pictures' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
                'promo',
                'auction',
                'product_variant' => function ($query) {
                    $query->where('active', 1)
                        ->with(['product_detail.variant_value.variant']);
                }
            ])
                ->whereHas('product_variant', function ($query) {
                    $query->where('active', 1);
                })
                ->where('name', 'like', '%' . $keyword . '%')
                ->latest()
                ->paginate($perPage);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('components.client.product-items', compact('products'))->render()
                ]);
            }

            return view('client.product.product-by-keyword', compact('products', 'keyword'));
        }

        $product_data = Setting::where('section_name', 'product')->first();
        $products = Product::with([
            'product_pictures' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'promo',
            'auction',
            'product_variant' => function ($query) {
                $query->where('active', 1)
                    ->with(['product_detail.variant_value.variant']);
            }
        ])
            ->whereHas('product_variant', function ($query) {
                $query->where('active', 1);
            })
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.client.product-items', compact('products'))->render()
            ]);
        }

        return view('client.product.index', compact('products', 'product_data'));
    }

    function product($slug): View
    {
        $product = Product::with([
            'product_pictures' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'product_variant' => function ($query) {
                $query->where('active', 1)
                    ->orderBy('id', 'asc')
                    ->with([
                        'product_detail.variant_value.variant'
                    ]);
            },
        ])->where('slug', $slug)->firstOrFail();

        return view('client.product.detail', compact('product'));
    }

    function productsByCategory(Request $request, $category)
    {
        $perPage = 10;
        $products = Product::with([
            'product_pictures' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'promo',
            'auction',
            'product_variant' => function ($query) {
                $query->where('active', 1)
                    ->with(['product_detail.variant_value.variant']);
            }
        ])
            ->whereHas('product_variant', function ($query) {
                $query->where('active', 1);
            })
            ->whereHas('category', function ($query) use ($category) {
                $query->where('name', $category);
            })
            // ->limit() // nanti dilimit setelah sudah bisa load more
            ->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.client.product-items', compact('products'))->render()
            ]);
        }

        return view('client.product.product-by-category', compact('products', 'category'));
    }

    function productsBySearch(Request $request): View
    {
        $keyword = $request->query('n');
        $products = Product::with([
            'product_pictures' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'promo',
            'auction'
        ])
            ->where('name', 'like', '%' . $keyword . '%')
            ->where('active', 1)
            // ->limit() // nanti dilimit setelah sudah bisa load more
            ->latest()->get();

        return view('client.product.product-by-keyword', compact('products'));
    }

    function import(Request $request)
    {
        Excel::import(new ProductImport, $request->file("product-excel"));

        return redirect()->back()->with('success', 'Berhasil import data product excel !');
    }

    public function updateProductSingle(Request $request, Product $product)
    {
        $validated = $request->validate([
            'field' => 'required|in:price,stock,active',
            'value' => 'required',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $field = $validated['field'];
        $value = $validated['value'];

        if (in_array($field, ['price', 'stock', 'active'])) {
            if (empty($validated['variant_id'])) {
                return response()->json(['success' => false, 'message' => 'Variant ID diperlukan untuk update varian'], 422);
            }

            $variant = $product->product_variant()->find($validated['variant_id']);

            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Varian tidak ditemukan'], 404);
            }

            $variant->update([$field => $value]);
        }

        return response()->json(['success' => true]);
    }
}
