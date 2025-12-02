{{-- NOTE : $data adalah DATA PRODUK --}}
<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-300 text-base-content overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 product-form" x-data="productForm">
                <div class="flex flex-col items-center sm:flex-row sm:justify-between sm:items-stretch">
                    <h2 class="text-xl uppercase mb-6 font-bold">{{ isset($data) ? 'Ubah' : 'Tambah' }}
                        {{ __('Produk') }}</h2>
                </div>

                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-700 rounded-lg bg-red-300 dark:bg-gray-800 dark:text-red-400 w-fit"
                        role="alert">
                        @foreach ($errors->all() as $error)
                            <span class="font-medium block"><i
                                    class="fas fa-circle-exclamation mr-2"></i>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                <form action="{{ isset($data) ? route('product.update', $data) : route('product.store') }}"
                    method="POST" id="main" enctype="multipart/form-data" @submit.prevent="submit">
                    @csrf
                    @isset($data)
                        @method('PUT')
                    @endisset

                    <div class="form-container flex flex-col gap-4">
                        <x-ar.note-required-fields />

                        {{-- === PRE-ORDER MODAL SYSTEM === --}}
                        <div x-data="{
                            type: '{{ old('type', $data->call_to_buy ?? false ? 'call_to_buy' : ($data->pre_order ?? false ? 'pre_order' : 'regular')) }}',
                            showModal: false,
                            date: '{{ old('pre_order_date', $data->pre_order_date ?? '') }}',
                            message: `{{ old('pre_order_message', $data->pre_order_message ?? '') }}`
                        }" class="border-t border-base-300">

                            {{-- Label & Button --}}
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <label class="font-semibold text-base-content">Tipe Pemesanan:</label>
                                    <span
                                        class="ml-2 px-2 py-1 rounded bg-primary/10 text-primary font-medium capitalize"
                                        x-text="type === 'regular' ? 'Reguler' : (type === 'pre_order' ? 'Pre-Order' : 'Call To Buy')">
                                    </span>
                                </div>
                                <button type="button"
                                    class="bg-primary text-primary-content px-4 py-2 rounded shadow hover:brightness-110 transition"
                                    @click="showModal = true">
                                    <i class="fa fa-gear mr-1"></i> Atur Tipe Pemesanan
                                </button>
                            </div>

                            {{-- Hidden fields (untuk dikirim ke backend) --}}
                            <input type="hidden" name="pre_order" :value="type === 'pre_order' ? 1 : 0">
                            <input type="hidden" name="pre_order_date" :value="date">
                            <input type="hidden" name="pre_order_message" :value="message">
                            <input type="hidden" name="call_to_buy" :value="type === 'call_to_buy' ? 1 : 0">

                            {{-- === MODAL === --}}
                            <template x-if="showModal">
                                <div
                                    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 animate-fade-in backdrop-blur-sm">
                                    <div class="bg-base-100 rounded-2xl shadow-xl w-full max-w-lg p-6 relative">
                                        <button type="button"
                                            class="absolute top-3 right-3 text-gray-400 hover:text-black"
                                            @click="showModal = false">
                                            <i class="fa fa-times text-lg"></i>
                                        </button>

                                        <h3 class="text-xl font-bold mb-5 text-base-content">Atur Tipe Pemesanan</h3>

                                        <div class="flex flex-col gap-5">
                                            {{-- Pilihan Tipe --}}
                                            <div class="flex items-center gap-6">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" value="regular" x-model="type"
                                                        class="radio radio-primary">
                                                    <span class="font-medium">Reguler</span>
                                                </label>

                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" value="pre_order" x-model="type"
                                                        class="radio radio-primary">
                                                    <span class="font-medium">Pre-Order</span>
                                                </label>

                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" value="call_to_buy" x-model="type"
                                                        class="radio radio-primary">
                                                    <span class="font-medium">Call To Buy</span>
                                                </label>
                                            </div>

                                            {{-- Form Pre-Order --}}
                                            <template x-if="type === 'pre_order'">
                                                <div class="flex flex-col gap-4 mt-3">
                                                    <div>
                                                        <label for="pre_order_date" class="font-semibold mb-1 block">
                                                            Tanggal Kirim (Estimasi)
                                                        </label>
                                                        <input type="datetime-local" id="pre_order_date" x-model="date"
                                                            class="my-input w-full bg-primary/5 rounded border border-base-300 px-3 py-2
                                        focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary cursor-pointer">
                                                    </div>

                                                    <div>
                                                        <label for="pre_order_message" class="font-semibold mb-1 block">
                                                            Pesan Pre-Order
                                                        </label>
                                                        <textarea id="pre_order_message" rows="3" x-model="message"
                                                            placeholder="Contoh: Barang akan dikirim 7 hari setelah pemesanan."
                                                            class="my-input w-full bg-primary/5 rounded border border-base-300 px-3 py-2
                                        focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary resize-none"></textarea>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- Info Call To Buy --}}
                                            <template x-if="type === 'call_to_buy'">
                                                <div class="mt-3 bg-primary/5 p-3 rounded border border-base-300">
                                                    <p class="text-sm text-gray-700">
                                                        Mode <strong>Call To Buy</strong> aktif — pelanggan akan
                                                        diarahkan
                                                        untuk menghubungi penjual sebelum membeli produk ini.
                                                    </p>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Buttons --}}
                                        <div class="mt-6 flex justify-end gap-2">
                                            <button type="button" class="px-4 py-2 bg-base-200 rounded"
                                                @click="showModal = false">Batal</button>
                                            <button type="button"
                                                class="px-4 py-2 bg-primary text-primary-content rounded"
                                                @click="showModal = false">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        @push('styles')
                            <style>
                                @keyframes fadeIn {
                                    from {
                                        opacity: 0;
                                        transform: translateY(-10px);
                                    }

                                    to {
                                        opacity: 1;
                                        transform: translateY(0);
                                    }
                                }

                                .animate-fade-in {
                                    animation: fadeIn 0.25s ease-out;
                                }
                            </style>
                        @endpush

                        <div class="form-content-without-variant">
                            <div class="flex flex-wrap gap-2">
                                <div class="flex flex-col mb-4 flex-1">
                                    <label for="name" class="font-semibold mb-2">Nama Produk
                                        <x-ar.required-label />
                                    </label>
                                    <input type="text" id="name" name="name"
                                        class="my-input bg-primary/5 rounded"
                                        value="{{ old('name', isset($data) ? $data->name : '') }}" required autofocus>
                                </div>
                                <div class="flex flex-col mb-4">
                                    <label for="youtube" class="font-semibold mb-2">Youtube ID</label>
                                    <input type="text" id="youtube" name="youtube"
                                        class="my-input bg-primary/5 rounded"
                                        value="{{ old('youtube', isset($data) ? $data->youtube : '') }}">
                                </div>
                            </div>

                            <div>
                                @include('components.ar.variant-toggle')
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2 my-2">
                                <div class="flex flex-col mb-4">
                                    <label for="discount" class="font-semibold mb-2">Discount <div
                                            class="tooltip ml-1" data-tip="Promo akan aktif jika ada diskon"><i
                                                class="fa fa-circle-exclamation animate-pulse"></i></div></label>
                                    <div class="flex">
                                        <input type="number" id="discount" name="discount" min="0"
                                            step=".01" class="my-input bg-primary/5 rounded-l"
                                            value="{{ old('discount', isset($data->promo->discount) ? $data->promo->discount : 0) }}">
                                        <span
                                            class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-r font-bold">%</span>
                                    </div>
                                </div>
                                <template x-if="variantMode">
                                    <div
                                        class="bg-primary/5 p-2 self-center mx-auto animate-pulse text-center rounded">
                                        <i class="fa fa-circle-exclamation mr-1"></i>
                                        jika produk memiliki varian beberapa input dipindah ke masing-masing varian
                                    </div>
                                </template>
                                <template x-if="!variantMode">
                                    <div class="flex flex-col mb-4 flex-1">
                                        <label for="sku" class="font-semibold mb-2">SKU</label>
                                        <input type="text" id="sku" name="sku"
                                            class="my-input bg-primary/5 rounded"
                                            value="{{ old('sku', isset($data) ? $data->product_variant[0]->sku : '') }}"
                                            :disabled="variantMode">
                                    </div>
                                </template>
                            </div>

                            <template x-if="!variantMode">
                                <div class="flex gap-2 [&>div]:flex-1 flex-wrap">
                                    <div class="flex flex-col mb-4">
                                        <label for="stock" class="font-semibold mb-2">Stok</label>
                                        <input type="number" id="stock" name="stock" min="0"
                                            class="my-input bg-primary/5 rounded"
                                            value="{{ old('stock', isset($data) ? $data->product_variant[0]->stock : 0) }}"
                                            :disabled="variantMode">
                                    </div>
                                    <div class="flex flex-col mb-4">
                                        <label for="price" class="font-semibold mb-2">Harga</label>
                                        <div class="flex">
                                            <span
                                                class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">Rp</span>
                                            <input type="number" id="price" name="price" min="0"
                                                class="my-input bg-primary/5 rounded-r flex-1"
                                                value="{{ old('price', isset($data) ? $data->product_variant[0]->price : 0) }}"
                                                :disabled="variantMode">
                                        </div>
                                    </div>
                                    <div class="flex flex-col mb-4">
                                        <label for="weight" class="font-semibold mb-2">Berat</label>
                                        <div class="flex">
                                            <input type="number" id="weight" name="weight" min="0"
                                                step=".01" class="my-input bg-primary/5 rounded flex-1"
                                                value="{{ old('weight', isset($data) ? $data->product_variant[0]->weight : 0) }}"
                                                :disabled="variantMode">
                                            <span
                                                class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-r font-bold">g</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col mb-4 items-center">
                                        <label for="active" class="font-semibold mb-2">Aktif</label>
                                        <div class="flex">
                                            @include('components_custom.toggle-active-product', [
                                                'name' => 'active',
                                                'checked' => isset($data)
                                                    ? ($data->product_variant[0]->active
                                                        ? 'checked'
                                                        : '')
                                                    : 'checked',
                                            ])
                                        </div>
                                    </div>
                                    <div class="mt-4" x-data="{
                                        bulk: {{ $productVariantsJson }}[0]?.bulk_prices || []
                                    }">

                                        <label class="font-semibold mb-2 block">Harga Grosir</label>

                                        <template x-for="(bp, i) in bulk" :key="i">
                                            <div class="flex items-center gap-2 mb-2">
                                                <input type="number" min="1"
                                                    class="my-input w-20 text-center" placeholder="Qty"
                                                    :name="'bulk_prices_single[' + i + '][min_qty]'"
                                                    x-model="bp.min_qty">

                                                <input type="number" min="1"
                                                    class="my-input w-24 text-center" placeholder="Harga"
                                                    :name="'bulk_prices_single[' + i + '][price]'" x-model="bp.price">

                                                <button type="button" class="px-2 py-1 bg-red-600 text-white rounded"
                                                    @click="bulk.splice(i, 1)">✕</button>
                                            </div>
                                        </template>

                                        <button type="button" class="bg-primary text-white px-2 py-1 rounded text-xs"
                                            @click="bulk.push({min_qty: null, price: null})">
                                            + Tambah Harga
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div
                                class="flex gap-2 flex-wrap border border-primary p-2 my-4 rounded flex-col sm:flex-row">
                                <div class="flex flex-col items-center justify-center sm:mx-6">
                                    <label for="price" class="font-semibold">DIMENSI</label>
                                </div>

                                <div class="flex flex-col mb-4 flex-1">
                                    <label for="length" class="font-semibold mb-2">Panjang</label>
                                    <div class="flex">
                                        <input type="number" id="length" name="length" min="0"
                                            step=".01" class="my-input bg-primary/5 rounded-l flex-1"
                                            placeholder="Panjang" max="300"
                                            value="{{ old('stock', isset($data->dimention->length) ? $data->dimention->length : 0) }}">
                                        <span
                                            class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-r font-bold">cm</span>
                                    </div>
                                </div>

                                <div class="flex flex-col mb-4 flex-1">
                                    <label for="width" class="font-semibold mb-2">Lebar</label>
                                    <div class="flex">
                                        <input type="number" id="width" name="width" min="0"
                                            step=".01" class="my-input bg-primary/5 rounded-l flex-1"
                                            placeholder="Lebar" max="300"
                                            value="{{ old('stock', isset($data->dimention->width) ? $data->dimention->width : 0) }}">
                                        <span
                                            class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-r font-bold">cm</span>
                                    </div>
                                </div>

                                <div class="flex flex-col mb-4 flex-1">
                                    <label for="height" class="font-semibold mb-2">Tinggi</label>
                                    <div class="flex">
                                        <input type="number" id="height" name="height" min="0"
                                            step=".01" class="my-input bg-primary/5 rounded-l flex-1"
                                            placeholder="Tinggi" max="300"
                                            value="{{ old('stock', isset($data->dimention->height) ? $data->dimention->height : 0) }}">
                                        <span
                                            class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-r font-bold">cm</span>
                                    </div>
                                </div>

                            </div>

                            {{-- <div class="flex flex-col">
                                    <textarea name="description" id="description" rows="3" class="my-input bg-primary/5 rounded">{{ old('description', isset($data) ? $data->description : '') }}</textarea>
                                </div> --}}

                            <label for="description" class="font-semibold">Deskripsi</label>
                            <div id="toolbar" class="mt-2 rounded-t">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                                <button class="ql-link"></button>
                                <button class="ql-image"></button>
                                <button class="ql-video"></button>
                                {{-- <select class="ql-color"></select> --}}
                                <select class="ql-background"></select>
                                <button class="ql-script" value="sub"></button>
                                <button class="ql-script" value="super"></button>
                                <button class="ql-blockquote"></button>
                                <button class="ql-code-block"></button>
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                                <button class="ql-indent" value="-1"></button>
                                <button class="ql-indent" value="+1"></button>
                                <button class="ql-direction" value="rtl"></button>
                                <select class="ql-align"></select>
                                <button class="ql-clean"></button>
                            </div>
                            {{-- tinggi menggunakan [&>.ql-editor]:min-h-52 agar bisa fokus saat klik diarea editor bagian bawah --}}
                            <div id="description-editor"
                                class="bg-white text-black rounded-b [&>.ql-editor]:min-h-52">
                                {!! old('description', isset($data) ? $data->description : '') !!}
                            </div>

                            <input type="hidden" id="description" name="description">
                        </div>

                        {{-- CATEGORIES --}}
                        <div class="flex flex-col flex-1 text-black">
                            <label for="categories" class="font-semibold mb-2 text-base-content">Kategori</label>
                            <select name="categories[]" id="categories" multiple="multiple">
                                <option></option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @isset($data)
                                        @foreach ($data->category->pluck('id') as $currentCategoryId)
                                        @if ($currentCategoryId === $category->id)
                                        selected
                                        @endif
                                        @endforeach
                                        @endisset>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- BRAND --}}
                        <div class="flex flex-col flex-1 text-black">
                            <label for="brand_id" class="font-semibold mb-2 text-base-content">Brand</label>
                            <select name="brand_id" id="brand_id">
                                <option></option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        @isset($data)
                                        @if ($data->brand_id === $brand->id)
                                        selected
                                        @endif
                                        @endisset>
                                        {{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col flex-1 mb-6">
                            <label class="font-semibold mb-2 text-base-content">Foto Produk</label>
                            <div id="image-preview"
                                class="grid grid-cols-[12rem_repeat(6,8rem)] gap-3 overflow-x-auto pb-2">
                                @for ($i = 0; $i < 7; $i++)
                                    @php $pic = $data->product_pictures[$i] ?? null; @endphp
                                    <div class="relative flex items-center justify-center border-2 border-dashed rounded-lg cursor-move {{ $i === 0 ? 'w-48 h-48' : 'w-32 h-32' }} bg-gray-50 hover:bg-gray-100 transition-all"
                                        data-index="{{ $i }}" data-id="{{ $pic->id ?? '' }}">
                                        @if ($pic)
                                            <img src="{{ Storage::url($pic->path) }}"
                                                alt="Image {{ $i + 1 }}"
                                                class="object-cover w-full h-full rounded-lg preview-img">
                                            <button type="button"
                                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 text-xs delete-picture w-[20px] h-[20px] flex items-center justify-center"
                                                data-id="{{ $pic->id }}">✕</button>
                                        @endif
                                        <label for="product_pictures{{ $i }}"
                                            class="flex flex-col items-center justify-center w-full h-full text-gray-400 cursor-pointer upload-label {{ $pic ? 'hidden' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-1"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="text-xs">Upload</span>
                                        </label>
                                        <input type="file" name="product_pictures[]"
                                            id="product_pictures{{ $i }}" class="hidden file-input" />
                                        @if ($i === 0)
                                            <span
                                                class="thumb-label absolute bottom-1 left-1 bg-blue-600 text-white text-xs px-1 rounded">Thumbnail</span>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                            <input type="hidden" name="deleted_pictures" id="deleted_pictures">
                            <input type="hidden" name="picture_positions" id="picture_positions">
                        </div>

                        <div id="crop-modal"
                            class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg p-4 w-[90%] max-w-lg flex flex-col gap-3">
                                <h2 class="font-semibold text-lg mb-1">Crop Gambar</h2>
                                <div class="relative bg-gray-100 flex items-center justify-center overflow-hidden">
                                    <canvas id="crop-canvas"
                                        class="max-w-full max-h-[60vh] cursor-crosshair bg-white"></canvas>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex gap-2 items-center">
                                        <label class="text-sm">Rasio:</label>
                                        <select id="aspect-ratio" class="border rounded px-2 py-1 text-sm">
                                            <option value="1">1:1</option>
                                            <option value="0.5">1:2</option>
                                            <option value="2">2:1</option>
                                        </select>
                                    </div>
                                    <div class="flex gap-2">
                                        <button id="cancel-crop" type="button"
                                            class="px-3 py-1 bg-gray-300 rounded">Batal</button>
                                        <button id="save-crop" type="button"
                                            class="px-3 py-1 bg-blue-600 text-white rounded">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @push('scripts')
                            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const grid = document.getElementById('image-preview');
                                    const deletedPicturesInput = document.getElementById('deleted_pictures');
                                    const picturePositionsInput = document.getElementById('picture_positions');
                                    const deletedPictures = [];

                                    new Sortable(grid, {
                                        animation: 150,
                                        onSort: updateOrder
                                    });

                                    grid.addEventListener('click', e => {
                                        const btn = e.target.closest('.delete-picture');
                                        if (!btn) return;
                                        const parent = btn.closest('[data-index]');
                                        const id = btn.dataset.id;
                                        if (id) {
                                            deletedPictures.push(id);
                                            deletedPicturesInput.value = JSON.stringify(deletedPictures);
                                        }
                                        parent.querySelector('.preview-img')?.remove();
                                        btn.remove();
                                        parent.querySelector('.upload-label').classList.remove('hidden');
                                        parent.querySelector('.file-input').value = '';
                                        updateOrder();
                                    });

                                    grid.addEventListener('change', e => {
                                        const fileInput = e.target.closest('.file-input');
                                        if (!fileInput || !fileInput.files.length) return;
                                        openCropModal(fileInput);
                                    });

                                    grid.addEventListener('click', e => {
                                        const imgEl = e.target.closest('.preview-img');
                                        if (!imgEl) return;

                                        fileInput = imgEl.closest('[data-index]').querySelector('.file-input');

                                        img = new Image();
                                        img.onload = () => {
                                            const maxW = 600;
                                            displayScale = img.width > maxW ? maxW / img.width : 1;
                                            cropCanvas.width = Math.round(img.width * displayScale);
                                            cropCanvas.height = Math.round(img.height * displayScale);
                                            setCropBoxToFullWidth();
                                            draw();
                                            cropModal.classList.remove('hidden');
                                        };
                                        img.crossOrigin = "anonymous";
                                        img.src = imgEl.src;
                                    });

                                    function updateOrder() {
                                        const items = Array.from(grid.children);
                                        picturePositionsInput.value = JSON.stringify(items.map(i => i.dataset.id || null));
                                        items.forEach((div, i) => {
                                            div.classList.remove('w-48', 'h-48', 'ring-2', 'ring-blue-500');
                                            div.classList.add('w-32', 'h-32');
                                            div.querySelector('.thumb-label')?.remove();
                                            if (i === 0) {
                                                div.classList.remove('w-32', 'h-32');
                                                div.classList.add('w-48', 'h-48', 'ring-2', 'ring-blue-500');
                                                const label = document.createElement('span');
                                                label.className =
                                                    'thumb-label absolute bottom-1 left-1 bg-blue-600 text-white text-xs px-1 rounded';
                                                label.textContent = 'Thumbnail';
                                                div.appendChild(label);
                                            }
                                        });
                                    }
                                    updateOrder();

                                    const cropModal = document.getElementById('crop-modal');
                                    const saveBtn = document.getElementById('save-crop');
                                    const cancelBtn = document.getElementById('cancel-crop');
                                    const cropCanvas = document.getElementById('crop-canvas');
                                    const ctx = cropCanvas.getContext('2d');
                                    const aspectSelect = document.getElementById('aspect-ratio');

                                    let img = new Image();
                                    let fileInput = null;
                                    let displayScale = 1;
                                    let cropBox = {
                                        x: 50,
                                        y: 50,
                                        w: 200,
                                        h: 200
                                    };
                                    let isDragging = false,
                                        isResizing = false,
                                        activeHandle = null;
                                    let startX = 0,
                                        startY = 0;
                                    let aspectRatio = parseFloat(aspectSelect.value) || 1;

                                    const HANDLE = {
                                        NONE: 0,
                                        TL: 1,
                                        TR: 2,
                                        BL: 3,
                                        BR: 4,
                                    };
                                    const handleSize = 16;

                                    function openCropModal(input) {
                                        fileInput = input;
                                        const reader = new FileReader();
                                        reader.onload = ev => {
                                            img = new Image();
                                            img.onload = () => {
                                                const maxW = 600;
                                                displayScale = img.width > maxW ? maxW / img.width : 1;
                                                cropCanvas.width = Math.round(img.width * displayScale);
                                                cropCanvas.height = Math.round(img.height * displayScale);
                                                setCropBoxToFullWidth();
                                                draw();
                                                cropModal.classList.remove('hidden');
                                            };
                                            img.src = ev.target.result;
                                        };
                                        reader.readAsDataURL(input.files[0]);
                                    }

                                    function setCropBoxToFullWidth() {
                                        aspectRatio = parseFloat(aspectSelect.value) || 1;
                                        let w = cropCanvas.width;
                                        let h = Math.round(w / aspectRatio);
                                        if (h > cropCanvas.height) {
                                            h = cropCanvas.height;
                                            w = Math.round(h * aspectRatio);
                                        }
                                        cropBox.w = Math.max(1, w);
                                        cropBox.h = Math.max(1, h);
                                        cropBox.x = Math.round((cropCanvas.width - cropBox.w) / 2);
                                        cropBox.y = Math.round((cropCanvas.height - cropBox.h) / 2);
                                    }

                                    // ====== REPLACE getTouchPoint ======
                                    function getTouchPoint(e) {
                                        const rect = cropCanvas.getBoundingClientRect();
                                        const clientX = e.touches && e.touches[0] ? e.touches[0].clientX : e.clientX;
                                        const clientY = e.touches && e.touches[0] ? e.touches[0].clientY : e.clientY;

                                        // map dari CSS pixels (rect.width/height) ke canvas pixels (cropCanvas.width/height)
                                        const scaleX = cropCanvas.width / rect.width;
                                        const scaleY = cropCanvas.height / rect.height;

                                        const x = (clientX - rect.left) * scaleX;
                                        const y = (clientY - rect.top) * scaleY;

                                        return {
                                            x: Math.max(0, Math.min(cropCanvas.width, x)),
                                            y: Math.max(0, Math.min(cropCanvas.height, y))
                                        };
                                    }

                                    // ====== REPLACE whichHandle ======
                                    function whichHandle(x, y) {
                                        const cx = cropBox.x,
                                            cy = cropBox.y,
                                            cw = cropBox.w,
                                            ch = cropBox.h;
                                        // toleransi hitbox lebih besar agar klik/drag lebih mudah
                                        const s = handleSize / 2 + 6;

                                        // corners only
                                        const corners = [{
                                                type: HANDLE.TL,
                                                x: cx,
                                                y: cy
                                            },
                                            {
                                                type: HANDLE.TR,
                                                x: cx + cw,
                                                y: cy
                                            },
                                            {
                                                type: HANDLE.BL,
                                                x: cx,
                                                y: cy + ch
                                            },
                                            {
                                                type: HANDLE.BR,
                                                x: cx + cw,
                                                y: cy + ch
                                            }
                                        ];

                                        for (const c of corners) {
                                            if (x >= c.x - s && x <= c.x + s && y >= c.y - s && y <= c.y + s) {
                                                return c.type;
                                            }
                                        }

                                        // inside box -> drag
                                        if (x > cx + s && x < cx + cw - s && y > cy + s && y < cy + ch - s) {
                                            return HANDLE.NONE;
                                        }

                                        return null;
                                    }

                                    // ====== REPLACE draw ======
                                    function draw() {
                                        ctx.clearRect(0, 0, cropCanvas.width, cropCanvas.height);
                                        ctx.drawImage(img, 0, 0, cropCanvas.width, cropCanvas.height);

                                        // dim outside area (optional visual) - keep image visible but highlight box
                                        ctx.save();
                                        ctx.fillStyle = 'rgba(0,0,0,0.0)'; // keep transparent; can darken if desired
                                        ctx.fillRect(0, 0, cropCanvas.width, cropCanvas.height);
                                        ctx.restore();

                                        // crop border
                                        ctx.beginPath();
                                        ctx.lineWidth = 2;
                                        ctx.strokeStyle = 'dodgerblue';
                                        ctx.rect(cropBox.x, cropBox.y, cropBox.w, cropBox.h);
                                        ctx.stroke();
                                        ctx.closePath();

                                        // draw only 4 corner handles (centered on corner)
                                        const hs = handleSize;
                                        const half = hs / 2;
                                        const points = [
                                            [cropBox.x, cropBox.y], // TL
                                            [cropBox.x + cropBox.w, cropBox.y], // TR
                                            [cropBox.x, cropBox.y + cropBox.h], // BL
                                            [cropBox.x + cropBox.w, cropBox.y + cropBox.h] // BR
                                        ];
                                        ctx.fillStyle = 'dodgerblue';
                                        points.forEach(p => {
                                            ctx.fillRect(Math.round(p[0] - half), Math.round(p[1] - half), hs, hs);
                                        });
                                    }

                                    // ====== REPLACE startAction ======
                                    function startAction(e) {
                                        const p = getTouchPoint(e);
                                        const h = whichHandle(p.x, p.y);
                                        if (h === null) return; // klik di luar area crop -> abaikan

                                        // set posisi awal sesuai koordinat canvas
                                        startX = p.x;
                                        startY = p.y;
                                        activeHandle = h;

                                        if (h === HANDLE.NONE) {
                                            isDragging = true;
                                            isResizing = false;
                                        } else {
                                            isResizing = true;
                                            isDragging = false;
                                        }
                                    }

                                    // ====== REPLACE moveAction ======
                                    function moveAction(e) {
                                        // hanya proses kalau sedang drag/resize
                                        if (!isDragging && !isResizing) return;

                                        const p = getTouchPoint(e);
                                        if (e.touches) e.preventDefault();

                                        // delta berdasarkan canvas coordinates
                                        const dx = p.x - startX;
                                        const dy = p.y - startY;

                                        const minSize = Math.max(24, Math.round(Math.min(cropCanvas.width, cropCanvas.height) * 0.06));

                                        if (isDragging) {
                                            cropBox.x = Math.max(0, Math.min(cropCanvas.width - cropBox.w, cropBox.x + dx));
                                            cropBox.y = Math.max(0, Math.min(cropCanvas.height - cropBox.h, cropBox.y + dy));
                                        } else if (isResizing && activeHandle) {
                                            // gunakan aspect ratio seperti sebelumnya, tapi clamped ke dalam canvas
                                            const a = aspectRatio;
                                            let nx = cropBox.x,
                                                ny = cropBox.y,
                                                nw = cropBox.w,
                                                nh = cropBox.h;

                                            const applyWH = (w, h) => {
                                                // jaga aspect
                                                if (w / h > a) {
                                                    w = Math.round(h * a);
                                                } else {
                                                    h = Math.round(w / a);
                                                }
                                                if (w < minSize) {
                                                    w = minSize;
                                                    h = Math.round(w / a);
                                                }
                                                if (h < minSize) {
                                                    h = minSize;
                                                    w = Math.round(h * a);
                                                }
                                                return [w, h];
                                            };

                                            switch (activeHandle) {
                                                case HANDLE.BR:
                                                    nw = cropBox.w + dx;
                                                    nh = Math.round(nw / a);
                                                    [nw, nh] = applyWH(nw, nh);
                                                    break;
                                                case HANDLE.TR:
                                                    nw = cropBox.w + dx;
                                                    nh = Math.round(nw / a);
                                                    [nw, nh] = applyWH(nw, nh);
                                                    ny = cropBox.y + (cropBox.h - nh);
                                                    break;
                                                case HANDLE.TL:
                                                    nw = cropBox.w - dx;
                                                    nh = Math.round(nw / a);
                                                    [nw, nh] = applyWH(nw, nh);
                                                    nx = cropBox.x + (cropBox.w - nw);
                                                    ny = cropBox.y + (cropBox.h - nh);
                                                    break;
                                                case HANDLE.BL:
                                                    nw = cropBox.w - dx;
                                                    nh = Math.round(nw / a);
                                                    [nw, nh] = applyWH(nw, nh);
                                                    nx = cropBox.x + (cropBox.w - nw);
                                                    break;
                                            }

                                            // clamp posisi & ukuran supaya tetap di canvas
                                            if (nx < 0) nx = 0;
                                            if (ny < 0) ny = 0;
                                            if (nx + nw > cropCanvas.width) nx = cropCanvas.width - nw;
                                            if (ny + nh > cropCanvas.height) ny = cropCanvas.height - nh;

                                            cropBox.x = Math.round(nx);
                                            cropBox.y = Math.round(ny);
                                            cropBox.w = Math.round(nw);
                                            cropBox.h = Math.round(nh);
                                        }

                                        // update start point agar perubahan berikutnya relatif
                                        startX = p.x;
                                        startY = p.y;

                                        draw();
                                    }

                                    function endAction() {
                                        isDragging = false;
                                        isResizing = false;
                                        activeHandle = null;
                                        cropCanvas.style.cursor = 'default';
                                    }

                                    cropCanvas.addEventListener('mousedown', startAction);
                                    cropCanvas.addEventListener('mousemove', moveAction);
                                    cropCanvas.addEventListener('mouseup', endAction);
                                    cropCanvas.addEventListener('mouseleave', endAction);
                                    cropCanvas.addEventListener('touchstart', e => startAction(e), {
                                        passive: false
                                    });
                                    cropCanvas.addEventListener('touchmove', e => moveAction(e), {
                                        passive: false
                                    });
                                    cropCanvas.addEventListener('touchend', endAction);
                                    cropCanvas.addEventListener('touchcancel', endAction);

                                    aspectSelect.addEventListener('change', () => {
                                        aspectRatio = parseFloat(aspectSelect.value) || 1;
                                        setCropBoxToFullWidth();
                                        draw();
                                    });

                                    saveBtn.addEventListener('click', e => {
                                        e.preventDefault();
                                        const temp = document.createElement('canvas');
                                        const sx = cropBox.x / displayScale;
                                        const sy = cropBox.y / displayScale;
                                        const sw = cropBox.w / displayScale;
                                        const sh = cropBox.h / displayScale;
                                        temp.width = Math.round(sw);
                                        temp.height = Math.round(sh);
                                        const tctx = temp.getContext('2d');
                                        tctx.fillStyle = "white";
                                        tctx.fillRect(0, 0, temp.width, temp.height);
                                        tctx.drawImage(img, sx, sy, sw, sh, 0, 0, temp.width, temp.height);
                                        temp.toBlob(blob => {
                                            const file = new File([blob], "TEST", {
                                                type: 'image/jpeg'
                                            });
                                            const dt = new DataTransfer();
                                            dt.items.add(file);
                                            fileInput.files = dt.files;
                                            const reader = new FileReader();
                                            reader.onload = ev => {
                                                const parent = fileInput.closest('[data-index]');
                                                let preview = parent.querySelector('.preview-img');
                                                if (!preview) {
                                                    preview = document.createElement('img');
                                                    preview.className =
                                                        'object-cover w-full h-full rounded-lg preview-img';
                                                    parent.appendChild(preview);
                                                }
                                                preview.src = ev.target.result;
                                                parent.querySelector('.upload-label').classList.add('hidden');
                                                if (!parent.querySelector('.delete-picture')) {
                                                    const delBtn = document.createElement('button');
                                                    delBtn.type = 'button';
                                                    delBtn.className =
                                                        'absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 text-xs delete-picture w-[20px] h-[20px] flex items-center justify-center';
                                                    delBtn.textContent = '✕';
                                                    delBtn.dataset.id = '';
                                                    parent.appendChild(delBtn);
                                                }
                                            };
                                            reader.readAsDataURL(file);
                                            cropModal.classList.add('hidden');
                                        }, 'image/jpeg', 0.95);
                                    });

                                    cancelBtn.addEventListener('click', () => {
                                        cropModal.classList.add('hidden');
                                        fileInput.value = '';
                                    });
                                });
                            </script>
                        @endpush


                        {{-- PRODUCT DETAILS --}}
                        <div id="detail-product-container" class="mt-2 flex flex-col gap-4">
                            <div class="detail-product-item flex gap-2 items-center">
                                <div class="flex">
                                    <span
                                        class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">
                                        Label
                                    </span>
                                    <input type="text" name="detail[]" class="my-input bg-primary/5 rounded-r"
                                        placeholder="Kondisi">
                                </div>
                                <div class="flex">
                                    <span
                                        class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">
                                        Nilai
                                    </span>
                                    <input type="text" name="detail-value[]"
                                        class="my-input bg-primary/5 rounded-r" placeholder="Baru">
                                </div>
                                @include('components_custom.remove-detail-product')
                            </div>
                        </div>
                        <button type="button" class="btn add-item-detail"><i class="fa fa-plus"></i> Detail</button>

                        {{-- MODE VARIANT --}}
                        @include('admin.product.form.variant-fields')
                        {{-- MODE VARIANT END --}}

                        <div class="flex flex-col gap-2">
                            <div class="flex flex-col mb-4 flex-1">
                                <label for="seo_title" class="font-semibold mb-2">SEO Title
                                    <x-ar.required-label />
                                </label>
                                <input type="text" id="seo_title" name="seo_title"
                                    class="my-input bg-primary/5 rounded"
                                    value="{{ old('seo_title', isset($data) ? $data->seo_title : '') }}" required
                                    autofocus>
                            </div>
                            <div class="flex flex-col mb-4 flex-1">
                                <label for="seo_description" class="font-semibold mb-2">SEO Deskripsi</label>
                                <input type="text" id="seo_description" name="seo_description"
                                    class="my-input bg-primary/5 rounded"
                                    value="{{ old('seo_description', isset($data) ? $data->seo_description : '') }}">
                            </div>
                        </div>

                        {{-- BUTTON SUBMIT AND BACK CONTAINER --}}
                        <div class="grid grid-cols-2 gap-2 mt-6">
                            <a href="{{ route('product.index') }}"
                                class="mx-10 py-2 px-4 bg-base-200 text-base-content text-center rounded">{{ __('Kembali') }}</a>
                            <button type="submit"
                                :class="{ 'mx-10 py-2 px-4 bg-primary text-primary-content rounded': true, 'bg-primary/25 cursor-not-allowed': isSubmitting }"
                                :disabled="isSubmitting">Simpan</button>
                        </div>
                        {{-- BUTTON SUBMIT AND BACK CONTAINER END --}}
                    </div>
                </form>

                @isset($data)
                    @if (count($data->product_variant) > 1)
                        {{-- set variantmode true jika variant ada lebih dari 1 --}}
                        <div x-init="setVariantMode(true)"></div>
                    @endif
                    <div x-init="setVariants('{{ json_encode($existingVariants) }}')"></div>
                    <div x-init="initializeProductVariants('{{ json_encode($productVariants) }}')"></div>
                @endisset
            </div>
        </div>
    </div>
    @push('scripts')
        {{-- JQUERY  --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        {{-- SELECT2 --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        {{-- QUILLJS --}}
        <script src="https://cdn.quilljs.com/1.2.2/quill.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/kensnyder/quill-image-resize-module@3411c9a7/image-resize.min.js"></script>

        <script>
            $(function() {
                $('.product-form form').on('click', '.remove-detail-product', function() {
                    $(this).closest('.detail-product-item').remove()
                });

                $('.product-form form').on('click', '.add-item-detail', function() {
                    const detailCont = $('#detail-product-container')
                    detailCont.append(itemDetail())
                });

                const itemDetail = (label = '', nilai = '') => `
                <div class="detail-product-item flex gap-2 items-center">
                    <div class="flex">
                        <span
                            class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">
                            Label
                        </span>
                        <input type="text" name="detail[]" class="my-input bg-primary/5 rounded-r"
                            placeholder="Satuan / Unit" value="${label}">
                    </div>
                    <div class="flex">
                        <span
                            class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">
                            Nilai
                        </span>
                        <input type="text" name="detail-value[]"
                            class="my-input bg-primary/5 rounded-r" placeholder="Roll" value="${nilai}">
                    </div>
                    @include('components_custom.remove-detail-product')
                </div>`;

                const editor = new Quill('#description-editor', {
                    modules: {
                        toolbar: {
                            container: '#toolbar', // Selector for toolbar container
                        },
                        imageResize: {
                            displaySize: true
                        }
                    },
                    theme: 'snow'
                })
            })
        </script>
    @endpush

    @push('styles')
        {{-- SELECT2 --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2 {
                width: 100% !important;
            }
        </style>

        {{-- QUILLJS --}}
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
        <style>
            .ql-toolbar {
                background-color: oklch(var(--p));
                color: oklch(var(--pc));
            }
        </style>
    @endpush
</x-app-layout>
