<x-client.product-detail.thumbnail-picture :product="$product" />

<div
    class="detail-product-container rounded bg-base-200 shadow-xl text-base-content p-4 w-full h-fit sm:w-3/4 md:w-1/2 mx-auto md:flex-1 mb-6">
    <h1 class="text-center text-xl sm:text-2xl font-bold">
        {{ $product->name }}
    </h1>

    @if ($product->pre_order && $product->pre_order_date)
        <div class="divider text-red-500">Pre-Order</div>

        @php
            $preOrderDate = \Carbon\Carbon::parse($product->pre_order_date);
            $now = \Carbon\Carbon::now();
            $isFuture = $preOrderDate->greaterThan($now);
            $isToday = $preOrderDate->isToday();
            $days = ceil($now->diffInHours($preOrderDate) / 24);
        @endphp

        <div class="bg-primary/5 border-2 border-primary/20 rounded-lg p-4 text-sm md:text-base">
            <div class="flex flex-col gap-1">
                <span class="font-medium text-green-600">Estimasi pengiriman:</span>
                <span class="font-semibold">
                    {{ $preOrderDate->translatedFormat('d F Y') }}
                    @if ($isFuture && !$isToday)
                        <span class="text-gray-500">— {{ $days }} hari lagi</span>
                    @elseif ($isToday)
                        <span class="text-orange-500">— Dikirim hari ini</span>
                    @else
                        <span class="text-red-500">— Sudah lewat {{ abs($days) }} hari</span>
                    @endif
                </span>

                @if ($product->pre_order_message)
                    <p class="text-gray-700 mt-2 leading-relaxed">
                        {!! nl2br(e($product->pre_order_message)) !!}
                    </p>
                @endif
            </div>
        </div>
    @endif

    <div class="divider">
        Harga
    </div>

    @auth
        <span x-ref="priceEl">{{ 'Rp. ' . number_format($product->product_variant->first()->price, 0, ',', '.') }}</span>
    @endauth
    @guest
        <span><a href="{{ route('login') }}" class="text-green-600">Login</a> untuk lihat harga</span>
    @endguest

    {{-- MENGGUNKAN KONDISI JIKA PRODUCT_VARIANT > 1 KARENA PRODUK YANG TIDAK MEMILIKI VARIANT MEMILIKI 1 SAJA DATA DI
    TABEL PRODUCT VARIANT SEDANGKAN YANG MEMILIKI VARIANT AKAN MEMILIKI LEBIH DARI 1 DATA --}}
    @if (count($product->product_variant) > 1)
        <x-client.product-detail.variant :product="$product" />
    @endif

    <div class="divider">Deskripsi</div>
    <div class="text-sm md:text-base">{!! $product->description ?? '-' !!}</div>

    @if ($product->detail_value->isNotEmpty())
        <div class="divider">Spesifikasi</div>
        @foreach ($product->detail_value as $detail)
            <div class="specification">
                <span>{{ $detail->detail->detail }}</span> : <span>{{ $detail->value }}</span>
            </div>
        @endforeach
    @endif

    @if ($product->dimention->length > 0 || $product->dimention->height > 0 || $product->dimention->weight > 0)
        <div class="divider">Dimensi</div>
        <div class="dimention">
            <div><span>Panjang</span> : <span>{{ $product->dimention->length }} cm</span></div>
            <div><span>Lebar</span> : <span>{{ $product->dimention->width }} cm</span></div>
            <div><span>Tinggi</span> : <span>{{ $product->dimention->height }} cm</span></div>
        </div>
    @endif

    @if ($product->youtube)
        <div class="divider">Video</div>
        <div class="mt-8">
            <iframe src="https://www.youtube.com/embed/{{ $product->youtube }}" title="YouTube video player"
                class="w-full h-[400px] rounded" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
            </iframe>
        </div>
    @endif
</div>

<div
    class="action-container rounded bg-base-200 shadow-xl text-base-content p-4 w-full h-fit sm:w-3/4 md:w-1/2 mx-auto md:mx-0 md:ml-auto lg:w-1/4 mb-6">
    <h1>Atur jumlah dan catatan</h1>
    <div class="divider"></div>

    @if (count($product->product_variant) > 1)
        <input type="number" min="1" value="1" class="input w-20 mb-2" x-ref="quantity"
            @change="
    let selectedOption = $refs.variant_id_selected.options[$refs.variant_id_selected.selectedIndex];
    let originalPrice = parseFloat(selectedOption.getAttribute('data-price'));
    if ($refs.subtotal) {
        $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(originalPrice * $refs.quantity.value);
    }
    "
            x-init="let selectedOption = $refs.variant_id_selected.options[$refs.variant_id_selected.selectedIndex];
            let originalPrice = {{ $product->product_variant->first()->price }};
            if ($refs.subtotal) {
                $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(originalPrice * $refs.quantity.value);
            }">
    @else
        <input type="number" min="1" value="1" class="input w-20 mb-2" x-ref="quantity"
            @change="
            let originalPrice = {{ $product->product_variant->first()->price }};
            if ($refs.subtotal) {
                $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(originalPrice * $refs.quantity.value);
            }
            "
            x-init="let originalPrice = {{ $product->product_variant->first()->price }};
            if ($refs.subtotal) {
                $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(originalPrice * $refs.quantity.value);
            }">
    @endif


    <div>
        Sisa stok :
        <span x-ref="stock">{{ $product->product_variant->first()->stock }}</span>
    </div>

    {{-- Subtotal hanya tampil jika bukan call_to_buy --}}
    @auth
        @if (!($product->call_to_buy ?? false))
            <div class="mb-2" x-ref="subtotal"></div>
        @endif
    @endauth

    <div class="flex flex-col gap-3 mt-4">

        {{-- Tombol Share --}}
        <button
            class="h-12 w-full flex items-center justify-center gap-2 
               bg-gray-100 hover:bg-gray-200 text-gray-700
               rounded-xl shadow-sm transition font-semibold active:scale-[0.98]"
            x-data @click.prevent="$dispatch('open-modal', 'share-product')">
            <i class="fa fa-share-nodes text-base"></i>
            <span>Bagikan Produk</span>
        </button>

        @php
            use Carbon\Carbon;
            $now = Carbon::now('Asia/Jakarta');
            $hour = $now->hour;
            $isWorkingHour = $hour >= 9 && $hour < 17;
            $waNumber = $isWorkingHour ? '6281234567890' : '6281383655055';
        @endphp

        @can('is-member')
            {{-- Jika bukan Call To Buy → Keranjang --}}
            @if (!($product->call_to_buy ?? false))
                <button
                    class="h-12 w-full flex items-center justify-center gap-2 
                       bg-primary hover:bg-primary/90 text-white
                       rounded-xl shadow transition font-semibold active:scale-[0.98]"
                    @click="$store.cart.addToCart('{{ $product->id }}', $refs.variant_id_selected ? $refs.variant_id_selected.value : {{ $product->product_variant[0]->id }}, $refs.quantity.value)">
                    <i class="fa fa-cart-shopping"></i>
                    <span>Tambah ke Keranjang</span>
                </button>
            @endif

            {{-- Jika Call To Buy → WhatsApp --}}
            @if ($product->call_to_buy)
                <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                    class="h-12 w-full flex items-center justify-center gap-2 
                       bg-green-500 hover:bg-green-600 text-white
                       rounded-xl shadow transition font-semibold active:scale-[0.98]">
                    <i class="fa fa-whatsapp text-lg"></i>
                    <span>Hubungi via WhatsApp</span>
                </a>
            @endif
        @endcan

    </div>
</div>
