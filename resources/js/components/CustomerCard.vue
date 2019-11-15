<template>
    <b-row>
        <b-col :cols="12" :offset="0" :lg="8" :offset-lg="2" class="my-2">
            <b-card>
                <b-form @submit.prevent="onFormSubmit">
                    <div class="form-group bmd-form-group">
                        <label class="bmd-label-floating">شماره عضویت</label>
                        <b-form-input
                            type="number"
                            id="customer_id"
                            v-model="form.id"
                            @keyup="onFormChange('id')"
                        />
                    </div>
                    <div class="strike">
                        <span>یا</span>
                    </div>
                    <div class="form-group bmd-form-group">
                        <label class="bmd-label-floating">شماره همراه</label>
                        <b-form-input
                            id="customer_mobile"
                            v-model="form.mobile"
                            @keyup="onFormChange('mobile')"
                        />
                    </div>

                    <b-button v-if="!loading" type="submit" variant="success" block>جستجو / ثبت</b-button>
                    <b-button v-else variant="success" disabled block><b-spinner small></b-spinner></b-button>

                </b-form>
            </b-card>
            <b-card v-if="customer">
                <b-form @submit.prevent="onBuyFormSubmit">
                    <b-row>
                        <b-col cols=6 col-md=6>
                            <div class="form-group bmd-form-group non-float-label-group">
                                <money-widget v-model="form.price" decimal="," postfix=" تومان" prefix="نقد:" :precision="0" />
                            </div>
                        </b-col>
                        <b-col cols=6 col-md=4>
                            <div class="form-group bmd-form-group">
                                <label class="bmd-label-floating">سکه</label>
                                <b-form-input
                                    type="number"
                                    id="new_buy_coin"
                                    v-model="form.coin"
                                />
                            </div>
                        </b-col>

                        <div class="w-100 my-4">
                            <div class="strike">
                                <span>قیمت نهایی: {{ numberFormat((form.coin ? form.coin : 0) * 1000 + (form.price ? form.price : 0)) }} تومان</span>
                            </div>
                        </div>

                        <b-col cols=12 cols-md=2>
                            <b-button v-if="!loading" type="submit" variant="success" block>ثبت خرید</b-button>
                            <b-button v-else variant="success" disabled block><b-spinner small></b-spinner></b-button>
                        </b-col>
                    </b-row>

                </b-form>
                <b-row>
                    <b-col cols=12 md=6>
                        <div class="card card-stats">
                            <div class="card-header card-header-icon card-header-success">
                                <div class="card-icon">
                                    <i class="material-icons">beenhere</i>
                                </div>
                                <p class="card-category">سکه ها</p>
                                <h3 class="card-title">{{ customer.coin }}</h3>
                            </div>
                        </div>
                    </b-col>
                    <b-col cols=12 md=6>
                        <div class="card card-stats">
                            <div class="card-header card-header-icon card-header-success">
                                <div class="card-icon">
                                    <i class="material-icons">control_point</i>
                                </div>
                                <p class="card-category">امتیاز کل</p>
                                <h3 class="card-title">{{ customer.point }}</h3>
                            </div>
                        </div>
                    </b-col>
                </b-row>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    id: null,
                    mobile: null,
                    price: null,
                    coin: null,
                },
                customer: null,
                loading:false,
            }
        },
        methods: {
            onFormChange(name) {
                this.customer = null;
                if (name == 'id') {
                    this.form.mobile = null;
                } else if (name == 'mobile') {
                    this.form.id = null;
                }
            },
            onFormSubmit() {
                var self = this;
                self.loading = true;
                axios.post(sanjabUrl('/modules/customers/search'), self.form)
                .then(function (response) {
                    self.loading = false;
                    if (response.data.newCreated) {
                        sanjabSuccess('مشتری جدید با موفقیت اضافه شد.');
                        self.$parent.$parent.$refs.table.refresh();
                    }
                    self.customer = response.data.customer;
                }).catch(function (error) {
                    console.error(error);
                    self.loading = false;
                    if (error.response.status == 422) {
                        sanjabError(error.response.data.errors[Object.keys(error.response.data.errors)[0]][0]);
                    } else {
                        sanjabHttpError(error.response.status);
                    }
                });
            },
            onBuyFormSubmit() {
                var self = this;
                self.loading = true;
                axios.post(sanjabUrl('/modules/customers/buy/' + self.customer.id), self.form)
                .then(function (response) {
                    self.loading = false;
                    self.form.price = 0;
                    self.form.coin = null;
                    sanjabSuccess(response.data.message);
                    self.customer = response.data.customer;
                    self.$parent.$parent.$refs.table.refresh();
                    self.$forceUpdate();
                }).catch(function (error) {
                    console.error(error);
                    self.loading = false;
                    if (error.response.status == 422) {
                        sanjabError(error.response.data.errors[Object.keys(error.response.data.errors)[0]][0]);
                    } else {
                        sanjabHttpError(error.response.status);
                    }
                });
            }
        },
    }
</script>

<style lang="scss" scoped>
    .strike {
        display: block;
        text-align: center;
        overflow: hidden;
        white-space: nowrap;
    }

    .strike > span {
        position: relative;
        display: inline-block;
    }

    .strike > span:before,
    .strike > span:after {
        content: "";
        position: absolute;
        top: 50%;
        width: 9999px;
        height: 1px;
        background: #2196f3;
    }

    .strike > span:before {
        right: 100%;
        margin-right: 15px;
    }

    .strike > span:after {
        left: 100%;
        margin-left: 15px;
    }
</style>
