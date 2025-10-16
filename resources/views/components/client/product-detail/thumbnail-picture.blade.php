<div x-data="gallery({{ $product->product_pictures->map(
    fn($p) => [
        'url' => Storage::url($p->path),
        'variant_id' => $p->variant_id,
    ],
) }})"
    class="img-slide-container bg-base-200 shadow-xl text-base-content p-4 w-full h-fit sm:w-3/4 md:w-1/2 mx-auto lg:w-1/4"
    @open-fullscreen.window="openFullscreen($event.detail)" @keydown.escape.window="close()"
    @variant-changed.window="selectedVariantId = $event.detail; onVariantChanged()">

    {{-- Main Preview (sekarang menampilkan 1 gambar utama yang reaktif) --}}
    <div class="my-4">
        <div x-data="imageZoom(activeMainUrl)" class="relative overflow-hidden w-full cursor-zoom-in h-[250px] md:h-[350px]"
            @mousemove="move($event)" @mouseenter="show = true" @mouseleave="show = false"
            @click="$dispatch('open-fullscreen', activeMainUrl)">
            <img :src="activeMainUrl" class="object-contain w-full h-full transition-transform duration-200"
                :style="show ?
                    `transform: scale(${zoom}); transform-origin: ${originX}% ${originY}%;` :
                    'transform: scale(1);'">
        </div>
    </div>

    {{-- Thumbnail (semua gambar tampil, klik akan ganti activeMainUrl) --}}
    <div class="swiper productSwiper mt-2">
        <div class="swiper-wrapper">
            <template x-for="pic in pictures" :key="pic.url">
                <div class="swiper-slide">
                    <img :src="pic.url" class="object-contain cursor-pointer border-4"
                        :class="activeMainUrl === pic.url ? 'border-green-600' : 'border-transparent'"
                        @click="setMain(pic.url)" />
                </div>
            </template>
        </div>
    </div>

    {{-- Fullscreen (semua gambar tampil juga; klik di sini juga update activeMainUrl) --}}
    <div x-show="open" x-cloak x-transition.opacity
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-80 p-4"
        @click.self="close()">

        <div class="bg-white rounded-2xl p-4 relative max-w-6xl w-full flex flex-col md:flex-row gap-4" x-transition>
            <button @click="close()"
                class="absolute top-[5px] md:top-3 right-[5px] md:right-3 text-gray-600 hover:text-black text-2xl leading-none"
                aria-label="Close">&times;</button>

            <div class="flex-1 flex items-center justify-center">
                <img :src="imgSrc" class="max-h-[60vh] md:max-h-[85vh] object-contain rounded-xl"
                    alt="Product image">
            </div>

            <div
                class="w-full md:w-28 flex flex-row md:flex-col gap-2 overflow-x-auto md:overflow-y-auto max-h-[85vh] pr-4">
                <template x-for="pic in pictures" :key="pic.url">
                    <img :src="pic.url" @click="setMain(pic.url); imgSrc = pic.url"
                        class="w-20 h-20 object-cover cursor-pointer border-4"
                        :class="imgSrc === pic.url ? 'border-green-600' : activeMainUrl === pic.url ? 'border-gray-400' :
                            'border-transparent'">
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    function imageZoom(src) {
        return {
            src,
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
        };
    }

    function gallery(allPictures) {
        return {
            open: false,
            imgSrc: '',
            selectedVariantId: null,
            pictures: allPictures,
            activeMainUrl: '',

            // dipanggil saat varian berubah
            onVariantChanged() {
                const filtered = this.filteredPictures();
                this.activeMainUrl = filtered.length ? filtered[0].url : (this.pictures[0]?.url || '');
                // update fullscreen preview juga
                this.imgSrc = this.activeMainUrl;
                this.updateSlides();
            },

            // hanya gambar utama yang mengikuti varian — tapi thumbnails tetap semua gambar
            filteredPictures() {
                if (this.selectedVariantId) {
                    const filtered = this.pictures.filter(p => p.variant_id == this.selectedVariantId);
                    return filtered.length ? filtered : this.pictures.filter(p => !p.variant_id);
                }
                return this.pictures.filter(p => !p.variant_id || p.variant_id === null);
            },

            // set main image ketika klik thumbnail
            setMain(url) {
                this.activeMainUrl = url;
                // juga set imgSrc jika fullscreen sedang terbuka
                if (this.open) this.imgSrc = url;
            },

            openFullscreen(src) {
                this.imgSrc = src || this.activeMainUrl;
                this.open = true;
                document.documentElement.classList.add('overflow-hidden');
                document.body.classList.add('overflow-hidden');
            },

            close() {
                this.open = false;
                this.imgSrc = '';
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            },

            updateSlides() {
                // Kalau masih pakai Swiper untuk thumbnail, inisialisasi ulang kalau perlu
                if (window.Swiper) {
                    setTimeout(() => {
                        // inisialisasi thumb swiper (jika kamu mau)
                        try {
                            new Swiper('.mySwiper', {
                                slidesPerView: 4,
                                spaceBetween: 10
                            });
                        } catch (e) {
                            /* ignore */
                        }
                    }, 50);
                }
            },

            // inisialisasi kecil saat component dibuat
            init() {
                // set activeMainUrl ke gambar pertama sesuai varian (atau default)
                const start = this.filteredPictures();
                this.activeMainUrl = start.length ? start[0].url : (this.pictures[0]?.url || '');
                this.imgSrc = this.activeMainUrl;
                // inisialisasi swiper thumbnail jika ada
                this.updateSlides();
            }
        };
    }

    // panggil init ketika Alpine siap
    document.addEventListener('alpine:init', () => {
        // nothing here — Alpine akan memanggil init() bila kamu menambahkan x-init di markup
    });
</script>
<style>
    /* Hapus efek gray/inactive dari swiper thumbnail */
    .mySwiper .swiper-slide {
        opacity: 1 !important;
        filter: none !important;
        transition: none !important;
    }
</style>
