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

            <div>
                <label for="title" class="font-semibold mb-2 block">Judul</label>
                <input type="text" id="title" name="title" class="my-input bg-primary/5 rounded w-full"
                    value="{{ old('title', $data->title ?? '') }}" required autofocus>
            </div>

            <div>
                <label for="message" class="font-semibold mb-2 block">Pesan</label>
                <textarea id="message" name="message" rows="4" class="my-input bg-primary/5 rounded w-full" required>{{ old('message', $data->message ?? '') }}</textarea>
            </div>

            <div>
                <label for="user_id" class="font-semibold mb-2 block">Tujuan</label>
                <select id="user_id" name="user_id" class="my-input bg-primary/5 rounded w-full">
                    <option value="">Broadcast (All Users)</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $data->user_id ?? '') == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- <div class="flex flex-col items-start">
                <label for="is_active" class="font-semibold mb-2">Active</label>
                <div class="flex">
                    @include('components_custom.toggle-active-product', [
                        'name' => 'is_active',
                        'checked' => isset($data) ? ($data->is_active ? 'checked' : '') : 'checked',
                    ])
                </div>
            </div> --}}

            <div class="flex gap-2">
                <a href="{{ route('notification.index') }}"
                    class="py-2 px-4 bg-gray-500 text-gray-50 text-center rounded">Back</a>
                <button type="submit" class="py-2 px-4 bg-primary text-primary-content rounded">Save</button>
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
