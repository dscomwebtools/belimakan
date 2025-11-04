@props(['product'])

@section('meta')
    <x-meta title="{{ $product->name }}" seoTitle="{{ $product->seo_title }}" seoDescription="{{ $product->seo_description }}"
        description="{{ Str::limit(strip_tags($product->description), 150) }}"
        image="{{ url(Storage::url($product->product_pictures->first()->path ?? 'default.jpg')) }}"
        brand="{{ $product->brand->name ?? '' }}" type="product" />
@endsection

<div class="flex flex-wrap gap-2 mb-4" x-data>
    @if (isset($product->auction) && $product->auction->active)
        <x-client.product-detail.lelang :product="$product" />
    @elseif (isset($product->promo) && $product->promo->active)
        <x-client.product-detail.promo :product="$product" />
    @else
        <x-client.product-detail.normal :product="$product" />
    @endif
</div>

@if (isset($product->auction) && $product->auction->active)
    <div id="bidding" class="mb-14 mt-0"></div>
    @if (\Carbon\Carbon::now()->greaterThan($product->auction->endtime))
        {{-- Kalau waktu sudah lewat --}}
        <div class="text-center text-error animate-pulse py-6 uppercase font-bold">Lelang sudah berakhir</div>
        {{-- <span class="text-red-500">Lelang sudah berakhir</span> --}}
    @else
        {{-- Kalau waktu belum lewat --}}
        <div class="w-full sm:w-11/12 mb-6 mx-auto hidden lg:block">
            <!-- Side-by-side layout -->
            <div class="p-6 bg-base-200 rounded md:grid md:grid-cols-2 md:gap-6">

                <!-- Bidding -->
                <div>
                    @guest
                        <div class="text-center text-error animate-pulse">Silahkan login untuk melakukan bidding</div>
                    @endguest
                    <h2 class="text-lg font-semibold pb-1 px-3 rounded border-b-2 border-b-primary w-fit mx-auto">
                        Bidding
                    </h2>
                    @can('is-admin')
                        <div class="flex gap-1 justify-center my-4">
                            Hanya member yang bisa bidding
                        </div>
                    @endcan
                    @can('is-member')
                        <div class="flex gap-1 justify-center my-4">
                            <form action="{{ route('bid.store') }}" method="POST">
                                @csrf
                                <input type="number" name="value"
                                    value="{{ $product->auction->bids->isNotEmpty() ? $product->auction->bids->first()->value + $product->auction->bid_increment : $product->auction->bid_start }}"
                                    step="{{ $product->auction->bid_increment }}"
                                    min="{{ $product->auction->bids->isNotEmpty() ? $product->auction->bids->first()->value + $product->auction->bid_increment : $product->auction->bid_start }}"
                                    class="input">
                                <input type="hidden" name="auction_id" value="{{ $product->auction->id }}">
                                <div class="tooltip" data-tip="Bid">
                                    <button class="btn btn-primary" type="submit">
                                        <fa class="fa fa-bullseye"></fa>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endcan

                    @auth
                        @foreach ($product->auction->bids as $bid)
                            <div
                                class="chat {{ auth()->user() && $bid->user->id === auth()->user()->id ? 'chat-end' : 'chat-start' }}">
                                <div class="chat-image avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="{{ 'Foto ' . $bid->user->name }}"
                                            src="{{ Storage::url($bid->user->img) }}" />
                                    </div>
                                </div>
                                <div class="chat-header">
                                    {{ auth()->user() && $bid->user->id === auth()->user()->id ? 'Anda' : $bid->user->name }}
                                </div>
                                <div class="chat-bubble">
                                    <x-client.format-rp value="{{ $bid->value }}" />
                                </div>
                                <div class="chat-footer opacity-50">
                                    {{ $bid->created_at->isoFormat('LLLL') }}
                                </div>
                            </div>
                        @endforeach
                    @endauth
                </div>

                <!-- Komentar -->
                <div>
                    @guest
                        <div class="text-center text-error animate-pulse">Silahkan login untuk berkomentar</div>
                    @endguest
                    <h2 class="text-lg font-semibold pb-1 px-3 rounded border-b-2 border-b-primary w-fit mx-auto">
                        Komentar
                    </h2>
                    @auth
                        <div class="flex gap-1 justify-center my-4">
                            <form action="{{ route('comment.store') }}" method="POST">
                                @csrf
                                <input type="text" name="comment" class="input" placeholder="komentar yang sopan">
                                <input type="hidden" name="auction_id" value="{{ $product->auction->id }}">
                                <div class="tooltip" data-tip="Komentar">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fa fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                    @endauth

                    @auth
                        @foreach ($product->auction->comments as $comment)
                            <div
                                class="chat {{ auth()->user() && $comment->user->id === auth()->user()->id ? 'chat-end' : 'chat-start' }}">
                                <div class="chat-image avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="{{ 'Foto ' . $comment->user->name }}"
                                            src="{{ Storage::url($comment->user->img) }}" />
                                    </div>
                                </div>
                                <div class="chat-header">
                                    {{ auth()->user() && $comment->user->id === auth()->user()->id ? 'Anda' : $comment->user->name }}
                                </div>
                                <div class="chat-bubble">{{ $comment->comment }}</div>
                                <div class="chat-footer opacity-50">
                                    {{ $comment->created_at->isoFormat('LLLL') }}
                                </div>
                            </div>
                        @endforeach
                    @endauth
                </div>
            </div>
        </div>

        <div class="w-full sm:w-11/12 mb-6 mx-auto block lg:hidden" x-data>
            <div class="flex justify-center">
                <div class="flex justify-center border-b border-gray-300">
                    <button class="px-6 py-2 text-sm font-semibold rounded-t-lg transition-colors duration-300"
                        :class="!$store.auction.isCommentTab ?
                            'bg-primary text-white border-b-2 border-primary' :
                            'text-gray-600 hover:text-gray-900'"
                        @click="$store.auction.isCommentTab = false">
                        Bidding
                    </button>

                    <button class="px-6 py-2 text-sm font-semibold rounded-t-lg transition-colors duration-300"
                        :class="$store.auction.isCommentTab ?
                            'bg-primary text-white border-b-2 border-primary' :
                            'text-gray-600 hover:text-gray-900'"
                        @click="$store.auction.isCommentTab = true">
                        Komentar
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="p-6 bg-base-200 rounded">
                <div x-show="!$store.auction.isCommentTab">
                    @guest
                        <div class="text-center text-error animate-pulse">Silahkan login untuk melakukan bidding</div>
                    @endguest
                    <h2 class="text-lg font-semibold pb-1 px-3 rounded border-b-2 border-b-primary w-fit mx-auto">
                        Bidding
                    </h2>
                    @can('is-admin')
                        <div class="flex gap-1 justify-center my-4">
                            Hanya member yang bisa bidding
                        </div>
                    @endcan
                    @can('is-member')
                        <div class="flex gap-1 justify-center my-4">
                            <form action="{{ route('bid.store') }}" method="POST">
                                @csrf
                                <input type="number" name="value"
                                    value="{{ $product->auction->bids->isNotEmpty() ? $product->auction->bids->first()->value + $product->auction->bid_increment : $product->auction->bid_start }}"
                                    step="{{ $product->auction->bid_increment }}"
                                    min="{{ $product->auction->bids->isNotEmpty() ? $product->auction->bids->first()->value + $product->auction->bid_increment : $product->auction->bid_start }}"
                                    class="input">
                                <input type="hidden" name="auction_id" value="{{ $product->auction->id }}">
                                <div class="tooltip" data-tip="Bid">
                                    <button class="btn btn-primary" type="submit">
                                        <fa class="fa fa-bullseye"></fa>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endcan

                    @auth
                        @foreach ($product->auction->bids as $bid)
                            <div
                                class="chat {{ auth()->user() && $bid->user->id === auth()->user()->id ? 'chat-end' : 'chat-start' }}">
                                <div class="chat-image avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="{{ 'Foto ' . $bid->user->name }}"
                                            src="{{ Storage::url($bid->user->img) }}" />
                                    </div>
                                </div>
                                <div class="chat-header">
                                    {{ auth()->user() && $bid->user->id === auth()->user()->id ? 'Anda' : $bid->user->name }}
                                </div>
                                <div class="chat-bubble">
                                    <x-client.format-rp value="{{ $bid->value }}" />
                                </div>
                                <div class="chat-footer opacity-50">
                                    {{ $bid->created_at->isoFormat('LLLL') }}
                                </div>
                            </div>
                        @endforeach
                    @endauth

                </div>
                <div x-show="$store.auction.isCommentTab">
                    @guest
                        <div class="text-center text-error animate-pulse">Silahkan login untuk berkomentar</div>
                    @endguest
                    <h2 class="text-lg font-semibold pb-1 px-3 rounded border-b-2 border-b-primary w-fit mx-auto">
                        Komentar
                    </h2>
                    @auth
                        <div class="flex gap-1 justify-center my-4">
                            <form action="{{ route('comment.store') }}" method="POST">
                                @csrf
                                <input type="text" name="comment" class="input" placeholder="komentar yang sopan">
                                <input type="hidden" name="auction_id" value="{{ $product->auction->id }}">
                                <div class="tooltip" data-tip="Komentar">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fa fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                    @endauth

                    @auth
                        @foreach ($product->auction->comments as $comment)
                            <div
                                class="chat {{ auth()->user() && $comment->user->id === auth()->user()->id ? 'chat-end' : 'chat-start' }}">
                                <div class="chat-image avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="{{ 'Foto ' . $comment->user->name }}"
                                            src="{{ Storage::url($comment->user->img) }}" />
                                    </div>
                                </div>
                                <div class="chat-header">
                                    {{ auth()->user() && $comment->user->id === auth()->user()->id ? 'Anda' : $comment->user->name }}
                                </div>
                                <div class="chat-bubble">{{ $comment->comment }}</div>
                                <div class="chat-footer opacity-50">
                                    {{ $comment->created_at->isoFormat('LLLL') }}
                                </div>
                            </div>
                        @endforeach
                    @endauth
                </div>
            </div>
        </div>
    @endif
