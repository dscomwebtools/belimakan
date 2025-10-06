import Alpine from 'alpinejs';

export default {
    items: [],
    selectedIds: [],
    showNotifSuccess: false,
    showNotifFailed: false,
    message: '',
    notifTimeout: null,
    isThereItemWithNoStock: false,
    _fetchPromise: null,

    get selectedItems() {
        return this.items.filter(item => this.selectedIds.includes(item.id));
    },

    get totalItem() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },

    get subtotal() {
        return this.items.reduce((sum, item) => {
            let price = item.product_variant ? item.product_variant.price : item.product.product_variant[0].price;

            if (item.product.promo) {
                price = price - (price * item.product.promo.discount / 100);
            }

            return sum + (item.quantity * price);
        }, 0);
    },

    get totalItemCart() {
        return this.selectedItems.reduce((sum, item) => sum + item.quantity, 0);
    },

    get subtotalCart() {
        return this.selectedItems.reduce((sum, item) => {
            let price = item.product_variant ? item.product_variant.price : item.product.product_variant[0].price;

            if (item.product.promo) {
                price = price - (price * item.product.promo.discount / 100);
            }

            return sum + (item.quantity * price);
        }, 0);
    },

    init() {
        if (localStorage.getItem('orderSuccess')) {
            this.showNotification("Terimakasih, data order berhasil kami terima !");
            localStorage.removeItem('orderSuccess');
        }

        // Restore checkout selection
        let checkoutId = sessionStorage.getItem('checkoutId');
        if (checkoutId) {
            let stored = localStorage.getItem(`checkoutSelected_${checkoutId}`);
            try {
                this.selectedIds = stored ? JSON.parse(stored) : [];
                if (!Array.isArray(this.selectedIds)) {
                    this.selectedIds = [];
                }
            } catch {
                this.selectedIds = [];
            }
        }
    },

    // --- utility for saving ---
    saveSelection() {
        let checkoutId = sessionStorage.getItem('checkoutId');
        if (!checkoutId) {
            checkoutId = Date.now();
            sessionStorage.setItem('checkoutId', checkoutId);
        }
        localStorage.setItem(`checkoutSelected_${checkoutId}`, JSON.stringify(this.selectedIds));
    },

    isSelected(item) {
        return this.selectedIds.includes(item.id);
    },

    toggleSelection(item) {
        const id = item.id;
        if (this.selectedIds.includes(id)) {
            this.selectedIds = this.selectedIds.filter(pid => pid !== id);
        } else {
            this.selectedIds = [...this.selectedIds, id];
        }
        this.saveSelection();
    },

    toggleAll(items) {
        if (this.selectedIds.length === items.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = items.map(i => i.id);
        }
        this.saveSelection();
    },

    async getDataCart() {
        this.items = await (await fetch('/data-cart')).json();
    },

    async fetchItems(force = false) {
        if (this.items.length > 0 && !force) return this.items;
        if (this._fetchPromise && !force) return this._fetchPromise;

        this._fetchPromise = fetch("/data-cart")
            .then(res => res.json())
            .then(data => {
                this.items = data;
                this._fetchPromise = null;
                return this.items;
            });

        return this._fetchPromise;
    },

    async addToCart(productId, variantId, qty) {
        if (qty < 1) {
            this.showNotificationFailed('Kuantitas tidak boleh kurang dari 1');
            return;
        }

        const response = await fetch('/add-to-cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: productId, product_variant_id: variantId, quantity: qty })
        });

        const result = await response.json();

        if (result.status === 1) {
            await this.getDataCart();
            this.showNotification('Berhasil ditambah ke keranjang Anda !');
        } else {
            this.showNotificationFailed(result.message);
        }
    },

    async updateQty(item, qty) {
        if (qty < 1) {
            this.showNotificationFailed('Kuantitas tidak boleh kurang dari 1');
            await this.getDataCart();
            return;
        }

        const response = await fetch(`/update-qty/${item.id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ qty })
        });

        const result = await response.json();

        if (result.status === 1) {
            await this.getDataCart();
            this.showNotification('Berhasil diupdate !');
        } else {
            await this.getDataCart();
            this.showNotificationFailed(result.message);
        }
    },

    async removeItem(item) {
        const response = await fetch(`/remove-item/${item.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        });

        const result = await response.json();

        if (result.status === 1) {
            this.selectedIds = this.selectedIds.filter(id => id !== item.id);
            this.saveSelection();
            await this.getDataCart();
            this.showNotification('Berhasil dihapus !');
        } else {
            this.showNotificationFailed('Gagal dihapus !');
        }
    },

    showNotification(message) {
        this.message = message;
        this.showNotifSuccess = true;
        if (this.notifTimeout) clearTimeout(this.notifTimeout);
        this.notifTimeout = setTimeout(() => {
            this.showNotifSuccess = false;
            this.notifTimeout = null;
        }, 5000);
    },

    showNotificationFailed(message) {
        this.message = message;
        this.showNotifFailed = true;
        if (this.notifTimeout) clearTimeout(this.notifTimeout);
        this.notifTimeout = setTimeout(() => {
            this.showNotifFailed = false;
            this.notifTimeout = null;
        }, 5000);
    },

    orderDetail: {},
    async getOrderDetail(order_id) {
        this.orderDetail = await (await fetch(`/order/${order_id}`)).json();
    },

    trackingDetail: null,
    async getTrackingDetail(order_id) {
        this.trackingDetail = null;
        let response = await fetch(`/order/${order_id}/tracking`);
        let data = await response.json();

        if (data.status && data.response.success) {
            this.trackingDetail = data.response;
        } else {
            console.warn("Tracking not available:", data.response?.message || data.message);
            this.trackingDetail = null;
        }
    },

    async getAdminTrackingDetail(order_id) {
        let response = await fetch(`/admin/order/${order_id}/tracking`);
        let data = await response.json();

        if (data.status && data.response.success) {
            this.trackingDetail = data.response;
        } else {
            console.warn("Tracking not available:", data.response?.message || data.message);
            this.trackingDetail = {};
        }
    },

    cancelReason: [],
    async getCancelReason() {
        if (this.cancelReason.length > 0) return;

        try {
            const res = await fetch(`/order/cancel/reason`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include',
            });

            const json = await res.json();
            const data = json.response;

            if (data.success && Array.isArray(data.cancellation_reasons)) {
                this.cancelReason = data.cancellation_reasons;
                console.log("This : ", this.cancelReason);
            } else {
                console.warn("Gagal ambil alasan pembatalan:", data.message);
                this.cancelReason = [];
            }
        } catch (error) {
            console.error("Error saat fetch alasan pembatalan:", error);
            this.cancelReason = [];
        }
    },

    proceedToCheckout() {
        window.location.href = "/checkout";
    },

    copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => this.showNotification('Nomor rekening berhasil disalin !'))
            .catch(() => this.showNotificationFailed('Nomor rekening gagal disalin !'));
    },
};
