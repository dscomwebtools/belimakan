<div class="card sm:w-72 bg-base-200 shadow-xl mb-8 h-fit">
    <figure class="sm:px-5 sm:pt-5 relative">
        <a href="{{ route('client.product', $product['slug']) }}">
            @if ($product->product_pictures->isNotEmpty())
                <img src="{{ Storage::url($product->product_pictures->first()->path) }}" alt="{{ $product->name }}"
                    class="rounded-xl" />
            @else
                <img src="{{ '/assets/images/image-not-found.webp' }}" alt="{{ $product->name }}" class="rounded-xl" />
            @endif
        </a>

        @if (isset($product->auction) && $product->auction->active)
            <x-client.logo-lelang :endtime="$product->auction->endtime" />
        @elseif (isset($product->promo) && $product->promo->active)
            <x-client.logo-promo />
        @endif
    </figure>

    <div class="p-4 md:p-8 flex flex-col gap-2 items-center text-center">
        <a href="{{ route('client.product', $product['slug']) }}">
            <h2 class="card-title text-left text-sm md:text-lg">{{ $product->name }}</h2>
        </a>

        {{-- Variants + Prices --}}
        @if ($product->product_variant->count() > 0)
            <div class="w-full flex flex-col items-center gap-2" x-data="{
                selectedVariant: {{ $product->product_variant[0]->id }},
                variants: {{ $product->product_variant->toJson() }},
                promo: {{ isset($product->promo) && $product->promo->active ? $product->promo->discount : 0 }}
            }">

                {{-- Variant Selector --}}
                @if ($product->product_variant->count() > 1)
                    <select name="variant" class="my-input w-full text-sm md:text-base mb-2" x-model="selectedVariant">
                        <template x-for="variant in variants" :key="variant.id">
                            <option :value="variant.id"
                                x-text="variant.product_detail.map(d => d.variant_value.variant.variant + ' - ' + d.variant_value.value).join(' & ')">
                            </option>
                        </template>
                    </select>
                @endif

                {{-- Price Display --}}
                <div>
                    <template x-for="variant in variants" :key="variant.id">
                        <div x-show="selectedVariant == variant.id">
                            <template x-if="promo > 0">
                                <div>
                                    <span class="line-through text-sm md:text-base"
                                        x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(variant.price)">
                                    </span>
                                    <span class="text-xs align-top ml-2" x-text="'-' + promo + '%'"></span>
                                    <br>
                                    <span class="text-sm md:text-base"
                                        x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(variant.price - (variant.price * promo / 100))">
                                    </span>
                                </div>
                            </template>

                            <template x-if="promo == 0">
                                <span class="text-sm md:text-base"
                                    x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(variant.price)">
                                </span>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Add to Cart --}}
                @can('is-member')
                    @if (auth()->user()->hasVerifiedEmail())
                        <div class="card-actions mt-2">
                            <div class="flex gap-2">
                                <input type="number" name="quantity" x-ref="quantity"
                                    class="my-input px-1 w-16 dark:border-b-white " min="1" value="1">
                                <div class="tooltip tooltip-bottom" data-tip="Tambah ke keranjang">
                                    <button class="btn btn-circle btn-primary"
                                        @click="$store.cart.addToCart('{{ $product->id }}', selectedVariant, $refs.quantity.value)">
                                        <i class="fa fa-cart-shopping"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="tooltip tooltip-bottom before:w-40"
                            data-tip="Verifikasi email untuk menambahkan ke keranjang">
                            <a href="{{ route('verification.notice') }}" class="btn btn-circle btn-primary">
                                <i class="fa fa-cart-shopping"></i>
                            </a>
                        </div>
                    @endif
                @endcan

                @guest
                    <div class="tooltip tooltip-bottom before:w-40" data-tip="Login untuk menambahkan ke keranjang">
                        <a href="{{ route('login') }}" class="btn btn-circle btn-primary">
                            <i class="fa fa-cart-shopping"></i>
                        </a>
                    </div>
                @endguest
            </div>
        @endif
    </div>
</div>