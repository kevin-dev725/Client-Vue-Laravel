<template>
    <div class="animated fadeIn frontend-content mb-5 mt-5">
        <b-container>
            <b-row align-h="center">
                <b-col md="8">
                    <b-row>
                        <b-col sm="10">
                            <b-form>
                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Subscription"
                                              label-class="text-md-right">
                                    <div class="pt-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="radio-monthly" name="subscription" class="custom-control-input" value="monthly" v-model="form.plan_interval">
                                            <label class="custom-control-label" for="radio-monthly">Monthly - {{ config.services.stripe.plan.price | currency }}</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="radio-yearly" name="subscription" class="custom-control-input" value="yearly" v-model="form.plan_interval">
                                            <label class="custom-control-label" for="radio-yearly">Yearly - {{ config.services.stripe.yearly_plan.price | currency }}</label>
                                        </div>
                                    </div>
                                </b-form-group>

                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Credit Card"
                                              label-class="text-md-right"
                                              label-for="credit_card">
                                    <card id="credit_card"
                                          class='stripe-card'
                                          :class='{ complete: card.complete }'
                                          :stripe='stripeKey'
                                          :options='card.options'
                                          @change='cardChange'></card>
                                    <div class="text-danger small mt-1"
                                         v-if="form.errors.has('card_token')"
                                         v-text="form.errors.get('card_token')"></div>
                                </b-form-group>

                                <b-form-group horizontal
                                              class="mb-1"
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Promo Code"
                                              label-class="text-md-right"
                                              label-for="coupon_code"
                                              :invalid-feedback="form.errors.get('coupon_code')"
                                >
                                    <template v-if="!coupon">
                                        <b-form-input id="coupon_code"
                                                      v-model="form.coupon_code"
                                                      :state="form.errors.state('coupon_code')"></b-form-input>
                                    </template>
                                    <div class="alert alert-success" v-else>
                                        <i class="fa fa-check-circle"></i> {{ coupon.name }}
                                    </div>
                                </b-form-group>
                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label-class="text-md-right"
                                              v-if="!coupon"
                                >
                                    <a href="#" @click.prevent="checkCoupon"><i class="fa fa-spinner fa-spin" v-if="checkingCoupon"></i> Click here to see if the promo code is valid</a>
                                </b-form-group>
                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label-class="text-md-right"
                                >
                                    <b-btn class="mt-2 d-block" variant="success" @click="resume" :disabled="form.busy"><i class="fa" :class="{'fa-check': !form.busy || checkingCoupon, 'fa-spinner fa-spin': form.busy && !checkingCoupon}"></i> Subscribe Now</b-btn>
                                </b-form-group>
                            </b-form>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import { Card, createToken } from 'vue-stripe-elements-plus';

    export default {
        components: {
            Card
        },
        data () {
            return {
                card: {
                    complete: false,
                    options: {},
                },
                form: new Form({
                    card_token: null,
                    plan_interval: 'monthly',
                    coupon_code: null,
                }),
                coupon: null,
                checkingCoupon: false,
            };
        },
        mounted () {

        },
        computed: {
            stripeKey () {
                return window.Store.config.keys.stripe;
            },
            config () {
                return window.Store.config;
            },
            user () {
                return window.Store.user;
            }
        },
        methods: {
            cardChange (e) {
                this.card.complete = e.complete;
            },
            resume () {
                this.confirmResume();
            },
            confirmResume() {
                this.form.startProcessing();
                createToken()
                    .then(response => {
                        this.form.card_token = response.token.id;
                        this.submit();
                    })
                    .catch(() => {
                        this.form.finishProcessing(false);
                        this.form.errors.add('card_token', 'Invalid card details.');
                    });
            },
            submit () {
                App.post(`/api/v1/subscription/resume`, this.form)
                    .then(() => {
                        this.form.startProcessing();
                        window.location.reload();
                        /*window.Store.getUser(null, ['stripe_subscription'])
                            .then((user) => {
                                this.$emit('success', user.stripe_subscription)
                            })
                            .finally(() => {
                                this.form.finishProcessing();
                            })*/
                    })
            },
            checkCoupon() {
                if (this.checkingCoupon) {
                    return;
                }
                this.checkingCoupon = true;
                window.App.post('/web-api/coupon/check', this.form)
                    .then(response => {
                        this.checkingCoupon = false;
                        this.coupon = response.data;
                    })
                    .catch(() => this.checkingCoupon = false);
            }
        },
        watch: {}
    };
</script>

<style>
    .StripeElement {
        background-color: white;
        height: 40px;
        padding: 10px 12px;
        border-radius: 4px;
        /*border: 1px solid transparent;*/
        border: 1px solid #869fac;
        /*box-shadow: 0 1px 3px 0 #e6ebf1;*/
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
