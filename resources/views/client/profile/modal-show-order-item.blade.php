<x-modal name="modal-show-order-item">
    <div class="p-4">
        <h1 class="text-xl md:text-2xl mb-4 font-bold text-center">Data Detail Order</h1>

        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4 text-sm md:text-base">
            <div class="md:w-1/2">
                <h2 class="font-bold text-base md:text-lg">Alamat Pengiriman</h2>
                <span x-text="$store.cart.orderDetail.order_address?.name"></span> •
                <span x-text="$store.cart.orderDetail.order_address?.recipient"></span> •
                <span x-text="$store.cart.orderDetail.order_address?.hp"></span>
                <address x-text="$store.cart.orderDetail.order_address?.address"></address>
                <span x-text="$store.cart.orderDetail.order_address?.area_name"></span>
            </div>

            <div class="md:w-1/2 md:text-end">
                <h2 class="font-bold text-base md:text-lg">Tanggal Order</h2>
                <span
                    x-text="new Date($store.cart.orderDetail.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })"></span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4 text-sm md:text-base">
            <div class="md:w-1/2">
                <h2 class="font-bold text-base md:text-lg">Kurir</h2>
                <div x-show="$store.cart.orderDetail.shipping_method">
                    <span x-text="$store.cart.orderDetail.shipping_method?.courier_name"></span> •
                    <span x-text="$store.cart.orderDetail.shipping_method?.courier_service_name"></span> •
                    Estimasi <span x-text="$store.cart.orderDetail.shipping_method?.duration"></span> •
                    <span
                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.orderDetail.shipping_method?.price)"></span>
                </div>
                <div x-show="!$store.cart.orderDetail.shipping_method">
                    <span>Manual</span> •
                    <span x-text="$store.cart.orderDetail.shipping_method_manual"></span>
                </div>
            </div>

            <div class="md:w-1/2 md:text-end">
                <h2 class="font-bold text-base md:text-lg">Status Pengiriman</h2>
                <span x-text="$store.cart.orderDetail.delivery_state?.name"></span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4 text-sm md:text-base">
            <div class="md:w-1/2 space-y-1">
                <h2 class="font-semibold text-base md:text-lg">Pembayaran</h2>
                <p class="text-gray-700">
                    <span x-text="$store.cart.orderDetail.payment_method"></span>
                    <span x-text="$store.cart.orderDetail.bank_account?.bank_name"></span>
                </p>
                <div class="flex items-center gap-2" x-show="$store.cart.orderDetail.bank_account">
                    <span x-text="$store.cart.orderDetail.bank_account?.account_number"></span>
                    <button @click="$store.cart.copyToClipboard($store.cart.orderDetail.bank_account?.account_number)"
                        class="text-gray-500 hover:text-gray-700 tooltip" data-tip="Copy nomor rekening">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
                <span x-show="$store.cart.showNotifSuccess" x-text="$store.cart.message"
                    class="text-xs text-green-600"></span>
            </div>

            <div class="md:w-1/2 md:text-end">
                <h2 class="font-bold text-base md:text-lg">Status Pembayaran</h2>
                <span x-text="$store.cart.orderDetail.is_paid == 0 ? 'Belum Dibayar' : 'Lunas'"></span>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <h2 class="font-bold text-base md:text-lg mb-3">Resi Pengiriman</h2>

            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="font-semibold">Nomor Resi:</span>
                    <span
                        x-text="$store.cart.orderDetail.receipt?.receipt_number || $store.cart.orderDetail.biteship_waybill || '-'"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="font-semibold">Bukti Resi:</span>
                    <template x-if="$store.cart.orderDetail.receipt?.receipt_img">
                        <a :href="$store.cart.orderDetail.receipt.receipt_img_url"
                            :download="'resi-' + ($store.cart.orderDetail.receipt?.receipt_number || $store.cart.orderDetail
                                .biteship_waybill)"
                            class="text-blue-600 hover:underline flex items-center gap-1">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </template>
                    <template x-if="!$store.cart.orderDetail.receipt?.receipt_img">
                        <span class="text-gray-500">Belum ada</span>
                    </template>
                </div>
            </div>

            <template x-if="$store.cart.orderDetail.biteship_waybill">
                <div class="mt-4 pt-3 border-t text-sm">
                    <a href="#"
                        @click.prevent="$dispatch('open-modal', 'modal-show-order-tracking'); $store.cart.getTrackingDetail($store.cart.orderDetail.id)"
                        class="text-teal-600 hover:underline flex items-center gap-1">
                        <i class="fa fa-eye"></i> Lihat Pengiriman
                    </a>
                </div>
            </template>
        </div>

        <div class="mb-8 mt-8 border-t pt-4">
            <h2 class="font-bold text-xl md:text-2xl text-center">Data Pesanan</h2>

            <div class="space-y-3">
                <template x-for="orderDetail in $store.cart.orderDetail.order_detail" :key="orderDetail.id">
                    <div class="border rounded-xl p-3 bg-white dark:bg-gray-800 flex flex-col sm:flex-row gap-3 items-start"
                        x-data="priceCalc(orderDetail)">
                        <div class="w-full sm:w-24 flex-shrink-0 flex gap-2 items-start">
                            <img :src="orderDetail.product.product_pictures?.length > 0 ? '/storage/' + orderDetail.product
                                .product_pictures[0].path : '/placeholder.png'"
                                :alt="orderDetail.product.name" loading="lazy"
                                class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">

                            <div class="flex-1 block sm:hidden text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="font-semibold text-gray-900 dark:text-gray-50 text-base sm:text-lg"
                                        x-text="orderDetail.product.name"></div>

                                    <template x-if="orderDetail.product.pre_order">
                                        <span
                                            class="text-green-600 border border-green-600 text-[11px] font-semibold px-2 py-[2px] rounded-full">Pre-Order</span>
                                    </template>
                                </div>

                                <template
                                    x-if="orderDetail.product_variant && orderDetail.product_variant.product_detail.length > 0">
                                    <div class="text-xs text-gray-500 mt-1">
                                        Varian:
                                        <template x-for="(detail, index) in orderDetail.product_variant.product_detail"
                                            :key="index">
                                            <span><span x-text="detail.variant_value.value"></span><span
                                                    x-text="index < orderDetail.product_variant.product_detail.length - 1 ? ', ' : ''"></span></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex-1 text-sm w-full">
                            <div class="hidden sm:block">
                                <div class="flex items-center gap-2">
                                    <div class="font-semibold text-gray-900 dark:text-gray-50 text-base sm:text-lg"
                                        x-text="orderDetail.product.name"></div>

                                    <template x-if="orderDetail.product.pre_order">
                                        <span
                                            class="text-green-600 border border-green-600 text-[11px] font-semibold px-2 py-[2px] rounded-full">Pre-Order</span>
                                    </template>
                                </div>

                                <template
                                    x-if="orderDetail.product_variant && orderDetail.product_variant.product_detail.length > 0">
                                    <div class="text-xs text-gray-500 mt-1">
                                        Varian:
                                        <template x-for="(detail, index) in orderDetail.product_variant.product_detail"
                                            :key="index">
                                            <span><span x-text="detail.variant_value.value"></span><span
                                                    x-text="index < orderDetail.product_variant.product_detail.length - 1 ? ', ' : ''"></span></span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-3 gap-2 mt-3 text-xs sm:text-sm">
                                <div>
                                    <div class="text-gray-400">Harga</div>
                                    <div class="font-medium" x-text="formattedFinalPrice"></div>
                                </div>

                                <div>
                                    <div class="text-gray-400">Qty</div>
                                    <div class="font-medium" x-text="orderDetail.quantity"></div>
                                </div>

                                <div x-show="orderDetail.discount > 0">
                                    <div class="text-gray-400">Diskon</div>
                                    <div class="font-medium"
                                        x-text="orderDetail.discount > 0 ? '(' + orderDetail.discount + '%) ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(formattedDiscountAmountRaw) : '-'">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="text-gray-400">Subtotal</div>
                                <div class="font-semibold text-green-600 dark:text-green-400"
                                    x-text="formattedSubtotal"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="mb-4 mt-6 bg-white dark:bg-gray-800">
            <h2 class="font-bold text-base md:text-lg mb-3 border-b pb-2 dark:text-gray-100">Ringkasan Pembayaran</h2>

            <div class="flex justify-between py-1 text-sm md:text-base">
                <span class="text-gray-600 dark:text-gray-300">Subtotal Barang</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100"
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.orderDetail.order_detail ? $store.cart.orderDetail.order_detail.reduce((sum, d) => { let v = d.product_variant ? d.product_variant : (d.product && d.product.product_variant ? d.product.product_variant[0] : null); let base = v ? Number(v.price) : Number(d.price || 0); if (v && v.bulk_prices && v.bulk_prices.length > 0) { let qty = Number(d.quantity || 0); let bulk = v.bulk_prices.slice().sort((a,b)=>b.min_qty - a.min_qty).find(bp=>qty>=Number(bp.min_qty)); if (bulk) base = Number(bulk.price); } let finalPrice = base; if (d.discount && d.discount > 0) finalPrice = finalPrice - (finalPrice * d.discount / 100); return sum + (finalPrice * Number(d.quantity || 0)); }, 0) : 0)">
                </span>
            </div>

            <div class="flex justify-between py-1 text-sm md:text-base">
                <span class="text-gray-600 dark:text-gray-300">Ongkos Kirim</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100"
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.orderDetail.shipping_method?.price || $store.cart.orderDetail.shipping_method_manual_fee || 0)">
                </span>
            </div>

            <template x-if="$store.cart.orderDetail.coupon_code">
                <div class="flex justify-between py-1 text-sm md:text-base text-red-600">
                    <span>Voucher (<span x-text="$store.cart.orderDetail.coupon_code"></span>)</span>
                    <span>-<span
                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.orderDetail.coupon_discount || 0)"></span></span>
                </div>
            </template>

            <hr class="my-2 border-gray-200 dark:border-gray-700" />

            <div class="flex justify-between pt-1 text-base font-bold">
                <span>Total Pembayaran</span>
                <span
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(( $store.cart.orderDetail.order_detail ? $store.cart.orderDetail.order_detail.reduce((sum, d) => { let v = d.product_variant ? d.product_variant : (d.product && d.product.product_variant ? d.product.product_variant[0] : null); let base = v ? Number(v.price) : Number(d.price || 0); if (v && v.bulk_prices && v.bulk_prices.length > 0) { let qty = Number(d.quantity || 0); let bulk = v.bulk_prices.slice().sort((a,b)=>b.min_qty - a.min_qty).find(bp=>qty>=Number(bp.min_qty)); if (bulk) base = Number(bulk.price); } let finalPrice = base; if (d.discount && d.discount > 0) finalPrice = finalPrice - (finalPrice * d.discount / 100); return sum + (finalPrice * Number(d.quantity || 0)); }, 0) : 0 ) + ($store.cart.orderDetail.shipping_method?.price || $store.cart.orderDetail.shipping_method_manual_fee || 0) - ($store.cart.orderDetail.coupon_discount || 0) )"></span>
            </div>
        </div>

        <div class="text-end self-end">
            <x-secondary-button x-on:click="$dispatch('close')" class="font-normal">Tutup</x-secondary-button>
        </div>
    </div>

    <script>
        function priceCalc(orderDetail) {
            return {
                orderDetail,

                getVariant() {
                    if (this.orderDetail.product_variant) return this.orderDetail.product_variant;
                    if (this.orderDetail.product && this.orderDetail.product.product_variant && this.orderDetail.product
                        .product_variant.length > 0) return this.orderDetail.product.product_variant[0];
                    return null;
                },

                getFinalBase() {
                    let v = this.getVariant();
                    let base = v ? Number(v.price || 0) : Number(this.orderDetail.price || 0);
                    if (v && Array.isArray(v.bulk_prices) && v.bulk_prices.length > 0) {
                        let qty = Number(this.orderDetail.quantity || 0);
                        let bulk = v.bulk_prices.slice().sort((a, b) => Number(b.min_qty) - Number(a.min_qty)).find(bp =>
                            qty >= Number(bp.min_qty));
                        if (bulk) base = Number(bulk.price || base);
                    }
                    return base;
                },

                get formattedFinalPrice() {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(this.getFinalBase());
                },

                get formattedDiscountAmountRaw() {
                    let base = this.getFinalBase();
                    if (this.orderDetail.discount && this.orderDetail.discount > 0) {
                        return base * (this.orderDetail.discount / 100);
                    }
                    return 0;
                },

                get formattedSubtotal() {
                    let base = this.getFinalBase();
                    let qty = Number(this.orderDetail.quantity || 0);
                    let final = base;
                    if (this.orderDetail.discount && this.orderDetail.discount > 0) final = final - (final * (this
                        .orderDetail.discount / 100));
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(final * qty);
                }
            }
        }
    </script>
</x-modal>
