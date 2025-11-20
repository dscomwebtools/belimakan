<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\DeliveryState;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderDetail;
use App\Models\OrderReceipt;
use App\Models\ProductVariant;
use App\Models\SettingAddress;
use App\Models\ShippingMethod;
use App\Notifications\OrderInfoNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Milon\Barcode\DNS1D;

class OrderController extends Controller
{
    protected string $base;
    protected string $key;

    public function __construct()
    {
        $this->base = config('services.biteship_api.base');
        $this->key  = config('services.biteship_api.key');
    }

    function __invoke(Request $request)
    {
        $user_id = auth()->user()->id;

        // BUAT INVOICE
        // Mendapatkan tahun, bulan, dan tanggal saat ini
        $tahun = now()->format("Y");
        $bulan = now()->format("m");
        $tanggal = now()->format("d");

        // MPL (Misalnya, Anda bisa menentukan MPL sesuai dengan kebutuhan)
        $mpl = "DSC";

        // Menghasilkan angka urutan 6 digit (misalnya dari database)
        // Menghitung jumlah total invoice yang sudah ada
        $totalInvoices = Order::count();

        // Menghasilkan nomor invoice baru
        $numbInvoice = $totalInvoices + 1;

        // Menggabungkan semua komponen menjadi string
        $invoice = "PO/{$tahun}{$bulan}{$tanggal}/{$mpl}/" . str_pad($numbInvoice, 6, "0", STR_PAD_LEFT);
        // PO/20240901/CSS/000030

        // tax DSC GA PAKE PAJAK jadi tax dikosongin aja karena default nya null 
        // is_paid kosongin aja karena otomatis false di sql
        // bukti_pembayaran juga diupload nanti setelah order jadi default nya null
        // delivery_state_id default 1 (MENUNGGU KONFIRMASI)

        // SIMAPAN ADDRESS ORDER (DUPLICATE DARI ADDRESS) LALU AMBIL ID NYA
        $address = Address::find($request->orderAddressId);
        // Mengonversi model menjadi array dan menghapus atribut 'id'
        $addressData = collect($address->toArray())->except('id')->toArray();
        // MENGGUNAKAN FIRST OR CREATE BIAR GA TERLALU BANYAK DUPLIKAT, MISAL ADA DATA YANG SAMA MAKA GA BUAT DATA BARU LAGI
        $order_address_id = OrderAddress::firstOrCreate($addressData)->id;
        $shippingMethodData = [];

        if (gettype($request->shippingMethod) == 'string') {
            // kalo string berarti shipping method nya manual set shipping_method_id = null then isi shipping_method_manual
            $shipping_method_id = null;
            $shipping_method_manual = $request->shippingMethod;
        } else {
            // SHIPPINGMETHOD
            $shipping_method_manual = null;

            $shippingMethodData = collect($request->shippingMethod)->except(['currency', 'tax_lines', 'shipping_fee_discount', 'shipping_fee_surcharge', 'insurance_fee', 'cash_on_delivery_fee'])->toArray();
            $shipping_method_id = ShippingMethod::firstOrCreate($shippingMethodData)->id;

            // ADA PERUBAHAN COLUMN2 DARI BITESHIP NYA, NANTI KALO ADA ERROR KARENA KOLOM SHIPPING METHOD NYA GA COCOK PAKE KODE DIBAWAH INI AJA BUAT GANTI KODE "// SHIPPINGMETHOD" DI ATAS
            // SEMENTARA PAKE KODE "SHIPPINGMETHOD" DI ATAS DULU BUAT TAU APA AJA METHOD YG DITAMBAH BITESHIP NYA
            // $allowedColumns = Schema::getColumnListing('shipping_methods');

            // $shippingMethodData = collect($request->shippingMethod)
            //     ->only($allowedColumns) // hanya ambil key yang memang ada di tabel
            //     ->toArray();
            // $shipping_method_id = ShippingMethod::firstOrCreate($shippingMethodData)->id;
        }

        $new_order = new Order();
        $new_order->user_id = $user_id;
        $new_order->invoice = $invoice;
        $new_order->payment_method = $request->paymentMethod;
        $new_order->total = $request->total;
        $new_order->order_address_id = $order_address_id;
        $new_order->shipping_method_id = $shipping_method_id;
        $new_order->shipping_method_manual = $shipping_method_manual;
        // $new_order->bank_account_id = $request->bankAccountId;
        $new_order->coupon_id = $request->voucherId ?? null;
        $new_order->coupon_code = $request->voucherCode ?? null;
        $new_order->coupon_discount = $request->voucherDiscount ?? 0;
        $new_order->delivery_note = $request->deliveryNote;
        $shippingSource = $request->shippingSource;

        if ($new_order->coupon_id) {
            Coupon::where('id', $new_order->coupon_id)->increment('used_count');
        }


        if ($request->paymentMethod == 'Cash') {
            $new_order->delivery_state_id = 2; //Jika Cash set status langsung 'Menunggu Konfirmasi', jika tidak maka defaultnya 'menunggu pembayaran'
        }

        DB::transaction(function () use ($new_order, $request, $addressData, $shippingMethodData, $shipping_method_manual, $shippingSource) {
            // 1. Save order first so it has an ID
            $new_order->save();

            // 2. Process carts
            $carts = Cart::where('user_id', auth()->id())
                ->whereIn('id', $request->selectedCartIds ?? [])
                ->get();

            foreach ($carts as $cart) {
                $order_detail = new OrderDetail();
                $order_detail->order_id = $new_order->id;
                $order_detail->product_id = $cart->product_id;
                $order_detail->product_variant_id = $cart->product_variant_id;
                $order_detail->quantity = $cart->quantity;
                $order_detail->price = $cart->product_variant
                    ? $cart->product_variant->price
                    : $cart->product->product_variant->first()->price;
                $order_detail->discount = $cart->product->promo ? $cart->product->promo->discount : 0;
                $order_detail->save();

                $product_variant = ProductVariant::find(
                    $cart->product_variant
                        ? $cart->product_variant->id
                        : $cart->product->product_variant->first()->id
                );
                $product_variant->stock -= $cart->quantity;
                $product_variant->save();

                $cart->delete();
            }

            if (!$shipping_method_manual) {
                // 3. Build items
                $items = $this->buildItemsFromCarts($carts);

                // 4. Call Biteship
                $response = $this->sendToBiteship($new_order, $addressData, $shippingMethodData, $items, $shippingSource);
                if (!($response['status'] ?? false)) {
                    throw new \Exception("Gagal membuat draft order di Biteship");
                }

                $biteship = $response['response'];

                $response = $this->updateBiteshipCourier($shippingMethodData, $biteship['id']);
                if (!($response['status'] ?? false)) {
                    throw new \Exception("Gagal update draft order di Biteship");
                }

                // 5. Update order with biteship fields
                $new_order->biteship_draft_order_id = $biteship['id'] ?? null;
                $new_order->biteship_reference_id   = $biteship['reference_id'] ?? null;
                $new_order->biteship_order_id       = $biteship['order_id'] ?? null;
                $new_order->biteship_waybill        = $biteship['courier']['waybill_id'] ?? null;
            }

            $new_order->save();
        });

        return response()->json(["status" => 1, "message" => "Order berhasil dibuat", $new_order]);
    }

