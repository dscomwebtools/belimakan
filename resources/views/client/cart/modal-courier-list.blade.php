<x-modal name="courier-list">
    <div class="p-4 relative">
        <h1 class="text-xl mb-4 font-bold">Pilih Metode Pengiriman</h1>

        <!-- Manual Section -->
        <label class="text-lg font-bold mb-2" for="manual">Manual</label>
        <div class="flex flex-col gap-2">
            <input type="text" id="manual" class="my-input flex-1" x-ref="shippingManual"
                placeholder="Contoh: COD, Ambil di Toko, Baraka, Kalog, Mex, Gojek, Cargo, Lainnya">
            <button class="btn btn-primary"
                @click.prevent="$store.checkout.setCourierSelected($refs.shippingManual.value, 'manual'); $dispatch('close');">
                Pilih Manual
            </button>
        </div>

        <!-- Sistem Section -->
        <h2 class="text-lg font-bold mb-2 mt-10">Melalui Sistem</h2>

        <div class="h-96 overflow-auto relative space-y-8">

            <div x-show="$store.checkout.regular_loading || $store.checkout.instant_loading"
                class="absolute inset-0 flex items-center justify-center bg-white/80 z-20">
                <div class="flex flex-col items-center gap-3">
                    <i class="fa fa-spinner fa-spin text-4xl text-primary"></i>
                    <span class="text-base font-semibold">Mengambil data kurir...</span>
                </div>
            </div>


            <!-- Reguler Section -->
            <div x-show="!$store.checkout.regular_loading && !$store.checkout.instant_loading" class="relative">
                <h2 class="text-lg font-bold mb-2">Reguler</h2>

                <!-- Loading State Reguler -->
                <div x-show="$store.checkout.regular_loading"
                    class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                    <div class="flex flex-col items-center gap-4 p-6 bg-white rounded-2xl shadow-lg">
                        <i class="fa fa-spinner fa-spin text-4xl text-primary"></i>
                        <span class="text-base font-semibold text-gray-700">Mengambil data kurir reguler...</span>
                        <div class="w-48 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-primary animate-pulse"></div>
                        </div>
                        <p class="text-xs text-gray-400 italic">Mohon tunggu sebentar, data sedang dimuat</p>
                    </div>
                </div>

                <!-- Courier Grouped -->
                <template x-for="(services, courier) in $store.checkout.groupedCourierListRegular"
                    :key="courier">
                    <div class="mb-4 bg-base-100 rounded border">
                        <div class="p-3 font-bold cursor-pointer flex justify-between items-center"
                            @click="services.open = !services.open">
                            <span x-text="courier"></span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div x-show="services.open" class="divide-y">
                            <template x-for="courierRate in services.data" :key="courierRate.courier_service_code">
                                <div @click.prevent="$store.checkout.setCourierSelected(courierRate, 'regular'); $dispatch('close');"
                                    class="p-3 hover:bg-base-200 cursor-pointer">
                                    <div class="flex justify-between">
                                        <span x-text="courierRate.courier_service_name"></span>
                                        <span class="font-bold"
                                            x-text="new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(courierRate.price)">
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500"
                                        x-text="`Estimasi: ${$store.checkout.translateDuration(courierRate.duration)}`">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Instant Section -->
            <div x-show="!$store.checkout.regular_loading && !$store.checkout.instant_loading" class="relative">
                <h2 class="text-lg font-bold mb-2">Instant</h2>

                <!-- Loading State Instant -->
                <div x-show="$store.checkout.instant_loading && $store.checkout.profileHasLocation"
                    class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                    <div
                        class="flex flex-col items-center gap-4 p-6 bg-white rounded-2xl shadow-lg border border-green-200">
                        <i class="fa fa-spinner fa-spin text-4xl text-green-600"></i>
                        <span class="text-base font-semibold text-green-700">Mengambil data kurir instant...</span>
                        <div class="w-48 h-1.5 bg-green-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 animate-pulse"></div>
                        </div>
                        <p class="text-xs text-gray-400 italic">Mohon tunggu sebentar, data sedang dimuat</p>
                    </div>
                </div>

                <!-- Kalau user belum isi koordinat -->
                <div x-show="!$store.checkout.profileHasLocation"
                    class="p-3 bg-yellow-100 rounded text-sm text-yellow-800">
                    Untuk menggunakan layanan pengiriman <b>Instant</b>, silakan lengkapi koordinat lokasi (Latitude &
                    Longitude) di profil Anda.
                </div>

                <!-- ⚠️ Kalau tidak ada kurir instant yang tersedia -->
                <div x-show="$store.checkout.profileHasLocation && !$store.checkout.instant_loading && Object.keys($store.checkout.groupedCourierListInstant || {}).length === 0"
                    class="p-3 bg-red-100 rounded text-sm text-red-700 border border-red-300">
                    <b>Tidak ada kurir instant tersedia.</b><br>
                    Lokasi tujuan kemungkinan terlalu jauh dari titik pengiriman, atau layanan belum mencakup area
                    tersebut.
                </div>

                <!-- Courier Grouped Instant -->
                <template x-if="$store.checkout.profileHasLocation">
                    <template x-for="(services, courier) in $store.checkout.groupedCourierListInstant"
                        :key="courier">
                        <div class="mb-4 bg-green-50 rounded border border-green-200">
                            <div class="p-3 font-bold cursor-pointer flex justify-between items-center text-green-700"
                                @click="services.open = !services.open">
                                <span x-text="courier"></span>
                                <i class="fa fa-chevron-down"></i>
                            </div>
                            <div x-show="services.open" class="divide-y">
                                <template x-for="courierRate in services.data" :key="courierRate.courier_service_code">
                                    <div @click.prevent="$store.checkout.setCourierSelected(courierRate, 'instant'); $dispatch('close');"
                                        class="p-3 hover:bg-green-100 cursor-pointer">
                                        <div class="flex justify-between">
                                            <span x-text="courierRate.courier_service_name"></span>
                                            <span class="font-bold"
                                                x-text="new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(courierRate.price)">
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500"
                                            x-text="`Estimasi: ${$store.checkout.translateDuration(courierRate.duration)}`">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</x-modal>

<!-- Notifikasi Error -->
<div x-show="$store.checkout.showNotifFailed" x-transition:leave.duration.500ms
    class="toast toast-top toast-end mt-24 z-50">
    <div role="alert" class="alert alert-error mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span x-text="$store.checkout.message"></span>
    </div>
</div>
