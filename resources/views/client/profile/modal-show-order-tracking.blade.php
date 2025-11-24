<x-modal name="modal-show-order-tracking">
    <div class="max-w-4xl mx-auto p-6" x-data>
        <h1 class="text-2xl font-bold mb-6">Pelacakan Pesanan</h1>

        <!-- Loading Skeleton -->
        <template x-if="!$store.cart.trackingDetail || !$store.cart.orderDetail">
            <div class="space-y-6">
                <div class="h-20 bg-gray-200 animate-pulse rounded-lg"></div>
                <div class="h-32 bg-gray-200 animate-pulse rounded-lg"></div>
                <div class="h-40 bg-gray-200 animate-pulse rounded-lg"></div>
            </div>
        </template>

        <!-- Order Info -->
        <template x-if="$store.cart.trackingDetail && $store.cart.orderDetail">
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold mb-2">Info Pesanan</h2>
                <p><strong>No. Resi:</strong> <span x-text="$store.cart.trackingDetail.waybill_id"></span></p>
                <p><strong>Status:</strong>
                    <span x-text="$store.cart.orderDetail.delivery_state?.name"></span>
                </p>
                <p><strong>Berat:</strong> <span x-text="$store.cart.trackingDetail.weight"></span> gram</p>
            </div>
        </template>

        <!-- Courier Info -->
        <template x-if="$store.cart.trackingDetail && $store.cart.trackingDetail.courier">
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold mb-2">Info Kurir</h2>
                <div class="flex flex-col md:flex-row items-start md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <p><strong>Perusahaan:</strong>
                            <span x-text="$store.cart.trackingDetail.courier.company?.toUpperCase()"></span>
                        </p>

                        <template x-if="$store.cart.trackingDetail.courier.driver_name">
                            <p><strong>Nama Pengemudi:</strong>
                                <span x-text="$store.cart.trackingDetail.courier.driver_name"></span>
                            </p>
                        </template>

                        <template x-if="$store.cart.trackingDetail.courier.driver_phone">
                            <p><strong>No. Telepon:</strong>
                                <span x-text="$store.cart.trackingDetail.courier.driver_phone"></span>
                            </p>
                        </template>

                        <template x-if="$store.cart.trackingDetail.courier.driver_plate_number">
                            <p><strong>No. Plat:</strong>
                                <span x-text="$store.cart.trackingDetail.courier.driver_plate_number"></span>
                            </p>
                        </template>
                    </div>

                    <template x-if="$store.cart.trackingDetail.courier.driver_photo_url">
                        <div class="w-24 h-24 flex-shrink-0">
                            <img :src="$store.cart.trackingDetail.courier.driver_photo_url" alt="Foto Kurir"
                                class="w-24 h-24 rounded-full object-cover">
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Address -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <template x-if="$store.cart.trackingDetail && $store.cart.trackingDetail.origin">
                <div class="bg-white shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Asal</h2>
                    <p><strong>Nama:</strong> <span x-text="$store.cart.trackingDetail.origin.contact_name"></span></p>
                    <p><strong>Alamat:</strong> <span x-text="$store.cart.trackingDetail.origin.address"></span></p>
                </div>
            </template>
            <template x-if="$store.cart.trackingDetail && $store.cart.trackingDetail.destination">
                <div class="bg-white shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Tujuan</h2>
                    <p><strong>Nama:</strong> <span x-text="$store.cart.trackingDetail.destination.contact_name"></span>
                    </p>
                    <p><strong>Alamat:</strong> <span x-text="$store.cart.trackingDetail.destination.address"></span>
                    </p>
                </div>
            </template>
        </div>

        <!-- Tracking History -->
        <template x-if="$store.cart.trackingDetail">
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-4">Riwayat Pelacakan</h2>
                <template x-if="$store.cart.trackingDetail.history && $store.cart.trackingDetail.history.length">
                    <ul class="space-y-4">
                        <template x-for="(event, index) in $store.cart.trackingDetail.history" :key="index">
                            <li class="border-l-4 pl-4"
                                :class="{
                                    'border-green-500': index === $store.cart.trackingDetail.history.length - 1,
                                    'border-red-500': event.status === 'courier_not_found' && index !== $store.cart
                                        .trackingDetail.history.length - 1,
                                    'border-gray-400': event.status !== 'courier_not_found' && index !== $store.cart
                                        .trackingDetail.history.length - 1
                                }">
                                <p class="text-sm text-gray-600"
                                    x-text="new Date(event.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric'}) + ', ' + 
                                        new Date(event.updated_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'">
                                </p>
                                <p class="font-medium"
                                    x-text="$store.statusMap[event.status] || event.status.replaceAll('_',' ')">
                                </p>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="!$store.cart.trackingDetail.history || !$store.cart.trackingDetail.history.length">
                    <p class="text-gray-500">Belum ada riwayat pelacakan.</p>
                </template>
            </div>
        </template>

        <div class="text-end self-end mt-6">
            <x-secondary-button x-on:click="$dispatch('close')" class="font-normal">
                {{ __('Tutup') }}
            </x-secondary-button>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('statusMap', {
                confirmed: 'Pesanan dikonfirmasi. Mencari kurir terdekat untuk pickup.',
                allocated: 'Kurir sudah dialokasikan. Menunggu pickup.',
                picking_up: 'Kurir dalam perjalanan untuk pickup.',
                picked: 'Barang sudah diambil kurir dan siap dikirim.',
                dropping_off: 'Barang dalam perjalanan ke alamat tujuan.',
                return_in_transit: 'Pesanan sedang dalam perjalanan kembali ke pengirim.',
                on_hold: 'Pengiriman sedang ditahan. Akan dilanjutkan setelah masalah terselesaikan.',
                delivered: 'Barang sudah diterima.',
                rejected: 'Pengiriman ditolak. Silakan hubungi Biteship untuk informasi lebih lanjut.',
                courier_not_found: 'Pengiriman dibatalkan karena tidak ada kurir tersedia.',
                returned: 'Barang berhasil dikembalikan.',
                cancelled: 'Pesanan dibatalkan.',
                disposed: 'Barang berhasil dimusnahkan.'
            });
        });
    </script>
</x-modal>
