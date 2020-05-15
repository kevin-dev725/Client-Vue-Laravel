<template>
    <div class="animated fadeIn">
        <b-row>
            <b-col>
                <b-card :title="cardTitle">
                    <b-form @submit.prevent="submit">
                        <b-row>
                            <b-col md="6" lg="6">
                                <b-form-row>
                                    <b-col md="6">
                                        <b-form-group label="Account Type*"
                                                      label-for="clientType" :invalid-feedback="form.errors.get('client_type')">
                                            <b-form-select id="clientType"
                                                           v-model="form.client_type"
                                                           :options="clientTypeOptions" :state="form.errors.state('client_type')">
                                            </b-form-select>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="6">
                                        <b-form-group :label="'Organization Name' | appendRequired(isOrganization)"
                                                      label-for="organizationName" :invalid-feedback="form.errors.get('organization_name')">
                                            <b-form-input id="organizationName"
                                                          type="text"
                                                          v-model="form.organization_name"
                                                          :disabled="!isOrganization" :state="form.errors.state('organization_name')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col md="12" lg="4">
                                        <b-form-group label="First name*"
                                                      label-for="firstName"
                                                      :invalid-feedback="form.errors.get('first_name')">
                                            <b-form-input id="firstName"
                                                          type="text"
                                                          v-model="form.first_name" :state="form.errors.state('first_name')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="12" lg="4">
                                        <b-form-group label="Middle name / initial"
                                                      label-for="middleName" :invalid-feedback="form.errors.get('middle_name')">
                                            <b-form-input id="middleName"
                                                          type="text"
                                                          v-model="form.middle_name" :state="form.errors.state('middle_name')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="12" lg="4">
                                        <b-form-group label="Last name*"
                                                      label-for="lastName" :invalid-feedback="form.errors.get('last_name')">
                                            <b-form-input id="lastName"
                                                          type="text"
                                                          v-model="form.last_name" :state="form.errors.state('last_name')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Street Address*"
                                                      label-for="street" :invalid-feedback="form.errors.get('street_address')">
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
                                                      label-for="street2" :invalid-feedback="form.errors.get('street_address2')">
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
                                                      label-for="city" :invalid-feedback="form.errors.get('city')">
                                            <b-form-input id="city"
                                                          type="text"
                                                          v-model="form.city" :state="form.errors.state('city')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-group label="State*"
                                                      label-for="state" :invalid-feedback="form.errors.get('state')">
                                            <b-form-input id="state"
                                                          type="text"
                                                          v-model="form.state" :state="form.errors.state('state')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Postal Code*"
                                                      label-for="postalCode" :invalid-feedback="form.errors.get('postal_code')">
                                            <b-form-input id="postalCode"
                                                          type="text"
                                                          v-model="form.postal_code" :state="form.errors.state('postal_code')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Email*"
                                                      label-for="email" :invalid-feedback="form.errors.get('email')">
                                            <b-form-input id="email"
                                                          type="email"
                                                          v-model="form.email" :state="form.errors.state('email')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Phone Number*"
                                                      label-for="phone" :invalid-feedback="form.errors.get('phone_number')"
                                                      description="if with extension add <code>ext. 1234</code>">
                                            <b-form-input id="phone" class="d-none" :state="form.errors.state('phone_number')">
                                            </b-form-input>
                                            <intl-tel-input v-model="form.phone_number"
                                                            :has-error="form.errors.has('phone_number')"
                                                            :extension="form.phone_number_ext"
                                                            @with-extension="setExtensionPhone"/>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Alternate Phone No."
                                                      label-for="altPhone" :invalid-feedback="form.errors.get('alt_phone_number')"
                                                      description="if with extension add <code>ext. 1234</code>">
                                            <b-form-input id="altPhone" class="d-none" :state="form.errors.state('alt_phone_number')">
                                            </b-form-input>
                                            <intl-tel-input v-model="form.alt_phone_number"
                                                            :has-error="form.errors.has('alt_phone_number')"
                                                            :extension="form.alt_phone_number_ext"
                                                            @with-extension="setExtensionAltPhone"/>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                            </b-col>
                            <b-col md="6" lg="6">

                                <b-form-row>
                                    <b-col md="12" lg="4">
                                        <b-form-group :label="'Billing First name' | appendRequired(isOrganization)"
                                                      label-for="bfirstName"
                                                      :invalid-feedback="form.errors.get('billing_first_name')">
                                            <b-form-input id="bfirstName"
                                                          type="text"
                                                          v-model="form.billing_first_name" :state="form.errors.state('billing_first_name')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="12" lg="4">
                                        <b-form-group label="Billing Middle name / initial"
                                                      label-for="bmiddleName" :invalid-feedback="form.errors.get('billing_middle_name')">
                                            <b-form-input id="bmiddleName"
                                                          type="text"
                                                          v-model="form.billing_middle_name" :state="form.errors.state('billing_middle_name')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="12" lg="4">
                                        <b-form-group :label="'Billing Last name' | appendRequired(isOrganization)"
                                                      label-for="blastName" :invalid-feedback="form.errors.get('billing_last_name')">
                                            <b-form-input id="blastName"
                                                          type="text"
                                                          v-model="form.billing_last_name" :state="form.errors.state('billing_last_name')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group :label="'Billing Street Address' | appendRequired(isOrganization)"
                                                      label-for="bstreet" :invalid-feedback="form.errors.get('billing_street_address')">
                                            <b-form-input id="bstreet"
                                                          type="text"
                                                          v-model="form.billing_street_address"
                                                          :state="form.errors.state('billing_street_address')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Billing Street Address 2"
                                                      label-for="bstreet2" :invalid-feedback="form.errors.get('billing_street_address2')">
                                            <b-form-input id="bstreet2"
                                                          type="text"
                                                          v-model="form.billing_street_address2"
                                                          :state="form.errors.state('billing_street_address2')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col cols="6">
                                        <b-form-group :label="'Billing City' | appendRequired(isOrganization)"
                                                      label-for="bcity" :invalid-feedback="form.errors.get('billing_city')">
                                            <b-form-input id="bcity"
                                                          type="text"
                                                          v-model="form.billing_city" :state="form.errors.state('billing_city')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-group :label="'Billing State' | appendRequired(isOrganization)"
                                                      label-for="bstate" :invalid-feedback="form.errors.get('billing_state')">
                                            <b-form-input id="bstate"
                                                          type="text"
                                                          v-model="form.billing_state" :state="form.errors.state('billing_state')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group :label="'Billing Postal Code' | appendRequired(isOrganization)"
                                                      label-for="bpostalCode" :invalid-feedback="form.errors.get('billing_postal_code')">
                                            <b-form-input id="bpostalCode"
                                                          type="text"
                                                          v-model="form.billing_postal_code" :state="form.errors.state('billing_postal_code')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group :label="'Billing Email' | appendRequired(isOrganization)"
                                                      label-for="bemail" :invalid-feedback="form.errors.get('billing_email')">
                                            <b-form-input id="bemail"
                                                          type="email"
                                                          v-model="form.billing_email" :state="form.errors.state('billing_email')" :disabled="!isOrganization">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>

                                <b-form-row>
                                    <b-col>
                                        <b-form-group :label="'Billing Phone' | appendRequired(isOrganization)"
                                                      label-for="bphone"
                                                      :invalid-feedback="form.errors.get('billing_phone_number')"
                                                      description="if with extension add <code>ext. 1234</code>">
                                            <b-form-input id="bphone"
                                                          class="d-none"
                                                          :state="form.errors.state('billing_phone_number')" :disabled="!isOrganization">
                                            </b-form-input>
                                            <intl-tel-input v-model="form.billing_phone_number"
                                                            :has-error="form.errors.has('billing_phone_number')"
                                                            :disabled="!isOrganization"
                                                            :extension="form.billing_phone_number_ext"
                                                            @with-extension="setExtensionBillingPhone"/>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group label="Initial Star Rating"
                                                      label-for="starRating" :invalid-feedback="form.errors.get('initial_star_rating')">
                                            <b-form-input id="starRating"
                                                          v-model="form.initial_star_rating" :state="form.errors.state('initial_star_rating')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                            </b-col>
                        </b-row>
                        <p>* are required fields.</p>
                        <b-button type="submit" variant="primary">
                            <icon v-if="!form.busy" name="save"/>
                            <icon v-else name="spinner" spin/>
                            Save Changes
                        </b-button>
                        <b-btn variant="secondary" class="text-white" @click="goTo('UserClientsList', {id: userId})">
                            <i class="fa fa-caret-left"></i>
                            Back to clients list
                        </b-btn>
                    </b-form>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import * as ClientType from './../../constants/client-type';
    export default {
        data () {
            return {
                editingClient: {},
                form: new Form({
                    first_name: null,
                    middle_name: null,
                    last_name: null,
                    email: null,
                    client_type: null,
                    organization_name: null,
                    phone_number: null,
                    alt_phone_number: null,
                    phone_number_ext: null,
                    alt_phone_number_ext: null,
                    street_address: null,
                    street_address2: null,
                    city: null,
                    state: null,
                    postal_code: null,
                    billing_street_address: null,
                    billing_street_address2: null,
                    billing_city: null,
                    billing_state: null,
                    billing_postal_code: null,
                    billing_phone_number: null,
                    billing_phone_number_ext: null,
                    billing_email: null,
                    inital_star_rating: null,
                }),
                clientTypeOptions: [
                    {value: ClientType.CLIENT_TYPE_ORGANIZATION,  text: 'Organization'},
                    {value: ClientType.CLIENT_TYPE_INDIVIDUAL, text: 'Individual'}
                ]
            };
        },
        mounted () {
            this.getClient();
        },
        computed: {
            isOrganization () {
                return this.form.client_type === ClientType.CLIENT_TYPE_ORGANIZATION;
            },
            clientId () {
                return this.$route.params.clientId;
            },
            userId () {
                return this.$route.params.id;
            },
            cardTitle () {
                let name = this.isOrganization ? this.editingClient.organization_name : this.editingClient.name;
                return 'Editing client: ' + name
            }
        },
        methods: {
            setExtensionPhone (data) {
                this.form.phone_number_ext = data;
            },
            setExtensionAltPhone (data) {
                this.form.alt_phone_number_ext = data;
            },
            setExtensionBillingPhone (data) {
                this.form.billing_phone_number_ext = data;
            },
            submit () {
                App.put(`/api/v1/client/${this.clientId}`, this.form)
                  .then(response => {
                      console.log(response);
                      this.notify('success', 'Successfully updated client');
                  })
                  .catch(error => {
                      console.log(error);
                  });
            },
            getClient () {
                axios.get(`/api/v1/client/${this.clientId}`)
                  .then(response => {
                      this.editingClient = response.data;
                      _.extend(this.form, _.pick(response.data, [
                          'first_name', 'middle_name', 'last_name', 'email', 'client_type', 'organization_name', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext', 'street_address', 'street_address2', 'city', 'state', 'postal_code', 'billing_first_name', 'billing_middle_name', 'billing_last_name', 'billing_email', 'billing_phone_number', 'billing_phone_number_ext', 'billing_email', 'initial_star_rating', 'billing_street_address', 'billing_street_address2', 'billing_city', 'billing_state', 'billing_postal_code',
                      ]));
                  })
            }
        },
        watch: {},
        filters: {}
    };
</script>