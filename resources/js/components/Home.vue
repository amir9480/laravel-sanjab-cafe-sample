<template>
    <div class="row h-100 justify-content-center align-items-center">
        <form id="point-form" @submit.prevent="onSearchFormSubmit" class="col-12">
            <div class="row">
                <div class="col-lg-5 col-12 form-group">
                    <input type="number" class="form-control" v-model="form.code" name="code" placeholder="کد عضویت">
                </div>
                <div class="col-lg-2 col-12 text-center">
                    <span>و</span>
                </div>
                <div class="col-lg-5 col-12 form-group">
                    <input type="tel" class="form-control" v-model="form.mobile" name="mobile" placeholder="شماره همراه">
                </div>
                <div class="col-12">
                    <button v-if="loading" type="button" class="btn btn-block btn-outline-secondary" disabled>درحال بارگذاری</button>
                    <button v-else type="submit" class="btn btn-block btn-outline-secondary">بررسی</button>
                </div>
            </div>
        </form>
        <b-modal ref="modal" title="ثبت خرید" size="lg" @hidden="currentCart = null; form.cart = []; form.id = null;" hide-footer>
            <b-alert variant="success" class="text-right" show>
                سکه های شما: {{ coins }}
            </b-alert>
            <b-form @submit.prevent="addCartItem">
                <b-row>
                    <b-col v-if="recentCartTime" cols=12 class="py-2 text-right">
                        زمان مراجعه: {{ recentCartTime }}
                    </b-col>
                    <b-col v-else cols=12 class="py-2">
                        <vue-select v-model="form.time" placeholder="زمان مراجعه" :options="timeOptions" />
                    </b-col>
                    <b-col cols=12 md=6 class="py-2">
                        <vue-select v-model="form.category" placeholder="دسته بندی" :options="categoryOptions" />
                    </b-col>
                    <b-col cols=12 md=6 class="py-2">
                        <vue-select v-model="form.product" placeholder="محصول" :options="productOptions" />
                    </b-col>
                    <b-col cols=12 class="py-2">
                        <b-button type="submit" block>افزودن</b-button>
                    </b-col>
                </b-row>
                <b-list-group v-if="form.cart.length > 0">
                    <b-list-group-item class="text-right justify-content-between align-items-center">
                        <b-row>
                            <b-col cols=12 md=4>
                                نام
                            </b-col>
                            <b-col cols=12 md=2>
                                تعداد
                            </b-col>
                            <b-col cols=12 md=4>
                                قیمت
                            </b-col>
                            <b-col cols=12 md=2>
                                حذف
                            </b-col>
                        </b-row>
                    </b-list-group-item>
                    <b-list-group-item v-for="(cartItem, index) in form.cart" :key="index" class="text-right justify-content-between align-items-center">
                        <b-row>
                            <b-col cols=12 md=4>
                                {{ findProduct(cartItem.product_id).name }}
                            </b-col>
                            <b-col cols=12 md=2>
                                <b-form-input type="number" min="1" max="100" v-model="form.cart[index].quantity" placeholder="تعداد"></b-form-input>
                            </b-col>
                            <b-col cols=12 md=4>
                                <div v-if="form.cart[index].quantity > 0">
                                    {{ numberFormat(findProduct(cartItem.product_id).price * form.cart[index].quantity) }} تومان
                                </div>
                                <div v-else>
                                    {{ numberFormat(findProduct(cartItem.product_id).price) }} تومان
                                </div>
                            </b-col>
                            <b-col cols=12 md=2>
                                <b-button @click="form.cart.splice(index, 1)" variant="danger" size="sm" title="حذف" v-b-tooltip block>
                                    حذف
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-list-group-item>
                    <b-list-group-item variant="success" class="text-right justify-content-between align-items-center">
                        <b-row>
                            <b-col cols=12 md=6>
                                جمع کل: {{ numberFormat(totalPrice) }} تومان
                            </b-col>
                            <b-col cols=12 md=6>
                                <b-button @click="onBuyFormSubmit" variant="success" block>ثبت خرید</b-button>
                            </b-col>
                        </b-row>
                    </b-list-group-item>
                </b-list-group>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                loading: false,
                coins: 0,
                categories: [],
                recentCartTime: null,
                form: {
                    mobile: null,
                    code: null,
                    category: null,
                    product: null,
                    time: 60,
                    cart: []
                }
            }
        },
        methods: {
            onSearchFormSubmit() {
                var self = this;
                self.loading = true;
                self.recentCartTime = null;
                axios.post('/get-info', self.form)
                .then(function (response) {
                    if (response.data.id) {
                        self.form.id = response.data.id;
                        self.coins = response.data.coins;
                        self.categories = response.data.categories;
                        if (response.data.recentCart) {
                            self.form.cart = response.data.recentCart.products.map((prod) => {return { product_id: prod.id, quantity: prod.pivot.quantity };});
                            self.recentCartTime = response.data.recentCart.dateForHuman;
                        }
                        self.$refs.modal.show();
                    } else {
                        Swal.fire({
                            title: response.data.message,
                            type: "success",
                            confirmButtonText: 'تایید',
                        });
                    }
                }).catch(function (error) {
                    console.error(error);
                    Swal.fire({
                        title: error.response.status == 422 ? error.response.data.errors[Object.keys(error.response.data.errors)[0]][0] : "خطایی رخ داد",
                        type: "error",
                        confirmButtonText: 'تایید',
                    });
                }).then(function () {
                    self.loading = false;
                });
            },
            onBuyFormSubmit() {
                var self = this;
                self.loading = true;
                axios.post('/buy', self.form)
                .then(function (response) {
                    Swal.fire({
                        title: "سفارش شما با موفقیت ثبت شد.",
                        type: "success",
                        confirmButtonText: 'تایید',
                    }).then(function () {
                        self.$refs.modal.hide();
                    });
                }).catch(function (error) {
                    console.error(error);
                    Swal.fire({
                        title: error.response.status == 422 ? error.response.data.errors[Object.keys(error.response.data.errors)[0]][0] : "خطایی رخ داد",
                        type: "error",
                        confirmButtonText: 'تایید',
                    });
                }).then(function () {
                    self.loading = false;
                });
            },
            addCartItem() {
                if (this.form.product == null || this.form.product == "") {
                    return Swal.fire({type: 'error', title: "لطفا محصول مورد نظرتان را انتخاب کنید."});
                }
                this.form.cart.push({product_id: this.form.product, quantity: 1});
                this.form.product = null;
            },
            findProduct(id) {
                for (var i in this.categories) {
                    for (var j in this.categories[i].products) {
                        if (this.categories[i].products[j].id == id) {
                            return this.categories[i].products[j];
                        }
                    }
                }
            }
        },
        computed: {
            categoryOptions() {
                return this.categories.map((cat) => {return { label: cat.name, value: cat.id };});
            },
            productOptions() {
                if (this.form.category) {
                    var category = this.categories.filter((cat) => cat.id == this.form.category);
                    if (category.length > 0) {
                        category = category[0];
                        return category.products.filter((pro) => !this.selectedProducts.includes(pro.id)).map((pro) => {return { label: pro.name, value: pro.id };});
                    }
                }
                return [];
            },
            totalPrice() {
                var total = 0;
                for (var i in this.form.cart) {
                    total += this.findProduct(this.form.cart[i].product_id).price * this.form.cart[i].quantity;
                }
                return total;
            },
            timeOptions() {
                return [
                    {label: "نیم ساعت بعد", value: 30},
                    {label: "1 ساعت بعد", value: 60},
                    {label: "1 ساعت و نیم بعد", value: 90},
                    {label: "2 ساعت بعد", value: 120},
                    {label: "3 ساعت بعد", value: 180},
                    {label: "4 ساعت بعد", value: 240},
                    {label: "5 ساعت بعد", value: 300},
                    {label: "6 ساعت بعد", value: 360},
                    {label: "7 ساعت بعد", value: 420},
                    {label: "8 ساعت بعد", value: 480},
                    {label: "9 ساعت بعد", value: 540},
                    {label: "10 ساعت بعد", value: 600},
                    {label: "11 ساعت بعد", value: 660},
                    {label: "12 ساعت بعد", value: 720},
                ];
            },
            selectedProducts() {
                return this.form.cart.map((cartItem) => cartItem.product_id);
            },
        },
    }
</script>
