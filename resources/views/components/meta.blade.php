{{-- ✅ Title & Meta --}}
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $url }}">

{{-- ✅ Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ config('app.name', 'BeliMakan') }} - Toko Sayur Segar Online">

{{-- ✅ Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

{{-- ✅ Schema.org Product JSON-LD --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{ $title }}",
    "image": "{{ $image }}",
    "description": "{{ $description }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $brand }}"
    },
    "offers": {
        "@type": "Offer",
        "url": "{{ $url }}",
        "priceCurrency": "IDR",
        "availability": "https://schema.org/InStock"
    }
}
</script>
