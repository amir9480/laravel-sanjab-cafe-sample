/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue';
require("./bootstrap");
require("./scripts");

Vue.prototype.numberFormat = numberFormat;

Vue.use(require('bootstrap-vue').default);

Vue.component('vue-select', require('sanjab/resources/js/components/widgets/fields/SelectWidget.vue').default);

new Vue({
    el: "#app",
    render: h => h(require('./components/Home.vue').default),
});
