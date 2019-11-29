<template>
    <b-row>
        <b-col :cols="12" :offset="0" :xl="8" :offset-xl="2" class="my-2">
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
                                    <i class="material-icons">money_off</i>
                                </div>
                                <p class="card-category">تعداد خریدها</p>
                                <h3 class="card-title">{{ customer.transactions_count }}</h3>
                            </div>
                        </div>
                    </b-col>
                    <b-col cols=12 md=6>
                        <div class="card card-stats">
                            <div class="card-header card-header-icon card-header-success">
                                <div class="card-icon">
                                    <i class="material-icons">money_off</i>
                                </div>
                                <p class="card-category">تعداد خریدهای 30 روز گذشته</p>
                                <h3 class="card-title">{{ customer.last_month_transactions_count }}</h3>
                            </div>
                        </div>
                    </b-col>
                    <b-col cols=12 md=6>
                        <div class="card card-stats">
                            <div class="card-header card-header-icon card-header-success">
                                <div class="card-icon">
                                    <i class="material-icons">control_point</i>
                                </div>
                                <p class="card-category">خرید کل</p>
                                <h3 class="card-title">{{ numberFormat(customer.total_buy) }}</h3>
                            </div>
                        </div>
                    </b-col>
                </b-row>
                <b-table
                    ref="transactionsTable"
                    :items="transactions"
                    :fields="transactionFields"
                    :current-page="transactionsPage"
                    per-page=10
                    striped
                    hover
                    responsive
                    show-empty
                >
                    <template #table-busy>
                        <div class="text-center text-danger my-2">
                            <b-spinner variant="default" class="align-middle"/>
                        </div>
                    </template>
                    <template #empty>
                        <center>خریدی وجود ندارد.</center>
                    </template>
                    <template #cell(delete)="row">
                        <b-button :disabled="!row.item.can_delete" @click="deleteTransaction(row.item)" variant="danger" size="sm" title="حذف" v-b-tooltip>
                            <i class="material-icons">delete</i>
                        </b-button>
                    </template>
                    <template #cell(money)="row">
                        {{ numberFormat(row.item.money) }}
                    </template>
                </b-table>
                <b-pagination
                    v-show="transactionsTotal > 1"
                    v-model="transactionsPage"
                    :total-rows="transactionsTotal"
                    per-page=10
                    class="my-0"
                    variant="warning"
                    align="center"
                />
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
                transactionsPage: 1,
                transactionsTotal: 0,
                transactionFields: [
                    {key: 'money', label: 'نقد', sortable: false},
                    {key: 'coin', label: 'سکه', sortable: false},
                    {key: 'date', label: 'تاریخ', sortable: false},
                    {key: 'delete', label: 'حذف', sortable: false},
                ],
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
                    self.form.id = self.customer.id;
                    self.form.mobile = self.customer.mobile;
                    self.transactionsPage = 1;
                    self.transactionsTotal = 0;
                    if (self.$refs.transactionsTable) {
                        self.$refs.transactionsTable.refresh();
                    }
                    setTimeout(() => $("input").trigger("change"), 100);
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
                    self.transactionsTotal = response.data.customer.total_buy;
                    if (self.$refs.transactionsTable) {
                        self.$refs.transactionsTable.refresh();
                    }
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
            },
            transactions(info) {
                info.page = info.currentPage;
                info.searchTypes = this.searchTypes;
                info.search = this.search;
                var self = this;
                return axios.get(sanjabUrl("/modules/customers/transactions/" + self.customer.id), {
                    params: info,
                    paramsSerializer: params => qs.stringify(params)
                })
                .then(function (response) {
                    self.transactionsTotal = response.data.total;
                    return response.data.data;
                })
                .catch(function (error) {
                    console.error(error);
                    sanjabError(sanjabTrans('some_error_happend'));
                });
            },
            deleteTransaction(transaction) {
                var self = this;
                Swal.fire({
                    title: "آیا برای حذف مطمئن هستید؟",
                    showCancelButton: true,
                    showLoaderOnConfirm: true,
                    confirmButtonText: "بله",
                    cancelButtonText: "خیر",
                    preConfirm: (input) => {
                        return axios.delete(sanjabUrl("/modules/customers/transactions/" + transaction.id))
                        .then(function (response) {
                            return response.data;
                        }).catch((error) => {
                            Swal.showValidationMessage(error.response.data.message ? error.response.data.message : sanjabHttpErrorMessage(error.response.status));
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            type: 'success',
                            title: result.value.message ? result.value.message : sanjabTrans('success'),
                            confirmButtonText: "تایید",
                        });
                        if (self.$refs.transactionsTable) {
                            self.$refs.transactionsTable.refresh();
                        }
                        self.$parent.$parent.$refs.table.refresh();
                        console.log(result.value.customer);
                        self.customer = result.value.customer;
                    }
                })
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
