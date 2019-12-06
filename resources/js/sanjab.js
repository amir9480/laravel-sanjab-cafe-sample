require('sanjab');

Vue.component('customer-card', require('./components/CustomerCard.vue').default);
Vue.component('cart-products', require('./components/CartProducts.vue').default);

if (document.querySelector('#sanjab_app')) {
    window.sanjabApp = new Vue({
        el: '#sanjab_app',
    });
}