    public function sendToBiteship($new_order, $address, $shipping, $items, $shippingSource)
    {
        $origin = SettingAddress::first();

        $payload = [
            "shipper_contact_name"  => $origin->name,
            "shipper_contact_phone" => $origin->hp,
            "shipper_contact_email" => $origin->email,
            "origin_contact_name"   => $origin->name,
            "origin_contact_phone"  => $origin->hp,
            "origin_address"        => $origin->address,
            "origin_postal_code"    => $origin->postal_code,
            "destination_contact_name"  => $address['recipient'],
            "destination_contact_phone" => $address['hp'],
            "destination_address"       => $address['address'],
            "destination_postal_code"   => strval($address['postal_code']),
            "destination_note"          => $address['noteForCurrier'] ?? "",
            "courier_company"           => $shipping['company'],
            "courier_type"              => $shipping['type'],
            "courier_insurance"         => 0,
            "reference_id"              => $new_order->invoice,
            "order_note"                => $new_order->delivery_note ?? '',
            "delivery_type"             => "now",
            "items"                     => $items,
        ];

        if ($shippingSource == 'regular') {
            $payload["origin_area_id"] = $origin->area_id;
        }
        if ($shippingSource == 'instant') {
            $payload["origin_coordinate"] = [
                "latitude"  => $origin->latitude,
                "longitude" => $origin->longitude,
            ];
        }

        if ($shippingSource == 'regular') {
            $payload["destination_area_id"] = $address['area_id'];
        }
        if ($shippingSource == 'instant') {
            $payload["destination_coordinate"] = [
                "latitude"  => $address['latitude'],
                "longitude" => $address['longitude'],
            ];
        }

        $response = Http::withHeaders([
            "authorization" => "{$this->key}",
            "content-type" => "application/json",
        ])
            ->post($this->base . "/v1/draft_orders", $payload);

        return [
            "status"   => $response->successful(),
            "response" => $response->json(),
        ];
    }

