<template>
    <div>
        <b-form-row>
            <b-col>
                <b-form-group label="Street Address*"
                              label-for="street"
                              :invalid-feedback="form.errors.get('street_address')">
                    <b-form-input id="street"
                                  type="text"
                                  v-model="form.street_address"
                                  :state="form.errors.state('street_address')">
                    </b-form-input>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
                <b-form-group label="Street Address 2"
                              label-for="street2"
                              :invalid-feedback="form.errors.get('street_address2')">
                    <b-form-input id="street2"
                                  type="text"
                                  v-model="form.street_address2"
                                  :state="form.errors.state('street_address2')">
                    </b-form-input>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col cols="6">
                <b-form-group label="City*"
                              label-for="city"
                              :invalid-feedback="form.errors.get('city')">
                    <b-form-input id="city"
                                  type="text"
                                  v-model="form.city"
                                  :state="form.errors.state('city')">
                    </b-form-input>
                </b-form-group>
            </b-col>
            <b-col cols="6">
                <b-form-group label="State*"
                              label-for="state"
                              :invalid-feedback="form.errors.get('state')">
                    <b-form-select id="state"
                                   :options="states"
                                   v-model="form.state"
                                   :state="form.errors.state('state')">
                    </b-form-select>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
                <b-form-group label="Zip Code*"
                              label-for="postalCode"
                              :invalid-feedback="form.errors.get('postal_code')">
                    <b-form-input id="postalCode"
                                  type="text"
                                  v-model="form.postal_code"
                                  :state="form.errors.state('postal_code')">
                    </b-form-input>
                </b-form-group>
            </b-col>
            <!--<b-col>
                <b-form-group label="Country"
                              label-for="country"
                              :invalid-feedback="form.errors.get('country_id')">
                    <b-form-select id="country"
                                   v-model="form.country_id"
                                   :state="form.errors.state('country_id')"
                                   :options="countries"/>
                </b-form-group>
            </b-col>-->
        </b-form-row>
    </div>
</template>

<script>
    export default {
        props: {
            form: {
                type: Object,
                required: true,
            }
        },
        computed: {
            countries () {
                return window.Store.countrySelectOptions;
            },
            states () {
                return window.Store.states;
            }
        },
    };
</script>

<style scoped>

</style>
