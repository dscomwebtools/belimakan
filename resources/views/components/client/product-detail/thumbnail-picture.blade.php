<div x-data="gallery()"
    class="img-slide-container bg-base-200 shadow-xl text-base-content p-4 w-full h-fit sm:w-3/4 md:w-1/2 mx-auto lg:w-1/4"
    @open-fullscreen.window="openFullscreen($event.detail)" @keydown.escape.window="close()">
    <div class="swiper mySwiper2">
        <div class="swiper-wrapper">
            @if ($product->product_pictures->isNotEmpty())
                @foreach ($product->product_pictures as $picture)
                    <div class="swiper-slide">
                        <div x-data="imageZoom('{{ Storage::url($picture->path) }}')" class="relative overflow-hidden w-full cursor-zoom-in"
                            @mousemove="move($event)" @mouseenter="show = true" @mouseleave="show = false"
                            @click="$dispatch('open-fullscreen', src)">
                            <img :src="src"
                                class="object-cover transition-transform duration-200 w-full h-[420px] md:h-[500px]"
                                :style="show
                                    ?
                                    `transform: scale(${zoom}); transform-origin: ${originX}% ${originY}%;` :
                                    'transform: scale(1);'"
                                alt="{{ $product->name }}">
                        </div>
                    </div>
                @endforeach
            @else
                <img src="{{ '/assets/images/image-not-found.webp' }}" alt="{{ $product->name }}" class="rounded-xl" />
            @endif
        </div>
    </div>

    <div class="swiper mySwiper mt-2">
        <div class="swiper-wrapper">
            @foreach ($product->product_pictures as $picture)
                <div class="swiper-slide">
                    <img src="{{ Storage::url($picture->path) }}" class="rounded-md cursor-pointer" />
                </div>
            @endforeach
        </div>
    </div>

    <div x-show="open" x-cloak x-transition.opacity
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-80 p-4"
        @click.self="close()">

        <div class="bg-white rounded-2xl p-4 relative max-w-6xl w-full flex flex-col md:flex-row gap-4" x-transition>
            <button @click="close()" class="absolute top-[5px] md:top-3 right-[5px] md:right-3 text-gray-600 hover:text-black text-2xl leading-none"
                aria-label="Close">&times;</button>

            <div class="flex-1 flex items-center justify-center">
                <img :src="imgSrc" class="max-h-[60vh] md:max-h-[85vh] object-contain rounded-xl" alt="Product image">
            </div>

            <div
                class="w-full md:w-28 flex flex-row md:flex-col gap-2 overflow-x-auto md:overflow-y-auto max-h-[85vh] pr-4">
                @foreach ($product->product_pictures as $picture)
                    <img src="{{ Storage::url($picture->path) }}" @click="imgSrc = '{{ Storage::url($picture->path) }}'"
                        class="w-20 h-20 object-cover cursor-pointer rounded-md border-2"
                        :class="imgSrc === '{{ Storage::url($picture->path) }}' ? 'border-blue-500' : 'border-transparent'">
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function imageZoom(src) {
        return {
            src: src,
            zoom: 1.6,
            show: false,
            originX: 50,
            originY: 50,
            move(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                this.originX = x;
                this.originY = y;
            }
        }
    }

    function gallery() {
        return {
            open: false,
            imgSrc: '',
            openFullscreen(src) {
                this.imgSrc = src;
                this.open = true;
                document.documentElement.classList.add('overflow-hidden');
                document.body.classList.add('overflow-hidden');
            },
            close() {
                this.open = false;
                this.imgSrc = '';
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    }
</script>
