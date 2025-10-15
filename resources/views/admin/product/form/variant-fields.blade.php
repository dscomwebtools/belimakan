<template x-if="variantMode">
    <div class="form-content-with-variant mt-4" id="variant-container">
        <div class="divider tracking-widest font-bold text-xl">VARIAN PRODUK</div>

        <div class="flex items-end mb-4 mt-8">
            <div class="flex flex-col flex-1">
                <label for="variant" class="font-semibold mb-2">Tipe Varian</label>
                <select name="variant" id="variant" x-ref="variant">
                    <option></option>
                    @foreach ($variants as $item)
                        <option value="{{ $item->variant }}">{{ $item->variant }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-center mx-4">
                @include('components.ar.button-add-variant')
            </div>
        </div>

        <div class="flex flex-col flex-1 text-black mb-4">
            <select multiple="multiple" id="variant_values" x-ref="variant_values"></select>
        </div>

        <!-- Daftar tipe varian yang ditambahkan -->
        <div class="variant-list mt-4">
            <template x-for="(values, key) in variants" :key="key">
                <div class="flex items-center mb-2">
                    <div class="mr-2 p-1" x-text="key"></div>
                    <select x-model="variants[key]" multiple="multiple"
                        class="tipe-variant-edit mr-2 bg-gray-200 p-1 rounded"
                        @click="editVariantValues(key, $event.target.selectedOptions)" x-init="initSelect2($el)"
                        x-effect="updateSelect2($el, variants[key])" :data-key="key">
                    </select>
                    <button @click="removeVariant(key)" class="bg-red-500 text-white px-2 py-1 rounded ml-2"><i
                            class="fa fa-trash"></i></button>
                </div>
            </template>
        </div>

        @isset($data)
            <div class="animate-pulse my-2"><i class="fa fa-circle-exclamation mr-1"></i> Data varian akan direset jika Anda
                menambahkan varian baru</div>
        @endisset

        <div class="variant-fields-container flex flex-col border border-primary px-4 py-6 overflow-auto rounded">
            <table>
                <thead>
                    <tr>
                        <!-- Membuat header tabel dinamis berdasarkan keys dari variants -->
                        <template x-for="key in Object.keys(variants)" :key="key">
                            <th x-text="key" class="text-center"></th>
                        </template>
                        {{-- <th class="text-center">Varian</th> --}}
                        <th>Foto</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Berat <span class="text-sm">(g)</span></th>
                        <th>SKU</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(combination, index) in variantCombinations" :key="combination.join('-')">
                        <tr class="text-center">
                            <!-- kombinasi varian -->
                            <template x-for="value in combination" :key="value">
                                <td x-text="value" class="whitespace-nowrap px-2"></td>
                            </template>

                            <td>
                                <div
                                    class="relative flex flex-col items-center justify-center w-24 h-24 border-2 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                    <template x-if="productVariants[index]?.picture">
                                        <div class="relative w-full h-full">
                                            <img :src="productVariants[index].picture"
                                                class="object-cover w-full h-full rounded-lg" />
                                            <button type="button"
                                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                                                @click="removeVariantPicture(index)">
                                                ✕
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="!productVariants[index]?.picture">
                                        <label
                                            class="flex flex-col items-center justify-center w-full h-full cursor-pointer text-gray-400"
                                            :for="'variant_picture_' + index">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="text-xs">Upload</span>
                                        </label>
                                    </template>

                                    <input type="file" accept="image/*"
                                        :name="'variant_pictures[' + productVariants[index].id + ']'"
                                        :id="'variant_picture_' + index" class="hidden"
                                        @change="previewVariantPicture($event, index)" />
                                </div>
                            </td>

                            <!-- Kolom stok -->
                            <td>
                                <input type="number"
                                    @isset($data) :value="productVariants[index].stock" @endisset
                                    name="stock_variant[]" min="0" class="my-input bg-primary/5 rounded w-24">
                            </td>

                            <!-- 💰 Kolom harga -->
                            <td>
                                <input type="number"
                                    @isset($data) :value="productVariants[index].price" @endisset
                                    name="price_variant[]" min="0" class="my-input bg-primary/5 rounded w-40">
                            </td>

                            <!-- ⚖️ Kolom berat -->
                            <td>
                                <input type="number"
                                    @isset($data) :value="productVariants[index].weight" @endisset
                                    name="weight_variant[]" min="0" class="my-input bg-primary/5 rounded">
                            </td>

                            <!-- 🏷️ Kolom SKU -->
                            <td>
                                <input type="text"
                                    @isset($data) :value="productVariants[index].sku" @endisset
                                    name="sku_variant[]" class="my-input bg-primary/5 rounded">
                            </td>

                            <!-- ⚙️ Kolom status -->
                            <td>
                                <select name="active_variant[]" class="my-input">
                                    <option value="1"
                                        @isset($data) :selected="productVariants[index].active === 1" @else selected @endisset>
                                        Aktif</option>
                                    <option value="0"
                                        @isset($data) :selected="productVariants[index].active === 0" @endisset>
                                        Tidak Aktif</option>
                                </select>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <!-- Hidden inputs for variants and variantCombinations -->
            <input type="hidden" name="variantData" :value="JSON.stringify(variants)">
            <input type="hidden" name="variantCombinationsData" :value="JSON.stringify(variantCombinations)">

            <template x-for="(path, idx) in deletedVariantPictures" :key="idx">
                <input type="hidden" name="deleted_variant_pictures[]" :value="path">
            </template>
        </div>
    </div>
</template>
