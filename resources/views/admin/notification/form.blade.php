<x-app-layout>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-10 bg-white rounded">
        <h2 class="text-xl uppercase mb-6 font-bold">
            {{ isset($data) ? 'Edit' : 'Create' }} Notification
        </h2>

        @if ($errors->any())
            <div class="p-4 mb-6 text-sm text-red-700 rounded-lg bg-red-300">
                @foreach ($errors->all() as $error)
                    <span class="font-medium block">
                        <i class="fas fa-circle-exclamation mr-2"></i>{{ $error }}
                    </span>
                @endforeach
            </div>
        @endif

        <form action="{{ isset($data) ? route('notification.update', $data) : route('notification.store') }}"
            method="POST" id="main" class="space-y-6">
            @csrf
            @isset($data)
                @method('PUT')
            @endisset

            {{-- Title --}}
            <div>
                <label for="title" class="font-semibold mb-2 block">Judul</label>
                <input type="text" id="title" name="title" class="my-input bg-primary/5 rounded w-full"
                    value="{{ old('title', $data->title ?? '') }}" required autofocus>
            </div>

            {{-- Message --}}
            <div>
                <label class="font-semibold mb-2 block">Pesan</label>

                <div class="form-content">
                    <div id="toolbar">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                        <button class="ql-strike"></button>
                        <button class="ql-link"></button>
                        <button class="ql-image"></button>
                        <button class="ql-video"></button>
                        <select class="ql-color"></select>
                        <select class="ql-background"></select>
                        <button class="ql-script" value="sub"></button>
                        <button class="ql-script" value="super"></button>
                        <button class="ql-blockquote"></button>
                        <button class="ql-code-block"></button>
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                        <button class="ql-indent" value="-1"></button>
                        <button class="ql-indent" value="+1"></button>
                        <button class="ql-direction" value="rtl"></button>
                        <select class="ql-align"></select>
                        <button class="ql-clean"></button>
                    </div>

                    <div id="editor" class="bg-white text-black [&>.ql-editor]:min-h-52">
                        {!! old('message', $data->message ?? '') !!}
                    </div>

                    {{-- Hidden field to submit message --}}
                    <input type="hidden" id="message" name="message"
                        value="{{ old('message', $data->message ?? '') }}">
                </div>
            </div>

            <div>
                <label for="type" class="font-semibold mb-2 block">Tipe Notifikasi</label>
                <select id="type" name="type" class="my-input bg-primary/5 rounded w-full" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Sistem" {{ old('type', $data->type ?? '') == 'Sistem' ? 'selected' : '' }}>Sistem
                    </option>
                    <option value="Pemberitahuan"
                        {{ old('type', $data->type ?? '') == 'Pemberitahuan' ? 'selected' : '' }}>Pemberitahuan</option>
                </select>
            </div>

            {{-- User selection --}}
            <div>
                <label for="user_id" class="font-semibold mb-2 block">Tujuan</label>
                <select id="user_id" name="user_id" class="my-input bg-primary/5 rounded w-full">
                    <option value="broadcast">Broadcast (All Users)</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $data->user_id ?? '') == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            @push('styles')
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            @endpush

            @push('scripts')
                <!-- jQuery MUST be loaded before Select2 -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // jika kamu pakai turbolinks / alpine yang render ulang,
                        // pastikan inisialisasi dijalankan juga saat elemen muncul kembali.
                        const initSelect2 = () => {
                            if (typeof $ === 'undefined' || !$.fn.select2) {
                                console.error('jQuery atau Select2 belum dimuat');
                                return;
                            }

                            $('#user_id').select2({
                                placeholder: 'Pilih user atau broadcast ke semua',
                                allowClear: true,
                                width: '100%'
                            });
                        };

                        initSelect2();

                        // Jika kamu menggunakan Alpine dan form mungkin muncul di modal,
                        // panggil initSelect2 lagi saat modal dibuka.
                        document.addEventListener('alpine:init', () => {
                            initSelect2();
                        });
                    });
                </script>
            @endpush

            {{-- Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('notification.index') }}"
                    class="py-2 px-4 bg-gray-500 text-gray-50 text-center rounded">Kembali</a>
                <button type="submit" class="py-2 px-4 bg-primary text-primary-content rounded">Simpan</button>
            </div>
        </form>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
        <style>
            .ql-toolbar {
                background-color: ghostwhite
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.2.2/quill.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/kensnyder/quill-image-resize-module@3411c9a7/image-resize.min.js"></script>
        <script>
            (function() {
                const form = document.querySelector('form#main');
                const hiddenInput = document.querySelector('#message');

                // Initialize Quill
                const editor = new Quill('#editor', {
                    modules: {
                        toolbar: '#toolbar',
                        imageResize: {
                            displaySize: true
                        }
                    },
                    theme: 'snow'
                });

                // Set initial content (important for edit)
                const existingContent = hiddenInput.value;
                if (existingContent) {
                    editor.root.innerHTML = existingContent;
                }

                // Sync to hidden input on change
                editor.on('text-change', function() {
                    hiddenInput.value = editor.root.innerHTML;
                });

                // Disable button after submit
                form.addEventListener('submit', function() {
                    const content = editor.root.innerHTML.replace(/\n/g, '<br>');
                    hiddenInput.value = content;
                    document.querySelector('button[type="submit"]').setAttribute('disabled', true);
                });
            })();
        </script>
    @endpush
</x-app-layout>
