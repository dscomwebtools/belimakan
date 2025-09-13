<x-client-layout>
    <div class="cart-body p-2" x-data>
        <h1 class="text-xl md:text-2xl lg:text-3xl mb-6 font-bold pl-6">Atur Pengiriman</h1>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">

                <div x-show="$store.cart.items.length">
                    <div class="cart-item bg-base-200 p-4 rounded-t border-b border-primary">
                        <div class="text-base md:text-xl font-bold mb-1">List Barang Belanjaanmu</div>
                    </div>
                </div>

                <template x-for="item in $store.cart.items">
                    <div class="cart-item bg-base-200 p-4 rounded-t border-b border-primary" :key="item.product.id"
                        x-init="(item.product_variant ? item.product_variant.stock <= 0 : item.product.product_variant[0].stock <= 0) ? $store.cart.isThereItemWithNoStock = true: $store.cart.isThereItemWithNoStock = false">
                        <div class="flex flex-row">
                            <img :src="item.product.product_pictures.length ? `/storage/${item.product.product_pictures[0].path}` :
                                ''"
                                alt="product-image"
                                class="rounded mx-2 w-full max-h-[80px] md:max-h-[160px] max-w-[80px] md:max-w-[160px]">

                            <div class="flex flex-col">
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
                                        class="text-red-500 text-xs animate-pulse text-sm md:text-base mt-2">Stok Habis
                                    </div>
                                </div>
                                <div class="price flex flex-col text-sm md:text-base">
                                    <div>
                                        <span x-text="item.quantity"></span>
                                        {{-- JIKA PRODUCT VARIANT ID TIDAK ADA BERARTI TIDAK ADA VARIAN MAKA AMBIL DARI
                                    RELASI PRODUCT->PRODUCT_VARAINT, JIKA ADA YA AMBIL DARI PRODUCT_VARIANT GA USAH DARI
                                    PRODUCT KARENA BERARTI PRODUCT NYA ADA VARIANT --}}
                                        <template x-if="item.product_variant">
                                            <span
                                                x-text="'x ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.product.promo ? item.product_variant.price - (item.product_variant.price * (item.product.promo.discount / 100)) : item.product_variant.price)"></span>
                                        </template>
                                        <template x-if="!item.product_variant">
                                            <span
                                                x-text="item.product.product_variant ? 'x ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.product.promo ? item.product.product_variant[0].price - (item.product.product_variant[0].price * (item.product.promo.discount / 100)) : item.product.product_variant[0].price) : ''"></span>
                                        </template>
                                    </div>
                                    <template x-if="item.product.promo">
                                        <template x-if="item.product_variant">
                                            <div class="line-through"
                                                x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.product_variant.price)">
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="!$store.cart.items.length">
                    <div class="cart-item bg-base-200 p-4 rounded-t border-b border-primary">
                        Keranjang belanjamu masih kosong !! Cek produk kami <a href="{{ route('client.products') }}"
                            class="text-bold text-green-500">disini</a>
                    </div>
                </div>
                {{-- END LIST PRODUCT --}}

                <div class="flex flex-col gap-2 flex-1 mt-10">
                    <div class="address bg-base-200 p-6 rounded mb-4">
                        <div class="text-base md:text-xl font-bold mb-1">Alamat Pengiriman</div>
                        {{-- SET ADDRESS DI ALPINE JS --}}
                        <div x-init="$store.cart.setAddress(
                            '{{ $address->id }}', '{{ $address->name }}', '{{ $address->recipient }}', '{{ $address->hp }}', '{{ $address->address }}', '{{ $address->district }}', '{{ $address->city }}', '{{ $address->province }}', '{{ $address->postal_code }}', '{{ $address->area_id }}'
                        )">
                        </div>

                        <div class="flex flex-col gap-4">
                            <div class="flex-1 capitalize">
                                <div class="">
                                    <i class="fa fa-map-location-dot mr-2"></i>
                                    <span x-text="$store.cart.addressSelected.name"></span> •
                                    <span x-text="$store.cart.addressSelected.recipient"></span> •
                                    <span x-text="$store.cart.addressSelected.hp"></span>
                                </div>
                                <div class="mt-4">
                                    <span x-text="$store.cart.addressSelected.address"></span>
                                </div>
                                <div class="mt-4">
                                    Kec. <span x-text="$store.cart.addressSelected.district"></span>
                                </div>
                                <div class="">
                                    Kota/Kabupaten <span x-text="$store.cart.addressSelected.city"></span>
                                </div>
                                <div class="">
                                    Prov. <span x-text="$store.cart.addressSelected.province"></span>
                                </div>
                                <div class="">
                                    Kode Pos <span x-text="$store.cart.addressSelected.postal_code"></span>
                                </div>
                            </div>
                            <div class="flex-1 self-start tooltip tooltip-right" data-tip="Ubah alamat">
                                <select name="addresses" id="addresses" class="my-input"
                                    @change="$store.cart.setAddress(
                                        $event.target.value,
                                        $event.target.options[$event.target.selectedIndex].dataset.name,
                                        $event.target.options[$event.target.selectedIndex].dataset.recipient,
                                        $event.target.options[$event.target.selectedIndex].dataset.hp,
                                        $event.target.options[$event.target.selectedIndex].dataset.address,
                                        $event.target.options[$event.target.selectedIndex].dataset.district,
                                        $event.target.options[$event.target.selectedIndex].dataset.city,
                                        $event.target.options[$event.target.selectedIndex].dataset.province,
                                        $event.target.options[$event.target.selectedIndex].dataset.postal_code,
                                        $event.target.options[$event.target.selectedIndex].dataset.area_id,
                                    )">
                                    @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}" data-name="{{ $address->name }}"
                                            data-recipient="{{ $address->recipient }}" data-hp="{{ $address->hp }}"
                                            data-address="{{ $address->address }}"
                                            data-district="{{ $address->district }}" data-city="{{ $address->city }}"
                                            data-province="{{ $address->province }}"
                                            data-postal_code="{{ $address->postal_code }}"
                                            data-area_id="{{ $address->area_id }}">
                                            {{ $address->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- METODE PENGIRIMAN --}}
                    <div class="courier bg-base-200 p-6 rounded mb-4 flex items-center gap-4 flex-col lg:flex-row">
                        <button class="btn btn-primary lg:self-start"
                            @click.prevent="$dispatch('open-modal', 'courier-list'); $store.cart.setCourierList();">
                            <i class="fa fa-truck"></i> Pengiriman
                        </button>
                        <div x-show="Object.keys($store.cart.courierSelected).length == 0" class="text-base-content">
                            Anda belum memilih metode pengiriman !
                        </div>
                        <div x-show="typeof $store.cart.courierSelected == 'string'"
                            class="capitalize font-bold flex flex-col sm:flex-row w-full items-center justify-evenly gap-2">
                            <div class="flex flex-col items-center">
                                <div x-text="`${$store.cart.courierSelected}`">
                                </div>
                            </div>

                            <div class="flex flex-col items-center">
                                <div>Manual</div>
                            </div>
                        </div>
                        <div x-show="typeof $store.cart.courierSelected == 'object' && Object.keys($store.cart.courierSelected).length > 0"
                            class="capitalize font-bold flex flex-col sm:flex-row w-full items-center justify-evenly gap-2">
                            <div class="flex flex-col items-center">
                                <div
                                    x-text="`${$store.cart.courierSelected.courier_name} - ${$store.cart.courierSelected.courier_service_name}`">
                                </div>
                                <div
                                    x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($store.cart.courierSelected.price)">
                                </div>
                            </div>

                            <div class="flex flex-col items-center">
                                <div>Estimasi</div>
                                <div x-text="$store.cart.courierSelected.duration">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('client.cart.modal-courier-list')
                    {{-- END METODE PENGIRIMAN --}}

                    {{-- METODE PEMBAYARAN --}}
                    <div class="courier bg-base-200 p-6 rounded mb-4 flex flex-col gap-5">
                        <div class="flex flex-col lg:flex-row items-center gap-4">
                            <button class="btn btn-primary lg:self-start" @click.prevent="$refs.transferBtn.focus()">
                                <i class="fa fa-money-bill-wave"></i> Pembayaran
                            </button>
                            <div x-show="!$store.cart.paymentMethod" class="text-base-content text-center">
                                Pilih metode pembayaran !
                            </div>
                            <div class="flex gap-4 justify-center md:justify-end flex-1 flex-col sm:flex-row">
                                <button x-ref="transferBtn"
                                    :class="`btn ${$store.cart.paymentMethod=='Transfer' ? 'btn-primary' : 'bg-gray-300'} shadow-md`"
                                    @click.prevent="$store.cart.setPaymentMethod('Transfer')"><i
                                        class="fa fa-credit-card"></i> Transfer <i
                                        x-show="$store.cart.paymentMethod=='Transfer'"
                                        class="fa fa-circle-check text-[#FFFFFF]"></i>
                                </button>
                                <button
                                    :class="`btn ${$store.cart.paymentMethod=='Cash' ? 'btn-primary' : 'bg-gray-300'} shadow-md`"
                                    @click.prevent="$store.cart.setPaymentMethod('Cash')"><i
                                        class="fa fa-money-bills"></i>
                                    Cash <i x-show="$store.cart.paymentMethod=='Cash'"
                                        class="fa fa-circle-check text-[#FFFFFF]"></i>
                                </button>
                            </div>

                        </div>
                        {{-- Bank List --}}
                        <div x-show="$store.cart.paymentMethod=='Transfer'" class="flex flex-col gap-2 w-full">

                            <!-- Dropdown pilih bank -->
                            <select name="bank_account_id" id="bank_account_id"
                                class="my-input w-full border rounded p-2"
                                @change="$store.cart.setBankSelected(
                                    $event.target.value,
                                    $event.target.options[$event.target.selectedIndex].dataset.bank_name,
                                    $event.target.options[$event.target.selectedIndex].dataset.account_name,
                                    $event.target.options[$event.target.selectedIndex].dataset.account_number
                                )">
                                <option value="">Pilih Bank</option>
                                @foreach ($bank_accounts as $bank_account)
                                    <option value="{{ $bank_account->id }}""
                                        data-bank_name="{{ $bank_account->bank_name }}"
                                        data-account_name="{{ $bank_account->account_name }}"
                                        data-account_number="{{ $bank_account->account_number }}">
                                        {{ $bank_account->bank_name }} - {{ $bank_account->account_name }} -
                                        {{ $bank_account->account_number }}
                                    </option>
                                @endforeach
                            </select>

                            <div x-show="$store.cart.bankSelected.id"
                                class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white border rounded-lg p-4 shadow-sm gap-4">

                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <!-- Text Info -->
                                    <div>
                                        <div class="font-bold text-gray-800"
                                            x-text="$store.cart.bankSelected.bank_name"></div>
                                        <div class="text-gray-600"
                                            x-text="$store.cart.bankSelected.account_name + ' - ' + $store.cart.bankSelected.account_number">
                                        </div>
                                    </div>
                                </div>

                                <button type="button"
                                    @click="$store.cart.copyToClipboard($store.cart.bankSelected.account_number)"
                                    class="flex items-center gap-1 border px-3 py-1 rounded text-sm hover:bg-gray-100 self-end sm:self-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4">
                                        <path
                                            d="M480 400L288 400C279.2 400 272 392.8 272 384L272 128C272 119.2 279.2 112 288 112L421.5 112C425.7 112 429.8 113.7 432.8 116.7L491.3 175.2C494.3 178.2 496 182.3 496 186.5L496 384C496 392.8 488.8 400 480 400zM288 448L480 448C515.3 448 544 419.3 544 384L544 186.5C544 169.5 537.3 153.2 525.3 141.2L466.7 82.7C454.7 70.7 438.5 64 421.5 64L288 64C252.7 64 224 92.7 224 128L224 384C224 419.3 252.7 448 288 448zM160 192C124.7 192 96 220.7 96 256L96 512C96 547.3 124.7 576 160 576L352 576C387.3 576 416 547.3 416 512L416 496L368 496L368 512C368 520.8 360.8 528 352 528L160 528C151.2 528 144 520.8 144 512L144 256C144 247.2 151.2 240 160 240L176 240L176 192L160 192z" />
                                    </svg>
                                    Salin
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- END METODE PEMBAYARAN --}}
                </div>

            </div>

            {{-- RINGKASAN BELANJA --}}
            <div class="rounded bg-base-200 p-4 h-fit sticky top-24 bottom-16" x-data="{ showSummary: false }">

                <!-- Header -->
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-base md:text-xl font-bold">Ringkasan Belanja</h2>

                    <!-- Tombol detail hanya mobile -->
                    <button class="btn btn-xs btn-outline md:hidden" @click="showSummary = !showSummary">
                        <span x-show="!showSummary">Detail <i class="fa fa-chevron-down ml-1"></i></span>
                        <span x-show="showSummary">Tutup <i class="fa fa-chevron-up ml-1"></i></span>
                    </button>
                </div>

                <!-- Bagian collapsible (mobile) -->
                <div class="flex flex-col gap-2 md:block"
                    :class="{ 'hidden': !showSummary, 'flex': showSummary, 'md:flex': true }" x-transition>
                    <div class="flex justify-between text-sm md:text-base">
                        <div class="w-32">Total Item</div>
                        <div x-text="$store.cart.totalItem"></div>
                    </div>
                    <div class="flex justify-between text-sm md:text-base">
                        <div class="w-32">Total Berat</div>
                        <div x-text="$store.cart.totalWeight + ' g'"></div>
                    </div>
                    <div class="flex justify-between text-sm md:text-base">
                        <div class="w-32">Subtotal</div>
                        <div
                            x-text="new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format($store.cart.subtotal)">
                        </div>
                    </div>
                    <div class="flex justify-between text-sm md:text-base">
                        <div class="w-32">Ongkir</div>
                        <div
                            x-text="JSON.stringify($store.cart.courierSelected) !== '{}' 
                        ? new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format($store.cart.courierSelected.price || 0) 
                        : '-'">
                        </div>
                    </div>
                    <div class="divider my-0"></div>
                </div>

                <!-- Bagian selalu terlihat (ongkir + total bayar + order) -->
                <div class="mt-2 flex flex-col gap-2">
                    <div class="flex justify-between text-sm md:text-base">
                        <div class="w-32">Total bayar</div>
                        <div
                            x-text="new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format($store.cart.subtotal + ($store.cart.courierSelected.price || 0))">
                        </div>
                    </div>

                    <!-- Tombol ORDER -->
                    <div class="card-actions mb-2 mt-2">
                        <a href="#"
                            :class="`btn btn-primary btn-block font-bold h-[20px] ${!$store.cart.paymentMethod || Object.keys($store.cart.courierSelected).length == 0 || $store.cart.isLoading || $store.cart.totalItem == 0 || ($store.cart.paymentMethod == 'Transfer' ? Object.keys($store.cart.bankSelected).length == 0 : false) || $store.cart.isThereItemWithNoStock ? 'btn-disabled' : ''}`"
                            @click.prevent="$store.cart.submitOrder()">
                            <span x-show="!$store.cart.isLoading">
                                <i class="fa fa-paper-plane"></i>
                                ORDER
                            </span>
                            <span x-show="$store.cart.isLoading">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
