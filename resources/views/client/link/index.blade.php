<x-link-layout>
    <div
        class="min-h-screen flex flex-col items-center bg-green-600 text-white px-6 py-6 relative overflow-hidden rounded my-4 mx-4">

        {{-- Background Effect (optional blur bubble) --}}
        <div
            class="absolute top-0 left-0 w-64 h-64 bg-green-400 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3">
        </div>
        <div
            class="absolute bottom-0 right-0 w-72 h-72 bg-emerald-500 rounded-full blur-3xl translate-x-1/3 translate-y-1/3">
        </div>

        {{-- Store Intro --}}
        <div
            class="relative z-10 bg-white text-black flex flex-col items-center text-center bg-white backdrop-blur-sm px-4 py-4 rounded-2xl shadow-xl border border-white max-w-md w-full">
            <a href="/">
                <img src="{{ asset('assets/images/logo.webp') }}" alt="logo" width="100px">
            </a>
            <h1 class="text-2xl font-bold mb-2 tracking-wide mt-4">DSCOMPUTER SOLUTION</h1>
            <p class="text-sm leading-relaxed">
                Produk original 💻 | Harga bersaing 💰 | Layanan cepat ⚡
            </p>
        </div>

        <div class="flex items-center w-full my-4">
            <div class="flex-grow w-full bg-white rounded" style="height: 4px;"></div>
            <span class="px-3 text-base uppercase tracking-wider text-white font-semibold">Social</span>
            <div class="flex-grow w-full bg-white rounded" style="height: 4px;"></div>
        </div>

        {{-- Links Section --}}
        <div class="relative z-10 w-full flex flex-col gap-4">
            @foreach ($sosmed as $item)
                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer"
                    class="group w-full bg-white text-green-600 font-semibold py-3 rounded-xl shadow-md hover:bg-green-500 hover:text-white transition-all duration-200 flex items-center justify-center gap-3">
                    @if ($item->logo)
                        <img src="{{ Storage::url($item->logo) }}" alt="{{ $item->name }}" class="w-6 h-6 rounded">
                    @endif
                    <span>{{ $item->name }}</span>
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <footer class="relative z-10 mt-6 text-white/70 text-xs text-center">
            <p>© {{ date('Y') }} {{ config('app.name') }} — Komputer, Aksesoris & Perlengkapan IT Terbaik</p>
        </footer>

    </div>
</x-link-layout>
