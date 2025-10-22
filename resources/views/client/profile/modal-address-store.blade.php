<x-ar.modal name="address-form-store" :show="$errors->address_store->isNotEmpty()">
    <form method="post" action="{{ route('profile.store_address') }}" class="p-6">
        @csrf

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Tambah Alamat') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Lengkapi data alamat Anda.') }}
        </p>

        {{-- ===== INPUT DASAR ===== --}}
        <div class="mt-6">
            <x-input-label for="name" value="{{ __('Nama') }}" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Rumah, Kantor, Kampus') }}" required value="{{ old('name') }}" />
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

        {{-- ===== NOTE ===== --}}
        <p class="mt-4 text-xs text-gray-600 dark:text-gray-400 italic">
            {{ __('Layanan pengantaran Gojek / Grab hanya tersedia jika titik lokasi sudah diisi.') }}
        </p>
        <p class="mt-1 text-xs text-red-600 dark:text-red-400 italic">
            {{ __('Pastikan alamat pada map sama dengan alamat yang diisi') }}
        </p>

        {{-- ===== KOORDINAT ===== --}}
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="latitude_store" value="{{ __('Latitude') }}" />
                <x-text-input name="latitude_store" id="latitude_store" type="text" class="mt-1 block w-full"
                    placeholder="-6.200000" />
            </div>

            <div>
                <x-input-label for="longitude_store" value="{{ __('Longitude') }}" />
                <x-text-input name="longitude_store" id="longitude_store" type="text" class="mt-1 block w-full"
                    placeholder="106.816666" />
            </div>
        </div>

        {{-- ===== TOMBOL CARI DI PETA ===== --}}
        <div class="flex gap-2 mt-4">
            <x-secondary-button type="button" onclick="openmapDialogStore()">
                🗺️ {{ __('Cari di Peta') }}
            </x-secondary-button>
        </div>

        {{-- ===== INPUT LAIN ===== --}}
        <div class="mt-6">
            <x-input-label for="recipient" value="{{ __('Penerima') }}" />
            <x-text-input id="recipient" name="recipient" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Fulan') }}" required value="{{ old('recipient') }}" />
        </div>

        <div class="mt-6">
            <x-input-label for="hp" value="{{ __('HP') }}" />
            <x-text-input id="hp" name="hp" type="number" class="mt-1 block w-full"
                placeholder="{{ __('081xxx') }}" required value="{{ old('hp') }}" />
        </div>

        <div class="mt-6 flex gap-2 items-center">
            <input type="checkbox" name="isMain" id="isMain" value="1" />
            <label for="isMain">{{ __('Utama') }}</label>
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

    {{-- ===== DIALOG PETA ===== --}}
    <dialog id="mapDialogStore" class="rounded-lg shadow-lg p-0 overflow-hidden w-[90%] max-w-2xl">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Pilih Lokasi di Peta</h3>
            <button type="button" onclick="closemapDialogStore()">✕</button>
        </div>

        <div class="p-4">
            <div id="map" class="h-[350px] rounded-lg"></div>

            <div class="mt-3 flex flex-col justify-between items-center gap-2 text-sm">
                <div>
                    Latitude: <span id="latDisplay">-6.200000</span> |
                    Longitude: <span id="lngDisplay">106.816666</span>
                </div>

                <div class="flex gap-2 mt-4">
                    <x-secondary-button type="button" x-on:click="getMyLocationStore()">
                        📍 {{ __('Dapatkan Lokasi Saya') }}
                    </x-secondary-button>

                    <x-primary-button type="button" x-on:click="useThisLocation()">
                        {{ __('Gunakan Lokasi Ini') }}
                    </x-primary-button>
                </div>
            </div>
        </div>
    </dialog>

    {{-- ===== LEAFLET ===== --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        let map, marker;
        const indoBounds = L.latLngBounds([-12.5, 94.0], [7.5, 141.0]);

        function initMap() {
            map = L.map('map', {
                minZoom: 5,
                maxBounds: indoBounds.pad(0.5),
                maxBoundsViscosity: 1.0
            }).setView([-6.200000, 106.816666], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: ''
            }).addTo(map);

            marker = L.marker([-6.200000, 106.816666], {
                draggable: true
            }).addTo(map);

            map.on('click', e => {
                if (!indoBounds.contains(e.latlng)) {
                    alert("Pilih lokasi di dalam Indonesia!");
                    return;
                }
                marker.setLatLng(e.latlng);
                updateCoordsStore(e.latlng.lat, e.latlng.lng);
            });

            marker.on('dragend', () => {
                const {
                    lat,
                    lng
                } = marker.getLatLng();
                const point = L.latLng(lat, lng);
                if (!indoBounds.contains(point)) {
                    alert("Tidak bisa keluar wilayah Indonesia!");
                    // Kembalikan ke posisi lama
                    const oldLat = parseFloat(document.getElementById('latDisplay').textContent);
                    const oldLng = parseFloat(document.getElementById('lngDisplay').textContent);
                    marker.setLatLng([oldLat, oldLng]);
                    map.setView([oldLat, oldLng], 16);
                    return;
                }
                updateCoordsStore(lat, lng);
            });

            updateCoordsStore(-6.200000, 106.816666);
            showMap(); // render pertama
        }

        function updateCoordsStore(lat, lng) {
            document.getElementById('latDisplay').textContent = lat.toFixed(7);
            document.getElementById('lngDisplay').textContent = lng.toFixed(7);
        }

        function showMap() {
            setTimeout(() => {
                if (!map || !marker || !indoBounds) return; // amanin null
                map.invalidateSize();

                // Coba ambil posisi marker
                const pos = marker.getLatLng();
                if (!pos || isNaN(pos.lat) || isNaN(pos.lng)) {
                    console.warn("Marker belum siap, skip showMap");
                    return;
                }

                // Cek apakah sudah ada koordinat (user pernah pilih)
                const latVal = parseFloat(document.getElementById('latitude_store')?.value);
                const lngVal = parseFloat(document.getElementById('longitude_store')?.value);
                const hasCustom = !isNaN(latVal) && !isNaN(lngVal);

                if (hasCustom) {
                    map.setView([latVal, lngVal], 18);
                } else {
                    map.setView([pos.lat, pos.lng], 16);
                }

                // Batasi area & zoom agar tidak keluar Indonesia
                map.setMaxBounds(indoBounds.pad(0.3));
            }, 400);
        }

        function getMyLocationStore() {
            if (!navigator.geolocation) {
                alert("Browser Anda tidak mendukung geolokasi.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    const point = L.latLng(lat, lng);
                    if (!indoBounds.contains(point)) {
                        alert("Lokasi di luar batas Indonesia!");
                        return;
                    }

                    // Pastikan map aktif dan di-render ulang
                    map.invalidateSize(true);

                    // Gunakan animasi flyTo biar kelihatan bergerak
                    map.flyTo(point, 18, {
                        animate: true,
                        duration: 1.2
                    });

                    marker.setLatLng(point);
                    updateCoordsStore(lat, lng);
                },
                err => alert("Gagal mendapatkan lokasi: " + err.message), {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        }

        function useThisLocation() {
            const lat = document.getElementById('latDisplay').textContent;
            const lng = document.getElementById('lngDisplay').textContent;
            document.getElementById('latitude_store').value = lat;
            document.getElementById('longitude_store').value = lng;
            closemapDialogStore();
        }

        function openmapDialogStore() {
            const dlg = document.getElementById('mapDialogStore');
            dlg.showModal();
            showMap(); // selalu panggil invalidateSize pas dibuka
        }

        function closemapDialogStore() {
            document.getElementById('mapDialogStore').close();
        }

        window.addEventListener('load', initMap);
    </script>
</x-ar.modal>
