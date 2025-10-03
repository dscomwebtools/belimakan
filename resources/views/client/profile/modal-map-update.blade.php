<x-modal name="modal-map-update" focusable>
    <div x-data="mapModal()" x-init="initMap()"
        x-on:open-modal.window="if ($event.detail === 'modal-map-update') showMap()" class="p-6">
        <h2 class="font-bold mb-2">Pilih Lokasi</h2>

        <!-- Map -->
        <div x-ref="map" class="h-[400px] mt-2.5 rounded-[10px] overflow-hidden"></div>

        <div class="mt-4 flex justify-end">
            <button type="button" class="btn btn-primary"
                @click="
                    $dispatch('set-location', { lat: selectedLat, lng: selectedLng });
                    $dispatch('close');
                ">
                Gunakan Lokasi Ini
            </button>
        </div>
    </div>
</x-modal>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    function mapModal() {
        return {
            map: null,
            marker: null,
            selectedLat: -6.2,
            selectedLng: 106.816666,
            indoBounds: L.latLngBounds(
                [-12.5, 94.0], // kiri bawah (longgar)
                [7.5, 141.0] // kanan atas (longgar)
            ),

            initMap() {
                this.map = L.map(this.$refs.map, {
                    minZoom: 5,
                }).setView([this.selectedLat, this.selectedLng], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '', // attribution dihapus
                }).addTo(this.map);

                this.marker = L.marker([this.selectedLat, this.selectedLng], {
                    draggable: true
                }).addTo(this.map);

                this.marker.on('dragend', () => {
                    let {
                        lat,
                        lng
                    } = this.marker.getLatLng();
                    this.setPosition(lat, lng, true);
                });

                this.map.on('click', (e) => {
                    this.setPosition(e.latlng.lat, e.latlng.lng, true);
                });
            },

            updateLatLng(lat, lng) {
                this.selectedLat = parseFloat(lat).toFixed(6);
                this.selectedLng = parseFloat(lng).toFixed(6);
            },

            setPosition(lat, lng, enforceBounds = false) {
                let point = L.latLng(lat, lng);

                if (enforceBounds && !this.indoBounds.contains(point)) {
                    // kalau keluar batas, balikin marker ke posisi terakhir
                    this.marker.setLatLng([this.selectedLat, this.selectedLng]);
                    this.map.setView([this.selectedLat, this.selectedLng], 6);
                    return;
                }

                this.marker.setLatLng(point);
                this.updateLatLng(lat, lng);
            },

            showMap() {
                setTimeout(() => {
                    this.map.invalidateSize();

                    // kalau sudah ada posisi sebelumnya
                    if (this.selectedLat && this.selectedLng) {
                        this.map.setView([this.selectedLat, this.selectedLng], 6);
                    } else {
                        this.map.fitBounds(this.indoBounds, {
                            padding: [50, 50]
                        });
                    }

                    // batasi user zoom/panning
                    this.map.setMaxBounds(this.indoBounds.pad(0.3)); // longgarin 30%
                }, 300);
            }
        }
    }
</script>
