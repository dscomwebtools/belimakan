<x-modal name="modal-upload-bukti-bayar">
    <div class="p-4">
        <h1 class="text-2xl mb-4 font-bold text-center">Bukti Pembayaran</h1>

        {{-- If masih bisa upload --}}
        <template x-if="$store.cart.orderDetail.delivery_state_id <= 2">
            <form :action="`/order/${$store.cart.orderDetail.id}`"
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="flex flex-col mb-4">
                    <label for="bukti_bayar" class="font-semibold mb-2">Upload bukti bayar</label>
                    <input type="file" name="bukti_bayar" id="bukti_bayar" required>
                </div>

                <template x-if="$store.cart.orderDetail.bukti_pembayaran">
                    <div class="mt-1">
                        <p class="text-warning text-xs">Pilih file baru jika ingin mengubah bukti bayar saat ini</p>
                        <div class="mt-4 mb-1">Bukti bayar saat ini</div>
                        <img :src="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" 
                             alt="current bukti pembayaran" 
                             width="150">
                    </div>
                </template>

                <div class="tooltip mt-4" data-tip="Simpan">
                    <x-primary-button>
                        Submit <i class="fa fa-paper-plane"></i>
                    </x-primary-button>
                </div>
            </form>
        </template>

        {{-- Read only --}}
        <template x-if="$store.cart.orderDetail.delivery_state_id > 2">
            <div>
                <template x-if="$store.cart.orderDetail.bukti_pembayaran">
                    <div class="mt-1 text-center">
                        <div class="mb-2 font-semibold">Bukti bayar</div>
                        <a :href="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" target="_blank">
                            <img :src="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" 
                                 alt="bukti pembayaran" 
                                 class="w-40 h-[200px] object-contain rounded shadow mx-auto" />
                        </a>
                    </div>
                </template>
                <template x-if="!$store.cart.orderDetail.bukti_pembayaran">
                    <p class="text-center text-gray-500">Tidak ada bukti pembayaran</p>
                </template>

                <div class="text-end mt-4">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Tutup
                    </x-secondary-button>
                </div>
            </div>
        </template>
    </div>
</x-modal>
