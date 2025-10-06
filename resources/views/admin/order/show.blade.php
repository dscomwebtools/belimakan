<x-app-layout>
    <div class="sm:mx-6 lg:mx-8 p-6 py-10 bg-secondary text-secondary-content rounded overflow-x-auto">
        <div class="flex flex-col sm:flex-row items-center sm:items-start p-4 gap-8 sm:gap-2">
            {{-- LEFT SIDE --}}
            <div class="card w-80 bg-base-200 shadow-xl">
                <figure class="px-10 pt-10 pb-2 flex flex-col gap-4">
                    <h2 class="font-bold">DATA MEMBER</h2>
                    <div class="avatar">
                        <div class="w-full rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img src="{{ isset($data->user->img) ? Storage::url($data->user->img) : asset('assets/images/image-not-found.webp') }}"
                                alt="profile picture" />
                        </div>
                    </div>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ $data->user->fullname }}</h2>
                    <p>{{ $data->user->email }}</p>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="flex-1 bg-base-200 shadow-xl card">
                <div x-data="{ activeTab: 1 }">
                    <div class="flex bg-base-300 rounded-t-2xl dark:text-primary-content">
                        <button class="uppercase px-8 py-3 -mb-px text-sm rounded-t-2xl font-bold"
                            :class="activeTab === 1 ? 'border-base-200 bg-base-200' : 'text-gray-600'"
                            @click="activeTab = 1">
                            DATA ORDER
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-6 bg-base-200 rounded text-sm">
                        <div x-show="activeTab === 1">
                            <div class="flex items-center gap-4">
                                <h2 class="font-bold uppercase mb-2 dark:text-base-content">No Invoice : <b
                                        class="text-base-content">{{ $data->invoice }}</b></h2>
                                <div class="tooltip" data-tip="Download Invoice">
                                    <form action="{{ route('download-invoice', $data->id) }}" method="post"
                                        target="_blank" class="inline-block">
                                        @csrf
                                        <button type="submit">
                                            <i class="fa fa-download text-blue-600"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="leading-10" x-data="{ open: false }">
                                <div class="flex gap-2 items-center">
                                    <span class="opacity-75 dark:text-base-content">Dikirim Ke :</span>
                                    <span
                                        class="dark:text-base-content font-semibold">{{ $data->order_address->name }}</span>

                                    <!-- Toggle Button -->
                                    <button type="button" class="btn btn-xs btn-outline ml-2 flex items-center gap-1"
                                        @click="open = !open">
                                        <span>Detail</span>
                                    </button>
                                </div>

                                <!-- Collapsible Detail -->
                                <div x-cloak x-show="open" x-transition
                                    class="mt-2 ml-6 p-3 rounded bg-base-200 text-sm space-y-1">
                                    <div><span class="font-semibold">Penerima:</span>
                                        {{ $data->order_address->recipient }}</div>
                                    <div><span class="font-semibold">HP:</span> {{ $data->order_address->hp }}</div>
                                    <div><span class="font-semibold">Alamat:</span> {{ $data->order_address->address }}
                                    </div>
                                    @if ($data->order_address->noteForCurrier)
                                        <div><span class="font-semibold">Catatan Kurir:</span>
                                            {{ $data->order_address->noteForCurrier }}</div>
                                    @endif
                                    <div><span class="font-semibold">Area:</span> {{ $data->order_address->area_name }}
                                    </div>
                                    <div><span class="font-semibold">Kode Pos:</span>
                                        {{ $data->order_address->postal_code }}</div>
                                </div>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Kurir
                                    :</span><span class="dark:text-base-content">
                                    @isset($data->shipping_method)
                                        {{ $data->shipping_method->courier_name . ' - ' . $data->shipping_method->courier_service_name }}
                                    @else
                                        {{ 'Manual - ' . $data->shipping_method_manual }}
                                    @endisset
                                </span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Jenis Pembayaran
                                    :</span><span class="dark:text-base-content">{{ $data->payment_method }}</span>
                                @if ($data->payment_method === 'Transfer')
                                    <span>{{ $data->bank_account->bank_name ?? '' }}</span> -
                                    <span>{{ $data->bank_account->account_number ?? '' }}</span>
                                    <button
                                        @click="$store.cart.copyToClipboard('{{ $data->bank_account->account_number ?? '' }}')"
                                        class="tooltip" data-tip="Copy nomor rekening">
                                        <span class="far fa-copy"></span>
                                    </button>

                                    {{-- TAMPILKAN NOTIF COPY TO CLIPBOARD BERHASIL (INI KARENA PENAMBAHAN BELAKANGAN DI DATA ORDER INI JADI BERANTAKAN NGAMBIL DARI STORE CART, HARUS NYA DI ORGANIZE NANTI) --}}
                                    <div x-show="$store.cart.showNotifSuccess" x-transition:leave.duration.500ms
                                        class="toast toast-top toast-end mt-24 z-50">
                                        <div role="alert" class="alert alert-success mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="stroke-current shrink-0 h-6 w-6" fill="none"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="$store.cart.message"></span>
                                        </div>
                                    </div>
                                @endif
                                @if ($data->payment_method === 'Transfer' && $data->delivery_state_id > 1)
                                    <span class="tooltip" data-tip="Lihat bukti bayar">
                                        <a href="{{ Storage::url($data->bukti_pembayaran) }}" target="_blank"><i
                                                class="fa fa-download text-blue-500"></i></a>
                                    </span>
                                @endif
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Total Bayar :</span><span
                                    class="dark:text-base-content"><x-client.format-rp value="{{ $data->total }}" />
                                </span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Sudah Dibayar
                                    :</span><span
                                    class="dark:text-base-content">{{ $data->is_paid ? 'Lunas' : 'Belum Lunas' }}</span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Status
                                    :</span><span
                                    class="dark:text-base-content">{{ $data->delivery_state->name }}</span>
                            </div>

                            <div class="flex gap-2 leading-10">
                                <span class="opacity-75 dark:text-base-content">Tanggal Order
                                    :</span><span
                                    class="dark:text-base-content">{{ $data->created_at->isoFormat('LL') }}</span>
                            </div>

                            <dialog id="receiptModal" name="modal-upload-receipt" class="modal">
                                <div class="modal-box w-11/12 max-w-2xl">
                                    <h1 class="text-xl font-bold text-center mb-4">
                                        @if ($data->delivery_state_id >= 5)
                                            Lihat Resi Pengiriman
                                        @else
                                            {{ $data->receipt ? 'Update Resi Pengiriman' : 'Upload Resi Pengiriman' }}
                                        @endif
                                    </h1>

                                    @if ($data->delivery_state_id >= 5)
                                        {{-- SHOW-ONLY MODE --}}
                                        <div class="space-y-4">
                                            <div class="form-control">
                                                <label class="label font-semibold">Nomor Resi</label>
                                                <input type="text"
                                                    value="{{ $data->receipt->receipt_number ?? $data->biteship_waybill }}"
                                                    class="input input-bordered w-full bg-gray-100" readonly>
                                            </div>

                                            <div class="form-control">
                                                <label class="label font-semibold">Gambar Resi</label>
                                                @if ($data->receipt?->receipt_img)
                                                    <a href="{{ Storage::url($data->receipt->receipt_img) }}"
                                                        target="_blank" class="w-fit">
                                                        <img src="{{ Storage::url($data->receipt->receipt_img) }}"
                                                            alt="resi pengiriman"
                                                            class="w-40 h-[200px] object-contain rounded shadow">
                                                    </a>
                                                @else
                                                    <p class="text-gray-500">Belum ada gambar resi</p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        {{-- CREATE/UPDATE MODE --}}
                                        <form action="{{ route('admin-order.save-receipt', $data->id) }}"
                                            method="POST" enctype="multipart/form-data" class="space-y-4">
                                            @csrf

                                            <div class="form-control">
                                                <label for="receipt_number" class="label font-semibold">Nomor
                                                    Resi</label>

                                                @if ($data->biteship_waybill)
                                                    {{-- Dari Biteship, readonly --}}
                                                    <input type="text" name="receipt_number" id="receipt_number"
                                                        value="{{ $data->biteship_waybill }}"
                                                        class="input input-bordered w-full bg-gray-100" readonly>
                                                    <input type="hidden" name="receipt_number"
                                                        value="{{ $data->biteship_waybill }}">
                                                @else
                                                    {{-- Manual, bisa diketik --}}
                                                    <input type="text" name="receipt_number" id="receipt_number"
                                                        value="{{ $data->receipt->receipt_number ?? '' }}"
                                                        class="input input-bordered w-full" required>
                                                @endif
                                            </div>

                                            <div class="form-control">
                                                <label for="receipt_img" class="label font-semibold">Upload Foto
                                                    Resi</label>
                                                <input type="file" name="receipt_img" id="receipt_img"
                                                    class="file-input file-input-bordered w-full">
                                                @if ($data->receipt?->receipt_img)
                                                    <div class="mt-3">
                                                        <p class="text-xs text-warning">Pilih file baru jika ingin
                                                            mengganti resi saat ini</p>
                                                        <div class="mt-2 mb-1">Resi Saat Ini:</div>
                                                        <a href="{{ Storage::url($data->receipt->receipt_img) }}"
                                                            target="_blank" class="w-fit">
                                                            <img src="{{ Storage::url($data->receipt->receipt_img) }}"
                                                                alt="resi pengiriman"
                                                                class="w-40 h-[200px] object-contain rounded shadow">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <x-primary-button type="submit">
                                                    Simpan <i class="fa fa-paper-plane ml-1"></i>
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    @endif
                                </div>

                                <form method="dialog" class="modal-backdrop">
                                    <button>Batal</button>
                                </form>
                            </dialog>

                            <div class="mb-4">
                                <h2 class="font-bold text-lg text-center dark:text-primary-content">Data Barang</h2>

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
                                            @foreach ($data->order_detail as $key => $order_detail)
                                                <tr class="hover dark:text-accent-content">
                                                    <td>{{ ++$key }}</td>
                                                    <td>
                                                        @if ($order_detail->product_variant)
                                                            <span>
                                                                <span>{{ $order_detail->product->name }}</span>
                                                                <br />
                                                                @if ($order_detail->product_variant && $order_detail->product_variant->product_detail->isNotEmpty())
                                                                    <span>(
                                                                        @foreach ($order_detail->product_variant->product_detail as $index => $detail)
                                                                            {{ $detail->variant_value->value }}
                                                                            {{ $index < $order_detail->product_variant->product_detail->count() - 1 ? ', ' : '' }}
                                                                        @endforeach
                                                                        )
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span>{{ $order_detail->product->name }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $order_detail->quantity }}</td>
                                                    <td>
                                                        {{-- Harga Satuan (tanpa diskon) --}}
                                                        <x-client.format-rp :value="$order_detail->price" />
                                                    </td>

                                                    <td>
                                                        {{-- Diskon --}}
                                                        @if ($order_detail->discount > 0)
                                                            ({{ $order_detail->discount }}%)
                                                            <x-client.format-rp :value="($order_detail->price *
                                                                $order_detail->discount) /
                                                                100" />
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{-- Total setelah diskon * quantity --}}
                                                        @php
                                                            $priceAfterDiscount =
                                                                $order_detail->price -
                                                                ($order_detail->price * $order_detail->discount) / 100;
                                                            $total = $priceAfterDiscount * $order_detail->quantity;
                                                        @endphp

                                                        <x-client.format-rp :value="$total" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- END DATA BARANG --}}

                            {{-- RINGKASAN PEMBAYARAN --}}
                            <div class="mb-4">
                                <h2 class="font-bold text-lg text-center dark:text-primary-content">Ringkasan
                                    Pembayaran</h2>

                                <!-- Subtotal Barang -->
                                <div class="flex justify-between leading-8">
                                    <span>Subtotal Barang</span>
                                    <span>
                                        <x-client.format-rp :value="$data->order_detail->sum(function ($d) {
                                            $priceAfterDiscount = $d->price - ($d->price * $d->discount) / 100;
                                            return $priceAfterDiscount * $d->quantity;
                                        })" />
                                    </span>
                                </div>

                                <!-- Ongkos Kirim -->
                                <div class="flex justify-between leading-8">
                                    <span>Ongkos Kirim</span>
                                    <span>
                                        <x-client.format-rp :value="$data->shipping_method?->price ?? 0" />
                                    </span>
                                </div>

                                <!-- Voucher -->
                                @if ($data->coupon_code)
                                    <div class="flex justify-between leading-8 text-red-600">
                                        <span>Voucher ({{ $data->coupon_code }})</span>
                                        <span>
                                            -<x-client.format-rp :value="$data->coupon_discount" />
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Total Bayar -->
                            <div class="flex justify-between mb-4 font-bold text-lg">
                                <span>Total Bayar</span>
                                <span>
                                    <x-client.format-rp :value="$data->total" />
                                </span>
                            </div>

                            <div class="mt-6 border-t pt-4">
                                <div class="flex flex-wrap items-center gap-2 justify-between">
                                    {{-- Tombol kiri --}}
                                    <div class="flex flex-wrap gap-2">
                                        {{-- Tombol Kembali --}}
                                        <a href="{{ route('admin-order.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-arrow-left"></i> Kembali
                                        </a>

                                        {{-- Tombol Konfirmasi Order --}}
                                        @if ($data->delivery_state_id == 2)
                                            <form action="{{ route('admin-order.confirm_order', $data->id) }}"
                                                method="post" class="inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check"></i> Konfirm Order
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Dropdown Aksi Tambahan --}}
                                    <div class="dropdown dropdown-top dropdown-end">
                                        <label tabindex="0" class="btn btn-sm btn-primary flex items-center gap-1">
                                            <i class="fa fa-cog"></i> Aksi Lain
                                        </label>
                                        <ul tabindex="0"
                                            class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-56">

                                            {{-- Lihat / Upload Resi --}}
                                            @if (
                                                ($data->delivery_state_id >= 3 && $data->delivery_state_id != 6) ||
                                                    ($data->delivery_state_id == 6 && $data->receipt))
                                                <li>
                                                    <a onclick="receiptModal.showModal()">
                                                        <i class="fas fa-receipt"></i>
                                                        {{ $data->delivery_state_id >= 5 ? 'Lihat Resi' : ($data->receipt ? 'Edit Resi' : 'Upload Resi') }}
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Tracking Pengiriman --}}
                                            @if ($data->delivery_state_id >= 3 && $data->biteship_waybill)
                                                <li>
                                                    <a href="#"
                                                        @click.prevent="
                                                            $dispatch('open-modal', 'modal-show-order-tracking');
                                                            $store.cart.getAdminTrackingDetail({{ $data->id }})
                                                        ">
                                                        <i class="fa fa-eye"></i> Lihat Pengiriman
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Approve / Reject Pembatalan --}}
                                            @if ($data->cancel_status === 'pending')
                                                <li
                                                    class="border-t mt-1 pt-1 text-sm font-semibold text-gray-400 px-2">
                                                    Pembatalan Pesanan
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin-order.order_cancel', $data->id) }}"
                                                        method="post"
                                                        onsubmit="return confirm('Yakin ingin menyetujui pembatalan pesanan ini?')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="text-green-600">
                                                            <i class="fa fa-check"></i> Setujui Pembatalan
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin-order.order_cancel', $data->id) }}"
                                                        method="post"
                                                        onsubmit="return confirm('Tolak pembatalan pesanan ini?')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="text-red-600">
                                                            <i class="fa fa-times"></i> Tolak Pembatalan
                                                        </button>
                                                    </form>
                                                </li>
                                            @elseif ($data->cancel_status === 'approved')
                                                <li
                                                    class="text-green-600 font-medium flex items-center gap-2 border-t mt-1 pt-1 px-2">
                                                    <i class="fa fa-check-circle"></i> Pembatalan Disetujui
                                                </li>
                                            @elseif ($data->cancel_status === 'rejected')
                                                <li
                                                    class="text-red-600 font-medium flex items-center gap-2 border-t mt-1 pt-1 px-2">
                                                    <i class="fa fa-times-circle"></i> Pembatalan Ditolak
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('client.profile.modal-show-order-tracking')
</x-app-layout>
