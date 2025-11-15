@props(['value'])

@php
    $formatted = floor($value) != $value ? number_format($value, 2, ',', '.') : number_format($value, 0, ',', '.');
@endphp

<span>Rp. {{ $formatted }}</span>
