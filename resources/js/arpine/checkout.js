import Alpine from 'alpinejs';

export default {
    selectedIds: [],
    selectedItems: [],
    showNotifSuccess: false,
    showNotifFailed: false,
    voucherCode: '',
    voucherDescription: '',
    voucherError: '',
    voucherSuccess: false,
    voucherDiscount: 0,
    message: '',
    note: '',
    notifTimeout: null,
    isLoading: false,

    async getDataCart() {
        await Alpine.store("cart").fetchItems();
    },

    get selectedItems() {
        const cart = Alpine.store('cart');
        if (!cart || !cart.items) return [];

        return cart.items.filter(item => this.selectedIds.includes(item.id));
    },

    get totalItem() {
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

    getCheckoutSelected() {
        const data = localStorage.getItem(`checkoutSelected_${this.checkoutId}`);
        return data ? JSON.parse(data) : [];
    },

    init() {
        this.getDataCart();

        if (!window.location.pathname.startsWith('/checkout')) {
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('checkoutSelected_')) {
                    localStorage.removeItem(key);
                }
            });
        }

        let checkoutId = sessionStorage.getItem('checkoutId');
        this.checkoutId = checkoutId;
        this.selectedIds = this.getCheckoutSelected();
    },

    // ==== CHECKOUT PAGE STUFF ====
    addressSelected: {},
    profileHasLocation: false,
    setAddress(id, name, recipient, hp, address, district, city, province, postal_code, area_id, latitude, longitude) {
        this.addressSelected = {
            id, name, recipient, hp, address,
            district, city, province, postal_code, area_id, latitude, longitude
        };

        this.profileHasLocation = !!(latitude && longitude);
        this.setCourierSelected({}, '', true);
    },

    courierCategories(type = 'regular') {
        if (type === 'regular') {
            return "jne,jnt,sicepat,anteraja,sentralcargo"; // daftar kurir reguler
        }
        if (type === 'instant') {
            return "gojek,grab,lalamove"; // daftar kurir instant
        }
        return "";
    },

    courierListReguler: [],
    courierListInstant: [],
    groupedCourierListRegular: [],
    groupedCourierListInstant: [],
    lastAddressId: "",
    destination_latitude: "",
    destination_longitude: "",
    regular_loading: true,
    instant_loading: true,
    courierSelected: null,
    courierSource: null, // "regular" | "instant" | "manual"
    async setCourierList() {
        if (
            this.lastAddressId === this.addressSelected?.id &&
            this.courierListRegular &&
            (this.courierListInstant || !this.profileHasLocation)
        ) {
            return;
        }

        this.regular_loading = true;
        this.instant_loading = true;

        this.lastAddressId = this.addressSelected?.id;
        this.destination_latitude = this.addressSelected.latitude;
        this.destination_longitude = this.addressSelected.longitude;
        let dataOrigin = null;
        let originAreaId = null;

        try {
            const resOrigin = await fetch(`/origin-address`);
            dataOrigin = await resOrigin.json();
            if (dataOrigin.data?.area_id) {
                originAreaId = dataOrigin.data.area_id;
            }
        } catch (err) {
            console.error('Gagal ambil origin address:', err);
        }

        const items = this.selectedItems.map(item => {
            const variant = item.product_variant ?? item.product.product_variant[0];
            return {
                name: item.product.name,
                value: variant.price,
                weight: +variant.weight,
                quantity: item.quantity,
                sku: variant.sku,
                length: +item.product.dimention.length,
                width: +item.product.dimention.width,
                height: +item.product.dimention.height,
            };
        });

        try {
            const responseRegular = await fetch(`/cek-ongkir`, {
                method: 'POST',
                body: JSON.stringify({
                    origin_area_id: originAreaId,
                    destination_area_id: this.addressSelected.area_id,
                    couriers: this.courierCategories('regular'),
                    items,
                }),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });
            const resultRegular = await responseRegular.json();
            if (resultRegular.success) {
                this.courierListRegular = resultRegular.pricing;
                this.groupedCourierListRegular = this.groupByCourier(resultRegular.pricing);
                this.regular_loading = false;
            }
        } catch (err) {
            this.regular_loading = false;
            console.error("Error fetch regular couriers:", err);
        }

        if (this.destination_latitude && this.destination_longitude) {
            try {
                const responseInstant = await fetch(`/cek-ongkir`, {
                    method: 'POST',
                    body: JSON.stringify({
                        origin_latitude: dataOrigin?.data?.latitude ?? null,
                        origin_longitude: dataOrigin?.data?.longitude ?? null,
                        destination_latitude: this.destination_latitude,
                        destination_longitude: this.destination_longitude,
                        couriers: this.courierCategories('instant'),
                        items,
                    }),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });
                const resultInstant = await responseInstant.json();
                this.instant_loading = false;
                if (resultInstant.success) {
                    this.courierListInstant = resultInstant.pricing;
                    this.groupedCourierListInstant = this.groupByCourier(resultInstant.pricing);
                    this.instant_loading = false;
                }
            } catch (err) {
                this.instant_loading = false;
                console.error("Error fetch instant couriers:", err);
            }
        } else {
            this.instant_loading = false;
        }
    },

    translateDuration(duration) {
        if (!duration) return '-';

        // Hilangkan spasi berlebih & lowercase semua
        let text = duration.toLowerCase().trim();

        // Tambahkan spasi otomatis sebelum "hour"/"day" jika belum ada
        text = text.replace(/(\d)(?=[a-z])/g, '$1 '); // ubah "1day" -> "1 day"

        // Ubah tanda "-" jadi "–" (en dash)
        text = text.replace(/-/g, '–');

        // Terjemahan utama
        if (text.includes('hour')) {
            text = text.replace(/hours?/g, 'jam');
        } else if (text.includes('day')) {
            text = text.replace(/days?/g, 'hari');
        }

        return text;
    },

    groupByCourier(list) {
        const grouped = {};
        list.forEach(item => {
            const courier = item.courier_name;
            if (!grouped[courier]) {
                grouped[courier] = { open: false, data: [] };
            }
            grouped[courier].data.push(item);
        });
        return grouped;
    },


    courierSelected: {},
    setCourierSelected(courier, source, clear = false) {
        if ((courier && Object.keys(courier).length > 0) || clear) {
            this.courierSelected = courier;
            this.courierSource = source;
            if (source !== "manual") {
                this.paymentMethod = "Transfer";
            }
        }
    },

    paymentMethod: null,
    setPaymentMethod(method) {
        this.paymentMethod = method;
    },

    removeVoucher() {
        this.voucherError = '';
        this.voucherSuccess = false;
        this.voucherDiscount = 0;
        this.voucherCode = '';
        this.voucherId = '';
        this.voucherDescription = '';
    },

    async applyVoucher() {
        this.voucherError = '';
        this.voucherSuccess = false;
        this.voucherDiscount = 0;

        if (!this.voucherCode) {
            this.voucherError = 'Silakan masukkan kode voucher';
            return;
        }

        try {
            const response = await fetch(`/coupon`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    code: this.voucherCode,
                    subtotal: this.subtotal,
                    selectedIds: this.selectedIds
                })
            });

            const data = await response.json();

            if (data.valid) {
                this.voucherSuccess = true;
                this.voucherDiscount = data.discount_amount ?? 0;
                this.voucherId = data.id;
                this.voucherDescription = data.description;
            } else {
                this.voucherError = data.message ?? 'Kode voucher tidak valid';
            }
        } catch (e) {
            this.voucherError = 'Terjadi kesalahan, coba lagi';
        }
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
                total: this.subtotal + (this.courierSelected.price || 0) - (this.voucherDiscount || 0),
                orderAddressId: this.addressSelected.id,
                shippingMethod: this.courierSelected,
                shippingSource: this.courierSource,
                bankAccountId: this.paymentMethod === 'Transfer' ? this.bankSelected.id : null,
                selectedCartIds: this.selectedIds,
                voucherId: this.voucherId || null,
                voucherCode: this.voucherCode || null,
                voucherDiscount: this.voucherDiscount || 0,
                deliveryNote: this.note || '',
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
        localStorage.removeItem('checkoutSelected');
        let checkoutId = sessionStorage.getItem('checkoutId');
        if (checkoutId) {
            localStorage.removeItem(`checkoutSelected_${checkoutId}`);
        }
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