    public function updateBiteshipCourier($shipping, $id)
    {
        $payload = [
            "courier_company"           => $shipping['company'],
            "courier_type"              => $shipping['type'],
        ];

        $response = Http::withHeaders([
            "authorization" => "{$this->key}",
            "content-type" => "application/json",
        ])
            ->post($this->base . "/v1/draft_orders/" . $id, $payload);

        return [
            "status"   => $response->successful(),
            "response" => $response->json(),
        ];
    }

    private function buildItemsFromCarts($carts)
    {
        $items = [];

        foreach ($carts as $cart) {
            $product = $cart->product;
            $variant = $cart->product_variant ?? $product->product_variant->first();

            $items[] = [
                "name"        => $product->name,
                "description" => "description",
                "category"    => "others",
                "value"       => $variant->price,
                "quantity"    => $cart->quantity,
                "height"      => (int) ($product->dimention->height ?? 0),
                "length"      => (int) ($product->dimention->length ?? 0),
                "weight"      => (int) ($variant->weight ?? 0),
                "width"       => (int) ($product->dimention->width ?? 0),
            ];
        }

        return $items;
    }

    function order_tracking(Order $order)
    {
        $order->load(['shipping_method', 'delivery_state']);

        if ($order->delivery_state_id <= 2) {
            return response()->json([
                "status"   => false,
                "message"  => "Data tracking tidak lengkap",
            ], 400);
        }

        $waybill   = $order->biteship_waybill ?? null;
        $courierCode = $order->shipping_method?->courier_code ?? null;

        if (!$waybill || !$courierCode) {
            return response()->json([
                "status"   => false,
                "message"  => "Data tracking tidak lengkap",
            ], 400);
        }

        $response = Http::withHeaders([
            "authorization" => $this->key,
            "content-type"  => "application/json",
        ])->get("{$this->base}/v1/trackings/{$waybill}/couriers/{$courierCode}");

        return [
            "status"   => $response->successful(),
            "response" => $response->json(),
        ];
    }

    function order_by_id(Order $order)
    {
        $order->load(['delivery_state', 'order_address', 'shipping_method', 'order_detail.product.product_pictures', 'order_detail.product_variant.product_detail.variant_value.variant', 'bank_account', 'receipt']);

        return response()->json($order);
    }

    function upload_bukti_bayar(Request $request, Order $order)
    {
        $isOngkir = $request->has('bukti_bayar_ongkir');

        $validated = $request->validate([
            $isOngkir ? 'bukti_bayar_ongkir' : 'bukti_bayar' => ['required']
        ]);

        if ($isOngkir) {
            if ($order->bukti_pembayaran_ongkir) {
                Storage::delete($order->bukti_pembayaran_ongkir);
            }

            $filename = 'bukti_ongkir-' . uniqid() . '.webp';
            $path = 'bukti-bayar-ongkir/' . $filename;

            Storage::put($path, file_get_contents($validated['bukti_bayar_ongkir']));

            $order->bukti_pembayaran_ongkir = $path;
            $order->save();

            return redirect()->route('client.profile')
                ->with('success', 'Berhasil upload bukti bayar ongkir!');
        }

        if ($order->bukti_pembayaran) {
            Storage::delete($order->bukti_pembayaran);
        }

        $filename = 'bukti_bayar-' . uniqid() . '.webp';
        $path = 'bukti-bayar/' . $filename;

        Storage::put($path, file_get_contents($validated['bukti_bayar']));

        $order->bukti_pembayaran = $path;
        $order->delivery_state_id = 3;
        $order->save();

        return redirect()->route('client.profile')
            ->with('success', 'Berhasil upload bukti bayar!');
    }