@endif


<x-modal name="share-product" focusable>

    <div class="p-6 pb-12" x-data="{ copied: false }">
        <div class="text-center mb-6">
            <h1 class="font-bold text-2xl">Bagikan Produk</h1>
            <p class="text-sm text-gray-500">Pilih platform favoritmu untuk membagikan link ini</p>
        </div>

        <div class="flex justify-center gap-6">
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer.php?u={{ url()->current() }}" target="_blank"
                class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-600 text-white hover:scale-110 transition">
                <i class="fa fa-facebook text-xl"></i>
            </a>

            <!-- WhatsApp -->
            <a :href="whatsappUrl" target="_blank" x-data="deviceDetection()"
                class="w-12 h-12 rounded-full flex items-center justify-center bg-green-500 text-white hover:scale-110 transition">
                <i class="fa fa-whatsapp text-xl"></i>
            </a>

            <!-- Twitter -->
            <a href="https://twitter.com/share?url={{ url()->current() }}&text={{ $product->name }}" target="_blank"
                class="w-12 h-12 rounded-full flex items-center justify-center bg-sky-400 text-white hover:scale-110 transition">
                <i class="fa fa-twitter text-xl"></i>
            </a>

            <!-- Copy -->
            <button
                @click.prevent="
            navigator.clipboard.writeText('{{ url()->current() }}');
            copied = true;
            setTimeout(() => copied = false, 2500);
        "
                class="w-12 h-12 rounded-full flex items-center justify-center bg-gray-600 text-white hover:scale-110 transition">
                <i class="fa fa-copy text-xl"></i>
            </button>
        </div>

        <!-- Toast Notification -->
        <div x-show="copied" x-transition
            class="fixed bottom-6 right-6 bg-success text-white px-4 py-2 rounded-lg shadow-lg">
            ✅ Link copied to clipboard!
        </div>
    </div>

    <script>
        function deviceDetection() {
            return {
                isMobile: window.matchMedia("(max-width: 767px)").matches,
                get whatsappUrl() {
                    const currentUrl = '{{ url()->current() }}';
                    return this.isMobile ?
                        `https://api.whatsapp.com/send?text=${currentUrl}` :
                        `https://web.whatsapp.com/send?text=${currentUrl}`;
                }
            }
        }
    </script>
</x-modal>
