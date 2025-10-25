<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-secondary text-secondary-content overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl uppercase mb-4 font-bold">{{ isset($data) ? 'Ubah' : 'Tambah' }} Data
                    {{ __('Sosmed') }}</h2>

                {{-- Error Message --}}
                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-700 rounded-lg bg-red-300 dark:bg-gray-800 dark:text-red-400 w-fit"
                        role="alert">
                        @foreach ($errors->all() as $error)
                            <span class="font-medium block"><i
                                    class="fas fa-circle-exclamation mr-2"></i>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ isset($data) ? route('sosmed.update', $data) : route('sosmed.store') }}" method="POST"
                    id="main" enctype="multipart/form-data">
                    @csrf
                    @isset($data)
                        @method('PUT')
                    @endisset

                    <div class="form-container flex flex-col">
                        <div class="form-content">

                            {{-- Nama --}}
                            <div class="flex flex-col mb-4">
                                <label for="name" class="font-semibold mb-2">Nama</label>
                                <input type="text" id="name" name="name"
                                    class="my-input bg-primary/5 rounded w-fit border-transparent border-b border-b-primary focus:ring-transparent focus:border-transparent focus:border-b-primary"
                                    value="{{ old('name', isset($data) ? $data->name : '') }}" placeholder="example"
                                    required autofocus>
                            </div>

                            {{-- Logo --}}
                            <div class="flex flex-col mb-4">
                                <label for="logo" class="font-semibold mb-2">Logo</label>
                                <input type="file" name="logo" id="logo">
                                @isset($data->logo)
                                    <p class="text-warning text-xs">Upload logo baru jika ingin mengubah logo saat ini</p>
                                    <div class="mt-2">Logo saat ini:</div>
                                    <img src="{{ Storage::url($data->logo) }}" alt="current logo" width="150"
                                        class="rounded mt-1 border border-white/20">
                                @endisset
                            </div>

                            {{-- URL --}}
                            <div class="flex flex-col mb-4">
                                <label for="url" class="font-semibold mb-2">Link</label>
                                <input type="text" id="url" name="url"
                                    class="my-input bg-primary/5 rounded w-fit border-transparent border-b border-b-primary focus:ring-transparent focus:border-transparent focus:border-b-primary"
                                    value="{{ old('url', isset($data) ? $data->url : '') }}"
                                    placeholder="https://example.com">
                            </div>

                            {{-- Checkbox Section --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="is_footer" value="1"
                                        {{ old('is_footer', isset($data) ? $data->is_footer : false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="font-medium">Tampilkan di Footer</span>
                                </label>

                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="is_link" value="1"
                                        {{ old('is_link', isset($data) ? $data->is_link : false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="font-medium">Tampilkan di Link</span>
                                </label>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('sosmed.index') }}"
                                class="py-2 px-4 bg-gray-500 text-gray-50 text-center mt-6 rounded">
                                {{ __('Kembali') }}
                            </a>
                            <button type="submit"
                                class="py-2 px-4 bg-primary text-primary-content mt-6 rounded">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const form = document.querySelector('form#main');
                const submitBtn = document.querySelector('button[type="submit"]');

                form.addEventListener('submit', function() {
                    submitBtn.setAttribute('disabled', true);
                });
            })();
        </script>
    @endpush
</x-app-layout>
