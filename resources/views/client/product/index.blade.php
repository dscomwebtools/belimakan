<x-client-layout>
    <div id="product-container">
        <h2 class="text-center text-xl sm:text-3xl uppercase font-bold tracking-wider">{{ $product_data->title }}</h2>
        <p class="text-center mb-8 mt-2">{{ $product_data->description }}</p>
        <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 justify-around" id="products">
            @foreach ($products->items() as $product)
                <x-client.product-item :product="$product" />
            @endforeach
        </div>

        <div id="loading" style="display: none; text-center w-full">Sedang memuat data...</div>
        <div class="w-full text-center my-2">
            <button id="load-more" class="btn btn-primary" data-page="{{ $products->currentPage() }}"
                data-last-page="{{ $products->lastPage() }}">
                Muat lebih banyak
            </button>
        </div>
    </div>
    <x-client.features />
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <script>
            $(document).ready(function() {
                $('#load-more').click(function() {
                    var page = $(this).data('page');
                    var lastPage = $(this).data('last-page');

                    if (page < lastPage) {
                        var nextPage = page + 1;
                        var nextNextPage = page + 2;

                        $('#loading').show();

                        // Ambil page pertama
                        $.ajax({
                            url: `${window.location.href}?page=${nextPage}`,
                            method: 'GET',
                            success: function(data) {
                                $('#products').append(data.html);

                                // Kalau masih ada page berikutnya → ambil page ke-2 juga
                                if (nextNextPage <= lastPage) {
                                    $.ajax({
                                        url: `${window.location.href}?page=${nextNextPage}`,
                                        method: 'GET',
                                        success: function(data) {
                                            $('#products').append(data.html);
                                            $('#loading').hide();
                                        },
                                        error: function() {
                                            $('#loading').hide();
                                            alert('Error loading page ' + nextNextPage);
                                        }
                                    });
                                } else {
                                    $('#loading').hide();
                                }

                                // Update data-page sesuai yang terakhir di-load
                                $('#load-more').data('page', Math.min(nextNextPage, lastPage));

                                // Sembunyikan tombol kalau sudah lastPage
                                if (nextNextPage >= lastPage) {
                                    $('#load-more').hide();
                                }
                            },
                            error: function() {
                                $('#loading').hide();
                                alert('Error loading page ' + nextPage);
                            }
                        });
                    } else {
                        $('#load-more').hide();
                    }
                });
            });
        </script>
    @endpush
</x-client-layout>
