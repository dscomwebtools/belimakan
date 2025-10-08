<x-modal name="modal-show-order-item">
    <div class="p-4">
        <h1 class="text-xl md:text-2xl mb-4 font-bold text-center">Data Detail Order</h1>

        <!-- Alamat -->
        <!-- Alamat & Tanggal Order -->
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
                    x-text="new Date($store.cart.orderDetail.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })">
                </span>
            </div>
        </div>

        <!-- Kurir & Status Pengiriman -->
        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4 text-sm md:text-base">
            <div class="md:w-1/2">
                <h2 class="font-bold text-base md:text-lg">Kurir</h2>
                <div x-show="$store.cart.orderDetail.shipping_method">
                    <span x-text="$store.cart.orderDetail.shipping_method?.courier_name"></span> •
                    <span x-text="$store.cart.orderDetail.shipping_method?.courier_service_name"></span> •
                    Estimasi <span x-text="$store.cart.orderDetail.shipping_method?.duration"></span> •
                    <span
                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.orderDetail.shipping_method?.price)">
                    </span>
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

        <!-- Pembayaran & Status Pembayaran -->
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

        <!-- Delivery Receipt -->
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <h2 class="font-bold text-base md:text-lg mb-3">Resi Pengiriman</h2>

            <div class="space-y-3 text-sm">
                <!-- Nomor Resi -->
                <div class="flex items-center justify-between">
                    <span class="font-semibold">Nomor Resi:</span>
                    <span
                        x-text="$store.cart.orderDetail.receipt?.receipt_number || $store.cart.orderDetail.biteship_waybill || '-'"></span>
                </div>

                <!-- Bukti Resi -->
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

            <!-- Tracking link -->
            <template x-if="$store.cart.orderDetail.biteship_waybill">
                <div class="mt-4 pt-3 border-t text-sm">
                    <a href="#"
                        @click.prevent="
                    $dispatch('open-modal', 'modal-show-order-tracking'); 
                    $store.cart.getTrackingDetail($store.cart.orderDetail.id)
               "
                        class="text-teal-600 hover:underline flex items-center gap-1">
                        <i class="fa fa-eye"></i> Lihat Pengiriman
                    </a>
                </div>
            </template>
        </div>

        <!-- Data Barang -->
        <div class="mb-8 mt-8 border-t pt-4">
            <h2 class="font-bold text-xl md:text-2xl text-center">Data Pesanan</h2>

            <div class="space-y-3">
                <template x-for="(orderDetail, key) in $store.cart.orderDetail.order_detail" :key="key">
                    <div class="border rounded-xl p-3 bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-center mb-2">
                            {{-- <div class="text-sm font-semibold text-gray-700 dark:text-gray-100">
                                <span>No.</span> <span x-text="++key"></span>
                            </div> --}}
                            <div
                                class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                Qty: <span x-text="orderDetail.quantity"></span>
                            </div>
                        </div>

                        <div class="text-base font-semibold text-gray-900 dark:text-gray-50">
                            <span x-text="orderDetail.product.name"></span>
                        </div>

                        <template
                            x-if="orderDetail.product_variant && orderDetail.product_variant.product_detail.length > 0">
                            <div class="text-xs text-gray-500 mt-1">
                                Varian:
                                <template x-for="(detail, index) in orderDetail.product_variant.product_detail">
                                    <span>
                                        <span x-text="detail.variant_value.value"></span><span
                                            x-text="index < orderDetail.product_variant.product_detail.length - 1 ? ', ' : ''"></span>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-3 text-sm">
                            <div>
                                <div class="text-gray-400">Harga</div>
                                <div class="font-medium"
                                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                            .format(orderDetail.price)">
                                </div>
                            </div>

                            <div>
                                <div class="text-gray-400">Diskon</div>
                                <div class="font-medium"
                                    x-text="orderDetail.discount > 0 
                            ? '(' + orderDetail.discount + '%) ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                                .format(orderDetail.price * (orderDetail.discount / 100))
                            : '-'">
                                </div>
                            </div>

                            <div>
                                <div class="text-gray-400">Subtotal</div>
                                <div class="font-semibold text-green-600 dark:text-green-400"
                                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                            .format((orderDetail.price - (orderDetail.price * (orderDetail.discount / 100))) * orderDetail.quantity)">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <!-- Ringkasan Pembayaran -->
        <div class="mb-4 mt-6 bg-white dark:bg-gray-800">
            <h2 class="font-bold text-base md:text-lg mb-3 border-b pb-2 dark:text-gray-100">
                Ringkasan Pembayaran
            </h2>

            <!-- Subtotal Barang -->
            <div class="flex justify-between py-1 text-sm md:text-base">
                <span class="text-gray-600 dark:text-gray-300">Subtotal Barang</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100"
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                .format(
                    $store.cart.orderDetail.order_detail
                        ? $store.cart.orderDetail.order_detail.reduce((sum, d) => {
                            let price = d.price;
                            if (d.discount && d.discount > 0) {
                                price = price - (price * d.discount / 100);
                            }
                            return sum + (price * d.quantity);
                        }, 0)
                        : 0
                )">
                </span>
            </div>

            <!-- Ongkos Kirim -->
            <div class="flex justify-between py-1 text-sm md:text-base">
                <span class="text-gray-600 dark:text-gray-300">Ongkos Kirim</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100"
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                .format($store.cart.orderDetail.shipping_method?.price || 0)">
                </span>
            </div>

            <!-- Voucher -->
            <template x-if="$store.cart.orderDetail.coupon_code">
                <div class="flex justify-between py-1 text-sm md:text-base text-red-600">
                    <span>
                        Voucher (<span x-text="$store.cart.orderDetail.coupon_code"></span>)
                    </span>
                    <span>
                        -<span
                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                        .format($store.cart.orderDetail.coupon_discount)">
                        </span>
                    </span>
                </div>
            </template>

            <hr class="my-2 border-gray-200 dark:border-gray-700" />

            <!-- Total -->
            <div class="flex justify-between pt-1 text-base font-bold">
                <span>Total Pembayaran</span>
                <span
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                .format(
                    (
                        ($store.cart.orderDetail.order_detail
                            ? $store.cart.orderDetail.order_detail.reduce((sum, d) => {
                                let price = d.price;
                                if (d.discount && d.discount > 0) {
                                    price = price - (price * d.discount / 100);
                                }
                                return sum + (price * d.quantity);
                            }, 0)
                            : 0
                        ) 
                        + ($store.cart.orderDetail.shipping_method?.price || 0)
                        - ($store.cart.orderDetail.coupon_discount || 0)
                    )
                )">
                </span>
            </div>
        </div>

        <div class="text-end self-end">
            <x-secondary-button x-on:click="$dispatch('close')" class="font-normal">
                {{ __('Tutup') }}
            </x-secondary-button>
        </div>

    </div>
</x-modal>
