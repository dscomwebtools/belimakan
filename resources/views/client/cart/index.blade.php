<x-client-layout>
    <div class="cart-body p-2" x-data>
        <h1 class="text-xl md:text-2xl lg:text-3xl mb-6 font-bold">
            <i class="fa fa-cart-shopping"></i> Keranjang Belanjaanmu
        </h1>
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex flex-col gap-2 flex-1">
                <div x-show="$store.cart.items.length" class="flex items-center gap-2 mb-2 bg-base-200 p-2 rounded">
                    <input type="checkbox" id="check-all"
                        :checked="$store.cart.selectedIds.length === $store.cart.items.length && $store.cart.items.length > 0"
                        @change="$store.cart.toggleAll($store.cart.items)">
                    <label for="check-all" class="cursor-pointer">Pilih Semua</label>
                </div>
                <template x-for="item in $store.cart.items" :key="item.id">
                    <div class="cart-item bg-base-200 p-4 rounded-t border-b border-primary flex gap-2">
                        <!-- ✅ Checkbox -->
                        <div class="flex gap-2">
                            <input type="checkbox" :id="`check-${item.id}`" :checked="$store.cart.isSelected(item)"
                                @change="$store.cart.toggleSelection(item)" class="w-[20px] h-[20px]">
                        </div>

                        <div class="flex flex-row gap-2 w-full">
                            <img :src="item.product.product_pictures.length ? `/storage/${item.product.product_pictures[0].path}` :
                                ''"
                                alt="product-image"
                                class="rounded mx-2 w-full max-h-[80px] md:max-h-[160px] max-w-[80px] md:max-w-[160px]">

                            <div class="flex flex-col w-full">
                                <div class="name mb-4">
                                    <div class="text-base md:text-lg lg:text-xl font-bold" x-text="item.product.name">
                                    </div>

                                    <template x-if="item.product_variant">
                                        <template x-for="detail in item.product_variant.product_detail">
                                            <div class="text-sm md:text-base mt-2"
                                                x-text="`${detail.variant_value.variant.variant} ${detail.variant_value.value}`">
                                            </div>
                                        </template>
                                    </template>

                                    <div x-show="item.product_variant ? item.product_variant.stock <= 0 : item.product.product_variant[0].stock <= 0"
                                        class="text-red-500 text-xs animate-pulse text-sm md:text-base mt-2">
                                        Stok Habis
                                    </div>
                                </div>

                                <!-- Price & Qty -->

                                <template x-if="item.product.promo && item.product.promo.discount > 0">
                                    <template x-if="item.product_variant">
                                        <div class="line-through"
                                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.product_variant.price)">
                                        </div>
                                    </template>
                                </template>

                                <div class="price flex flex-col text-sm md:text-base">
                                    <div>
                                        <span x-text="item.quantity"></span>
                                        <template x-if="item.product_variant">
                                            <span
                                                x-text="'x ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.product.promo ? item.product_variant.price - (item.product_variant.price * (item.product.promo.discount / 100)) : item.product_variant.price)">
                                            </span>
                                        </template>
                                        <template x-if="!item.product_variant">
                                            <span
                                                x-text="item.product.product_variant ? 'x ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.product.promo ? item.product.product_variant[0].price - (item.product.product_variant[0].price * (item.product.promo.discount / 100)) : item.product.product_variant[0].price) : ''">
                                            </span>
                                        </template>
                                    </div>

                                    <div class="mt-3 flex flex-wrap justify-between w-full items-center gap-2">
                                        <!-- Quantity controls -->
                                        <div class="flex items-center gap-1 flex-grow sm:flex-grow-0">
                                            <button class="btn btn-sm btn-ghost p-1"
                                                @click="item.quantity--; $store.cart.updateQty(item, item.quantity)">
                                                -
                                            </button>
                                            <input type="number" min="1" x-model="item.quantity"
                                                class="my-input w-16 sm:w-20 text-center"
                                                @change="$store.cart.updateQty(item, item.quantity)">
                                            <button class="btn btn-sm btn-ghost p-1"
                                                @click="item.quantity++; $store.cart.updateQty(item, item.quantity)">
                                                +
                                            </button>
                                        </div>

                                        <!-- Remove button -->
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-sm btn-ghost p-1"
                                                @click="$store.cart.removeItem(item)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="!$store.cart.items.length">
                    Keranjang belanjamu masih kosong !!!
                </div>
            </div>

            <!-- ✅ Summary for Selected -->
            <div class="rounded bg-base-200 p-4 h-fit sticky top-24 bottom-16 md:w-[300px]">
                <h2 class="text-lg md:text-xl font-bold mb-2">Ringkasan Belanja</h2>
                <div class="flex justify-between text-sm md:text-base">
                    <div class="w-32">Total Dipilih</div>
                    <div x-text="$store.cart.totalItemCart"></div>
                </div>
                <div class="flex justify-between text-sm md:text-base">
                    <div class="w-32">Subtotal</div>
                    <div
                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.subtotalCart)">
                    </div>
                </div>
                <div class="card-actions my-2">
                    <a href="{{ route('client.checkout') }}"
                        @click.prevent="
                            if ($store.cart.selectedIds.length > 0) {
                                localStorage.setItem('checkoutSelected', JSON.stringify($store.cart.selectedIds));
                                window.location.href='{{ route('client.checkout') }}';
                            }
                        "
                        class="btn btn-primary btn-block"
                        :class="{ 'btn-disabled': $store.cart.selectedIds.length === 0 }"
                        :disabled="$store.cart.selectedIds.length === 0">
                        Beli
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
