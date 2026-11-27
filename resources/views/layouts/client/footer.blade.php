<footer class="footer p-10 bg-base-200 text-base-content">
    <nav class="md:mx-auto">
        <h6 class="footer-title tracking-widest">Panduan Belanja</h6>
        <a class="link link-hover" href="{{ route('client.how-to-order') }}">Cara Belanja</a>
        <a class="link link-hover" href="{{ route('client.how-to-return') }}">Cara Pengembalian</a>
        <a class="link link-hover" href="{{ route('client.payment-method') }}">Metode Pembayaran</a>
        <a class="link link-hover" href="{{ route('client.pricelist') }}">Download Pricelist</a>
    </nav>
    <nav class="md:mx-auto">
        <h6 class="footer-title tracking-widest">Promo & Layanan</h6>
        <a class="link link-hover" href="{{ route('client.promo') }}">Promo</a>
        <a class="link link-hover" href="{{ route('client.lelang') }}">Lelang</a>
    </nav>
    <nav class="md:mx-auto">
        <h6 class="footer-title tracking-widest">Informasi</h6>
        <a class="link link-hover" href="{{ route('client.about-us') }}">Tentang kami</a>
        <a class="link link-hover" href="{{ route('client.contact') }}">Kontak kami</a>
    </nav>
</footer>

<footer class="footer px-10 py-3 border-t bg-primary text-primary-content border-base-300 mb-16 lg:mb-0">
    <aside class="items-center grid-flow-col text-white">
        <span class="bg-white rounded p-2 hidden sm:block">
            <img src="{{ asset('assets/images/logo.webp') }}" alt="logo" width="50px" class="hidden sm:block">
        </span>
        <div class="flex flex-col">
            <p>Toko Sayur Segar Online</p>
            <strong>BELIMAKAN.ID</strong>
        </div>
    </aside>

    <nav class="md:place-self-center md:justify-self-end">
        <div class="grid grid-flow-col gap-1">
            @foreach ($sosmed as $item)
                <a href="{{ $item->url }}" target="_blank"
                    class="bg-white rounded-full p-2 flex items-center justify-center">
                    <img src="{{ Storage::url($item->logo) }}" alt="{{ $item->name . ' logo' }}"
                        class="w-[30px] h-[30px] object-contain">
                </a>
            @endforeach
        </div>
    </nav>

    {{-- WA ICON --}}
    @php
        use Carbon\Carbon;
        $now = Carbon::now('Asia/Jakarta');
        $hour = $now->hour;
        $isWorkingHour = $hour >= 9 && $hour < 17;
    @endphp

    <div x-data="{ open: false }" x-cloak
        class="fixed bottom-[120px] sm:bottom-[160px] lg:bottom-[100px] right-5 z-[10]">

        <!-- Main button -->
        <style>
            @keyframes pulse-ring {
                0% {
                    transform: scale(1);
                    opacity: 0.6;
                }

                70% {
                    transform: scale(3);
                    opacity: 0;
                }

                100% {
                    transform: scale(3);
                    opacity: 0;
                }
            }

            .pulse-bg::before,
            .pulse-bg::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 9999px;
                background-color: rgb(34 197 94 / 0.4);
                animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                z-index: -1;
            }

            .pulse-bg::after {
                animation-delay: 1s;
            }
        </style>

        <div class="relative inline-flex items-center gap-3">
            <!-- Label -->
            <div class="flex flex-col items-end bg-green-500 text-white p-2 rounded-xl z-10 shadow-md">
                <span class="text-sm font-semibold">Hubungi Kami</span>
            </div>

            <!-- Tombol WhatsApp -->
            <div class="relative flex items-center justify-center">
                <div class="pulse-bg absolute inset-0 flex items-center justify-center pointer-events-none"></div>

                <button @click="open = !open"
                    class="relative z-10 bg-green-500 rounded-full p-3 h-[50px] w-[50px] flex items-center justify-center shadow-lg text-white hover:scale-110 transition">
                    <i class="fa fa-whatsapp text-3xl"></i>
                </button>
            </div>
        </div>

        <!-- Expanded options -->
        <div x-show="open" x-transition @click.outside="open = false"
            class="flex flex-col gap-3 absolute bottom-16 right-0 z-10">

            @if ($isWorkingHour)
                {{-- Jam kerja: tampilkan semua kontak --}}
                @foreach ($wa as $contact)
                    <a href="https://wa.me/{{ $contact['hp'] }}" target="_blank"
                        class="bg-white shadow-md rounded-full p-2 flex items-center gap-2 hover:bg-gray-100">
                        <i class="fa fa-whatsapp text-green-500 text-xl"></i>
                        <span class="text-sm text-black">{{ $contact['name'] }}</span>
                    </a>
                @endforeach
            @else
                {{-- Di luar jam kerja: tampilkan nomor default --}}
                <a href="https://wa.me/6281383655055" target="_blank"
                    class="bg-white shadow-md rounded-full p-2 flex items-center gap-2 hover:bg-gray-100">
                    <i class="fa fa-whatsapp text-green-500 text-xl"></i>
                    <span class="text-sm text-black">Admin</span>
                </a>
            @endif
        </div>
    </div>
</footer>
