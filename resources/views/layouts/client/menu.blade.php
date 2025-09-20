<dialog id="menuKategori" class="modal">
    <div class="modal-box w-11/12 max-w-5xl">
        <h3 class="font-bold text-xl md:text-2xl mb-4">Kategori</h3>

        <div class="flex flex-col items-start w-full">
            @foreach ($category_labels as $label)
                <div class="collapse collapse-arrow kategori-collapse">
                    <input type="checkbox" />
                    <div class="collapse-title text-sm md:text-base font-medium">
                        {{ $label->name }}
                    </div>
                    <div class="collapse-content">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach ($label->categories as $item)
                                <a href="{{ route('client.productsByCategory', $item->name) }}"
                                    class="px-3 py-2 font-bold rounded-lg hover:bg-base-300 transition text-left w-[220px] text-sm md:text-base leading-[20px]">
                                    {{ $item->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
