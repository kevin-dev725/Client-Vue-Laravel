<template>
    <b-card class="animated fadeIn">
        <h4 slot="header" class="text-center">
            Create a new client
        </h4>
        <form-wizard ref="wizard" color="#539DCF" error-color="#f86c6b" :title="null" :subtitle="null"
                @on-validate="handleValidation" @on-complete="submit">
            <tab-content title="Personal Info" :before-change="validateStep">
                <personal-info :form="form"/>
            </tab-content>
            <tab-content title="Address" :before-change="validateStep">
                <address-form :form="form"/>
            </tab-content>
            <tab-content title="Rating" v-if="isOrganization" :before-change="validateStep">
                <rating :form="form"/>
            </tab-content>
            <tab-content title="Billing" v-if="isOrganization" :before-change="validateStep">
                <billing :form="form"/>
            </tab-content>
            <tab-content title="Rating" v-if="!isOrganization" :before-change="validateStep">
                <rating :form="form"/>
            </tab-content>
            <b-button variant="primary" :disabled="form.busy" slot="prev">Back</b-button>
            <b-button variant="primary" :disabled="form.busy" slot="next">Next</b-button>
            <b-button variant="primary" :disabled="form.busy" slot="finish"><icon :name="form.busy ? 'spinner' : 'save'" :spin="form.busy"/> Submit</b-button>
        </form-wizard>
        <p>* are required fields.</p>
        <!--<b-form @submit.prevent="submit">
            <b-button type="submit" variant="primary" :disabled="form.busy">
                <icon v-if="!form.busy" name="save"/>
                <icon v-else name="spinner" spin/>
                Save Changes
            </b-button>
            <b-btn variant="secondary" class="text-white" :disabled="form.busy" @click="goTo('UserClientsList', {id: userId})">
                <i class="fa fa-caret-left"></i>
                Back to clients list
            </b-btn>
        </b-form>-->
    </b-card>
</template>

