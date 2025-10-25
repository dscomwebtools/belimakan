<footer class="footer p-10 bg-base-200 text-base-content">
    <nav class="md:mx-auto">
        <h6 class="footer-title tracking-widest">Panduan Belanja</h6>
        <a class="link link-hover" href="{{ route('client.how-to-order') }}">Cara Belanja</a>
        <a class="link link-hover" href="{{ route('client.how-to-return') }}">Cara Pengembalian</a>
        <a class="link link-hover" href="{{ route('client.payment-method') }}">Metode Pembayaran</a>
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
            <p>Toko Online</p>
            <strong>DSCOMPUTER SOLUTION</strong>

            {{-- Visitor Counter --}}
            <div class="mt-2 flex items-center space-x-1 text-sm">
                <span class="text-xs text-white/80 mr-2">Pengunjung:</span>
                <div class="flex bg-black/40 px-2 py-1 rounded-md shadow-inner tracking-widest font-mono">
                    @foreach (str_split(str_pad($visitorCount, 4, '0', STR_PAD_LEFT)) as $digit)
                        <span
                            class="bg-green-600 text-white rounded-sm px-1 mx-0.5 shadow-md">{{ $digit }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>
    <nav class="md:place-self-center md:justify-self-end">
        <div class="grid grid-flow-col gap-1">
            @foreach ($sosmed as $item)
                <a href="{{ $item->url }}" target="_blank"
                    class="bg-white rounded-full p-2 flex items-center justify-center">
                    <img src="{{ Storage::url($item->logo) }}" alt="{{ $item->name . 's logo' }}"
                        class="w-[30px] h-[30px] object-contain">
                </a>
            @endforeach
        </div>
    </nav>
</footer>
