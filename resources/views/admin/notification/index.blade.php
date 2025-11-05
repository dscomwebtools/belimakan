<x-app-layout>
    <div x-data="notificationTabs()" x-init="init()" class="p-6 py-10 rounded overflow-x-auto">

        <div class="flex flex-col md:flex-row mb-4">
            <a href="{{ route('notification.create') }}"
                class="bg-primary text-primary-content w-fit h-fit rounded px-4 py-2 mb-4 inline-block">
                <i class="fa fa-plus"></i> Tambah Notifikasi
            </a>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 border-b border-base-300">
            <button class="px-4 py-2 rounded-t-md"
                :class="type === 'pemberitahuan' ? 'bg-primary text-primary-content font-semibold' :
                    'bg-base-200 text-base-content'"
                @click="changeType('pemberitahuan')">
                Pemberitahuan
            </button>

            <button class="px-4 py-2 rounded-t-md"
                :class="type === 'sistem' ? 'bg-primary text-primary-content font-semibold' : 'bg-base-200 text-base-content'"
                @click="changeType('sistem')">
                Sistem
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <template x-if="loading">
                <p class="p-4 text-center">Memuat data...</p>
            </template>

            <table x-show="!loading" class="table bg-base-200 w-full">
                <thead>
                    <tr class="bg-base-300">
                        <th>#</th>
                        <th>Judul</th>
                        <th>Pesan</th>
                        <th>Tipe</th>
                        <th>Broadcast</th>
                        <th>Tanggal</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-for="(item, index) in notifications" :key="item.id">
                        <tr class="odd:bg-primary/5">
                            <td x-text="index + 1"></td>
                            <td x-text="item.title"></td>
                            <td x-text="item.message"></td>
                            <td x-text="item.type ?? '-'"></td>
                            <td>
                                <span :class="item.is_broadcast ? 'text-green-600 font-bold' : 'text-gray-500'"
                                    x-text="item.is_broadcast ? 'Yes' : 'No'">
                                </span>
                            </td>
                            <td x-text="item.formatted_date"></td>
                            <td class="flex justify-evenly">
                                <!-- Delete -->
                                <form :action="`{{ route('notification.destroy', ':id') }}`.replace(':id', item.id)"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600"><i class="fa fa-trash"></i></button>
                                </form>

                                <!-- Edit -->
                                <a :href="`{{ route('notification.edit', ':id') }}`.replace(':id', item.id)">
                                    <i class="fa fa-pen text-blue-600"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alpine.js Logic -->
    <script>
        function notificationTabs() {
            return {
                type: 'pemberitahuan',
                notifications: [],
                loading: false,
                initialized: false,

                async init() {
                    if (this.initialized) return;
                    this.initialized = true;

                    if (this.notifications.length === 0) {
                        await this.fetchNotifications();
                    }
                },

                async changeType(newType) {
                    if (this.type === newType) return;
                    this.type = newType;
                    await this.fetchNotifications();
                },

                async fetchNotifications() {
                    this.loading = true;
                    const res = await fetch(`{{ route('notification.index') }}?type=${this.type}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    this.notifications = await res.json();
                    this.loading = false;
                }
            }
        }
    </script>
</x-app-layout>
