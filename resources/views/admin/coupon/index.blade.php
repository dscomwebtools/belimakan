<x-app-layout>
    <div class="sm:mx-6 lg:mx-8 p-6 py-10 rounded overflow-x-auto">
        <div class="flex flex-col md:flex-row">
            <a href="{{ route('coupon.create') }}"
                class="bg-primary text-primary-content w-fit h-fit rounded px-4 py-2 mb-4 inline-block">
                <i class="fa fa-plus"></i>
            </a>

            <span class="ml-auto mb-3">
                <form class="flex flex-wrap gap-2 [&_option]:bg-secondary">
                    {{-- jumlah per halaman --}}
                    <div class="tooltip" data-tip="Data per halaman">
                        <select name="numb_per_page" class="my-input mr-2">
                            <option value="5" @if ($numb_per_page == '5') selected @endif>5</option>
                            <option value="10" @if ($numb_per_page == '10') selected @endif>10</option>
                            <option value="25" @if ($numb_per_page == '25') selected @endif>25</option>
                            <option value="50" @if ($numb_per_page == '50') selected @endif>50</option>
                            <option value="100" @if ($numb_per_page == '100') selected @endif>100</option>
                        </select>
                    </div>

                    {{-- cari berdasarkan --}}
                    <div class="tooltip" data-tip="Cari Berdasarkan">
                        <select id="search-by" class="my-input mr-2">
                            <option value="code" @isset($validated['code']) selected @endisset>Kode</option>
                            <option value="description" @isset($validated['description']) selected @endisset>Deskripsi
                            </option>
                        </select>
                    </div>

                    {{-- filter tanggal --}}
                    <div class="tooltip" data-tip="Tanggal Dari">
                        <input type="date" id="date_from" name="date_from" class="my-input mr-2 text-accent-content"
                            value="{{ $validated['date_from'] ?? '' }}">
                    </div>
                    <div class="tooltip" data-tip="Tanggal Sampai">
                        <input type="date" id="date_to" name="date_to" class="my-input mr-2 text-accent-content"
                            value="{{ $validated['date_to'] ?? '' }}">
                    </div>

                    {{-- filter aktif / nonaktif --}}
                    <div class="tooltip" data-tip="Status Kupon">
                        <select name="active" class="my-input mr-2">
                            <option value="">Semua</option>
                            <option value="1" @if (($validated['active'] ?? '') === '1') selected @endif>Aktif</option>
                            <option value="0" @if (($validated['active'] ?? '') === '0') selected @endif>Tidak Aktif
                            </option>
                        </select>
                    </div>

                    {{-- input pencarian --}}
                    <div class="inline-block my-input whitespace-nowrap">
                        <input type="text" id="search"
                            class="border-transparent focus:outline-none focus:ring-0 focus:border-transparent bg-transparent"
                            @isset($validated['code']) value="{{ $validated['code'] }}" @endisset
                            @isset($validated['description']) value="{{ $validated['description'] }}" @endisset>

                        <button type="submit"
                            class="bg-primary/70 hover:bg-primary border border-primary py-1 px-2 rounded-full cursor-pointer hover:scale-105 text-primary-content">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="table bg-base-200">
                <thead>
                    <tr class="bg-base-300">
                        <th class="text-center">No</th>
                        <th>Kode</th>
                        <th>Deskripsi</th>
                        <th>Minimal Belanja</th>
                        <th>Diskon</th>
                        <th>Aktif</th>
                        <th>Digunakan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr class="odd:bg-primary/5">
                            <td class="text-center">{{ ++$indexNumber }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->description }}</td>
                            <td>Rp {{ number_format($item->min_amount, 0, ',', '.') }}</td>
                            <td>
                                @if ($item->discount_amount)
                                    Rp {{ number_format($item->discount_amount, 0, ',', '.') }}
                                @elseif ($item->discount_percent)
                                    {{ $item->discount_percent }}%
                                @endif
                            </td>
                            <td>
                                @if ($item->active)
                                    <span class="text-green-600 font-bold">Aktif</span>
                                @else
                                    <span class="text-red-600 font-bold">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $item->used_count }}x</td>
                            <td class="flex justify-evenly">
                                <div class="tooltip" data-tip="Delete">
                                    <form action="{{ route('coupon.destroy', $item->id) }}" method="post"
                                        class="inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="delete">
                                            <i class="fa fa-trash text-red-600"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="tooltip" data-tip="Edit">
                                    <a href="{{ route('coupon.edit', $item->id) }}" class="mx-2">
                                        <i class="fa fa-pen text-blue-600"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div
                class="[&_p]:!text-secondary-content [&_.bg-white]:bg-primary [&_.bg-white]:text-primary-content mt-10">
                {!! $data->links() !!}
            </div>
        </div>

        @push('scripts')
            <script>
                (function() {
                    // DELETE CONFIRMATION
                    const deleteBtn = document.querySelectorAll('button.delete')
                    deleteBtn.forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            const isDeleteConfirmed = confirm('Apakah Anda yakin ingin menghapus kupon ini?')
                            if (!isDeleteConfirmed) {
                                e.preventDefault()
                            }
                        })
                    })

                    // UPDATE SEARCH INPUT PLACEHOLDER
                    const searchInput = document.getElementById('search')
                    const searchBy = document.getElementById('search-by')

                    function updateSearchInputAttr() {
                        searchInput.setAttribute('placeholder',
                            `Cari berdasarkan ${searchBy.options[searchBy.selectedIndex].text}`)
                        searchInput.setAttribute('name', searchBy.value)
                    }

                    updateSearchInputAttr()
                    searchBy.addEventListener('change', function() {
                        updateSearchInputAttr()
                        searchInput.value = null
                        searchInput.focus()
                    })
                })()
            </script>
        @endpush
    </div>
</x-app-layout>
