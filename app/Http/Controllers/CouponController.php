<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected CartController $cartController;

    public function __construct(CartController $cartController)
    {
        $this->cartController = $cartController;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'code'       => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
            'search_by'  => ['string', 'nullable', 'in:code,description'],
            'active'     => ['nullable', 'boolean'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date'],
        ]);

        $data = Coupon::query()->latest();

        // 🔎 filter by code
        if (($validated['search_by'] ?? null) === 'code' && !empty($validated['code'])) {
            $data = $data->where('code', 'like', '%' . $validated['code'] . '%');
        }

        // 🔎 filter by description
        if (($validated['search_by'] ?? null) === 'description' && !empty($validated['description'])) {
            $data = $data->where('description', 'like', '%' . $validated['description'] . '%');
        }

        // 🔎 filter by active state
        if (isset($validated['active'])) {
            $data = $data->where('active', $validated['active']);
        }

        // 🔎 filter tanggal (valid_from / valid_until range OR created_at)
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

        return view('admin.coupon.index', compact(
            'data',
            'indexNumber',
            'validated',
            'numb_per_page'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupon.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request)
    {
        $validated = $request->validated();

        Coupon::create($validated);

        return redirect()->route('coupon.index')
            ->with('success', 'Coupon berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function check_coupon(Request $request)
    {
        $code = $request->input('code');
        $selectedIds = $request->input('selectedIds', []);
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher tidak ditemukan.'
            ], 404);
        }

        // Validasi: aktif atau tidak
        if (!$coupon->active) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher sudah tidak aktif.'
            ], 400);
        }

        $today = now()->startOfDay();
        $validFrom = \Carbon\Carbon::parse($coupon->valid_from)->startOfDay();

        if ($coupon->valid_from && $today->lt($validFrom)) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher belum berlaku.'
            ], 400);
        }

        if ($coupon->valid_until && $today > $coupon->valid_until) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher sudah kedaluwarsa.'
            ], 400);
        }

        // Validasi max penggunaan global
        if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher sudah mencapai batas penggunaan.'
            ], 400);
        }

        $cartJson = $this->cartController->get_data();

        $cartItems = $cartJson->getData(true);

        $selectedItems = collect($cartItems)->filter(function ($item) use ($selectedIds) {
            return in_array($item['id'], $selectedIds);
        });

        $subtotal = $selectedItems->reduce(function ($sum, $item) {
            $price = isset($item['product_variant'])
                ? $item['product_variant']['price']
                : $item['products'][0]['product_variant'][0]['price'];

            if (!empty($item['products'][0]['promo'])) {
                $price -= $price * ($item['products'][0]['promo']['discount'] / 100);
            }

            return $sum + ($item['quantity'] * $price);
        }, 0);

        if ($coupon->min_amount && $subtotal < $coupon->min_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Subtotal harus minimal Rp{$coupon->min_amount} untuk menggunakan voucher ini."
            ], 400);
        }

        if ($coupon->max_uses) {
            $totalUses = Order::where('coupon_id', $coupon->id)->where('user_id', auth()->id())
                ->count();

            if ($totalUses >= $coupon->max_uses) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Kode voucher sudah mencapai batas penggunaan.'
                ], 400);
            }
        }

        return response()->json([
            'valid' => true,
            'id'    => $coupon->id,
            'code'  => $coupon->code,
            'description' => $coupon->description,
            'discount_amount' => $coupon->discount_amount,
            'discount_percent' => $coupon->discount_percent,
            'message' => 'Kode voucher berhasil digunakan.'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $data = $coupon;

        return view('admin.coupon.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $validated = $request->validated();

        $coupon->update($validated);

        return redirect()->route('coupon.index')
            ->with('success', 'Coupon berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupon.index')
            ->with('success', 'Coupon berhasil dihapus!');
    }
}
