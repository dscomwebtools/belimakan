import './bootstrap';
import './client/home';
import './client/countdown';
// import Swal from 'sweetalert2';
// window.Swal = Swal;

import Alpine from 'alpinejs';
import productForm from './arpine/productForm.js'
import productTable from './arpine/productTable.js'
import cart from './arpine/cart.js'
import checkout from './arpine/checkout.js'
import auction from './arpine/auction.js'

Alpine.data('productForm', productForm);
Alpine.data('productTable', productTable);

Alpine.store('cart', cart);
Alpine.store('checkout', checkout);
Alpine.store('auction', auction);

window.Alpine = Alpine;

Alpine.start();