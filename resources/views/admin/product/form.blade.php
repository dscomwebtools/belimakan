{{-- NOTE : $data adalah DATA PRODUK --}}
<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-300 text-base-content overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 product-form" x-data="productForm">
                <div class="flex flex-col items-center sm:flex-row sm:justify-between sm:items-stretch">
                    <h2 class="text-xl uppercase mb-6 font-bold">{{ isset($data) ? 'Ubah' : 'Tambah' }}
                        {{ __('Produk') }}</h2>
                    <div>
                        @include('components.ar.variant-toggle')
                    </div>
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
                            type: '{{ old('pre_order', $data->pre_order ?? false) ? 'pre_order' : 'regular' }}',
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
                                        x-text="type === 'regular' ? 'Reguler' : 'Pre-Order'"></span>
                                </div>
                                <button type="button"
                                    class="bg-primary text-primary-content px-4 py-2 rounded shadow hover:brightness-110 transition"
                                    @click="showModal = true">
                                    <i class="fa fa-gear mr-1"></i> Atur Tipe Pemesanan
                                </button>
                            </div>

                            {{-- Hidden fields --}}
                            <input type="hidden" name="pre_order" :value="type === 'pre_order' ? 1 : 0">
                            <input type="hidden" name="pre_order_date" :value="date">
                            <input type="hidden" name="pre_order_message" :value="message">

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
                                            </div>

                                            {{-- Form Pre-Order --}}
                                            <template x-if="type === 'pre_order'">
                                                <div class="flex flex-col gap-4 mt-3">
                                                    <div>
                                                        <label for="pre_order_date"
                                                            class="font-semibold mb-1 block">Tanggal Kirim
                                                            (Estimasi)</label>
                                                        <input type="datetime-local" id="pre_order_date" x-model="date"
                                                            class="my-input w-full bg-primary/5 rounded border border-base-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary cursor-pointer">
                                                    </div>

                                                    <div>
                                                        <label for="pre_order_message"
                                                            class="font-semibold mb-1 block">Pesan Pre-Order</label>
                                                        <textarea id="pre_order_message" rows="3" x-model="message"
                                                            placeholder="Contoh: Barang akan dikirim 7 hari setelah pemesanan."
                                                            class="my-input w-full bg-primary/5 rounded border border-base-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary resize-none"></textarea>
                                                    </div>
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

                            <div class="flex flex-col sm:flex-row gap-2 my-2">
                                <div class="flex flex-col mb-4">
                                    <label for="discount" class="font-semibold mb-2">Discount <div class="tooltip ml-1"
                                            data-tip="Promo akan aktif jika ada diskon"><i
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
                                            placeholder="Panjang" max="100"
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
                                            placeholder="Lebar" max="100"
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
                                            placeholder="Tinggi" max="100"
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
                            <label class="font-semibold mb-2 text-base-content">
                                Foto Produk
                            </label>

                            <div id="image-preview"
                                class="grid grid-cols-[12rem_repeat(6,8rem)] gap-3 overflow-x-auto pb-2">
                                @for ($i = 0; $i < 7; $i++)
                                    @php
                                        $pic = $data->product_pictures[$i] ?? null;
                                    @endphp
                                    <div class="relative flex items-center justify-center border-2 border-dashed rounded-lg cursor-move
                {{ $i === 0 ? 'w-48 h-48' : 'w-32 h-32' }}
                bg-gray-50 hover:bg-gray-100 transition-all"
                                        data-index="{{ $i }}" data-id="{{ $pic->id ?? '' }}">

                                        @if ($pic)
                                            <img src="{{ Storage::url($pic->path) }}"
                                                alt="Image {{ $i + 1 }}"
                                                class="object-cover w-full h-full rounded-lg preview-img">
                                            <button type="button"
                                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 text-xs delete-picture w-[20px] h-[20px] flex items-center justify-center"
                                                data-id="{{ $pic->id }}">✕</button>
                                        @endif

                                        {{-- Input tetap ada walau ada preview --}}
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

                        @push('scripts')
                            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const radios = document.querySelectorAll('input[name="pre_order"]');
                                    const section = document.getElementById('pre-order-section');
                                    radios.forEach(r => {
                                        r.addEventListener('change', () => {
                                            if (r.value == '1') section.classList.remove('hidden');
                                            else section.classList.add('hidden');
                                        });
                                    });
                                });
                                document.addEventListener('DOMContentLoaded', () => {
                                    const grid = document.getElementById('image-preview');
                                    const deletedPicturesInput = document.getElementById('deleted_pictures');
                                    const picturePositionsInput = document.getElementById('picture_positions');
                                    const deletedPictures = [];

                                    // Enable drag sort
                                    new Sortable(grid, {
                                        animation: 150,
                                        onSort: updateOrder
                                    });

                                    // DELETE PICTURE
                                    grid.addEventListener('click', e => {
                                        const btn = e.target.closest('.delete-picture');
                                        if (!btn) return;
                                        const parent = btn.closest('[data-index]');
                                        const id = btn.dataset.id;

                                        if (id) {
                                            deletedPictures.push(id);
                                            deletedPicturesInput.value = JSON.stringify(deletedPictures);
                                        }

                                        // Reset slot ke upload mode (tidak hapus input)
                                        parent.querySelector('.preview-img')?.remove();
                                        btn.remove();
                                        parent.querySelector('.upload-label').classList.remove('hidden');
                                        parent.querySelector('.file-input').value = '';
                                        updateOrder();
                                    });

                                    // FILE UPLOAD PREVIEW
                                    grid.addEventListener('change', e => {
                                        const fileInput = e.target.closest('.file-input');
                                        if (!fileInput || !fileInput.files.length) return;

                                        const parent = fileInput.closest('[data-index]');
                                        const reader = new FileReader();
                                        reader.onload = ev => {
                                            let preview = parent.querySelector('.preview-img');
                                            if (!preview) {
                                                preview = document.createElement('img');
                                                preview.className = 'object-cover w-full h-full rounded-lg preview-img';
                                                parent.appendChild(preview);
                                            }
                                            preview.src = ev.target.result;

                                            let delBtn = parent.querySelector('.delete-picture');
                                            if (!delBtn) {
                                                delBtn = document.createElement('button');
                                                delBtn.type = 'button';
                                                delBtn.className =
                                                    'absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 text-xs delete-picture w-[20px] h-[20px] flex items-center justify-center';
                                                delBtn.textContent = '✕';
                                                parent.appendChild(delBtn);
                                            }

                                            parent.querySelector('.upload-label').classList.add('hidden');
                                            updateOrder();
                                        };
                                        reader.readAsDataURL(fileInput.files[0]);
                                    });

                                    // Update posisi gambar & label thumbnail
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
