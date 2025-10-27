@php
    $url = url()->current();
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="robots" content="index, follow">

<link rel="canonical" href="{{ $url }}">

<meta name="description" content="{{ $description }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:type" content="{{ $type }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">


@if (isset($data->price))
    {{-- Schema.org JSON-LD untuk Produk --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "{{ $title }}",
        "image": "{{ $image }}",
        "description": "{{ $description }}",
        "brand": {
            "@type": "Brand",
            "name": "DSCOMPUTER"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ $url }}",
            "priceCurrency": "IDR",
            "price": "{{ $data->price }}",
            "availability": "https://schema.org/InStock"
        }
    }
    </script>
@endif