<script>
    import * as ClientType from './../../../constants/client-type';
    import RatingInput from '../../../components/Globals/RatingInput';
    import TypeAhead from '../../../components/Globals/TypeAhead';
    import PersonalInfo from './create/PersonalInfo';
    import AddressForm from './create/Address';
    import Billing from './create/Billing';
    import Rating from './create/Rating';
    import { Validator } from 'vee-validate';
    import * as ReviewRating from './../../../constants/review-rating';
    export default {
        components: {
            PersonalInfo, RatingInput, TypeAhead, AddressForm, Billing, Rating
        },
        data () {
            return {
                form: new Form ({
                    client_type: ClientType.CLIENT_TYPE_INDIVIDUAL,
                    organization_name: null,
                    first_name: null,
                    middle_name: null,
                    last_name: null,
                    phone_number: null,
                    phone_number_ext: null,
                    alt_phone_number: null,
                    alt_phone_number_ext: null,
                    street_address: null,
                    street_address2: null,
                    city: null,
                    state: null,
                    postal_code: null,
                    email: null,
                    billing_first_name: null,
                    billing_middle_name: null,
                    billing_last_name: null,
                    billing_phone_number: null,
                    billing_phone_number_ext: null,
                    billing_street_address: null,
                    billing_street_address2: null,
                    billing_city: null,
                    billing_state: null,
                    billing_postal_code: null,
                    billing_email: null,
                    initial_star_rating: null,
                    country_id: null,
                    review: {
                        star_rating: 4,
                        payment_rating: ReviewRating.RATING_NO_OPINION,
                        character_rating: ReviewRating.RATING_NO_OPINION,
                        repeat_rating: ReviewRating.RATING_NO_OPINION,
                        comment: null,
                    }
                }, this.veeErrors),
                veeErrors: null,
                clientTypeOptions: [
                    {value: ClientType.CLIENT_TYPE_ORGANIZATION,  text: 'Organization'},
                    {value: ClientType.CLIENT_TYPE_INDIVIDUAL, text: 'Individual'}
                ],
                initialStarRatingOptions: [
                  '1', '2', '3', '4', '5'
                ],
                organizations: [],
            };
        },
        mounted () {
            this.getOrganizations();
        },
        computed: {
            isOrganization () {
                return this.form.client_type === ClientType.CLIENT_TYPE_ORGANIZATION;
            }
        },
        methods: {
            getOrganizations() {
                window.axios.get(`/api/v1/client/organizations`)
                    .then(response => {
                        this.organizations = response.data;
                    });
            },
            submit () {
                let step_fields = [
                    ['first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext'],
                    ['street_address', 'street_address2', 'city', 'state', 'postal_code'],
                    ['billing_first_name', 'billing_middle_name', 'billing_last_name', 'billing_email', 'billing_phone_number', 'billing_phone_number_ext', 'billing_street_address', 'billing_street_address2', 'billing_city', 'billing_state', 'billing_postal_code'],
                    ['star_rating']
                ];
                window.App.post(`/api/v1/client`, this.form)
                    .then(response => {
                        this.goTo('dashboard-clients-single', {id: response.data.id});
                        this.notify('success', 'Successfully Created a new client');
                    })
                    .catch(error => {
                        console.log(error);
                        let stop = false;
                        window.$.each(step_fields, (index, value) => {
                            if (stop) {
                                let newIndex = index - 1;
                                this.$refs.wizard.changeTab(this.$refs.wizard.activeTabIndex, newIndex);
                                return false;
                            }
                            window.$.each(value, (index1, field) => {
                                if (this.form.errors.has(field)) {
                                    stop = true;
                                    return false;
                                }
                            });
                        })
                    });
            },
            fetchOrganizations: function (url) {
                return window.axios.get(url)
            },
            handleValidation() {

            },
            validateStep() {
                let step = this.$refs.wizard.activeTabIndex + 1;
                return new Promise((resolve, reject) => {
                    const validator = new Validator();
                    switch (step) {
                        case 1:
                            if (this.isOrganization) {
                                validator.attach({
                                    name: 'organization_name',
                                    rules: 'required|max:191',
                                    alias: 'organization name'
                                });
                            }
                            validator.attach({
                                name: 'first_name',
                                rules: 'required|max:191',
                                alias: 'first name'
                            });
                            validator.attach({
                                name: 'middle_name',
                                rules: 'max:191',
                                alias: 'middle name'
                            });
                            validator.attach({
                                name: 'last_name',
                                rules: 'required|max:191',
                                alias: 'last name'
                            });
                            validator.attach({
                                name: 'email',
                                rules: 'required|email|max:191'
                            });
                            validator.attach({
                                name: 'phone_number',
                                rules: 'required',
                                alias: 'phone number'
                            });
                            break;
                        case 2:
                            validator.attach({
                                name: 'street_address',
                                rules: 'required',
                                alias: 'street address'
                            });
                            validator.attach({
                                name: 'city',
                                rules: 'required',
                                alias: 'city'
                            });
                            validator.attach({
                                name: 'state',
                                rules: 'required|max:2',
                                alias: 'state'
                            });
                            validator.attach({
                                name: 'postal_code',
                                rules: 'required',
                                alias: 'zip code'
                            });
                            break;
                        case 3:
                            if (this.isOrganization) {
                                validator.attach({
                                    name: 'billing_first_name',
                                    rules: 'required|max:191',
                                    alias: 'billing first name'
                                });
                                validator.attach({
                                    name: 'billing_last_name',
                                    rules: 'required|max:191',
                                    alias: 'billing last name'
                                });
                                validator.attach({
                                    name: 'billing_middle_name',
                                    rules: 'max:191',
                                    alias: 'billing middle name'
                                });
                                validator.attach({
                                    name: 'billing_street_address',
                                    rules: 'required',
                                    alias: 'billing street address'
                                });
                                validator.attach({
                                    name: 'billing_city',
                                    rules: 'required',
                                    alias: 'billing city'
                                });
                                validator.attach({
                                    name: 'billing_state',
                                    rules: 'required|max:2',
                                    alias: 'billing state'
                                });
                                validator.attach({
                                    name: 'billing_postal_code',
                                    rules: 'required',
                                    alias: 'billing zip code'
                                });
                                validator.attach({
                                    name: 'billing_email',
                                    rules: 'required|email|max:191',
                                    alias: 'billing email'
                                });
                                validator.attach({
                                    name: 'billing_phone_number',
                                    rules: 'required',
                                    alias: 'billing phone number'
                                });
                            } else {

                            }
                            break;
                    }
                    validator.validateAll(window._.pick(this.form, ['first_name', 'organization_name', 'middle_name', 'last_name', 'email', 'phone_number', 'street_address', 'city', 'state', 'postal_code', 'billing_first_name', 'billing_middle_name', 'billing_last_name', 'billing_street_address', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_email', 'billing_phone_number']))
                        .then(result => {
                            this.form.errors.veeErrors = validator.errors;
                            if (!result) {
                                reject(validator.errors.all());
                            } else {
                                resolve(true);
                            }
                        });
                });
            }
        },
        watch: {}
    };
</script>

<style lang="scss" scoped>
    .v-autocomplete {
        .v-autocomplete-list {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
            .v-autocomplete-list-item {
                position: relative;
                display: block;
                padding: .75rem 1.25rem;
                margin-bottom: -1px;
                background-color: #fff;
                border: 1px solid rgba(0,0,0,.125);
                &.v-autocomplete-item-active {

                }
            }
        }
    }
</style>
