<div class="sm:mx-6 lg:mx-8" x-data="productTable">
    <x-modal name="modal-import-excel-product" :show="$errors->importExcelProduct->isNotEmpty()" focusable>
        <h1 class="text-center mt-8 mb-2 font-bold text-xl mx-4">Import Data Excel Produk</h1>
        <form method="post" action="{{ route('product.import') }}" class="p-6" enctype="multipart/form-data">
            @csrf
            <div class="flex justify-center gap-2">
                <input type="file" name="product-excel" id="product-excel" required>
                <button type="submit" class="btn btn-sm btn-primary">Import</button>
            </div>
        </form>
    </x-modal>

    <div class="flex">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route($routeName . '.create') }}" class="btn btn-primary btn-sm tooltip flex"
                data-tip="Tambah data">
                <i class="fa fa-plus"></i>
            </a>
            @isset($import)
                <button class="btn bg-green-600 text-green-200 btn-sm"
                    @click="$dispatch('open-modal', 'modal-import-excel-product')">
                    Import Produk <i class="fa fa-file-excel"></i>
                </button>
            @endisset
        </div>

        <span class="ml-auto mb-3">
            <form class="flex [&_option]:bg-secondary" id="form_search">
                <div class="tooltip" data-tip="Data per halaman">
                    <select id="numbRows" name="numbRows" class="my-input mr-2">
                        <option value="5" @if ($numbRows == '5') selected @endif>5</option>
                        <option value="10" @if ($numbRows == '10') selected @endif>10</option>
                        <option value="25" @if ($numbRows == '25') selected @endif>25</option>
                        <option value="50" @if ($numbRows == '50') selected @endif>50</option>
                        <option value="100" @if ($numbRows == '100') selected @endif>100</option>
                    </select>
                </div>
                <div class="inline-block whitespace-nowrap tooltip" data-tip="Pencarian">
                    @if (count($queryParams) === 1)
                        <input type="text" id="search" name="{{ key($queryParams) }}" class="my-input"
                            placeholder="Cari berdasarkan {{ key($queryParams) }}"
                            @isset($validated[key($queryParams)]) value="{{ $validated[key($queryParams)] }}" @endisset>
                    @else
                        @if (count($queryParams) > 1)
                            <div class="tooltip" data-tip="Cari">
                                <select id="search-by" class="my-input mr-2">
                                    @foreach (array_keys($queryParams) as $item)
                                        <option value="{{ $item }}"
                                            @isset($validated[$item]) selected @endisset>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="inline-block my-input whitespace-nowrap">
                                <input type="text" id="search"
                                    class="border-transparent focus:outline-none focus:ring-0 focus:border-transparent bg-transparent"
                                    @foreach (array_keys($queryParams) as $item) @isset($validated[$item])
                                        value="{{ $validated[$item] }}"
                                    @endisset @endforeach>
                            </div>
                        @endif
                    @endif
                    <button type="submit"
                        class="bg-primary/70 hover:bg-primary btn btn-circle btn-sm hover:scale-105 text-primary-content">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </span>
    </div>

    <div class="overflow-x-auto">
        <div x-data="{ show: false, message: '', type: 'success' }"
            @flash.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
            x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed top-5 right-5 z-50 p-4 rounded-lg shadow-xl text-white transition-all duration-300"
            :class="{ 'bg-green-500': type === 'success', 'bg-red-500': type === 'error' }" style="display: none;">
            <span x-text="message"></span>
        </div>
        <table class="table bg-base-200">
            <thead>
                <tr class="bg-base-300">
                    @foreach ($headers as $header)
                        <th @class(['text-center' => $header === 'Aksi'])>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $rowKey => $row)
                    <tr class="odd:bg-primary/5">
                        <td>{{ ++$indexNumber }}</td>

                        @foreach ($row as $k => $cell)
                            @if ($k === 'id')
                                @continue
                            @endif

                            {{-- Inline Edit Harga per Varian --}}
                            @if ($k === 'price')
                                <td class="text-left py-2 px-4">
                                    @foreach ($cell as $variant)
                                        <div x-data="{
                                            editing: false,
                                            initialValue: {{ $variant['price'] }},
                                            value: {{ $variant['price'] }},
                                            saving: false,
                                            variantId: {{ $variant['id'] }},
                                            savePrice() {
                                                this.saving = true;
                                                fetch('{{ route('product.update-single', $row['id']) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({
                                                            variant_id: this.variantId,
                                                            field: 'price',
                                                            value: this.value
                                                        })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            this.initialValue = this.value; // Update initial value on success
                                                            window.dispatchEvent(new CustomEvent('flash', { detail: { message: '✅ Harga varian berhasil diubah', type: 'success' } }));
                                                        } else {
                                                            this.value = this.initialValue; // Revert on failure
                                                            window.dispatchEvent(new CustomEvent('flash', { detail: { message: '❌ ' + (data.message || 'Gagal menyimpan harga varian!'), type: 'error' } }));
                                                        }
                                                    })
                                                    .catch(() => {
                                                        this.value = this.initialValue; // Revert on error
                                                        window.dispatchEvent(new CustomEvent('flash', { detail: { message: '❌ Terjadi kesalahan saat menyimpan data', type: 'error' } }));
                                                    })
                                                    .finally(() => {
                                                        this.saving = false;
                                                        this.editing = false;
                                                    });
                                            }
                                        }"
                                            class="flex items-center space-x-2 mb-2 p-1 rounded transition-colors duration-150">
                                            <span
                                                class="font-semibold text-sm text-gray-700">{{ $variant['name'] }}:</span>

                                            {{-- Display Mode --}}
                                            <template x-if="!editing">
                                                <div class="flex items-center">
                                                    <span
                                                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(initialValue)"
                                                        class="text-sm font-medium">
                                                    </span>
                                                    <button type="button"
                                                        @click="editing = true; $nextTick(() => $el.parentNode.querySelector('input').focus())"
                                                        class="ml-2 text-gray-400 hover:text-indigo-600 transition-colors duration-150 p-1 rounded-full hover:bg-indigo-100"
                                                        :disabled="saving">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            {{-- Edit Mode --}}
                                            <template x-if="editing">
                                                <div class="flex items-center space-x-1">
                                                    <input type="number" x-model.number="value"
                                                        @keydown.enter.prevent="savePrice()"
                                                        @keydown.escape.prevent="editing = false; value = initialValue"
                                                        @blur.away="editing = false; value = initialValue"
                                                        :disabled="saving"
                                                        class="border border-indigo-300 rounded-lg w-32 px-2 py-1 text-sm text-right focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150">

                                                    <button type="button" @click="savePrice()"
                                                        class="bg-indigo-500 text-white p-1 rounded-full hover:bg-indigo-600 disabled:opacity-50 transition-colors"
                                                        :disabled="saving">
                                                        {{-- Save Icon --}}
                                                        <svg x-show="!saving" xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        {{-- Loading Spinner (Replace with your preferred spinner if needed) --}}
                                                        <svg x-show="saving" class="animate-spin h-4 w-4 text-white"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12"
                                                                r="10" stroke="currentColor" stroke-width="4">
                                                            </circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    @endforeach
                                </td>
                                @continue
                            @endif

                            {{-- Inline Edit Stok per Varian --}}
                            @if ($k === 'stock')
                                <td class="text-left py-2 px-4">
                                    @foreach ($cell as $variant)
                                        <div x-data="{
                                            editing: false,
                                            initialValue: {{ $variant['stock'] }},
                                            value: {{ $variant['stock'] }},
                                            saving: false,
                                            variantId: {{ $variant['id'] }},
                                            saveStock() {
                                                this.saving = true;
                                                fetch('{{ route('product.update-single', $row['id']) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({
                                                            variant_id: this.variantId,
                                                            field: 'stock',
                                                            value: this.value
                                                        })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            this.initialValue = this.value; // Update initial value on success
                                                            window.dispatchEvent(new CustomEvent('flash', { detail: { message: '✅ Stok varian berhasil diubah', type: 'success' } }));
                                                        } else {
                                                            this.value = this.initialValue; // Revert on failure
                                                            window.dispatchEvent(new CustomEvent('flash', { detail: { message: '❌ ' + (data.message || 'Gagal menyimpan stok varian!'), type: 'error' } }));
                                                        }
                                                    })
                                                    .catch(() => {
                                                        this.value = this.initialValue; // Revert on error
                                                        window.dispatchEvent(new CustomEvent('flash', { detail: { message: '❌ Terjadi kesalahan saat menyimpan data', type: 'error' } }));
                                                    })
                                                    .finally(() => {
                                                        this.saving = false;
                                                        this.editing = false;
                                                    });
                                            }
                                        }"
                                            class="flex items-center space-x-2 mb-2 p-1 rounded transition-colors duration-150">
                                            <span
                                                class="font-semibold text-sm text-gray-700">{{ $variant['name'] }}:</span>

                                            {{-- Display Mode --}}
                                            <template x-if="!editing">
                                                <div class="flex items-center">
                                                    <span x-text="initialValue"
                                                        class="text-sm font-medium text-gray-800">
                                                    </span>
                                                    <button type="button"
                                                        @click="editing = true; $nextTick(() => $el.parentNode.querySelector('input').focus())"
                                                        class="ml-2 text-gray-400 hover:text-indigo-600 transition-colors duration-150 p-1 rounded-full hover:bg-indigo-100"
                                                        :disabled="saving">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            {{-- Edit Mode --}}
                                            <template x-if="editing">
                                                <div class="flex items-center space-x-1">
                                                    <input type="number" x-model.number="value"
                                                        @keydown.enter.prevent="saveStock()"
                                                        @keydown.escape.prevent="editing = false; value = initialValue"
                                                        @blur.away="editing = false; value = initialValue"
                                                        :disabled="saving"
                                                        class="border border-indigo-300 rounded-lg w-24 px-2 py-1 text-sm text-right focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150">

                                                    <button type="button" @click="saveStock()"
                                                        class="bg-indigo-500 text-white p-1 rounded-full hover:bg-indigo-600 disabled:opacity-50 transition-colors"
                                                        :disabled="saving">
                                                        {{-- Save Icon --}}
                                                        <svg x-show="!saving" xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        {{-- Loading Spinner --}}
                                                        <svg x-show="saving" class="animate-spin h-4 w-4 text-white"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12"
                                                                r="10" stroke="currentColor" stroke-width="4">
                                                            </circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    @endforeach
                                </td>
                                @continue
                            @endif

                            {{-- Toggle Aktif per Varian --}}
                            @if ($k === 'active')
                                <td class="text-left py-2 px-4">
                                    @foreach ($cell as $variant)
                                        <div x-data="{
                                            active: {{ $variant['active'] ? 'true' : 'false' }},
                                            saving: false,
                                            variantId: {{ $variant['id'] }},
                                            toggleActive() {
                                                this.saving = true;
                                                fetch('{{ route('product.update-single', $row['id']) }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({
                                                            variant_id: this.variantId,
                                                            field: 'active',
                                                            value: this.active ? 1 : 0
                                                        })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            window.dispatchEvent(new CustomEvent('flash', { detail: { message: '✅ Status varian berhasil diperbarui', type: 'success' } }));
                                                        } else {
                                                            this.active = !this.active; // revert kalau gagal
                                                            window.dispatchEvent(new CustomEvent('flash', { detail: { message: '❌ ' + (data.message || 'Gagal menyimpan status varian!'), type: 'error' } }));
                                                        }
                                                    })
                                                    .catch(() => {
                                                        this.active = !this.active;
                                                        window.dispatchEvent(new CustomEvent('flash', { detail: { message: '❌ Terjadi kesalahan saat menyimpan data', type: 'error' } }));
                                                    })
                                                    .finally(() => this.saving = false);
                                            }
                                        }"
                                            class="flex items-center space-x-2 mb-2 p-1 rounded transition-colors duration-150">
                                            <button type="button" @click="active = !active; toggleActive()"
                                                :disabled="saving"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300"
                                                :class="active ? 'bg-green-600' : 'bg-gray-300'">
                                                <span class="sr-only">Toggle Active</span>
                                                <span
                                                    class="inline-block h-4 w-4 transform bg-white rounded-full transition-transform duration-300"
                                                    :class="active ? 'translate-x-6' : 'translate-x-1'"></span>
                                            </button>

                                            <svg x-show="saving" class="animate-spin h-4 w-4 text-gray-500 ml-1"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endforeach
                                </td>
                                @continue
                            @endif

                            {{-- Kolom promo --}}
                            @if ($k === 'promo')
                                <td>
                                    @if ($cell == 1)
                                        <i class="fa fa-toggle-on text-[#086B35]"></i>
                                    @else
                                        <i class="fa fa-toggle-off text-gray-800"></i>
                                    @endif
                                </td>
                                @continue
                            @endif

                            {{-- Kolom auction --}}
                            @if ($k === 'auction')
                                <td>
                                    @if ($cell && isset($cell['active']) && $cell['active'] == 1)
                                        <i class="fa fa-gear hover:rotate-90 duration-1000 cursor-pointer text-[#086B35]"
                                            x-on:click="$dispatch('open-modal', {data: JSON.stringify(@js($cell)), name: 'modal-auction'})"></i>
                                    @else
                                        <i class="fa fa-gear hover:-rotate-90 duration-1000 cursor-pointer text-gray-800"
                                            x-on:click="$dispatch('open-modal', {data: JSON.stringify(@js($cell)), name: 'modal-auction'})"></i>
                                    @endif
                                </td>
                                @continue
                            @endif

                            {{-- Default cell --}}
                            <td class="w-[500px]">
                                @if (isset($specialCol) && $k === $specialCol['colName'] && $specialCol['type'] === 'list')
                                    <ol class="list-decimal">
                                        @foreach ($cell as $item)
                                            <li class="border-b border-primary mb-2 pb-1">{{ $item }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    {!! $cell !!}
                                @endif
                            </td>
                        @endforeach

                        {{-- Aksi --}}
                        <td class="flex items-center justify-evenly gap-2">
                            @if (Route::has($routeName . '.destroy'))
                                <div class="tooltip" data-tip="Delete">
                                    <form action="{{ route($routeName . '.destroy', $row['id']) }}" method="post"
                                        class="inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="delete">
                                            <i class="fa fa-trash text-red-600"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if (Route::has($routeName . '.edit'))
                                <div class="tooltip" data-tip="Edit">
                                    <a href="{{ route($routeName . '.edit', $row['id']) }}">
                                        <i class="fa fa-pen text-blue-600"></i>
                                    </a>
                                </div>
                            @endif

                            @if (Route::has($routeName . '.show'))
                                <div class="tooltip" data-tip="Lihat">
                                    <a href="{{ route($routeName . '.show', $row['id']) }}">
                                        <i class="fa fa-eye text-teal-600"></i>
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {!! $data->links() !!}
    </div>

    @include('admin.product.auction-modal')
</div>

@push('scripts')
    <script>
        (function() {
            const deleteBtn = document.querySelectorAll('button.delete')
            deleteBtn.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    const isDeleteConfirmed = confirm('Apakah Anda yakin ingin menghapus item ini ?')
                    if (!isDeleteConfirmed) e.preventDefault()
                })
            })

            // UPDATE SEARCH INPUT PLACEHOLDER
            const searchInput = document.getElementById('search')
            const searchBy = document.getElementById('search-by')

            function updateSearchInputAttr() {
                searchInput.setAttribute('placeholder',
                    `Cari berdasarkan ${searchBy ? searchBy.options[searchBy.selectedIndex].text : "nama"}`)
                searchInput.setAttribute('name', searchBy ? searchBy.value : 'nama')
            }
            updateSearchInputAttr()
            searchBy && searchBy.addEventListener('change', function() {
                updateSearchInputAttr()
                searchInput.value = null
                searchInput.focus()
            })

            // SUBMIT SEARCH AFTER NUMBER OF ROWS FILTER CHANGED
            const numbRows = document.getElementById('numbRows')
            const submitForm = document.getElementById('form_search')
            numbRows.addEventListener('change', function() {
                submitForm.submit()
            })
        })()
    </script>
@endpush
