@props(['value'])

<span class="text-sm md:text-base lg:text-lg">Rp. {{ number_format($value, 0, ',', '.') }}</span>