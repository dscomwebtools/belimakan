<x-app-layout>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-10">
        <h2 class="text-xl uppercase mb-6 font-bold">
            {{ isset($data) ? 'Ubah' : 'Tambah' }} Data Kupon
        </h2>

        {{-- error messages --}}
        @if ($errors->any())
            <div class="p-4 mb-6 text-sm text-red-700 rounded-lg bg-red-300 dark:bg-gray-800 dark:text-red-400">
                @foreach ($errors->all() as $error)
                    <span class="font-medium block">
                        <i class="fas fa-circle-exclamation mr-2"></i>{{ $error }}
                    </span>
                @endforeach
            </div>
        @endif

        <form action="{{ isset($data) ? route('coupon.update', $data) : route('coupon.store') }}" method="POST"
            id="main" class="space-y-6">
            @csrf
            @isset($data)
                @method('PUT')
            @endisset

            {{-- Coupon Code --}}
            <div>
                <label for="code" class="font-semibold mb-2 block">Kode Kupon</label>
                <input type="text" id="code" name="code" class="my-input bg-primary/5 rounded w-full"
                    value="{{ old('code', isset($data) ? $data->code : '') }}" required autofocus>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="font-semibold mb-2 block">Deskripsi</label>
                <textarea id="description" name="description" rows="2" class="my-input bg-primary/5 rounded w-full">{{ old('description', isset($data) ? $data->description : '') }}</textarea>
            </div>

            {{-- Minimum Order Amount --}}
            <div>
                <label for="min_amount" class="font-semibold mb-2 block">Minimal Belanja (Rp)</label>
                <div class="flex">
                    <span
                        class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">Rp</span>
                    <input type="number" step="0.01" id="min_amount" name="min_amount"
                        class="my-input bg-primary/5 rounded w-full"
                        value="{{ old('min_amount', isset($data) ? $data->min_amount : '') }}" required>
                </div>
            </div>

            {{-- Fixed Discount --}}
            <div>
                <label for="discount_amount" class="font-semibold mb-2 block">Diskon Tetap (Rp)</label>
                <div class="flex">
                    <span
                        class="bg-primary text-primary-content flex justify-center items-center p-2 border-b border-primary rounded-l font-bold">Rp</span>
                    <input type="number" step="0.01" id="discount_amount" name="discount_amount"
                        class="my-input bg-primary/5 rounded w-full"
                        value="{{ old('discount_amount', isset($data) ? $data->discount_amount : '') }}">
                </div>
            </div>

            {{-- Percentage Discount --}}
            {{-- <div>
                <label for="discount_percent" class="font-semibold mb-2 block">Diskon Persen (%)</label>
                <input type="number" step="0.01" id="discount_percent" name="discount_percent"
                    class="my-input bg-primary/5 rounded w-full"
                    value="{{ old('discount_percent', isset($data) ? $data->discount_percent : '') }}">
            </div> --}}

            {{-- Date Range --}}
            <div>
                <label class="font-semibold mb-2 block">Masa Berlaku</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="tooltip" data-tip="Tanggal Dari">
                        <input type="date" id="valid_from" name="valid_from"
                            class="my-input text-accent-content w-full"
                            value="{{ old('valid_from', isset($data) ? $data->valid_from->format('Y-m-d') : '') }}">
                    </div>
                    <div class="tooltip" data-tip="Tanggal Sampai">
                        <input type="date" id="valid_until" name="valid_until"
                            class="my-input text-accent-content w-full"
                            value="{{ old('valid_until', isset($data) ? $data->valid_until->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>

            {{-- Max Uses --}}
            <div>
                <label for="max_uses" class="font-semibold mb-2 block">Maksimal Penggunaan (Total)</label>
                <input type="number" id="max_uses" name="max_uses" class="my-input bg-primary/5 rounded w-full"
                    value="{{ old('max_uses', isset($data) ? $data->max_uses : '') }}">
            </div>

            {{-- Max Uses per User --}}
            <div>
                <label for="max_uses_per_user" class="font-semibold mb-2 block">Maksimal per User</label>
                <input type="number" id="max_uses_per_user" name="max_uses_per_user"
                    class="my-input bg-primary/5 rounded w-full"
                    value="{{ old('max_uses_per_user', isset($data) ? $data->max_uses_per_user : '') }}">
            </div>

            {{-- Active Toggle --}}
            <div class="flex flex-col items-start">
                <label for="active" class="font-semibold mb-2">Aktif</label>
                <div class="flex">
                    @include('components_custom.toggle-active-product', [
                        'name' => 'active',
                        'checked' => isset($data) ? ($data->active ? 'checked' : '') : 'checked',
                    ])
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2">
                <a href="{{ route('coupon.index') }}"
                    class="py-2 px-4 bg-gray-500 text-gray-50 text-center rounded">{{ __('Kembali') }}</a>
                <button type="submit" class="py-2 px-4 bg-primary text-primary-content rounded">Simpan</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            (function() {
                const form = document.querySelector('form#main')
                const submitBtn = document.querySelector('button[type="submit"]')

                form.addEventListener('submit', function() {
                    submitBtn.setAttribute('disabled', true)
                })
            })()
        </script>
    @endpush
</x-app-layout>
