<x-ar.modal name="address-form-update" :show="$errors->address_update->isNotEmpty()">
    <form method="post"
        :action="'{{ route('profile.update_address', '') }}/' + (data ? data.id : '{{ old('id') }}')"
        class="p-6 update-form">
        @csrf
        @method('PATCH')

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Ubah Alamat') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Perbarui data alamat Anda.') }}
        </p>

        <input type="hidden" name="id" x-bind:value="data ? data.id : '{{ old('id') }}'">

        {{-- ===== INPUT DASAR ===== --}}
        <div class="mt-6">
            <x-input-label for="name" value="{{ __('Nama') }}" />
            <x-text-input name="name" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Rumah, Kantor, Kampus') }}" required
                x-bind:value="`{{ old('name') }}`
                !== '' ? `{{ old('name') }}` : data?.name" />
            <x-input-error :messages="$errors->address_update->get('name')" class="mt-2" />
        </div>

        <div class="flex flex-col my-4">
            <x-input-label for="address" class="mb-2" value="{{ __('Alamat lengkap') }}" />
            <textarea name="address" rows="3" class="my-input bg-primary/5 rounded"
                placeholder="Jalan kenangan no. 1001 blok A" required
                x-bind:value="`{{ old('address') }}`
                !== '' ? `{{ old('address') }}` : data?.address"></textarea>
            <x-input-error :messages="$errors->address_update->get('address')" class="mt-2" />
        </div>

        <div class="flex flex-col my-4">
            <x-input-label for="area_id" class="mb-2"
                value="{{ __('Daerah (Kecamatan/Kota/Kabupaten/Provinsi)') }}" />
            <select name="area_id" class="area_id" x-init="$watch('show', (value) => {
                if (value) {
                    let areaId = document.querySelector('form.update-form .area_id');
                    let option = new Option(data.area_name, data.area_id, true, true);
                    areaId.appendChild(option);
                }
            })" required>
            </select>
        </div>

        <div class="mt-6">
            <x-input-label for="noteForCurrier" value="{{ __('Catatan untuk kurir') }}" />
            <x-text-input name="noteForCurrier" type="text" class="mt-1 block w-full"
                placeholder="{{ __('Depan Masjid') }}"
                x-bind:value="`{{ old('noteForCurrier') }}`
                !== '' ? `{{ old('noteForCurrier') }}` : data?.noteForCurrier" />
            <x-input-error :messages="$errors->address_update->get('noteForCurrier')" class="mt-2" />
        </div>

        {{-- ===== NOTE ===== --}}
        <p class="mt-4 text-xs text-gray-600 dark:text-gray-400 italic">
            {{ __('Layanan pengantaran Gojek / Grab hanya tersedia jika titik lokasi sudah diisi.') }}
        </p>
        <p class="mt-1 text-xs text-red-600 dark:text-red-400 italic">
            {{ __('Pastikan alamat pada mapUpdate sama dengan alamat yang diisi') }}
        </p>

        {{-- ===== KOORDINAT + MAP ===== --}}
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="latitude_update" value="{{ __('Latitude') }}" />
                <x-text-input name="latitude_update" id="latitude_update" type="text" class="mt-1 block w-full"
                    placeholder="-6.200000"
                    x-bind:value="`{{ old('latitude') }}`
                    !== '' ? `{{ old('latitude') }}` : data?.latitude" />
                <x-input-error :messages="$errors->address_update->get('latitude_update')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="longitude_update" value="{{ __('Longitude') }}" />
                <x-text-input name="longitude_update" id="longitude_update" type="text" class="mt-1 block w-full"
                    placeholder="106.816666"
                    x-bind:value="`{{ old('longitude') }}`
                    !== '' ? `{{ old('longitude') }}` : data?.longitude" />
                <x-input-error :messages="$errors->address_update->get('longitude_update')" class="mt-2" />
            </div>
        </div>

        {{-- ===== TOMBOL CARI DI PETA ===== --}}
        <div class="flex gap-2 mt-4">
            <x-secondary-button type="button" onclick="openMapUpdateDialogUpdate()">
                🗺️ {{ __('Cari di Peta') }}
            </x-secondary-button>
        </div>

        {{-- ===== INPUT LAIN ===== --}}
        <div class="mt-6">
            <x-input-label for="recipient" value="{{ __('Penerima') }}" />
            <x-text-input name="recipient" type="text" class="mt-1 block w-full" placeholder="{{ __('Fulan') }}"
                required
                x-bind:value="`{{ old('recipient') }}`
                !== '' ? `{{ old('recipient') }}` : data?.recipient" />
        </div>

        <div class="mt-6">
            <x-input-label for="hp" value="{{ __('HP') }}" />
            <x-text-input name="hp" type="text" class="mt-1 block w-full" placeholder="{{ __('081xxx') }}"
                required
                x-bind:value="`{{ old('hp') }}`
                !== '' ? `{{ old('hp') }}` : data?.hp" />
        </div>

        <div class="mt-6 flex gap-2 items-center">
            <input type="hidden" name="isMain" value="0">
            <input type="checkbox" name="isMain" value="1" :checked="data?.isMain ? true : false" />
            <label for="isMain">{{ __('Utama') }}</label>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button class="ms-3"><i class="fa fa-paper-plane"></i></x-primary-button>
        </div>
    </form>

    {{-- ===== DIALOG PETA ===== --}}
    <dialog id="mapUpdateDialogUpdate" class="rounded-lg shadow-lg p-0 overflow-hidden w-[90%] max-w-2xl">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Pilih Lokasi di Peta</h3>
            <button type="button" onclick="closeMapUpdateDialogUpdate()">✕</button>
        </div>

        <div class="p-4">
            <div id="mapUpdate" class="h-[350px] rounded-lg"></div>

            <div class="mt-3 flex flex-col justify-between items-center gap-2 text-sm">
                <div>
                    Latitude: <span id="latDisplayUpdate">-6.200000</span> |
                    Longitude: <span id="lngDisplayUpdate">106.816666</span>
                </div>

                <div class="flex gap-2 mt-4">
                    <x-secondary-button type="button" x-on:click="getMyLocationUpdate()">
                        📍 {{ __('Dapatkan Lokasi Saya') }}
                    </x-secondary-button>

                    <x-primary-button type="button" x-on:click="useThisLocationUpdate()">
                        {{ __('Gunakan Lokasi Ini') }}
                    </x-primary-button>
                </div>
            </div>
        </div>
    </dialog>

    {{-- ===== LEAFLET MAP UNTUK UPDATE ===== --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        let mapUpdate, markerUpdate;
        const indoBoundsUpdate = L.latLngBounds([-12.5, 94.0], [7.5, 141.0]);

        function initMapUpdate() {
            const latInput = document.getElementById('latitude_update');
            const lngInput = document.getElementById('longitude_update');

            const startLat = parseFloat(latInput?.value) || -6.200000;
            const startLng = parseFloat(lngInput?.value) || 106.816666;

            if (!mapUpdate) {
                // buat map pertama kali
                mapUpdate = L.map('mapUpdate', {
                    minZoom: 5,
                    maxBounds: indoBoundsUpdate.pad(0.5),
                    maxBoundsViscosity: 1.0
                }).setView([startLat, startLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: ''
                }).addTo(mapUpdate);

                markerUpdate = L.marker([startLat, startLng], {
                    draggable: true
                }).addTo(mapUpdate);

                // klik map
                mapUpdate.on('click', e => {
                    if (!indoBoundsUpdate.contains(e.latlng)) {
                        alert("Pilih lokasi di dalam Indonesia!");
                        return;
                    }
                    markerUpdate.setLatLng(e.latlng);
                    updateCoordsUpdate(e.latlng.lat, e.latlng.lng);
                });

                // drag marker
                markerUpdate.on('dragend', () => {
                    const {
                        lat,
                        lng
                    } = markerUpdate.getLatLng();
                    const point = L.latLng(lat, lng);
                    if (!indoBoundsUpdate.contains(point)) {
                        alert("Tidak bisa keluar wilayah Indonesia!");
                        return;
                    }
                    updateCoordsUpdate(lat, lng);
                });
            } else {
                // kalau map sudah ada → reset view dan marker ke posisi baru
                markerUpdate.setLatLng([startLat, startLng]);
                mapUpdate.setView([startLat, startLng], 16);
            }

            updateCoordsUpdate(startLat, startLng);
            mapUpdate.invalidateSize();
        }

        function updateCoordsUpdate(lat, lng) {
            document.getElementById('latDisplayUpdate').textContent = lat.toFixed(7);
            document.getElementById('lngDisplayUpdate').textContent = lng.toFixed(7);
        }

        function showMapUpdate() {
            setTimeout(() => {
                if (!mapUpdate || !markerUpdate || !indoBoundsUpdate) return; // amanin null
                mapUpdate.invalidateSize();

                // Coba ambil posisi markerUpdate
                const pos = markerUpdate.getLatLng();
                if (!pos || isNaN(pos.lat) || isNaN(pos.lng)) {
                    console.warn("Marker belum siap, skip showMapUpdate");
                    return;
                }

                // Cek apakah sudah ada koordinat (user pernah pilih)
                const latVal = parseFloat(document.getElementById('latitude_update')?.value);
                const lngVal = parseFloat(document.getElementById('longitude_update')?.value);
                const hasCustom = !isNaN(latVal) && !isNaN(lngVal);

                if (hasCustom) {
                    mapUpdate.setView([latVal, lngVal], 18);
                } else {
                    mapUpdate.setView([pos.lat, pos.lng], 16);
                }

                // Batasi area & zoom agar tidak keluar Indonesia
                mapUpdate.setMaxBounds(indoBoundsUpdate.pad(0.3));
            }, 400);
        }

        function getMyLocationUpdate() {
            if (!navigator.geolocation) {
                alert("Browser Anda tidak mendukung geolokasi.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    const point = L.latLng(lat, lng);
                    if (!indoBoundsUpdate.contains(point)) {
                        alert("Lokasi di luar batas Indonesia!");
                        return;
                    }

                    // Pastikan mapUpdate aktif dan di-render ulang
                    mapUpdate.invalidateSize(true);

                    // Gunakan animasi flyTo biar kelihatan bergerak
                    mapUpdate.flyTo(point, 18, {
                        animate: true,
                        duration: 1.2
                    });

                    markerUpdate.setLatLng(point);
                    updateCoordsUpdate(lat, lng);
                },
                err => alert("Gagal mendapatkan lokasi: " + err.message), {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        }

        function useThisLocationUpdate() {
            const lat = document.getElementById('latDisplayUpdate').textContent;
            const lng = document.getElementById('lngDisplayUpdate').textContent;
            document.getElementById('latitude_update').value = lat;
            document.getElementById('longitude_update').value = lng;
            closeMapUpdateDialogUpdate();
        }

        function openMapUpdateDialogUpdate() {
            const dlg = document.getElementById('mapUpdateDialogUpdate');
            dlg.showModal();
            setTimeout(initMapUpdate, 300);
            showMapUpdate(); // selalu panggil invalidateSize pas dibuka
        }

        function closeMapUpdateDialogUpdate() {
            document.getElementById('mapUpdateDialogUpdate').close();
        }

        window.addEventListener('load', initMapUpdate);
    </script>
</x-ar.modal>
