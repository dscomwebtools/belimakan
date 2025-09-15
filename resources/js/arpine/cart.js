import Alpine from 'alpinejs';

export default {
    items: [],
    showNotifSuccess: false,
    showNotifFailed: false,
    message: '',
    notifTimeout: null,
    isThereItemWithNoStock: false,
    _fetchPromise: null,

    get totalItem() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0)
    },
    get subtotal() {
        return this.items.reduce((sum, item) => {
            let price = item.product_variant ? item.product_variant.price : item.product.product_variant[0].price

            if (item.product.promo) {
                price = price - (price * item.product.promo.discount / 100)
            }

            return sum + (item.quantity * price)
        }, 0)
    },
    get totalWeight() {
        return this.items.reduce((sum, item) => {
            let weight = item.product_variant ? item.product_variant.weight : item.product.product_variant[0].weight

            return sum + (item.quantity * weight)
        }, 0)
    },
    init() {
        if (localStorage.getItem('orderSuccess')) {
            this.showNotification("Terimakasih, data order berhasil kami terima !")
            localStorage.removeItem('orderSuccess');
        }

    },

    async getDataCart() {
        this.items = await (await fetch('/data-cart')).json();
    },

    async fetchItems(force = false) {
        if (this.items.length > 0 && !force) {
            return this.items;
        }

        if (this._fetchPromise && !force) {
            return this._fetchPromise;
        }

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
            body: JSON.stringify({
                product_id: productId,
                product_variant_id: variantId,
                quantity: qty
            })
        });

        const result = await response.json();

        if (result.status === 1) {
            // Successfully added to cart, now refresh the cart data
            await this.getDataCart();
            this.showNotification('Berhasil ditambah ke keranjang Anda !');
        } else {
            // Handle the error case
            console.error(result.message);
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
            body: JSON.stringify({
                qty
            })
        });

        const result = await response.json();

        if (result.status === 1) {
            // Successfully added to cart, now refresh the cart data
            await this.getDataCart();
            this.showNotification('Berhasil ditambah ke keranjang Anda !');
        } else {
            // Handle the error case
            await this.getDataCart();
            this.showNotificationFailed(result.message);
            console.error('Gagal udpate data');
        }
    },
    async removeItem(item) {
        const response = await fetch(`/remove-item/${item.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.status === 1) {
            let saved = JSON.parse(localStorage.getItem('checkoutSelected') || "[]");
            saved = saved.filter(id => id !== item.id);

            if (saved.length > 0) {
                localStorage.setItem('checkoutSelected', JSON.stringify(saved));
            } else {
                localStorage.removeItem('checkoutSelected'); // clean up if empty
            }
            await this.getDataCart();
            this.showNotification('Berhasil ditambah ke keranjang Anda !');
        } else {
            // Handle the error case
            this.showNotificationFailed('Gagal dihapus !');
            console.error('Gagal dihapus');
        }
    },
    showNotification(message) {
        this.message = message;
        this.showNotifSuccess = true;

        // Clear existing timeout if any
        if (this.notifTimeout) {
            clearTimeout(this.notifTimeout);
        }

        // Set a new timeout
        this.notifTimeout = setTimeout(() => {
            this.showNotifSuccess = false;
            this.notifTimeout = null; // Clear the timeout ID
        }, 5000);
    },
    showNotificationFailed(message) {
        this.message = message;
        this.showNotifFailed = true;

        // Clear existing timeout if any
        if (this.notifTimeout) {
            clearTimeout(this.notifTimeout);
        }

        // Set a new timeout
        this.notifTimeout = setTimeout(() => {
            this.showNotifFailed = false;
            this.notifTimeout = null; // Clear the timeout ID
        }, 5000);
    },

    orderDetail: {},
    async getOrderDetail(order_id) {
        this.orderDetail = await (await fetch(`/order/${order_id}`)).json();
    },

    proceedToCheckout() {
        window.location.href = "/checkout";
    },

    copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => {
                this.showNotification('Nomor rekening berhasil disalin !');
            })
            .catch(err => {
                this.showNotificationFailed('Nomor rekening gagal disalin !');
                console.error(err);
            })
    },
}