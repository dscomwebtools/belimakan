<x-ar.modal name="address-form-store" :show="$errors->address_store->isNotEmpty()">
    <form method="post" action="{{ route('profile.store_address') }}" class="p-6">
        @csrf

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Tambah Alamat') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Lengkapi data alamat Anda.') }}
        </p>

        <div class="mt-6">
            <x-input-label for="name" value="{{ __('Nama') }}" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Rumah, Kantor, Kampus') }}" required value="{{ old('name') }}"
                {{-- tidak bisa
                gunakan shortcut dari x-bind (yaitu : ) disini karena menggunakan blade component, jadi pake x-bind saja
                --}} />
            <x-input-error :messages="$errors->address_store->get('name')" class="mt-2" />
        </div>

        <div class="flex flex-col my-4">
            <x-input-label for="address" class="mb-2" value="{{ __('Alamat lengkap') }}" />
            <textarea name="address" id="address" rows="3" class="my-input bg-primary/5 rounded"
                placeholder="Jalan kenangan no. 1001 RT. 02 RW. 116 blok A" required>{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->address_store->get('address')" class="mt-2" />
        </div>

        <div class="flex flex-col my-4">
            <x-input-label for="area_id" class="mb-2"
                value="{{ __('Daerah (Kecamatan/Kota/Kabupaten/Provinsi)') }}" />
            <select name="area_id" class="area_id" required></select>
        </div>

        <div class="mt-6">
            <x-input-label for="noteForCurrier" value="{{ __('Catatan untuk kurir') }}" />
            <x-text-input id="noteForCurrier" name="noteForCurrier" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Depan Masjid') }}" value="{{ old('noteForCurrier') }}" />
            <x-input-error :messages="$errors->address_store->get('noteForCurrier')" class="mt-2" />
        </div>

        {{-- Note tentang layanan ojek online --}}
        <p class="mt-4 text-xs text-gray-600 dark:text-gray-400 italic">
            {{ __('Layanan pengantaran Gojek / Grab hanya tersedia jika titik lokasi (latitude dan longitude) sudah diisi.') }}
        </p>

        <div x-data
            x-on:set-location.window="
        document.getElementById('latitude_store').value = $event.detail.lat;
        document.getElementById('longitude_store').value = $event.detail.lng;
    ">
            {{-- Latitude & Longitude sejajar --}}
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="latitude_store" value="{{ __('Latitude') }}" />
                    <x-text-input name="latitude_store" id="latitude_store" type="text" class="mt-1 block w-full"
                        placeholder="-6.200000"
                        x-bind:value="`{{ old('latitude_store') }}`
                        !== '' ? `{{ old('latitude_store') }}` : data?.latitude_store" />
                    <x-input-error :messages="$errors->address_update->get('latitude_store')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="longitude_store" value="{{ __('Longitude') }}" />
                    <x-text-input name="longitude_store" id="longitude_store" type="text" class="mt-1 block w-full"
                        placeholder="106.816666"
                        x-bind:value="`{{ old('longitude_store') }}`
                        !== '' ? `{{ old('longitude_store') }}` : data?.longitude_store" />
                    <x-input-error :messages="$errors->address_update->get('longitude_store')" class="mt-2" />
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex gap-2 mt-4">
            <!-- Tombol GeoLocation -->
            <x-secondary-button type="button" x-on:click="getMyLocationStore()">
                📍 {{ __('Dapatkan Lokasi Saya') }}
            </x-secondary-button>

            <!-- Tombol buka modal peta -->
            <x-secondary-button type="button" x-on:click="$dispatch('open-modal', 'modal-map-store')">
                🗺️ {{ __('Cari di Peta') }}
            </x-secondary-button>
        </div>

        <div class="mt-6">
            <x-input-label for="recipient" value="{{ __('Penerima') }}" />
            <x-text-input id="recipient" name="recipient" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Fulan') }}" required value="{{ old('recipient') }}" />
            <x-input-error :messages="$errors->address_store->get('recipient')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="hp" value="{{ __('HP') }}" />
            <x-text-input id="hp" name="hp" type="number" class="mt-1 block w-full"
                placeholder="{{ __('081xxx') }}" required value="{{ old('hp') }}" />
            <x-input-error :messages="$errors->address_store->get('hp')" class="mt-2" />
        </div>

        <div class="mt-6 flex gap-2 items-center">
            <input type="checkbox" name="isMain" id="isMain" value="1"
                :checked="data?.isMain ? true : false" /> <label for="isMain">{{ __('Utama') }}</label>
        </div>
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <div class="tooltip" data-tip="Simpan">
                <x-primary-button class="ms-3">
                    <i class="fa fa-paper-plane"></i>
                </x-primary-button>
            </div>
        </div>
    </form>
    <script>
        function getMyLocationStore() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.querySelector('input[name=latitude_store]').value = position.coords.latitude.toFixed(6);
                        document.querySelector('input[name=longitude_store]').value = position.coords.longitude.toFixed(6);
                    },
                    function(error) {
                        alert("Gagal mendapatkan lokasi: " + error.message);
                    }
                );
            } else {
                alert("Browser Anda tidak mendukung geolokasi.");
            }
        }
    </script>
</x-ar.modal>
@include('client.profile.modal-map-store')
