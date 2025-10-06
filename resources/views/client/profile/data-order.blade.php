<div class="mx-2 md:mx-4 text-base-content rounded">
    <!-- Filter -->
    <div class="flex mb-6">
        <form class="flex [&_option]:bg-secondary" id="form_search">
            <div class="tooltip" data-tip="Data per halaman">
                <select id="numb_per_page" name="numb_per_page" class="my-input mr-2">
                    <option value="5" @if ($numb_per_page == '5') selected @endif>5</option>
                    <option value="10" @if ($numb_per_page == '10') selected @endif>10</option>
                    <option value="25" @if ($numb_per_page == '25') selected @endif>25</option>
                    <option value="50" @if ($numb_per_page == '50') selected @endif>50</option>
                    <option value="100" @if ($numb_per_page == '100') selected @endif>100</option>
                </select>
            </div>
        </form>
    </div>

    <!-- LIST ORDER -->
    <!-- LIST ORDER -->
    <div class="grid gap-6">
        @foreach ($data as $item)
            <div class="bg-base-200 rounded-xl shadow-md overflow-hidden border border-base-300">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 bg-base-300 px-4 py-2">
                    <div>
                        <p class="font-semibold text-sm sm:text-base">
                            Invoice {{ $item->invoice }}
                        </p>
                        <p class="text-xs sm:text-sm opacity-70">
                            {{ $item->created_at->isoFormat('LL') }}
                        </p>
                    </div>
                    <span
                        class="badge badge-outline whitespace-nowrap 
                        {{ $item->delivery_state_id == 6 ? 'badge-error' : 'badge-primary' }}
                        md:text-base text-sm">
                        {{ $item->delivery_state->name }}
                    </span>
                </div>

                <!-- Body -->
                <div class="p-4 flex gap-4">
                    <!-- Gambar produk pertama -->
                    @php
                        $firstDetail = $item->order_detail->first();
                    @endphp
                    @if ($firstDetail && $firstDetail->product)
                        <div class="w-24 md:w-32 h-24 md:h-32 flex-shrink-0 overflow-hidden rounded-lg border bg-white">
                            <img src="{{ asset('storage/' . $firstDetail->product->product_pictures[0]->path) }}"
                                alt="{{ $firstDetail->product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <!-- Info ringkas -->
                    <div class="flex flex-col gap-2 text-sm md:text-base">
                        <p class="font-semibold">
                            {{ $firstDetail?->product?->name ?? 'Produk tidak ditemukan' }}
                            @if ($item->order_detail->count() > 1)
                                <span class="text-xs opacity-70">
                                    +{{ $item->order_detail->count() - 1 }} produk lain
                                </span>
                            @endif
                        </p>
                        <p class="mt-1">
                            Total: <x-client.format-rp value="{{ $item->total }}" />
                        </p>
                    </div>
                </div>

                <!-- Footer (Actions) -->
                <div class="flex justify-end gap-4 px-4 py-3 bg-base-100 border-t">
                    @if ($item->payment_method == 'Transfer')
                        <div class="tooltip"
                            data-tip="{{ $item->delivery_state_id < 3 ? 'Upload Bukti Bayar' : 'Lihat Bukti Transfer' }}">
                            <a href="#"
                                @click.prevent="$dispatch('open-modal', 'modal-upload-bukti-bayar'); $store.cart.getOrderDetail('{{ $item->id }}')">
                                <i class="fa fa-money-bills text-red-600 hover:scale-110 transition"></i>
                            </a>
                        </div>
                    @endif
                    <div class="tooltip" data-tip="Download Invoice">
                        <form action="{{ route('download-invoice', $item->id) }}" method="post" target="_blank"
                            class="inline-block">
                            @csrf
                            <button type="submit">
                                <i class="fa fa-download text-blue-600 hover:scale-110 transition"></i>
                            </button>
                        </form>
                    </div>
                    <div class="tooltip mr-4" data-tip="Lihat Detail">
                        <a href="#"
                            @click.prevent="$dispatch('open-modal', 'modal-show-order-item'); $store.cart.getOrderDetail('{{ $item->id }}')">
                            <i class="fa fa-eye text-teal-600 hover:scale-110 transition"></i>
                        </a>
                    </div>
                    {{-- <div class="tooltip mr-4" data-tip="Lihat Detail">
                        <a href="#"
                            @click.prevent="$dispatch('open-modal', 'modal-cancel-order'); $store.cart.getOrderDetail('{{ $item->id }}'); $store.cart.getCancelReason()">
                            <i class="fa fa-eye text-teal-600 hover:scale-110 transition"></i>
                        </a>
                    </div> --}}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="[&_p]:!text-secondary-content [&_.bg-white]:bg-primary [&_.bg-white]:text-primary-content mt-10">
        {!! $data->links() !!}
    </div>

    @include('client.profile.modal-show-order-item')
    @include('client.profile.modal-show-order-tracking')
    @include('client.profile.modal-upload-bukti-bayar')
    @include('client.profile.modal-cancel-order')
</div>
@push('scripts')
    <script>
        (function() {
            const deleteBtn = document.querySelectorAll('button.delete')

            deleteBtn.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    const isDeleteConfirmed = confirm('Apakah Anda yakin ingin menghapus item ini ?')
                    // jika konfirmasi ditolak maka batalkan submit
                    if (!isDeleteConfirmed) {
                        e.preventDefault()
                    }
                })
            })

            // SUBMIT SEARCH AFTER NUMBER OF ROWS FILTER CHANGED
            const numbRows = document.getElementById('numb_per_page')
            const submitForm = document.getElementById('form_search')

            numbRows.addEventListener('change', function(e) {
                submitForm.submit()
            })
        })()
    </script>
@endpush
