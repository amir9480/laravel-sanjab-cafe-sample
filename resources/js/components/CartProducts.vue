<template>
    <div>
        <p v-if="products == null" class="text-center">
            <b-spinner></b-spinner>
        </p>
        <div v-else>
            <b-list-group>
                <b-list-group-item v-for="(product, index) in products" :key="product.id" :variant="index % 2 ? 'secondary' : 'default'" href="#" class="d-flex justify-content-between align-items-center">
                    {{ product.name }}
                    <b-badge variant="primary" pill>{{ product.pivot.quantity }}</b-badge>
                </b-list-group-item>
                <b-list-group-item variant="primary">
                    مجموع: {{ numberFormat(total) }} تومان
                </b-list-group-item>
            </b-list-group>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            item: null
        },
        data() {
            return {
                products: null
            }
        },
        mounted () {
            this.loadProducts();
        },
        methods: {
            loadProducts() {
                var self = this;
                axios.get(sanjabUrl('modules/carts/cart-products/'+ this.item.id))
                .then(function (response) {
                    self.products = response.data;
                })
                .catch(function (error) {
                    console.error(error);
                    setTimeout(() => self.loadProducts(), 1000);
                });
            }
        },
        computed: {
            total() {
                var total = 0;
                if (this.products) {
                    for (var i in this.products) {
                        total += this.products[i].price * this.products[i].pivot.quantity;
                    }
                }
                return total;
            }
        },
    }
</script>

<style lang="scss" scoped>
    html[dir=rtl] .list-group {
        padding: unset !important;
    }
</style>
