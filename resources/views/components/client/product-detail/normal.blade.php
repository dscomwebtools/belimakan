<x-client.product-detail.thumbnail-picture :product="$product" />

<div
    class="detail-product-container rounded bg-base-200 shadow-xl text-base-content p-4 w-full h-fit sm:w-3/4 md:w-1/2 mx-auto md:flex-1 mb-6">
    <h1 class="text-center text-xl sm:text-2xl font-bold">
        {{ $product->name }}
    </h1>

    {{-- PRE ORDER --}}
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

    <div class="divider">Harga</div>

    {{-- HARGA UTAMA --}}
    @auth
        <span x-ref="priceEl">{{ 'Rp. ' . number_format($product->product_variant->first()->price, 0, ',', '.') }}</span>
    @endauth
    @guest
        <span><a href="{{ route('login') }}" class="text-green-600">Login</a> untuk lihat harga</span>
    @endguest

    {{-- TABEL HARGA GROSIR --}}
    @php
        $firstVariant = $product->product_variant->first();
    @endphp

    @if ($firstVariant && $firstVariant->bulk_prices->isNotEmpty())
        <div class="mt-4">
            <h3 class="font-semibold mb-2">Harga Grosir:</h3>

            <table class="table table-sm w-full text-sm">
                <thead>
                    <tr>
                        <th>Min Qty</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($firstVariant->bulk_prices as $bp)
                        <tr>
                            <td>{{ $bp->min_qty }}+</td>
                            <td>Rp. {{ number_format($bp->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- VARIANT --}}
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
            <iframe src="https://www.youtube.com/embed/{{ $product->youtube }}" class="w-full h-[400px] rounded"
                allowfullscreen></iframe>
        </div>
    @endif
</div>


{{-- ================================================================= --}}
{{-- ACTION CONTAINER --}}
{{-- ================================================================= --}}
<div class="action-container rounded bg-base-200 shadow-xl text-base-content p-4 w-full sm:w-3/4 md:w-1/2 lg:w-1/4 mx-auto mb-6"
    x-data="priceHandler">
    <h1>Atur jumlah dan catatan</h1>
    <div class="divider"></div>

    {{-- INPUT QTY --}}
    <input type="number" min="1" value="1" class="input w-20 mb-2" x-ref="quantity" @change="updatePrice()"
        x-init="loadInitial()">

    {{-- SISA STOK --}}
    <div>
        Sisa stok :
        <span x-ref="stock">{{ $product->product_variant->first()->stock }}</span>
    </div>

    {{-- SUBTOTAL --}}
    @auth
        @if (!($product->call_to_buy ?? false))
            <div class="mt-2 line-through" x-ref="subtotaloriginal"></div>
            <div class="mb-2" x-ref="subtotal"></div>
        @endif
    @endauth

    @php
        use Carbon\Carbon;
        $now = Carbon::now('Asia/Jakarta');
        $hour = $now->hour;
        $isWorkingHour = $hour >= 9 && $hour < 17;
        $waNumber = $isWorkingHour ? '6281234567890' : '6281383655055';
    @endphp

    <div class="flex flex-col gap-3 mt-4">

        <button
            class="h-12 w-full flex items-center justify-center gap-2 
               bg-gray-100 hover:bg-gray-200 text-gray-700
               rounded-xl shadow-sm transition font-semibold"
            x-data @click.prevent="$dispatch('open-modal', 'share-product')">
            <i class="fa fa-share-nodes text-base"></i>
            <span>Bagikan Produk</span>
        </button>

        @can('is-member')
            @if (!($product->call_to_buy ?? false))
                <button
                    class="h-12 w-full flex items-center justify-center gap-2 
                       bg-primary hover:bg-primary/90 text-white
                       rounded-xl shadow transition font-semibold"
                    @click="$store.cart.addToCart('{{ $product->id }}', $refs.variant_id_selected ? $refs.variant_id_selected.value : {{ $product->product_variant[0]->id }}, $refs.quantity.value)">
                    <i class="fa fa-cart-shopping"></i>
                    <span>Tambah ke Keranjang</span>
                </button>
            @endif

            @if ($product->call_to_buy)
                <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                    class="h-12 w-full flex items-center justify-center gap-2 
                   bg-green-500 hover:bg-green-600 text-white
                   rounded-xl shadow transition font-semibold">
                    <i class="fa fa-whatsapp text-lg"></i>
                    <span>Hubungi via WhatsApp</span>
                </a>
            @endif
        @endcan

    </div>
</div>


{{-- ================================================================= --}}
{{-- ALPINE SCRIPT LOGIC --}}
{{-- ================================================================= --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('priceHandler', () => ({
            product: @json($product),

            loadInitial() {
                this.updatePrice();
            },

            getSelectedVariant() {
                let selector = document.querySelector('[x-ref=variant_id_selected]');
                if (selector) {
                    let id = selector.value;
                    return this.product.product_variant.find(v => v.id == id);
                }
                return this.product.product_variant[0];
            },

            updatePrice() {
                let qty = parseInt(this.$refs.quantity.value);
                let v = this.getSelectedVariant();

                let bulkPrice = null;
                if (v.bulk_prices && v.bulk_prices.length > 0) {
                    v.bulk_prices.forEach(bp => {
                        if (qty >= bp.min_qty) bulkPrice = bp.price;
                    });
                }

                let finalPrice = bulkPrice ?? v.price;
                let originalPrice = v.price * qty;

                if (this.$refs.subtotal) {
                    this.$refs.subtotal.innerHTML =
                        'Rp. ' + new Intl.NumberFormat('id-ID').format(finalPrice * qty);

                    this.$refs.subtotaloriginal.innerHTML = (finalPrice * qty) < originalPrice ?
                        'Rp. ' + new Intl.NumberFormat('id-ID').format(originalPrice) : "";
                }

                this.$refs.stock.innerText = v.stock;
            }
        }));
    });
</script>