    function download_invoice(Order $order)
    {
        $pdf = Pdf::loadView('pdf.invoice', compact('order'));

        return $pdf->stream('invoice-' . $order->id . '.pdf');
        // return $pdf->download('invoice-' . $order->id . '.pdf');
    }

    protected function generateResiPdf(Order $order)
    {
        $order->load([
            'delivery_state',
            'order_address',
            'shipping_method',
            'order_detail.product',
            'order_detail.product_variant.product_detail.variant_value.variant',
            'bank_account',
            'receipt'
        ]);

        $barcode = (new DNS1D())->getBarcodePNG($order->receipt->receipt_number ?? '24BIT00000000013', 'C128');
        $reference = (new DNS1D())->getBarcodePNG($order->invoice ?? '24BIT00000000013', 'C128');
        $origin = SettingAddress::first();

        // Ambil nama kurir (prioritas manual)
        if (!empty($order->shipping_method_manual)) {
            $courierRaw = $order->shipping_method_manual;
        } else {
            $courierRaw = $order->shipping_method->company ?? '';
        }

        function normalizeCourier($str)
        {
            $str = strtolower($str);
            $str = trim($str);
            $str = preg_replace('/[^a-z0-9\s]/', '', $str);
            $str = preg_replace('/\s+/', ' ', $str);
            return $str;
        }

        $courier = normalizeCourier($courierRaw);

        $shippingLogo = null;
        $dir = public_path('assets/images/shipping');

        $aliases = [
            'baraka' => 'baraka',
            'braka' => 'baraka',
            'bara ka' => 'baraka',
            'mex' => 'mex',
            'kai' => 'kai',
            'kai kalog' => 'kai',
            'kalog' => 'kai',
        ];

        $courierKey = $aliases[$courier] ?? $courier;

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $path = "$dir/$courierKey.$ext";
            if (file_exists($path)) {
                $shippingLogo = $path;
                break;
            }
        }

        if (!$shippingLogo) {
            $shippingLogo = public_path('images/shipping/default.png');
        }

        $totalWeight = $order->order_detail->sum(function ($detail) {
            return ($detail->product_variant->weight ?? 0) * $detail->quantity;
        });
        $totalWeightKg = round($totalWeight / 1000, 2);

