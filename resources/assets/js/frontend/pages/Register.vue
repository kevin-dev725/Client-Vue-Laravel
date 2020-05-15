<template>
    <div class="container pt-5 pb-5 page-register">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-3">
                    <img src="/images/register here.png" class="img-fluid"/>
                </div>
                <div class="card">
                    <div class="card-header"
                         v-if="user">Finish Signup
                    </div>
                    <div class="card-body">
                        <b-form @submit.prevent="register">
                            <div v-if="!user">
                                <div class="section-title mt-0">
                                    <h4>Login Details</h4>
                                    <p>
                                        Enter your email address and make a password. You’ll use them to log into the clientDomain app and web site. Any info marked with * is mandatory.
                                    </p>
                                </div>

                                <!--<b-form-group horizontal
                                              class="mb-3"
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Account Type*"
                                              label-class="text-md-right"
                                              label-for="account_type"
                                              :invalid-feedback="form.errors.get('account_type')"
                                              :state="form.errors.state('account_type')">
                                    <div class="d-flex align-items-center">
                                        <b-form-radio-group id="account_type"
                                                            v-model="form.account_type"
                                                            :options="accountTypeOptions"
                                                            :state="form.errors.state('account_type')">
                                        </b-form-radio-group>
                                    </div>
                                </b-form-group>

                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              :label="companyNameLabel"
                                              label-for="companyName"
                                              label-class="text-md-right"
                                              :invalid-feedback="form.errors.get('company_name')"
                                              v-if="isCompany">
                                    <multi-select ref="companyMultiSelect"
                                                  :value="form.company_name"
                                                  v-model="companyMultiSelectModel"
                                                  :options="companyMultiSelectOptions"
                                                  @select="setUserCompany"
                                                  :disabled="!isCompany"
                                                  close-on-select
                                                  clear-on-select
                                                  label="name"
                                                  track-by="name">
                                    </multi-select>
                                </b-form-group>-->

                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Email Address*"
                                              label-class="text-md-right"
                                              label-for="email"
                                              :invalid-feedback="form.errors.get('email')">
                                    <b-form-input id="email"
                                                  type="email"
                                                  v-model="form.email"
                                                  :state="form.errors.state('email')"></b-form-input>
                                </b-form-group>

                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Password*"
                                              label-class="text-md-right"
                                              label-for="password"
                                              :invalid-feedback="form.errors.get('password')">
                                    <b-form-input id="password"
                                                  type="password"
                                                  v-model="form.password"
                                                  :state="form.errors.state('password')"></b-form-input>
                                </b-form-group>

                                <b-form-group horizontal
                                              label-cols="4"
                                              breakpoint="md"
                                              label="Confirm Password*"
                                              label-class="text-md-right"
                                              label-for="password_confirmation"
                                              :invalid-feedback="form.errors.get('password_confirmation')">
                                    <b-form-input id="password_confirmation"
                                                  type="password"
                                                  v-model="form.password_confirmation"
                                                  :state="form.errors.state('password_confirmation')"></b-form-input>
                                </b-form-group>
                            </div>

                            <div class="section-title">
                                <h4>Contact Details</h4>
                                <p>
                                    Enter your name, address and phone number. The clientDomain app uses these to build its community of contractors. Any info marked with * is mandatory.
                                </p>
                            </div>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="First name*"
                                          label-class="text-md-right"
                                          label-for="first_name"
                                          :invalid-feedback="form.errors.get('first_name')">
                                <b-form-input id="first_name"
                                              v-model="form.first_name"
                                              :state="form.errors.state('first_name')"></b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Middle name / initial"
                                          label-class="text-md-right"
                                          label-for="middle_name"
                                          :invalid-feedback="form.errors.get('middle_name')">
                                <b-form-input id="middle_name"
                                              v-model="form.middle_name"
                                              :state="form.errors.state('middle_name')"></b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Last name*"
                                          label-class="text-md-right"
                                          label-for="last_name"
                                          :invalid-feedback="form.errors.get('last_name')">
                                <b-form-input id="last_name"
                                              v-model="form.last_name"
                                              :state="form.errors.state('last_name')"></b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Street Address*"
                                          label-class="text-md-right"
                                          label-for="street"
                                          :invalid-feedback="form.errors.get('street_address')">
                                <b-form-input id="street"
                                              type="text"
                                              v-model="form.street_address"
                                              :state="form.errors.state('street_address')">
                                </b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Street Address 2"
                                          label-class="text-md-right"
                                          label-for="street2"
                                          :invalid-feedback="form.errors.get('street_address2')">
                                <b-form-input id="street2"
                                              type="text"
                                              v-model="form.street_address2"
                                              :state="form.errors.state('street_address2')">
                                </b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="City*"
                                          label-class="text-md-right"
                                          label-for="city"
                                          :invalid-feedback="form.errors.get('city')">
                                <b-form-input id="city"
                                              type="text"
                                              v-model="form.city"
                                              :state="form.errors.state('city')">
                                </b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="State*"
                                          label-class="text-md-right"
                                          label-for="state"
                                          :invalid-feedback="form.errors.get('state')">
                                <b-form-select id="state"
                                               :options="states"
                                               v-model="form.state"
                                               :state="form.errors.state('state')">
                                </b-form-select>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Zip Code*"
                                          label-class="text-md-right"
                                          label-for="postal_code"
                                          :invalid-feedback="form.errors.get('postal_code')">
                                <b-form-input id="postal_code"
                                              type="text"
                                              v-model="form.postal_code"
                                              :state="form.errors.state('postal_code')">
                                </b-form-input>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Phone Number*"
                                          label-class="text-md-right"
                                          label-for="phone"
                                          description="if with extension add <code>ext. 1234</code>"
                                          :invalid-feedback="form.errors.get('phone_number')">
                                <b-form-input class="d-none"
                                              :state="form.errors.state('phone_number')">
                                </b-form-input>
                                <intl-tel-input id="phone"
                                                v-model="form.phone_number"
                                                :has-error="form.errors.has('phone_number')"
                                                @with-extension="setExtensionPhone"/>
                            </b-form-group>

                            <b-form-group horizontal
                                          label-cols="4"
                                          breakpoint="md"
                                          label="Alt Phone Number"
                                          label-class="text-md-right"
                                          label-for="alt_phone"
                                          description="if with extension add <code>ext. 1234</code>"
                                          :invalid-feedback="form.errors.get('alt_phone_number')">
                                <b-form-input class="d-none"
                                              :state="form.errors.state('alt_phone_number')">
                                </b-form-input>
                                <intl-tel-input id="alt_phone"
                                                v-model="form.alt_phone_number"
                                                :has-error="form.errors.has('alt_phone_number')"
                                                @with-extension="setExtensionAltPhone"/>
                            </b-form-group>

                            <div class="section-title" v-if="config.settings.free_account_on_register_enabled">
                                <h4>For a limited time only, {{ config.app.name }} membership is free!</h4>
                                <p>
                                    We want to build our nationwide community of contractors who build their businesses by finding clients they love. So, for a limited time, we’re making it easier than ever to join {{ config.app.name }} by making it free!
                                </p>
                                <p>
                                    You’re nearly done — complete the registration process by agreeing to the terms of use below and clicking the <b>Register</b> button.
                                </p>
                            </div>
                            <template v-else>
                                <div class="section-title">
                                    <h4>Membership</h4>
                                    <p>
                                        clientDomain memberships automatically renew at the end of each billing period. You can save money with the yearly membership!
                                    </p>
                                </div>

                                <template v-if="config.app.env !== 'testing'">
                                    <b-form-group horizontal
                                                  class="mt-4"
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
                                </template>
                            </template>

                            <div class="section-title">
                                <h4>Privacy Policy / Terms of Use</h4>
                                <p>
                                    At clientDomain, we respect your right to privacy. Please read our <a href="https://clientDomain.app/privacy-policy/" target="_blank">privacy policy</a> and <a href="https://clientDomain.app/terms-of-use/" target="_blank">terms and conditions</a> and check the box below to confirm that you agree:
                                </p>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox agree-terms">
                                    <input type="checkbox" required class="custom-control-input" id="agree">
                                    <label class="custom-control-label" for="agree">I have read the privacy policy and agree to the terms of use.</label>
                                </div>
                            </div>

                            <div class="form-group row mb-0 mt-5">
                                <b-btn type="submit"
                                       variant="primary"
                                       :disabled="form.busy" style="margin: 0 auto">
                                    <!--<i v-if="form.busy"
                                       class="fa fa-spinner fa-spin"></i>--> Register
                                </b-btn>
                            </div>
                        </b-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import * as AccountType from './../../constants/user-account-type';
    import MultiSelect from 'vue-multiselect';

    import IntlTelInput from './../../components/Globals/IntlTelInput';
    import {Card, createToken} from 'vue-stripe-elements-plus';

    export default {
        props: {
            user: {
                type: Object,
                required: false,
            }
        },
        components: {
            MultiSelect, IntlTelInput, Card
        },
        data () {
            return {
                form: new window.Form({
                    account_type: 'individual',
                    company_id: '',
                    company_name: '',
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    street_address: '',
                    street_address2: '',
                    city: '',
                    state: '',
                    postal_code: '',
                    phone_number: '',
                    phone_number_ext: '',
                    alt_phone_number: '',
                    alt_phone_number_ext: '',
                    card_token: null,
                    country_id: null,
                    plan_interval: 'monthly',
                    coupon_code: null,
                }),
                accountTypeOptions: [
                    {value: AccountType.ACCOUNT_TYPE_INDIVIDUAL, text: 'Individual'},
                    {value: AccountType.ACCOUNT_TYPE_COMPANY, text: 'Company'}
                ],
                companyMultiSelectOptions: [],
                companyMultiSelectModel: null,
                card: {
                    complete: false,
                    options: {},
                },
                success: false,
                coupon: null,
                checkingCoupon: false,
                redirectUrl: '/dashboard/settings?after_signup=1',
            };
        },
        mounted () {
            this.getCompanies();
            if (this.user) {
                window.$.extend(this.form, window._.pick(this.user, ['first_name', 'middle_name', 'last_name', 'account_type', 'email']));
            }
            if (this.shouldReportAnalytics) {
                fbq('track', 'PageView');
            }
        },
        computed: {
            shouldReportAnalytics() {
                return this.config.app.env === 'production' || this.config.app.env === 'prod';
            },
            states () {
                return window.Store.states;
            },
            companyNameLabel () {
                return 'Company name';
            },
            isCompany () {
                return this.form.account_type === AccountType.ACCOUNT_TYPE_COMPANY;
            },
            stripeKey () {
                return window.Store.config.keys.stripe;
            },
            config () {
                return window.Store.config;
            },
            computedCountries () {
                return window.Store.countries.map(item => {
                    return {
                        value: item.id,
                        text: item.name
                    };
                });
            }
        },
        methods: {
            getCompanies () {
                window.axios.get('/api/v1/company')
                  .then(response => {
                      this.companyMultiSelectOptions = response.data.data;
                  });
            },
            setExtensionPhone (data) {
                this.form.phone_number_ext = data;
            },
            setExtensionAltPhone (data) {
                this.form.alt_phone_number_ext = data;
            },
            setUserCompany (data) {
                this.form.company_id = data.id;
                this.form.company_name = data.name;
            },
            register () {
                let self = this;
                this.form.startProcessing();
                if (this.config.app.env === 'testing') {
                    window.App.post(this.user ? '/register/finish' : '/register', this.form)
                        .then(() => {

                        });
                } else {
                    let proceed = () => {
                        window.App.post(this.user ? '/register/finish' : '/register', this.form)
                            .then(() => {
                                this.form.startProcessing();
                                if (this.shouldReportAnalytics) {
                                    if (typeof fbq('track', 'CompleteRegistration') === 'undefined') {
                                        window.location = this.redirectUrl;
                                    } else {
                                        window.location = this.redirectUrl;
                                    }
                                } else {
                                    window.location = this.redirectUrl;
                                }
                            });
                    };
                    if (this.config.settings.free_account_on_register_enabled) {
                        proceed();
                    } else {
                        createToken()
                            .then(response => {
                                this.form.card_token = response.token.id;
                                proceed();
                            })
                            .catch(() => {
                                this.form.finishProcessing(false);
                                this.form.errors.add('card_token', 'Invalid card details.');
                            });
                    }
                }
            },
            cardChange (e) {
                this.card.complete = e.complete;
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
        watch: {
            'form.account_type' (value) {
                if (value === AccountType.ACCOUNT_TYPE_INDIVIDUAL) {
                    this.form.company_id = null;
                    this.form.company_name = null;
                }
            }
        }
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
