<div x-data="inboxData(@js($notifications))" class="flex flex-col gap-4">

    <!-- 📨 LIST -->
    <div x-show="!showDetail" class="bg-base-100 shadow-md border border-base-300 rounded-xl overflow-hidden">
        <div class="divide-y divide-base-200 max-h-[600px] overflow-y-auto">

            <template x-for="notif in notifications" :key="notif.id">
                <div class="flex items-start gap-4 p-4 cursor-pointer transition-all duration-200 hover:bg-base-200"
                    :class="notif.users[0].pivot.is_read ? 'bg-base-100' : 'bg-primary/5 border-l-4 border-primary'"
                    @click="selected = notif; markAsRead(notif); showDetail = true">

                    <!-- Kiri: Icon -->
                    <div class="flex-shrink-0 mt-1">
                        <i class="fa-solid fa-bell text-primary text-lg"></i>
                    </div>

                    <!-- Tengah: Isi notifikasi -->
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-sm sm:text-base text-base-content line-clamp-1"
                                x-text="notif.title"></h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-medium"
                                :class="notif.users[0].pivot.is_read ? 'bg-gray-200 text-gray-700' : 'bg-primary text-white'">
                                <span x-text="notif.users[0].pivot.is_read ? 'Dibaca' : 'Baru'"></span>
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 mt-1" x-text="formatDate(notif.created_at)"></p>

                        <p class="text-sm text-gray-700 leading-snug mt-2 line-clamp-2"
                            x-text="notif.message
                                ? (notif.message.replace(/<[^>]+>/g, '').trim().substring(0, 100)
                                  + (notif.message.replace(/<[^>]+>/g, '').length > 100 ? '...' : ''))
                                : ''">
                        </p>
                    </div>

                    <!-- Kanan: Tombol hapus -->
                    {{-- <button @click.stop="openDeleteModal(notif)"
                        class="text-gray-400 hover:text-red-500 transition duration-150 rounded-full p-2"
                        title="Hapus notifikasi">
                        <i class="fa-solid fa-trash"></i>
                    </button> --}}
                </div>
            </template>

            <div x-show="notifications.length === 0" class="p-6 text-center text-gray-400 text-sm">
                <i class="fa-regular fa-envelope-open mb-2 block text-2xl"></i>
                Tidak ada notifikasi.
            </div>
        </div>
    </div>

    <!-- 📄 DETAIL -->
    <div x-show="showDetail" id="selected" x-transition
        class="bg-white rounded-xl shadow-lg border border-base-300 p-6 relative">

        <button class="absolute top-4 left-4 flex items-center text-sm text-primary hover:underline"
            @click="showDetail = false; selected = null">
            <i class="fa-solid fa-arrow-left mr-1"></i>
            Kembali
        </button>

        <template x-if="selected">
            <div class="mt-6">
                <div class="flex justify-between items-start mb-3">
                    <h2 class="text-lg font-bold text-base-content" x-text="selected.title"></h2>
                    {{-- <button @click="openDeleteModal(selected)"
                        class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button> --}}
                </div>

                <p class="text-xs text-gray-500 mb-4" x-text="formatDate(selected.created_at)"></p>
                <div class="text-sm leading-relaxed" x-html="formatMessage(selected.message)">
                </div>

                <template x-if="selected.link">
                    <div class="mt-4">
                        <a :href="selected.link" target="_blank"
                            class="inline-flex items-center gap-2 text-primary hover:underline text-sm font-medium">
                            <i class="fa-solid fa-link"></i> Buka tautan
                        </a>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- 🗑️ MODAL KONFIRMASI HAPUS -->
    <div x-show="showDeleteModal" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm text-center">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-4xl mb-3"></i>
            <h3 class="text-lg font-semibold mb-2">Hapus Notifikasi?</h3>
            <p class="text-gray-600 text-sm mb-5">Tindakan ini tidak dapat dibatalkan.</p>

            <div class="flex justify-center gap-3">
                <button @click="showDeleteModal = false" class="btn btn-sm bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <i class="fa-solid fa-xmark mr-1"></i> Batal
                </button>
                <button @click="deleteNotification()" class="btn btn-sm bg-red-500 text-white hover:bg-red-600">
                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function inboxData(data) {
            return {
                notifications: data,
                selected: null,
                showDetail: false,
                showDeleteModal: false,
                notifToDelete: null,

                formatMessage(content) {
                    if (/<[a-z][\s\S]*>/i.test(content)) return content;
                    return content.replace(/\n/g, '<br>');
                },

                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    });
                },

                openDeleteModal(notif) {
                    this.notifToDelete = notif;
                    this.showDeleteModal = true;
                },

                async deleteNotification() {
                    const notif = this.notifToDelete;
                    if (!notif) return;

                    try {
                        const res = await fetch(`/notification/delete/${notif.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        if (res.ok) {
                            this.notifications = this.notifications.filter(n => n.id !== notif.id);
                            if (this.selected?.id === notif.id) {
                                this.selected = null;
                                this.showDetail = false;
                            }
                            this.showDeleteModal = false;
                        } else {
                            alert('Gagal menghapus notifikasi.');
                        }
                    } catch (e) {
                        console.error('Error deleting notification:', e);
                    }
                },

                async markAsRead(notification) {
                    this.$nextTick(() => {
                        const el = document.getElementById('selected');
                        const navbarHeight = 200;
                        const elementTop = el.getBoundingClientRect().top + window.scrollY;
                        const offsetPosition = elementTop - navbarHeight - 10;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth',
                        });
                    });
                    if (notification.users[0].pivot.is_read) return;
                    try {
                        const res = await fetch(`/notification/read/${notification.id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        if (res.ok) {
                            this.notifications = this.notifications.map(n =>
                                n.id === notification.id ? {
                                    ...n,
                                    users: n.users.map((u, i) =>
                                        i === 0 ? {
                                            ...u,
                                            pivot: {
                                                ...u.pivot,
                                                is_read: true,
                                                read_at: new Date().toISOString()
                                            }
                                        } :
                                        u
                                    ),
                                } :
                                n
                            );
                        }
                    } catch (e) {
                        console.error('Error marking as read:', e);
                    }
                },
            };
        }
    </script>
@endpush
