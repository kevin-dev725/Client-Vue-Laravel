<template>
    <div class="frontend-content frontend-plans animated fadeIn pt-5 pb-5">
        <b-container>
            <b-row align-h="center" v-if="success">
                <b-col cols="6">
                    <b-alert class="text-center" show variant="success">
                        <h4 class="alert-heading">Congratulations!</h4>
                        <p>
                            You are now subscribed to <span class="text-capitalize">{{ plan.name }}</span>.
                        </p>
                    </b-alert>
                </b-col>
            </b-row>
            <b-row align-h="center" class="pb-3" v-else>
                <b-col cols="6" v-if="plan">
                    <b-card :title="plan.name" :sub-title="plan.price | currency" body-class="text-center">
                    </b-card>
                </b-col>
            </b-row>
            <b-row align-h="center" class="pb-3" v-if="!success">
                <b-col cols="6">
                    <b-card title="Payment Details">
                        <div class="payment-options" v-if="user.stripe_id">
                            <b-form-group label="Payment Options:" class="mb-0">
                                <b-form-radio-group id="payment-options" v-model="payWithExistingCard">
                                    <b-form-radio :value="true">Use {{ user.card_brand }} ending <strong>{{ user.card_last_four }}</strong></b-form-radio>
                                    <b-form-radio :value="false">Use other credit card</b-form-radio>
                                </b-form-radio-group>
                            </b-form-group>
                        </div>

                        <div class="stripe-details mt-3 mb-3">
                            <card class='stripe-card'
                                  :class='{ complete }'
                                  :stripe='stripeKey'
                                  :options='stripeOptions'
                                  @change='change'/>

                        </div>

                        <b-btn variant="primary" class="mt-3" @click="createToken" :disabled='disableBtn' block>
                            <icon v-if="form.busy" name="spinner" spin/>
                            Submit payment
                        </b-btn>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    import { Card, createToken } from 'vue-stripe-elements-plus';

    export default {
        beforeRouteEnter () {

        },
        components: {
            Card
        },
        data () {
            return {
                stripeKey: Store.config.keys.stripe,
                stripeOptions: {},
                complete: false,
                plan: {},
                form: new Form({
                    card_token: null
                }),
                payWithExistingCard: true,
                success: false
            };
        },
        mounted () {
            this.getPlan();
        },
        computed: {
            planId () {
                return this.$route.params.id;
            },
            user () {
                return Store.user;
            },
            disableBtn () {
                if (this.payWithExistingCard) {
                    return false;
                }
                return !this.complete;
            }
        },
        methods: {
            submit (token = null) {
                this.form.card_token = token ? token.id : null;
                App.post(`/api/v1/plan/${this.planId}/subscribe`, this.form)
                  .then(response => {
                      this.success = true;
                      window.Store.getUser();
                  })
            },
            createToken () {
                this.form.startProcessing();
                createToken()
                  .then(data => {
                      this.submit(data.token);
                  });
            },
            change (e) {
                console.log('stripe change');
                this.complete = e.complete;
            },
            getPlan () {
                axios.get(`/api/v1/plan/${this.planId}`)
                  .then(response => {
                      this.plan = response.data;
                  })
            }
        },
        watch: {}
    };
</script>