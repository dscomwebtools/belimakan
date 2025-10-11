<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form method="post" action="{{ route('admin-profile.address-update', old('id') ?? ($data->id ?? '')) }}"
            class="update-form">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Ubah Alamat') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Ubah data alamat Anda di bawah ini.') }}
            </p>

            <input type="hidden" name="id" value="{{ old('id') ?? ($data->id ?? '') }}">

            {{-- Nama --}}
            <div class="mt-6">
                <x-input-label for="name" value="{{ __('Nama') }}" />
                <x-text-input name="name" type="text" class="mt-1 block w-full"
                    placeholder="{{ __('Rumah, Kantor, Kampus') }}" required
                    value="{{ old('name', $data->name ?? '') }}" />
                <x-input-error :messages="$errors->address_update->get('name')" class="mt-2" />
            </div>

            {{-- Alamat --}}
            <div class="flex flex-col my-4">
                <x-input-label for="address" class="mb-2" value="{{ __('Alamat') }}" />
                <textarea name="address" rows="3" class="my-input bg-primary/5 rounded"
                    placeholder="Jalan Kenangan No. 1001 Blok A" required>{{ old('address', $data->address ?? '') }}</textarea>
                <x-input-error :messages="$errors->address_update->get('address')" class="mt-2" />
            </div>

            {{-- Area --}}
            <div class="flex flex-col my-4">
                <x-input-label for="area_id" class="mb-2"
                    value="{{ __('Daerah (Kecamatan/Kota/Kabupaten/Provinsi)') }}" />
                <select name="area_id" class="area_id w-full" required>
                    @if (old('area_id') || isset($data->area_id))
                        <option value="{{ old('area_id', $data->area_id ?? '') }}" selected>
                            {{ old('area_name', $data->area_name ?? 'Pilih daerah') }}
                        </option>
                    @endif
                </select>
                <x-input-error :messages="$errors->address_update->get('area_id')" class="mt-2" />
            </div>

            {{-- Catatan Kurir --}}
            {{-- <div class="mt-6">
                <x-input-label for="noteForCurrier" value="{{ __('Catatan untuk kurir') }}" />
                <x-text-input name="noteForCurrier" type="text" class="mt-1 block w-full"
                    placeholder="{{ __('Depan Masjid') }}"
                    value="{{ old('noteForCurrier', $data->noteForCurrier ?? '') }}" />
                <x-input-error :messages="$errors->address_update->get('noteForCurrier')" class="mt-2" />
            </div> --}}

            {{-- Catatan tambahan --}}
            <p class="mt-4 text-xs text-gray-600 dark:text-gray-400 italic">
                {{ __('Layanan pengantaran Gojek / Grab hanya tersedia jika titik lokasi (latitude dan longitude) sudah diisi.') }}
            </p>

            {{-- Latitude & Longitude --}}
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="latitude" value="{{ __('Latitude') }}" />
                    <x-text-input name="latitude" id="latitude" type="text" class="mt-1 block w-full"
                        placeholder="-6.200000" value="{{ old('latitude', $data->latitude ?? '') }}" />
                    <x-input-error :messages="$errors->address_update->get('latitude')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="longitude" value="{{ __('Longitude') }}" />
                    <x-text-input name="longitude" id="longitude" type="text" class="mt-1 block w-full"
                        placeholder="106.816666" value="{{ old('longitude', $data->longitude ?? '') }}" />
                    <x-input-error :messages="$errors->address_update->get('longitude')" class="mt-2" />
                </div>
            </div>

            {{-- Tombol Aksi Lokasi --}}
            <div class="flex gap-2 mt-4">
                <x-secondary-button type="button" onclick="getMyLocation()">
                    📍 {{ __('Dapatkan Lokasi Saya') }}
                </x-secondary-button>

                {{-- <x-secondary-button type="button" onclick="openMapModal()">
                    🗺️ {{ __('Cari di Peta') }}
                </x-secondary-button> --}}
            </div>

            {{-- Penerima --}}
            {{-- <div class="mt-6">
                <x-input-label for="recipient" value="{{ __('Penerima') }}" />
                <x-text-input name="recipient" type="text" class="mt-1 block w-full"
                    placeholder="{{ __('Fulan') }}" required
                    value="{{ old('recipient', $data->recipient ?? '') }}" />
                <x-input-error :messages="$errors->address_update->get('recipient')" class="mt-2" />
            </div> --}}

            {{-- HP --}}
            <div class="mt-6">
                <x-input-label for="hp" value="{{ __('HP') }}" />
                <x-text-input name="hp" type="text" class="mt-1 block w-full" placeholder="{{ __('081xxx') }}"
                    required value="{{ old('hp', $data->hp ?? '') }}" />
                <x-input-error :messages="$errors->address_update->get('hp')" class="mt-2" />
            </div>

            {{-- Utama --}}
            {{-- <div class="mt-6 flex gap-2 items-center">
                <input type="hidden" name="isMain" value="0">
                <input type="checkbox" name="isMain" value="1"
                    {{ old('isMain', $data->isMain ?? false) ? 'checked' : '' }} />
                <label for="isMain">{{ __('Utama') }}</label>
            </div> --}}

            {{-- Tombol Simpan --}}
            <div class="mt-6 flex justify-end gap-2">
                {{-- <x-secondary-button type="button" onclick="window.history.back()">
                    {{ __('Batal') }}
                </x-secondary-button> --}}

                <div class="tooltip" data-tip="Simpan">
                    <x-primary-button class="ms-3">
                        Simpan
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function getMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.querySelector('input[name=latitude]').value = position.coords.latitude.toFixed(
                            6);
                        document.querySelector('input[name=longitude]').value = position.coords.longitude
                            .toFixed(6);
                    },
                    function(error) {
                        alert("Gagal mendapatkan lokasi: " + error.message);
                    }
                );
            } else {
                alert("Browser Anda tidak mendukung geolokasi.");
            }
        }

        function openMapModal() {
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'modal-map-admin-update'
            }));
        }
    </script>
</x-app-layout>
@include('admin.profile.modal-map-admin-update')
