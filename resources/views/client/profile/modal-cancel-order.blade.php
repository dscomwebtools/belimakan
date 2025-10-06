<x-modal name="modal-cancel-order">
    <div class="p-4">
        <h1 class="text-2xl mb-4 font-bold text-center text-red-600">
            Pengajuan Pembatalan Pesanan
        </h1>

        {{-- Jika pesanan masih bisa dibatalkan --}}
        <template x-if="!$store.cart.orderDetail.cancel_status">
            <form :action="`/order/${$store.cart.orderDetail.id}/cancel-request`" method="POST"
                enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf

                <!-- Alasan Pembatalan -->
                <div class="flex flex-col gap-2">
                    <label for="cancel_reason" class="font-semibold text-gray-800">
                        Pilih Alasan Pembatalan
                    </label>
                    <select name="cancel_reason" id="cancel_reason" required
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none transition-all">
                        <option value="">-- Pilih Alasan --</option>
                        <template x-for="reason in $store.cart.cancelReason" :key="reason.code">
                            <option :value="reason.code" x-text="reason.reason"></option>
                        </template>
                    </select>
                </div>

                <!-- Deskripsi Tambahan -->
                <div class="flex flex-col gap-2">
                    <label for="description" class="font-semibold text-gray-800">
                        Keterangan Tambahan (opsional)
                    </label>
                    <textarea name="description" id="description" rows="3" placeholder="Tuliskan alasan tambahan jika diperlukan..."
                        class="border border-gray-300 rounded-lg px-3 py-2 resize-none focus:ring-2 focus:ring-primary focus:outline-none transition-all"></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end mt-4">
                    <x-primary-button class="bg-red-600 hover:bg-red-700 text-white">
                        Ajukan Pembatalan <i class="fa fa-paper-plane ml-2"></i>
                    </x-primary-button>
                </div>
            </form>
        </template>

        <template x-if="$store.cart.orderDetail && $store.cart.orderDetail.cancel_status">
            <form class="flex flex-col gap-4 opacity-75 pointer-events-none select-none">
                <!-- Alasan Pembatalan -->
                <div class="flex flex-col gap-2">
                    <label for="cancel_reason" class="font-semibold text-gray-800">
                        Pilih Alasan Pembatalan
                    </label>
                    <select id="cancel_reason" disabled
                        class="border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
                        <template x-for="reason in $store.cart.cancelReason" :key="reason.code">
                            <option :value="reason.code"
                                :selected="reason.code === $store.cart.orderDetail.cancel_code" x-text="reason.reason">
                            </option>
                        </template>
                    </select>
                </div>

                <!-- Keterangan Tambahan -->
                <div class="flex flex-col gap-2">
                    <label for="description" class="font-semibold text-gray-800">
                        Keterangan Tambahan
                    </label>
                    <textarea id="description" rows="3" disabled
                        class="border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-600 resize-none"
                        x-text="$store.cart.orderDetail.cancel_reason || '-'">
            </textarea>
                </div>

                <!-- Status Pembatalan -->
                <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-3 flex justify-between items-center">
                    <span class="font-semibold text-gray-800">Status Pembatalan:</span>
                    <span class="font-bold text-sm px-3 py-1 rounded-full"
                        :class="{
                            'bg-yellow-100 text-yellow-700': $store.cart.orderDetail.cancel_status === 'pending',
                            'bg-green-100 text-green-700': $store.cart.orderDetail.cancel_status === 'approved',
                            'bg-red-100 text-red-700': $store.cart.orderDetail.cancel_status === 'rejected'
                        }"
                        x-text="$store.cart.orderDetail.cancel_status.toUpperCase()">
                    </span>
                </div>

                <!-- Tombol Tutup -->
                <div class="text-end mt-4 pointer-events-auto opacity-100">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Tutup
                    </x-secondary-button>
                </div>
            </form>
        </template>


        <!-- Jika sudah pernah diajukan pembatalan -->
        {{-- <template x-if="$store.cart.orderDetail && $store.cart.orderDetail.cancel_status">
            <div class="text-center py-6 space-y-3">
                <div class="flex justify-center">
                    <i class="fa fa-info-circle text-blue-500 text-4xl"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Kamu sudah mengajukan pembatalan pesanan ini
                </h2>
                <p class="text-sm text-gray-600">
                    Status saat ini:
                    <span class="font-semibold"
                        :class="{
                            'text-yellow-500': $store.cart.orderDetail.cancel_status === 'pending',
                            'text-green-600': $store.cart.orderDetail.cancel_status === 'approved',
                            'text-red-600': $store.cart.orderDetail.cancel_status === 'rejected'
                        }"
                        x-text="$store.cart.orderDetail.cancel_status.toUpperCase()">
                    </span>
                </p>
                <div class="text-end mt-4">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Tutup
                    </x-secondary-button>
                </div>
            </div>
        </template> --}}

        {{-- Jika pesanan sudah tidak bisa dibatalkan --}}
        {{-- <template x-if="$store.cart.orderDetail.delivery_state_id > 2">
            <div class="text-center py-6">
                <i class="fa fa-ban text-5xl text-gray-400 mb-3"></i>
                <p class="text-gray-600 text-sm md:text-base font-medium">
                    Pesanan sudah dalam proses pengiriman atau selesai, sehingga tidak dapat dibatalkan.
                </p>
                <div class="text-end mt-4">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Tutup
                    </x-secondary-button>
                </div>
            </div>
        </template> --}}
    </div>
</x-modal>