        return Pdf::loadView('pdf.resi', compact('order', 'barcode', 'reference', 'shippingLogo', 'origin', 'totalWeightKg'));
    }

    public function download_resi(Order $order)
    {
        $pdf = $this->generateResiPdf($order);
        return $pdf->stream('resi-' . $order->id . '.pdf');
    }

    // FOR ADMIN ROUTE
    public function index(Request $request)
    {
        $validated = $request->validate([
            'invoice'            => ['string', 'nullable'],
            'member'             => ['string', 'nullable'],
            'search_by'          => ['string', 'nullable', 'in:invoice,member'],
            'delivery_state_id'  => ['nullable', 'integer', 'exists:delivery_states,id'],
            'date_from'          => ['nullable', 'date'],
            'date_to'            => ['nullable', 'date'],
        ]);

        $data = Order::with(['order_address', 'delivery_state', 'shipping_method', 'user', 'bank_account', 'order_detail.product.product_pictures'])->latest();

        // 🔎 filter invoice
        if (($validated['search_by'] ?? null) === 'invoice' && !empty($validated['invoice'])) {
            $data = $data->where('invoice', 'like', '%' . $validated['invoice'] . '%');
        }

        // 🔎 filter member
        if (($validated['search_by'] ?? null) === 'member' && !empty($validated['member'])) {
            $data = $data->whereHas('user', function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['member'] . '%');
            });
        }

        // 🔎 filter status
        if (!empty($validated['delivery_state_id'])) {
            $data = $data->where('delivery_state_id', $validated['delivery_state_id']);
        }

        // 🔎 filter tanggal
        if (!empty($validated['date_from']) && !empty($validated['date_to'])) {
            $data = $data->whereBetween('created_at', [
                $validated['date_from'] . ' 00:00:00',
                $validated['date_to'] . ' 23:59:59'
            ]);
        } elseif (!empty($validated['date_from'])) {
            $data = $data->whereDate('created_at', '>=', $validated['date_from']);
        } elseif (!empty($validated['date_to'])) {
            $data = $data->whereDate('created_at', '<=', $validated['date_to']);
        }

        // jumlah per halaman
        $numb_per_page = $request->input('numb_per_page', 10);

        $data = $data->paginate($numb_per_page)->appends(array_merge($validated, [
            'numb_per_page' => $numb_per_page,
        ]));

        $indexNumber = (request()->input('page', 1) - 1) * $numb_per_page;

        $delivery_states = \App\Models\DeliveryState::pluck('name', 'id');

        return view('admin.order.index', compact(
            'data',
            'indexNumber',
            'validated',
            'numb_per_page',
            'delivery_states'
        ));
    }

    function show(Order $order)
    {
        $data = $order->load(['delivery_state', 'order_address', 'shipping_method', 'order_detail.product', 'order_detail.product_variant.product_detail.variant_value.variant', 'receipt']);

        // dd($data);
        return view('admin.order.show', compact('data'));
    }

    public function next_state(Order $order)
    {
        $order->delivery_state_id += 1;

        if ($order->delivery_state_id == 4) {
            $order->is_paid = true;
        }
        $order->save();

        if ($order->delivery_state_id == 2) {
            $paymentUrl = route('client.profile', [
                'open' => 'order',
                'order_id' => $order->id,
            ]);

            $notification = Notification::create([
                'user_id' => $order->user_id,
                'is_broadcast' => false,
                'title' => 'Pesanan Telah Dikonfirmasi',
                'message' => '<p>Halo <strong>' . e($order->user->name) . '</strong>,</p><p>Pesanan Anda telah kami <strong>konfirmasi</strong> dan menunggu proses pembayaran.</p><p>Silakan lakukan pembayaran sesuai pesanan Anda melalui tautan berikut:</p><p>👉 <a href="' . $paymentUrl . '" target="_blank" style="color: rgb(0, 102, 204); font-weight: bold;">Buka Halaman Pembayaran</a></p><p><br></p><p>Mohon segera menyelesaikan pembayaran agar pesanan dapat diproses lebih lanjut.</p>',
                'type' => 'Sistem',
                'link' => null,
                'scheduled_at' => now(),
            ]);

            $users = [$order->user_id];
            $notification->users()->attach($users);

            $order->user->notify(new OrderInfoNotification($order));
        }

        return redirect()->back()->with('success', 'Berhasil update status menjadi ' . $order->delivery_state->name);
    }

    function confirm_order(Order $order)
    {
        if ($order->delivery_state_id == 1) {
            $order->delivery_state_id = $order->payment_method === "Cash" ? 4 : 2;
        } else {
            $order->delivery_state_id = 4;
            $order->is_paid = true;
        }

        $order->save();

        if (!$order->shipping_method_manual && $order->delivery_state_id == 4) {
            $response = Http::withHeaders([
                "authorization" => "{$this->key}",
                "content-type" => "application/json",
            ])->post($this->base . "/v1/draft_orders/" . $order->biteship_draft_order_id . "/confirm");

            $data = $response->json();

            if (isset($data['success']) && $data['success'] === true) {
                $order->biteship_order_id = $data['id'] ?? null;
                $order->biteship_waybill  = $data['courier']['waybill_id'] ?? null;
            }
        }

        $order->save();

        if ($order->biteship_waybill) {
            $receiptAttrs = [
                'receipt_number' => $order->biteship_waybill,
            ];

            $receipt = OrderReceipt::updateOrCreate(
                ['id' => $order->order_receipt_id],
                $receiptAttrs
            );

            if (!$order->order_receipt_id) {
                $order->order_receipt_id = $receipt->id;
                $order->save();
            }

            $pdf = $this->generateResiPdf($order->fresh('receipt'));
            $pdfPath = 'receipts/resi-' . $order->id . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());

            $receipt->update(['receipt_img' => $pdfPath]);
        }

        return redirect()->back()->with('success', 'Berhasil update status menjadi ' . $order->delivery_state->name);
    }

    function cancel(Order $order)
    {
        if ($order->delivery_state_id == DeliveryState::where('name', 'Dibatalkan')->first()->id) {
            return redirect()->back()->with('error', 'Order sudah dibatalkan sebelumnya');
        }


        if ($order->delivery_state_id == DeliveryState::where('name', 'Selesai')->first()->id) {
            return redirect()->back()->with('error', 'Order sudah selesai tidak bisa dibatalkan');
        }

        $order->delivery_state_id = DeliveryState::where('name', 'Dibatalkan')->first()->id;
        $order->save();

        // KEMBALIKAN STOK
        foreach ($order->order_detail as $detail) {
            $product_variant = ProductVariant::find($detail->product_variant ? $detail->product_variant->id : $detail->product->product_variant->first()->id);
            $product_variant->stock += $detail->quantity;
            $product_variant->save();
        }

        return redirect()->back()->with('success', 'Berhasil membatalkan order dan mengembalikan stok produk');
    }

    function save_receipt(Request $request, Order $order)
    {
        $rules = [
            'receipt_img' => 'nullable|file|max:2048',
            'shipping_method_manual_fee' => 'nullable|numeric', // <= simpan ke order
        ];

        // Kalau manual, wajib isi nomor resi
        if (!$order->biteship_waybill) {
            $rules['receipt_number'] = 'required|string|unique:order_receipts,receipt_number,' . $order->order_receipt_id;
        }

        $validated = $request->validate($rules);

        $oldPath = $order->receipt?->receipt_img;

        $attributes = [
            // kalau biteship, pakai langsung dari order
            'receipt_number' => $order->biteship_waybill ?: $validated['receipt_number'],
        ];

        // upload gambar resi
        if ($request->hasFile('receipt_img')) {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $attributes['receipt_img'] = $request->file('receipt_img')->store('receipts', 'public');
        }

        // simpan atau update receipt
        $receipt = OrderReceipt::updateOrCreate(
            ['id' => $order->order_receipt_id],
            $attributes
        );

        // pastikan order terhubung ke receipt
        if (!$order->order_receipt_id) {
            $order->order_receipt_id = $receipt->id;
        }

        // **simpan shipping_method_manual_fee ke table orders**
        $order->shipping_method_manual_fee = $validated['shipping_method_manual_fee'] ?? null;

        // simpan perubahan order
        $order->save();

        return back()->with('success', 'Resi berhasil disimpan!');
    }

    function order_cancel_reason()
    {
        $response = Http::withHeaders([
            "authorization" => $this->key,
            "content-type"  => "application/json",
        ])->get("{$this->base}/v1/orders/cancellation_reasons?lang=id");

        return [
            "status"   => $response->successful(),
            "response" => $response->json(),
        ];
    }

    function order_cancel_request(Order $order, Request $request)
    {
        $request->validate([
            'cancel_reason' => 'required|string',
            'description'   => 'nullable|string|max:255',
        ]);

        $order->cancel_code = $request->cancel_reason;
        $order->cancel_reason = $request->description;
        $order->cancel_status = 'pending';
        $order->save();

        return back()->with('success', 'Pembatalan berhasil diajukan!');
    }

    function order_cancel(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $order->cancel_status = $request->status;

        // Hanya jika pembatalan disetujui
        if ($request->status === 'approved') {
            // Default payload pembatalan
            $payload = [
                'cancellation_reason_code' => $order->cancel_code ?? 'others',
            ];

            if ($order->cancel_code === 'others' && !empty($order->cancel_reason)) {
                $payload['cancellation_reason'] = $order->cancel_reason;
            }

            try {
                // --- CASE 1: Pesanan belum dikirim ke Biteship (is_biteship_status == 0)
                if ($order->delivery_state && $order->delivery_state->is_biteship_status == 0) {
                    if ($order->biteship_draft_order_id) {
                        // draft order dihapus dari biteship
                        $response = Http::withHeaders([
                            'authorization' => $this->key,
                            'content-type'  => 'application/json',
                        ])->delete("{$this->base}/v1/draft_orders/{$order->biteship_draft_order_id}");

                        if (!$response->successful()) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Gagal menghapus draft order di Biteship.',
                                'api_response' => $response->json(),
                            ], 500);
                        }
                    }
                }
                // --- CASE 2: Pesanan sudah dikirim ke Biteship (is_biteship_status != 0)
                else {
                    $response = Http::withHeaders([
                        'authorization' => $this->key,
                        'content-type'  => 'application/json',
                    ])->post("{$this->base}/v1/orders/{$order->biteship_order_id}/cancel", $payload);

                    if (!$response->successful()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Gagal membatalkan pesanan di Biteship.',
                            'api_response' => $response->json(),
                        ], 500);
                    }
                }

                // Update status lokal
                $order->delivery_state_id = 13; // "Dibatalkan"
                $order->save();
            } catch (\Throwable $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat membatalkan pesanan.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $order->save();

        return back()->with('success', 'Pembatalan berhasil!');
    }
}
