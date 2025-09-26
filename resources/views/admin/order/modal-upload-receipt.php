<dialog id="receiptModal" name="modal-upload-receipt" class="modal">
    <div class="p-6 bg-base-200 rounded-lg">
        <h1 class="text-xl font-bold text-center mb-4">
            {{ $data->order_receipt ? 'Update Resi Pengiriman' : 'Upload Resi Pengiriman' }}
        </h1>

        @if ($data->order_receipt)
        <form action="{{ route('admin-order.receipt-update', $data->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            @else
            <form action="{{ route('admin-order.receipt-store', $data->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @endif

                <div class="form-control">
                    <label for="receipt_number" class="label font-semibold">Nomor Resi</label>
                    <input type="text" name="receipt_number" id="receipt_number"
                        value="{{ $data->receipt->receipt_number ?? '' }}"
                        class="input input-bordered w-full" required>
                </div>

                <div class="form-control">
                    <label for="receipt_img" class="label font-semibold">Upload Gambar Resi</label>
                    <input type="file" name="receipt_img" id="receipt_img" class="file-input file-input-bordered w-full">

                    @if ($data->receipt?->receipt_img)
                    <div class="mt-3">
                        <p class="text-xs text-warning">Pilih file baru jika ingin mengganti resi saat ini</p>
                        <div class="mt-2 mb-1">Resi Saat Ini:</div>
                        <a href="{{ Storage::url($data->receipt->receipt_img) }}" target="_blank" class="block">
                            <img src="{{ Storage::url($data->receipt->receipt_img) }}" alt="resi pengiriman" class="w-40 rounded shadow">
                        </a>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button type="submit">
                        Simpan <i class="fa fa-paper-plane ml-1"></i>
                    </x-primary-button>
                </div>
            </form>
    </div>
</dialog>