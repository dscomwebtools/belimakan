export default {
    unreadCount: 0,
    loading: false,

    async init() {
        await this.fetchUnread();
    },

    async fetchUnread() {
        this.loading = true;
        try {
            const res = await fetch('/notification', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) throw new Error('Gagal memuat notifikasi');

            const data = await res.json();

            this.unreadCount = data.unread_count ?? (
                Array.isArray(data)
                    ? data.filter(n => !n.pivot?.is_read).length
                    : 0
            );
        } catch (error) {
            console.error('Error fetching unread notifications:', error);
        } finally {
            this.loading = false;
        }
    },
};
