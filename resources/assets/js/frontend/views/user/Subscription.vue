<template>
    <div class="animated fadeIn frontend-content mb-5 mt-5">
        <b-container>
            <b-row align-h="center">
                <b-col md="8">
                    <b-card title="Subscription">
                        <div v-if="user.is_subscribed_to_plan">
                            <div v-if="user.subscription.is_on_grace_period">
                                <span class="badge badge-warning">Cancelled</span> <span>Ends on {{ user.subscription.ends_at | date }}.</span>
                                <br/>
                                <b-btn class="mt-2" variant="success" size="sm" @click="resume"><i class="fa fa-check"></i> Resume Subscription</b-btn>
                            </div>
                            <div v-else>
                                <span class="badge badge-success">Subscribed</span> <span v-if="renewsAt">Automatically renews on {{ renewsAt | date }}.</span><i v-else class="fa fa-spinner fa-spin"></i>
                                <br/>
                                <b-btn class="mt-2" variant="danger" size="sm" @click="cancel"><i class="fa fa-times"></i> Cancel Subscription</b-btn>
                            </div>
                        </div>
                        <div v-else>
                            <template v-if="user.subscription">
                                <span class="badge badge-danger">Cancelled</span> <span>Ended on {{ user.subscription.ends_at | date }}.</span>
                                <br/>
                                <b-form-group class="mt-3" label="Payment Details">
                                    <b-form-radio-group v-model="selected"
                                                        :options="options"
                                                        name="radioInline">
                                    </b-form-radio-group>
                                </b-form-group>
                                <b-row>
                                    <b-col sm="6">
                                        <b-card body-class="p-3">
                                            <template v-if="selected === 'old'">
                                                {{ user.card_brand }} ending with {{ user.card_last_four }}
                                            </template>
                                            <b-form v-else>
                                                <!--<div class="mb-3">
                                                    Monthly Subscription: {{ $options.filters.currency(config.services.stripe.plan.price) }}
                                                </div>-->
                                                <card id="credit_card"
                                                      class='stripe-card'
                                                      :class='{ complete: card.complete }'
                                                      :stripe='stripeKey'
                                                      :options='card.options'
                                                      @change='cardChange'></card>
                                                <div class="text-danger small mt-1"
                                                     v-if="form.errors.has('card_token')"
                                                     v-text="form.errors.get('card_token')"></div>
                                            </b-form>
                                        </b-card>
                                    </b-col>
                                </b-row>
                                <b-btn class="mt-2 d-block" variant="success" @click="resume"><i class="fa fa-check"></i> Resume Subscription</b-btn>
                            </template>
                            <template v-else>
                                <!--previously on trial-->
                                <div class="alert alert-warning"><i class="fa fa-warning"></i> Your trial period is up.</div>
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
                            </template>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
        <b-modal ref="modalResume" title="Confirmation">
            <div>Confirm resume subscription?</div>
            <div slot="modal-footer">
                <b-btn @click="$refs.modalResume.hide()" :disabled="form.busy">Cancel</b-btn>
                <b-btn variant="success" @click="confirmResume" :disabled="form.busy"><i class="fa" :class="{'fa-check': !form.busy, 'fa-spinner fa-spin': form.busy}"></i> Resume Subscription</b-btn>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import { Card, createToken } from 'vue-stripe-elements-plus';

    export default {
        beforeRouteEnter (to, from, next) {
            if (window.Store.user.is_subscribed_to_plan) {
                next({name: 'Dashboard'});
            }
            next();
        },
        components: {
            Card
        },
        data () {
            return {
                subscriptions: null,
                selected: 'old',
                options: [
                    { text: 'Saved Credit Card', value: 'old' },
                    { text: 'New Credit Card', value: 'new' }
                ],
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
            this.getSubscription();
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
            getSubscription() {
                window.axios.get('/api/v1/auth/user?include=stripe_subscription')
                  .then(response => {
                      this.subscription = response.data.stripe_subscription;
                  })
            },
            resume () {
                if (!this.user.subscription) {
                    //on trial
                    this.selected = 'new';
                    this.confirmResume();
                } else {
                    this.$refs.modalResume.show();
                }
            },
            confirmResume() {
                if (this.selected === 'new') {
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
                } else {
                    this.submit();
                }
            },
            submit () {
                App.post(`/api/v1/subscription/resume`, this.form)
                  .then(() => {
                      this.form.startProcessing();
                      window.Store.getUser(null, ['stripe_subscription'])
                        .then((user) => {
                            this.subscription = user.stripe_subscription;
                            this.form.clearFields();
                            this.$refs.modalResume.hide();
                            this.notify('success', 'You have successfully resumed your subscription!');
                            location.reload();
                        })
                        .finally(() => {
                            this.form.finishProcessing();
                        })
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
