<x-modal name="modal-upload-bukti-bayar">
    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-bold text-center text-gray-800">💳 Bukti Pembayaran</h1>

        {{-- Daftar Bank --}}
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
                            <button type="button"
                                @click="
                                    navigator.clipboard.writeText('{{ $bank->account_number }}');
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                "
                                class="mt-3 sm:mt-0 flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 active:scale-[.97] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4">
                                    <path
                                        d="M480 400L288 400C279.2 400 272 392.8 272 384L272 128C272 119.2 279.2 112 288 112L421.5 112C425.7 112 429.8 113.7 432.8 116.7L491.3 175.2C494.3 178.2 496 182.3 496 186.5L496 384C496 392.8 488.8 400 480 400zM288 448L480 448C515.3 448 544 419.3 544 384L544 186.5C544 169.5 537.3 153.2 525.3 141.2L466.7 82.7C454.7 70.7 438.5 64 421.5 64L288 64C252.7 64 224 92.7 224 128L224 384C224 419.3 252.7 448 288 448zM160 192C124.7 192 96 220.7 96 256L96 512C96 547.3 124.7 576 160 576L352 576C387.3 576 416 547.3 416 512L416 496L368 496L368 512C368 520.8 360.8 528 352 528L160 528C151.2 528 144 520.8 144 512L144 256C144 247.2 151.2 240 160 240L176 240L176 192L160 192z" />
                                </svg>
                                <span x-text="copied ? 'Tersalin!' : 'Salin Nomor'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Upload Bukti Pembayaran --}}
        <template x-if="$store.cart.orderDetail.delivery_state_id <= 3">
            <form :action="`/order/${$store.cart.orderDetail.id}`" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="bukti_bayar" class="block font-semibold text-gray-800 mb-2">Upload Bukti Bayar</label>
                    <input type="file" name="bukti_bayar" id="bukti_bayar" required
                        class="file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 text-gray-700 text-sm cursor-pointer" />
                </div>

                <template x-if="$store.cart.orderDetail.bukti_pembayaran">
                    <div class="pt-2">
                        <p class="text-amber-600 text-sm mb-2">Pilih file baru jika ingin mengganti bukti bayar saat
                            ini:</p>
                        <img :src="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" alt="bukti pembayaran"
                            class="w-40 h-[200px] object-contain border rounded-lg shadow-sm" />
                    </div>
                </template>

                <div class="text-end">
                    <x-primary-button>
                        <i class="fa fa-paper-plane mr-1"></i> Submit
                    </x-primary-button>
                </div>
            </form>
        </template>

        {{-- Read Only --}}
        <template x-if="$store.cart.orderDetail.delivery_state_id > 3">
            <div class="text-center">
                <template x-if="$store.cart.orderDetail.bukti_pembayaran">
                    <div>
                        <div class="mb-2 font-semibold text-gray-800">Bukti Bayar</div>
                        <a :href="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" target="_blank">
                            <img :src="`/storage/${$store.cart.orderDetail.bukti_pembayaran}`" alt="bukti pembayaran"
                                class="w-44 h-[220px] object-contain border rounded-xl shadow mx-auto hover:scale-[1.02] transition" />
                        </a>
                    </div>
                </template>

                <template x-if="!$store.cart.orderDetail.bukti_pembayaran">
                    <p class="text-gray-500">Tidak ada bukti pembayaran</p>
                </template>

                <div class="mt-5 text-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Tutup
                    </x-secondary-button>
                </div>
            </div>
        </template>
    </div>
</x-modal>
