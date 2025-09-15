import Alpine from 'alpinejs';

export default {
    selectedIds: [],
    selectedItems: [],
    showNotifSuccess: false,
    showNotifFailed: false,
    message: '',
    notifTimeout: null,
    isLoading: false,

    async getDataCart() {
        // fetch cart items into Alpine store
        await Alpine.store("cart").fetchItems();

        // restore selected ids from localStorage
        const saved = localStorage.getItem('checkoutSelected');
        if (saved) {
            this.selectedIds = JSON.parse(saved);
        }
    },

    get selectedItems() {
        const cart = Alpine.store('cart');
        if (!cart || !cart.items) return [];

        return cart.items.filter(item => this.selectedIds.includes(item.id));
    },

    get totalItem() {
        console.log("Total : ", this.selectedItems);
        return this.selectedItems.reduce((sum, item) => sum + item.quantity, 0);
    },

    get subtotal() {
        return this.selectedItems.reduce((sum, item) => {
            let price = item.product_variant
                ? item.product_variant.price
                : item.product.product_variant[0].price;

            if (item.product.promo) {
                price -= price * (item.product.promo.discount / 100);
            }

            return sum + (item.quantity * price); // ✅ use cart row quantity
        }, 0);
    },

    get totalWeight() {
        return this.selectedItems.reduce((sum, item) => {
            let weight = item.product_variant
                ? item.product_variant.weight
                : item.product.product_variant[0].weight;

            return sum + (item.quantity * weight); // ✅ use cart row quantity
        }, 0);
    },

    // ==== SELECTION HANDLERS ====
    toggleSelection(item) {
        const id = item.id; // use cart row id now

        if (this.selectedIds.includes(id)) {
            this.selectedIds = this.selectedIds.filter(pid => pid !== id);
        } else {
            this.selectedIds = [...this.selectedIds, id]; // new array (keeps reactivity)
        }

        localStorage.setItem('checkoutSelected', JSON.stringify(this.selectedIds));
    },

    toggleAll(items) {
        if (this.selectedIds.length === items.length) {
            // ✅ unselect all
            this.selectedIds = [];
        } else {
            // ✅ select all
            this.selectedIds = items.map(i => i.id);
        }

        // save to localStorage
        if (this.selectedIds.length > 0) {
            localStorage.setItem('checkoutSelected', JSON.stringify(this.selectedIds));
        } else {
            localStorage.removeItem('checkoutSelected');
        }
    },

    init() {
        this.getDataCart();
    },

    isSelected(item) {
        return this.selectedIds.includes(item.id);
    },

    // ==== CHECKOUT PAGE STUFF ====
    addressSelected: {},
    setAddress(id, name, recipient, hp, address, district, city, province, postal_code, area_id) {
        this.addressSelected = {
            id, name, recipient, hp, address,
            district, city, province, postal_code, area_id,
        };
    },

    courierList: [],
    async setCourierList() {
        if (this.courierList.length > 0) return;

        const origin_area_id = "IDNP6IDNC147IDND833IDZ10730";
        const destination_area_id = this.addressSelected.area_id;

        const items = this.selectedItems.map(item => {
            if (item.product_variant) {
                return {
                    name: item.product.name,
                    value: item.product_variant.price,
                    weight: +item.product_variant.weight,
                    quantity: item.quantity,
                    sku: item.product_variant.sku,
                    length: +item.product.dimention.length,
                    width: +item.product.dimention.width,
                    height: +item.product.dimention.height,
                }
            } else {
                return {
                    name: item.product.name,
                    value: item.product.product_variant[0].price,
                    weight: +item.product.product_variant[0].weight,
                    quantity: item.quantity,
                    sku: item.product.product_variant[0].sku,
                    length: +item.product.dimention.length,
                    width: +item.product.dimention.width,
                    height: +item.product.dimention.height,
                }
            }
        });

        const couriers = 'jne,jnt,sicepat,gojek,grab,lalamove,anteraja,pos,deliveree,sap';

        const response = await fetch(`/cek-ongkir`, {
            method: 'POST',
            body: JSON.stringify({
                origin_area_id,
                destination_area_id,
                couriers,
                items,
            }),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });

        const result = await response.json();

        if (result.success) {
            this.courierList = result.pricing;
        } else {
            this.showNotificationFailed('Terjadi kesalahan saat cek ongkir, segera hubungi admin !');
        }
    },

    courierSelected: {},
    setCourierSelected(courier) {
        this.courierSelected = courier;
    },

    paymentMethod: null,
    setPaymentMethod(method) {
        this.paymentMethod = method;
    },

    async submitOrder() {
        this.isLoading = true;

        const response = await fetch('/order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                paymentMethod: this.paymentMethod,
                total: this.subtotal + (this.courierSelected.price || 0),
                orderAddressId: this.addressSelected.id,
                shippingMethod: this.courierSelected,
                bankAccountId: this.paymentMethod === 'Transfer' ? this.bankSelected.id : null,
                selectedCartIds: this.selectedIds
            })
        });

        const result = await response.json();
        this.isLoading = false;

        if (result.status === 1) {
            // ✅ Clear checkout state
            this.clearCheckout();

            window.location.href = '/profile';
            localStorage.setItem('activeTab', 2);
            localStorage.setItem('orderSuccess', true);
        } else {
            this.showNotificationFailed("Terjadi kesalahan, order gagal !");
        }
    },

    clearCheckout() {
        this.selectedIds = [];
        localStorage.removeItem('checkoutSelected'); // ✅ clear LS
    },


    orderDetail: {},
    async getOrderDetail(order_id) {
        this.orderDetail = await (await fetch(`/order/${order_id}`)).json();
    },

    bankSelected: {},
    setBankSelected(id, logo, bank_name, account_name, account_number) {
        this.bankSelected = id ? { id, logo, bank_name, account_name, account_number } : {};
    },

    // ==== NOTIFICATIONS ====
    showNotification(message) {
        this.message = message;
        this.showNotifSuccess = true;

        if (this.notifTimeout) clearTimeout(this.notifTimeout);

        console.log("What ?");

        this.notifTimeout = setTimeout(() => {
            this.showNotifSuccess = false;
            this.notifTimeout = null;
            console.log("Anjay");
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
};
