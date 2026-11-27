<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        use Illuminate\Support\Str;

        $metaContent = $meta ?? (View::hasSection('meta') ? trim(View::getSections()['meta'] ?? '') : null);

        $hasTitle = $metaContent && Str::contains($metaContent, '<title');
        $hasOgSiteName = $metaContent && Str::contains($metaContent, 'og:site_name');
        $hasOgType = $metaContent && Str::contains($metaContent, 'og:type');
    @endphp

    {{-- Hanya render <title> kalau belum ada --}}
    @if (!$hasTitle) <title>{{ $title ?? config('app.name', 'NOS') }}</title> @endif

    {{-- Render meta page-specific atau default --}}
    @if (!empty($meta)) {!! $meta !!}
@elseif (View::hasSection('meta'))
    @yield('meta')
@else
    {{-- OG default --}}
    @if (!$hasOgSiteName)
        <meta property="og:site_name" content="BeliMakan - Toko Sayur Segar Online"> @endif
    @if (!$hasOgType) <meta property="og:type" content="website"> @endif

    {{-- Default meta component --}}
    <x-meta title="BeliMakan - Toko Sayur Segar Online"
        description="Toko online sayur segar BeliMakan. Dapatkan aneka sayuran segar, hidroponik, organik, dan kebutuhan dapur langsung dari kebun petani lokal dengan garansi kesegaran 100% dan pengiriman cepat." />
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body class="antialiased">
    <x-ar.alert />

    <div class="min-h-screen w-full bg-white flex flex-col sm:justify-center items-center bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ 'assets/images/bg.webp' }}'); background-repeat: no-repeat; background-position: center; background-size: cover;">
        <div class="w-full sm:max-w-lg text-base-content overflow-hidden sm:rounded-lg">

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{ $slot }}
        </div>
    </div>
</body>

</html>
