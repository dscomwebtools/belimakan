<x-modal name="modal-show-order-item">
    <div class="p-4">
        <h1 class="text-2xl mb-4 font-bold text-center">Data Detail Order</h1>

        <!-- Alamat -->
        <!-- Alamat & Tanggal Order -->
        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4">
            <div class="md:w-1/2">
                <h2 class="font-bold text-lg">Alamat Pengiriman</h2>
                <span x-text="$store.cart.orderDetail.order_address?.name"></span> •
                <span x-text="$store.cart.orderDetail.order_address?.recipient"></span> •
                <span x-text="$store.cart.orderDetail.order_address?.hp"></span>
                <address x-text="$store.cart.orderDetail.order_address?.address"></address>
                <span x-text="$store.cart.orderDetail.order_address?.area_name"></span>
            </div>

            <div class="md:w-1/2 md:text-end">
                <h2 class="font-bold text-lg">Tanggal Order</h2>
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
        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4">
            <div class="md:w-1/2">
                <h2 class="font-bold text-lg">Kurir</h2>
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
                <h2 class="font-bold text-lg">Status Pengiriman</h2>
                <span x-text="$store.cart.orderDetail.delivery_state?.name"></span>
            </div>
        </div>

        <!-- Pembayaran & Status Pembayaran -->
        <div class="flex flex-col md:flex-row md:justify-between mb-4 gap-4">
            <div class="md:w-1/2">
                <h2 class="font-bold text-lg">Pembayaran</h2>
                <span x-text="$store.cart.orderDetail.payment_method"></span>
                <span x-text="$store.cart.orderDetail.bank_account?.bank_name"></span>
                <div class="relative inline-block">
                    <span x-text="$store.cart.orderDetail.bank_account?.account_number"></span>
                    <button @click="$store.cart.copyToClipboard($store.cart.orderDetail.bank_account?.account_number)"
                        class="tooltip" data-tip="Copy nomor rekening">
                        <span class="far fa-copy"></span>
                    </button>

                    <span x-show="$store.cart.showNotifSuccess" x-text="$store.cart.message"
                        class="absolute left-[100%] top-full -translate-x-1/2 mt-2
           bg-white border border-gray-300 text-black 
           text-sm px-3 py-1 rounded shadow-lg whitespace-nowrap"></span>
                </div>
            </div>

            <div class="md:w-1/2 md:text-end">
                <h2 class="font-bold text-lg">Status Pembayaran</h2>
                <span x-text="$store.cart.orderDetail.is_paid == 0 ? 'Belum Dibayar' : 'Lunas'"></span>
            </div>
        </div>

        <!-- Delivery Receipt -->
        <div x-if="$store.cart.orderDetail.receipt">
            <div class="mb-4">
                <h2 class="font-bold text-lg">Resi Pengiriman</h2>
                <div class="flex flex-col gap-2">
                    <div>
                        <span class="font-semibold">Nomor Resi:</span>
                        <span x-text="$store.cart.orderDetail.receipt?.receipt_number || '-'"></span>
                    </div>
                    <div>
                        <span class="font-semibold">Bukti Resi:</span>
                        <template x-if="$store.cart.orderDetail.receipt?.receipt_img">
                            <div class="tooltip" data-tip="Download Resi">
                                <a :href="$store.cart.orderDetail.receipt.receipt_img_url"
                                    :download="'resi-' + $store.cart.orderDetail.id + '.jpg'" class="inline-block">
                                    <i class="fa fa-download text-blue-600"></i>
                                </a>
                            </div>
                        </template>
                        <template x-if="!$store.cart.orderDetail.receipt?.receipt_img">
                            <span class="text-gray-500">Resi belum dibuat</span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Barang -->
        <div class="mb-4">
            <h2 class="font-bold text-lg text-center">Data Barang</h2>

            <div class="overflow-auto max-h-40">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>qty</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(orderDetail, key) in $store.cart.orderDetail.order_detail">
                            <tr class="hover" :key="key">
                                <td x-text="++key"></td>
                                <td>
                                    <span x-text="orderDetail.product.name"></span>
                                    <template
                                        x-if="orderDetail.product_variant && orderDetail.product_variant.product_detail.length > 0">
                                        <span>
                                            <br />
                                            <span>
                                                (<template
                                                    x-for="(detail, index) in orderDetail.product_variant.product_detail">
                                                    <span>
                                                        <span x-text="detail.variant_value.value"></span>
                                                        <span
                                                            x-text="index < orderDetail.product_variant.product_detail.length - 1 ? ', ' : ''"></span>
                                                    </span>
                                                </template>)
                                            </span>
                                        </span>
                                    </template>
                                </td>
                                <td x-text="orderDetail.quantity"></td>
                                <td
                                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(orderDetail.price)">
                                </td>
                                <td
                                    x-text="orderDetail.discount > 0 
                                            ? '(' + orderDetail.discount + '%) ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(orderDetail.price * (orderDetail.discount / 100))
                                            : '-'">
                                </td>
                                <td
                                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(orderDetail.price - (orderDetail.price * (orderDetail.discount/100)) * orderDetail.quantity)">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <!-- Ringkasan Pembayaran -->
        <div class="mb-4">
            <h2 class="font-bold text-lg">Ringkasan Pembayaran</h2>

            <!-- Subtotal Barang -->
            <div class="flex justify-between">
                <span>Subtotal Barang</span>
                <span
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
            <div class="flex justify-between">
                <span>Ongkos Kirim</span>
                <span
                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                .format($store.cart.orderDetail.shipping_method?.price || 0)">
                </span>
            </div>

            <!-- Voucher -->
            <template x-if="$store.cart.orderDetail.coupon_code">
                <div class="flex justify-between text-red-600">
                    <span>Voucher (<span x-text="$store.cart.orderDetail.coupon_code"></span>)</span>
                    <span>
                        -<span
                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                        .format($store.cart.orderDetail.coupon_discount)">
                        </span>
                    </span>
                </div>
            </template>
        </div>

        <!-- Total Bayar -->
        <div class="flex justify-between mb-4">
            <h2 class="font-bold text-lg">Total Bayar</h2>
            <span
                x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                        .format($store.cart.orderDetail.total)">
            </span>
        </div>
        <div class="text-end self-end">
            <x-secondary-button x-on:click="$dispatch('close')" class="font-normal">
                {{ __('Tutup') }}
            </x-secondary-button>
        </div>

    </div>
</x-modal>
