<x-client.product-detail.thumbnail-picture :product="$product" />

<div
    class="detail-product-container rounded bg-base-200 shadow-xl text-base-content p-4 w-full h-fit sm:w-3/4 md:w-1/2 mx-auto md:flex-1 mb-6">
    <h1 class="text-center text-xl sm:text-2xl font-bold">
        {{ $product->name }}
        ( <span class="animate-pulse">PROMO !!!</span> )
    </h1>

    {{-- HARGA SECTION --}}
    <div class="divider">
        Harga <i class="fa fa-tags text-red-500 text-2xl"></i>
    </div>

    @auth
        <span class="line-through">
            <span x-ref="priceEl">{{ 'Rp. ' . number_format($product->product_variant->first()->price, 0, ',', '.') }}</span>
        </span>
        <span class="text-xs align-top ml-2">-{{ $product->promo->discount }}%</span>
        <br />
        <span
            x-ref="priceAfterDiscountEl">{{ 'Rp. ' .
                number_format(
                    $product->product_variant->first()->price -
                        ($product->product_variant->first()->price * $product->promo->discount) / 100,
                    0,
                    ',',
                    '.',
                ) }}</span>
        {{-- END HARGA SECTION --}}
    @endauth

    @guest
        <span><a href="{{ route('login') }}" class="text-green-600">Login</a> untuk lihat harga</span>
    @endguest

    @if (count($product->product_variant) > 1)
        <x-client.product-detail.variant :product="$product" />
    @endif

    <div class="divider">Deskripsi</div>
    <div class="text-sm md:text-base">{!! $product->description !!}</div>

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
        let discount = {{ $product->promo->discount ?? 0 }};
        let discountedPrice = originalPrice - (originalPrice * discount / 100);

        if ($refs.subtotal) {
            $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(discountedPrice * $refs.quantity.value);
        }
        "
            x-init="let selectedOption = $refs.variant_id_selected.options[$refs.variant_id_selected.selectedIndex];
            let originalPrice = parseFloat(selectedOption.getAttribute('data-price'));
            let discount = {{ $product->promo->discount ?? 0 }};
            let discountedPrice = originalPrice - (originalPrice * discount / 100);
            
            if ($refs.subtotal) {
                $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(discountedPrice * $refs.quantity.value);
            }">
    @else
        <input type="number" min="1" value="1" class="input w-20 mb-2" x-ref="quantity"
            @change="
        let originalPrice = {{ $product->product_variant->first()->price }};
        let discount = {{ $product->promo->discount ?? 0 }};
        let discountedPrice = originalPrice - (originalPrice * discount / 100);

        if ($refs.subtotal) {
            $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(discountedPrice * $refs.quantity.value);
        }
        "
            x-init="let originalPrice = {{ $product->product_variant->first()->price }};
            let discount = {{ $product->promo->discount ?? 0 }};
            let discountedPrice = originalPrice - (originalPrice * discount / 100);
            
            if ($refs.subtotal) {
                $refs.subtotal.innerHTML = 'Rp. ' + new Intl.NumberFormat('id-ID', { style: 'decimal', minimumFractionDigits: 0 }).format(discountedPrice * $refs.quantity.value);
            }">
    @endif

    <div>Sisa stok : <span x-ref="stock">{{ $product->product_variant->first()->stock }}</span></div>
    {{-- <input type="text" class="input w-11/12 my-2" placeholder="Catatan"> --}}
    @auth
        <div class="mb-2" x-ref="subtotal"></div>
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
