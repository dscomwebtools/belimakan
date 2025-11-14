<x-modal name="modal-upload-bukti-bayar">
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold text-center text-gray-800">💳 Bukti Pembayaran</h1>

        {{-- INFORMASI NOMINAL --}}
        <div class="bg-gray-50 border rounded-xl p-4 space-y-3">

            {{-- TOTAL PEMBAYARAN --}}
            <div class="flex items-center justify-between" x-data="{ copiedTotal: false }">
                <div>
                    <div class="font-semibold text-gray-800">Total Pembayaran</div>
                    <div class="font-mono text-gray-700 text-lg">
                        Rp <span x-text="$store.cart.orderDetail.total.toLocaleString()"></span>
                    </div>
                </div>

                <button
                    @click="
                        navigator.clipboard.writeText($store.cart.orderDetail.total);
                        copiedTotal = true;
                        setTimeout(()=> copiedTotal=false, 2000);
                    "
                    class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-100">
                    <span x-text="copiedTotal ? 'Tersalin!' : 'Copy'"></span>
                </button>
            </div>

            {{-- ONGKIR (HANYA MANUAL) --}}
            <template x-if="$store.cart.orderDetail.shipping_method_manual_fee">
                <div class="flex items-center justify-between" x-data="{ copiedOngkir: false }">
                    <div>
                        <div class="font-semibold text-gray-800">Ongkir</div>
                        <div class="font-mono text-gray-700 text-lg">
                            Rp <span
                                x-text="$store.cart.orderDetail.shipping_method_manual_fee.toLocaleString()"></span>
                        </div>
                    </div>

                    <button
                        @click="
                            navigator.clipboard.writeText($store.cart.orderDetail.shipping_method_manual_fee);
                            copiedOngkir = true;
                            setTimeout(()=> copiedOngkir=false, 2000);
                        "
                        class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-100">
                        <span x-text="copiedOngkir ? 'Tersalin!' : 'Copy'"></span>
                    </button>
                </div>
            </template>
        </div>

        {{-- BANK LIST --}}
        <div class="grid gap-4">
            @foreach ($bank_accounts as $bank)
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between bg-white border border-gray-200 rounded-2xl p-4">
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="flex gap-4">
                            @if ($bank->logo)
                                <img src="{{ asset('storage/' . $bank->logo) }}" alt="{{ $bank->bank_name }}"
                                    class="w-[120px] h-[40px] object-contain" />
                            @endif
                        </div>
                        <div x-data="{ copied: false }">
                            <div>
                                <div class="font-semibold text-gray-800 text-base">{{ $bank->bank_name }}</div>
                                <div class="text-gray-600 text-sm">
                                    {{ $bank->account_name }}<br>
                                    <span
                                        class="font-mono tracking-wide text-gray-700">{{ $bank->account_number }}</span>
                                </div>
                            </div>
                            <button
                                @click="
                                    navigator.clipboard.writeText('{{ $bank->account_number }}');
                                    copied=true;
                                    setTimeout(()=>copied=false, 2000);
                                "
                                class="mt-3 sm:mt-0 flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 active:scale-[.97] transition">
                                <span x-text="copied ? 'Tersalin!' : 'Salin Nomor'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- READ ONLY MODE --}}
        <template x-if="$store.cart.orderDetail.delivery_state_id > 3">
            <div class="space-y-6 flex gap-4">

                {{-- Bukti pembayaran utama --}}
                <template x-if="$store.cart.orderDetail.bukti_pembayaran">
                    <div class="text-center">
                        <div class="mb-2 font-semibold text-gray-800">Bukti Bayar</div>
                        <a :href="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" target="_blank">
                            <img :src="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`"
                                class="w-44 h-[220px] object-contain border rounded-xl shadow mx-auto hover:scale-[1.02] transition" />
                        </a>
                    </div>
                </template>

                {{-- Bukti pembayaran ongkir readonly --}}
                <template x-if="$store.cart.orderDetail.bukti_pembayaran_ongkir">
                    <div class="text-center">
                        <div class="mb-2 font-semibold text-gray-800">Bukti Pembayaran Ongkir</div>
                        <a :href="`/storage/${$store.cart.orderDetail.bukti_pembayaran_ongkir}`" target="_blank">
                            <img :src="`/storage/${$store.cart.orderDetail.bukti_pembayaran_ongkir}`"
                                class="w-44 h-[220px] object-contain border rounded-xl shadow mx-auto hover:scale-[1.02] transition" />
                        </a>
                    </div>
                </template>
            </div>
        </template>

        {{-- UPLOAD BUKTI PEMBAYARAN UTAMA --}}
        <template x-if="$store.cart.orderDetail.delivery_state_id <= 3">
            <form :action="`/order/${$store.cart.orderDetail.id}`" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold text-gray-800 mb-2">Upload Bukti Bayar</label>
                    <input type="file" name="bukti_bayar" required
                        class="file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 text-gray-700 text-sm cursor-pointer" />
                </div>

                {{-- CURRENT FILE --}}
                <template x-if="$store.cart.orderDetail.bukti_pembayaran">
                    <div class="pt-2">
                        <p class="text-amber-600 text-sm mb-2">Pilih file baru jika ingin mengganti bukti bayar saat
                            ini</p>
                    </div>
                </template>

                <div class="text-end">
                    <x-primary-button>
                        <i class="fa fa-paper-plane mr-1"></i> Submit
                    </x-primary-button>
                </div>
            </form>
        </template>

        {{-- UPLOAD BUKTI PEMBAYARAN ONGKIR (KHUSUS MANUAL & STATE 4) --}}
        <template
            x-if="$store.cart.orderDetail.shipping_method_manual_fee && $store.cart.orderDetail.delivery_state_id == 4">
            <form :action="`/order/${$store.cart.orderDetail.id}`" method="POST" enctype="multipart/form-data"
                class="space-y-4 pt-6">
                @csrf
                @method('PATCH')

                <h2 class="text-lg font-semibold text-gray-800">📦 Bukti Pembayaran Ongkir</h2>

                <div>
                    <label class="block font-semibold text-gray-800 mb-2">Upload Bukti Pembayaran Ongkir</label>
                    <input type="file" name="bukti_bayar_ongkir" required
                        class="file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-600 file:text-white hover:file:bg-green-700 text-gray-700 text-sm cursor-pointer" />
                </div>

                {{-- CURRENT ONGKIR FILE --}}
                <template x-if="$store.cart.orderDetail.bukti_pembayaran_ongkir">
                    <div class="pt-2">
                        <p class="text-amber-600 text-sm mb-2">Pilih file baru jika ingin mengganti bukti pembayaran
                            ongkir saat
                            ini</p>
                    </div>
                </template>

                <div class="text-end">
                    <x-primary-button class="bg-green-600 hover:bg-green-700">
                        <i class="fa fa-paper-plane mr-1"></i> Submit Ongkir
                    </x-primary-button>
                </div>
            </form>
        </template>

        <div class="mt-5 text-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                Tutup
            </x-secondary-button>
        </div>
    </div>
</x-modal>
