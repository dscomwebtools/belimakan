<x-app-layout>
    <div class="sm:mx-6 lg:mx-8 p-6 py-10 bg-secondary text-secondary-content rounded overflow-x-auto">
        <div class="flex flex-col sm:flex-row items-center sm:items-start p-4 gap-8 sm:gap-2">
            {{-- LEFT SIDE --}}
            <div class="card w-80 bg-base-200 shadow-xl">
                <figure class="px-10 pt-10 pb-2 flex flex-col gap-4">
                    <h2 class="font-bold">DATA MEMBER</h2>
                    <div class="avatar">
                        <div class="w-full rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img src="{{ isset($data->user->img) ? Storage::url($data->user->img) : asset('assets/images/user_not_found.png') }}"
                                alt="profile picture" />
                        </div>
                    </div>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ $data->user->fullname }}</h2>
                    <p>{{ $data->user->email }}</p>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="flex-1 bg-base-200 shadow-xl card">
                <div x-data="{ activeTab: 1 }">
                    <div class="flex bg-base-300 rounded-t-2xl dark:text-primary-content">
                        <button class="uppercase px-8 py-3 -mb-px text-sm rounded-t-2xl font-bold"
                            :class="activeTab === 1 ? 'border-base-200 bg-base-200' : 'text-gray-600'"
                            @click="activeTab = 1">
                            DATA ORDER
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-6 bg-base-200 rounded text-sm">
                        <div x-show="activeTab === 1">
                            <div class="flex flex-col-reverse md:flex-row gap-4 justify-between">
                                <!-- Invoice Info -->
                                <h2 class="font-semibold text-base-content uppercase text-sm md:text-base">
                                    No Invoice :
                                    <span class="font-bold text-primary">{{ $data->invoice }}</span>
                                </h2>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <!-- Download Invoice -->
                                    <form action="{{ route('download-invoice', $data->id) }}" method="post"
                                        target="_blank" class="tooltip" data-tip="Download Pesanan">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm bg-blue-600 hover:bg-blue-700 text-white border-none flex items-center gap-1">
                                            <i class="fa fa-file-pdf-o"></i>
                                            <span class="hidden sm:inline">Invoice</span>
                                        </button>
                                    </form>

                                    <!-- Download Resi -->
                                    <form action="{{ route('download-resi', $data->id) }}" method="post"
                                        target="_blank" class="tooltip" data-tip="Download Resi">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white border-none flex items-center gap-1">
                                            <i class="fa fa-barcode"></i>
                                            <span class="hidden sm:inline">Resi</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="leading-10" x-data="{ open: false }">
                                <div class="flex gap-2 items-center">
                                    <span class="opacity-75 dark:text-base-content">Dikirim Ke :</span>
                                    <span
                                        class="dark:text-base-content font-semibold">{{ $data->order_address->name }}</span>

                                    <!-- Toggle Button -->
                                    <button type="button" class="btn btn-xs btn-outline ml-2 flex items-center gap-1"
                                        @click="open = !open">
                                        <span>Detail</span>
                                    </button>
                                </div>

                                <!-- Collapsible Detail -->
                                <div x-cloak x-show="open" x-transition
                                    class="mt-2 ml-6 p-3 rounded bg-base-200 text-sm space-y-1">
                                    <div><span class="font-semibold">Penerima:</span>
                                        {{ $data->order_address->recipient }}</div>
                                    <div><span class="font-semibold">HP:</span> {{ $data->order_address->hp }}</div>
                                    <div><span class="font-semibold">Alamat:</span> {{ $data->order_address->address }}
                                    </div>
                                    @if ($data->order_address->noteForCurrier)
                                        <div><span class="font-semibold">Catatan Kurir:</span>
                                            {{ $data->order_address->noteForCurrier }}</div>
                                    @endif
                                    <div><span class="font-semibold">Area:</span> {{ $data->order_address->area_name }}
                                    </div>
                                    <div><span class="font-semibold">Kode Pos:</span>
                                        {{ $data->order_address->postal_code }}</div>
                                </div>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Kurir
                                    :</span><span class="dark:text-base-content">
                                    @isset($data->shipping_method)
                                        {{ $data->shipping_method->courier_name . ' - ' . $data->shipping_method->courier_service_name }}
                                    @else
                                        {{ 'Manual - ' . $data->shipping_method_manual }}
                                    @endisset
                                </span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Jenis Pembayaran
                                    :</span><span class="dark:text-base-content">{{ $data->payment_method }}</span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Total Bayar :</span><span
                                    class="dark:text-base-content"><x-client.format-rp value="{{ $data->total }}" />
                                </span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Sudah Dibayar
                                    :</span><span
                                    class="dark:text-base-content">{{ $data->is_paid ? 'Lunas' : 'Belum Lunas' }}</span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Status
                                    :</span><span
                                    class="dark:text-base-content">{{ $data->delivery_state->name }}</span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Tanggal Order
                                    :</span><span
                                    class="dark:text-base-content">{{ $data->created_at->isoFormat('LL') }}</span>
                            </div>

                            <dialog id="receiptModal" name="modal-upload-receipt" class="modal">
                                <div class="modal-box w-11/12 max-w-2xl">
                                    <h1 class="text-xl font-bold text-center mb-4">
                                        @if ($data->delivery_state_id >= 5)
                                            Lihat Resi Pengiriman
                                        @else
                                            {{ $data->receipt ? 'Update Resi Pengiriman' : 'Upload Resi Pengiriman' }}
                                        @endif
                                    </h1>

                                    {{-- CREATE/UPDATE MODE --}}
                                    <form action="{{ route('admin-order.save-receipt', $data->id) }}" method="POST"
                                        enctype="multipart/form-data" class="space-y-4">
                                        @csrf

                                        {{-- Field Ongkir (Khusus manual shipping) --}}
                                        @if (!empty($data->shipping_method_manual) && $data->delivery_state_id >= 3)
                                            <div class="form-control">
                                                <label for="shipping_method_manual_fee"
                                                    class="label font-semibold">Ongkir</label>
                                                <input type="number" name="shipping_method_manual_fee"
                                                    id="shipping_method_manual_fee"
                                                    value="{{ $data->shipping_method_manual_fee ?? '' }}"
                                                    class="input input-bordered w-full" required>
                                            </div>
                                        @endif

                                        <div class="form-control">
                                            <label for="receipt_number" class="label font-semibold">Nomor Resi</label>

                                            @if ($data->biteship_waybill)
                                                {{-- Dari Biteship, readonly --}}
                                                <input type="text" name="receipt_number" id="receipt_number"
                                                    value="{{ $data->biteship_waybill }}"
                                                    class="input input-bordered w-full bg-gray-100">
                                                <input type="hidden" name="receipt_number"
                                                    value="{{ $data->biteship_waybill }}">
                                            @else
                                                {{-- Manual, bisa diketik --}}
                                                <input type="text" name="receipt_number" id="receipt_number"
                                                    value="{{ $data->receipt->receipt_number ?? '' }}"
                                                    class="input input-bordered w-full" required>
                                            @endif
                                        </div>

                                        <div class="form-control">
                                            <label for="receipt_img" class="label font-semibold">Upload Foto
                                                Resi</label>
                                            <input type="file" name="receipt_img" id="receipt_img"
                                                class="file-input file-input-bordered w-full">

                                            @if ($data->receipt?->receipt_img)
                                                <div class="mt-3">
                                                    <p class="text-sm text-red-500">
                                                        Pilih file baru jika ingin mengganti resi saat ini
                                                    </p>
                                                    <div class="mt-2 mb-1">Resi Saat Ini:</div>

                                                    @if ($data->receipt?->receipt_img)
                                                        <a href="{{ Storage::url($data->receipt->receipt_img) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-base rounded-md hover:bg-blue-700 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                                            </svg>
                                                            Download Resi
                                                            ({{ $data->receipt?->receipt_number ?? 'Tanpa Nomor' }})
                                                        </a>
                                                    @else
                                                        <span class="text-gray-500 text-sm">Belum ada resi
                                                            diunggah</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-control">
                                            <label class="label font-semibold">Catatan</label>

                                            <div x-data="{ showList: false, keyword: '{{ old('shipping_method_manual_note', $data->shipping_method_manual_note ?? '') }}' }" class="relative">

                                                <!-- INPUT -->
                                                <input type="text" x-model="keyword" @focus="showList = true"
                                                    @click.outside="showList = false"
                                                    name="shipping_method_manual_note"
                                                    id="shipping_method_manual_note"
                                                    class="input input-bordered w-full"
                                                    placeholder="Pilih atau ketik manual...">

                                                <!-- LIST OPTIONS -->
                                                <ul x-show="showList"
                                                    class="absolute z-[1000] bg-base-100 border border-base-300 w-full mt-1 rounded shadow-md max-h-40 overflow-y-auto">

                                                    @foreach (['Ongkir bayar disini', 'Ongkir bayar disana'] as $option)
                                                        <li class="px-3 py-2 hover:bg-base-200 cursor-pointer"
                                                            @click="keyword = '{{ $option }}'; showList = false;">
                                                            {{ $option }}
                                                        </li>
                                                    @endforeach

                                                </ul>
                                            </div>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-2">
                                            <x-primary-button type="submit">
                                                Simpan <i class="fa fa-paper-plane ml-1"></i>
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </div>

                                <form method="dialog" class="modal-backdrop">
                                    <button>Batal</button>
                                </form>
                            </dialog>

                            <div class="mb-4">
                                <h2 class="font-bold text-lg text-center dark:text-primary-content">Data Pesanan</h2>

                                <div class="mb-8 mt-8 border-t pt-4">
                                    <div class="space-y-3">
                                        @foreach ($data->order_detail as $key => $order_detail)
                                            @php
                                                // CARI BULK PRICE DARI PRODUCT VARIANT
                                                $bulkPrice = null;

                                                if (
                                                    $order_detail->product_variant &&
                                                    $order_detail->product_variant->bulk_prices->isNotEmpty()
                                                ) {
                                                    foreach ($order_detail->product_variant->bulk_prices as $b) {
                                                        if ($order_detail->quantity >= $b->min_qty) {
                                                            $bulkPrice = $b->price;
                                                        }
                                                    }
                                                }

                                                // HARGA DISKON NORMAL (Jika ada diskon)
                                                $priceAfterDiscount =
                                                    $order_detail->price -
                                                    ($order_detail->price * $order_detail->discount) / 100;

                                                // FINAL PRICE PAKAI PRIORITAS:
                                                // bulk > diskon > harga asli
                                                $finalPrice = $bulkPrice ?? $priceAfterDiscount;

                                                // SUBTOTAL
                                                $subtotal = $finalPrice * $order_detail->quantity;
                                            @endphp


                                            <div
                                                class="border rounded-xl p-3 bg-white/50 dark:bg-gray-800 flex flex-col sm:flex-row gap-3 items-start">

                                                <!-- IMAGE -->
                                                <div class="w-full sm:w-24 flex-shrink-0 flex gap-2 items-start">
                                                    <img src="{{ $order_detail->product->product_pictures->count() > 0
                                                        ? asset('storage/' . $order_detail->product->product_pictures[0]->path)
                                                        : asset('/placeholder.png') }}"
                                                        alt="{{ $order_detail->product->name }}" loading="lazy"
                                                        class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">

                                                    <!-- MOBILE TITLE -->
                                                    <div class="flex-1 block sm:hidden text-sm">
                                                        <div class="flex items-center gap-2">
                                                            <div
                                                                class="font-semibold text-gray-900 dark:text-gray-50 text-base sm:text-lg">
                                                                {{ $order_detail->product->name }}
                                                            </div>

                                                            @if ($order_detail->product->pre_order)
                                                                <span
                                                                    class="text-green-600 border border-green-600 text-[11px] font-semibold px-2 py-[2px] rounded-full">
                                                                    Pre-Order
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if ($order_detail->product_variant && $order_detail->product_variant->product_detail->isNotEmpty())
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                Varian:
                                                                @foreach ($order_detail->product_variant->product_detail as $index => $detail)
                                                                    <span>
                                                                        {{ $detail->variant_value->value }}{{ $index < $order_detail->product_variant->product_detail->count() - 1 ? ', ' : '' }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- CONTENT -->
                                                <div class="flex-1 text-sm w-full">

                                                    <!-- DESKTOP TITLE -->
                                                    <div class="hidden sm:block">
                                                        <div class="flex items-center gap-2">
                                                            <div
                                                                class="font-semibold text-gray-900 dark:text-gray-50 text-base sm:text-lg">
                                                                {{ $order_detail->product->name }}
                                                            </div>

                                                            @if ($order_detail->product->pre_order)
                                                                <span
                                                                    class="text-green-600 border border-green-600 text-[11px] font-semibold px-2 py-[2px] rounded-full">
                                                                    Pre-Order
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if ($order_detail->product_variant && $order_detail->product_variant->product_detail->isNotEmpty())
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                Varian:
                                                                @foreach ($order_detail->product_variant->product_detail as $index => $detail)
                                                                    <span>
                                                                        {{ $detail->variant_value->value }}{{ $index < $order_detail->product_variant->product_detail->count() - 1 ? ', ' : '' }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- DETAIL GRID -->
                                                    <div class="grid grid-cols-3 gap-2 mt-3 text-xs sm:text-sm">

                                                        <div>
                                                            <div class="text-gray-400">Harga</div>

                                                            {{-- Jika ada bulk price --}}
                                                            @if ($bulkPrice)
                                                                <div class="line-through text-gray-500">
                                                                    <x-client.format-rp :value="$order_detail->price" />
                                                                </div>
                                                                <div class="font-bold text-green-600">
                                                                    <x-client.format-rp :value="$bulkPrice" />
                                                                </div>

                                                                {{-- Jika tak ada bulk price --}}
                                                            @else
                                                                <div class="font-medium">
                                                                    <x-client.format-rp :value="$priceAfterDiscount" />
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <div class="text-gray-400">Qty</div>
                                                            <div class="font-medium">{{ $order_detail->quantity }}
                                                            </div>
                                                        </div>

                                                        @if ($order_detail->discount > 0)
                                                            <div>
                                                                <div class="text-gray-400">Diskon</div>
                                                                <div class="font-medium">
                                                                    ({{ $order_detail->discount }}%)
                                                                    <x-client.format-rp :value="($order_detail->price *
                                                                        $order_detail->discount) /
                                                                        100" />
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- SUBTOTAL -->
                                                    <div class="mt-3">
                                                        <div class="text-gray-400">Subtotal</div>
                                                        <div class="font-semibold text-green-600 dark:text-green-400">
                                                            <x-client.format-rp :value="$subtotal" />
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- END DATA BARANG --}}

                            {{-- RINGKASAN PEMBAYARAN --}}
                            <div class="mb-4">
                                <h2 class="font-bold text-lg text-center dark:text-primary-content">Ringkasan
                                    Pembayaran</h2>

                                @php
                                    // Hitung subtotal barang (support bulk price + discount)
                                    $subtotalBarang = $data->order_detail->sum(function ($d) {
                                        // BASE PRICE
                                        $basePrice = $d->price;

                                        // Jika punya variant + bulk price
                                        if ($d->product_variant && $d->product_variant->bulk_prices->count() > 0) {
                                            $qty = $d->quantity;

                                            $bulk = $d->product_variant->bulk_prices
                                                ->sortByDesc('min_qty')
                                                ->first(function ($bp) use ($qty) {
                                                    return $qty >= $bp->min_qty;
                                                });

                                            if ($bulk) {
                                                $basePrice = $bulk->price;
                                            }
                                        }

                                        // APPLY DISCOUNT
                                        $finalPrice = $basePrice - ($basePrice * $d->discount) / 100;

                                        return $finalPrice * $d->quantity;
                                    });

                                    // Ongkir
                                    $ongkir = $data->shipping_method?->price ?? $data->shipping_method_manual_fee;

                                    // Voucher
                                    $voucher = $data->coupon_discount ?? 0;

                                    // Total bayar
                                    $totalBayar = $subtotalBarang + $ongkir - $voucher;
                                @endphp

                                <!-- Subtotal Barang -->
                                <div class="flex justify-between leading-8">
                                    <span>Subtotal Barang</span>
                                    <span>
                                        <x-client.format-rp :value="$subtotalBarang" />
                                    </span>
                                </div>

                                <!-- Ongkos Kirim -->
                                <div class="flex justify-between leading-8">
                                    <span>Ongkos Kirim</span>
                                    <span>
                                        <x-client.format-rp :value="$ongkir" />
                                    </span>
                                </div>

                                <!-- Voucher -->
                                @if ($data->coupon_code)
                                    <div class="flex justify-between leading-8 text-red-600">
                                        <span>Voucher ({{ $data->coupon_code }})</span>
                                        <span>
                                            -<x-client.format-rp :value="$voucher" />
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Total Bayar -->
                            <div class="flex justify-between mb-4 font-bold text-lg">
                                <span>Total Bayar</span>
                                <span>
                                    <x-client.format-rp :value="$totalBayar" />
                                </span>
                            </div>

                            <div class="mt-6 border-t pt-4">
                                <div class="flex flex-wrap items-center gap-2 justify-between">
                                    {{-- Tombol kiri --}}
                                    <div class="flex flex-wrap gap-2">
                                        {{-- Tombol Kembali --}}
                                        <a href="{{ route('admin-order.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-arrow-left"></i> Kembali
                                        </a>

                                        {{-- Tombol Konfirmasi Order --}}
                                        @if ($data->delivery_state_id == 3)
                                            <form action="{{ route('admin-order.confirm_order', $data->id) }}"
                                                method="post" class="inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check"></i> Konfirm Order
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Dropdown Aksi Tambahan --}}
                                    <div class="dropdown dropdown-top dropdown-end">
                                        <label tabindex="0" class="btn btn-sm btn-primary flex items-center gap-1">
                                            <i class="fa fa-cog"></i> Aksi Lain
                                        </label>
                                        <ul tabindex="0"
                                            class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-56">

                                            {{-- Batalkan Pesanan --}}
                                            @if ($data->delivery_state_id < 8 && $data->delivery_state && $data->delivery_state->is_biteship_status == 0)
                                                <li>
                                                    <form action="{{ route('admin-order.order_cancel', $data->id) }}"
                                                        method="POST" id="cancelForm_{{ $data->id }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="button"
                                                            onclick="confirmCancel({{ $data->id }})"
                                                            class="flex items-center gap-2 text-left text-red-600">
                                                            <i class="fa fa-ban"></i> Batalkan Pesanan
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            {{-- Lihat / Upload Resi --}}
                                            @if (
                                                ($data->delivery_state_id >= 4 && $data->delivery_state_id != 7) ||
                                                    ($data->delivery_state_id == 7 && $data->receipt))
                                                <li>
                                                    <a onclick="receiptModal.showModal()">
                                                        <i class="fas fa-receipt"></i>
                                                        {{ $data->delivery_state_id >= 6 ? 'Lihat Resi' : ($data->receipt ? 'Edit Resi' : 'Upload Resi') }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($data->bukti_pembayaran)
                                                <li>
                                                    <a href="{{ Storage::url($data->bukti_pembayaran) }}"
                                                        download="Bukti_Transfer_{{ $data->invoice }}.jpg">
                                                        <i class="fa fa-download text-blue-500"></i> Unduh bukti
                                                        transfer
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($data->bukti_pembayaran_ongkir)
                                                <li>
                                                    <a href="{{ Storage::url($data->bukti_pembayaran_ongkir) }}"
                                                        download="Bukti_Transfer_{{ $data->invoice }}.jpg">
                                                        <i class="fa fa-download text-blue-500"></i> Unduh bukti
                                                        ongkir
                                                    </a>
                                                </li>
                                            @endif
                                            @if (
                                                $data->delivery_state_id >= 1 &&
                                                    $data->delivery_state_id != 6 &&
                                                    !$data->biteship_waybill &&
                                                    $data->delivery_state_id != 3 &&
                                                    !$data->cancel_status)
                                                <li>
                                                    <form action="{{ route('admin-order.next-state', $data->id) }}"
                                                        method="POST" id="nextStateForm_{{ $data->id }}"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button"
                                                            onclick="confirmNextStatus({{ $data->id }})"
                                                            class="flex items-center gap-2 text-left">
                                                            <i class="fas fa-receipt"></i>
                                                            Update Status Selanjutnya
                                                        </button>

                                                    </form>
                                                </li>
                                            @endif

                                            {{-- Tracking Pengiriman --}}
                                            @if ($data->delivery_state_id >= 4 && $data->biteship_waybill)
                                                <li>
                                                    <a href="#"
                                                        @click.prevent="
                                                            $dispatch('open-modal', 'modal-show-order-tracking');
                                                            $store.cart.getAdminTrackingDetail({{ $data->id }})
                                                        ">
                                                        <i class="fa fa-eye"></i> Lihat Pengiriman
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Approve / Reject Pembatalan --}}
                                            @if ($data->cancel_status === 'pending')
                                                <li
                                                    class="border-t mt-1 pt-1 text-sm font-semibold text-gray-400 px-2">
                                                    Pembatalan Pesanan
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin-order.order_cancel', $data->id) }}"
                                                        method="post"
                                                        onsubmit="return confirm('Yakin ingin menyetujui pembatalan pesanan ini?')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="text-green-600">
                                                            <i class="fa fa-check"></i> Setujui Pembatalan
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin-order.order_cancel', $data->id) }}"
                                                        method="post"
                                                        onsubmit="return confirm('Tolak pembatalan pesanan ini?')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="text-red-600">
                                                            <i class="fa fa-times"></i> Tolak Pembatalan
                                                        </button>
                                                    </form>
                                                </li>
                                            @elseif ($data->cancel_status === 'approved')
                                                <li
                                                    class="text-green-600 font-medium flex items-center gap-2 border-t mt-1 pt-1 px-2">
                                                    <i class="fa fa-check-circle"></i> Pembatalan Disetujui
                                                </li>
                                            @elseif ($data->cancel_status === 'rejected')
                                                <li
                                                    class="text-red-600 font-medium flex items-center gap-2 border-t mt-1 pt-1 px-2">
                                                    <i class="fa fa-times-circle"></i> Pembatalan Ditolak
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi -->
            <dialog id="confirmModal" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg" id="confirmTitle">Konfirmasi</h3>
                    <p class="py-4" id="confirmMessage">Apakah kamu yakin?</p>
                    <div class="modal-action">
                        <button class="btn btn-sm" onclick="confirmModal.close()">Batal</button>
                        <button class="btn btn-sm btn-primary" id="confirmYesBtn">Ya, Lanjutkan</button>
                    </div>
                </div>
            </dialog>

        </div>
    </div>

    @push('scripts')
        <script>
            let currentAction = null;

            function confirmCancel(orderId) {
                currentAction = () => document.getElementById(`cancelForm_${orderId}`).submit();
                document.getElementById('confirmTitle').innerText = 'Konfirmasi Pembatalan';
                document.getElementById('confirmMessage').innerText =
                    'Yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.';
                confirmModal.showModal();
            }

            function confirmNextStatus(orderId) {
                currentAction = () => document.getElementById(`nextStateForm_${orderId}`).submit();
                document.getElementById('confirmTitle').innerText = 'Konfirmasi Update Status';
                document.getElementById('confirmMessage').innerText =
                    'Apakah kamu yakin ingin memperbarui status pesanan ke tahap selanjutnya?';
                confirmModal.showModal();
            }

            document.getElementById('confirmYesBtn').addEventListener('click', () => {
                if (currentAction) currentAction();
                confirmModal.close();
            });
        </script>
    @endpush

    @include('client.profile.modal-show-order-tracking')
</x-app-layout>
