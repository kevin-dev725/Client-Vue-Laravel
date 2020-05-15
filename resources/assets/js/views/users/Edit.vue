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
                                        <b-form-group id="inputGroup13"
                                                      label="Account Type*"
                                                      label-for="acctType" :invalid-feedback="form.errors.get('account_type')">
                                            <b-form-select id="acctType"
                                                           v-model="form.account_type"
                                                           :options="accountTypeOptions" :state="form.errors.state('account_type')">
                                            </b-form-select>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="6">
                                        <b-form-group id="inputGroup14"
                                                      :label="companyNameLabel"
                                                      label-for="companyName" :invalid-feedback="form.errors.get('company_name')">
                                            <!--<b-form-input id="companyName"
                                                          type="text"
                                                          v-model="form.company_name"
                                                          :disabled="!isCompany" :state="form.errors.state('company_name')">
                                            </b-form-input>-->
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
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col md="12" lg="4">
                                        <b-form-group id="inputGroup1"
                                                      label="First name*"
                                                      label-for="firstName"
                                                      :invalid-feedback="form.errors.get('first_name')">
                                            <b-form-input id="firstName"
                                                          type="text"
                                                          v-model="form.first_name" :state="form.errors.state('first_name')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="12" lg="4">
                                        <b-form-group id="inputGroup2"
                                                      label="Middle Name"
                                                      label-for="middleName" :invalid-feedback="form.errors.get('middle_name')">
                                            <b-form-input id="middleName"
                                                          type="text"
                                                          v-model="form.middle_name" :state="form.errors.state('middle_name')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="12" lg="4">
                                        <b-form-group id="inputGroup3"
                                                      label="Last Name*"
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
                                        <b-form-group id="inputGroup18"
                                                      label="Description"
                                                      label-for="desc" :invalid-feedback="form.errors.get('description')">
                                            <b-form-textarea id="desc"
                                                             type="text"
                                                             v-model="form.description" :rows="3"
                                                             :state="form.errors.state('description')">
                                            </b-form-textarea>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group id="inputGroup4"
                                                      label="Street Address*"
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
                                        <b-form-group id="inputGroup5"
                                                      label="Street Address 2"
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
                                        <b-form-group id="inputGroup6"
                                                      label="City*"
                                                      label-for="city" :invalid-feedback="form.errors.get('city')">
                                            <b-form-input id="city"
                                                          type="text"
                                                          v-model="form.city" :state="form.errors.state('city')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-group id="inputGroup7"
                                                      label="State*"
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
                                        <b-form-group id="inputGroup8"
                                                      label="Postal Code*"
                                                      label-for="postalCode" :invalid-feedback="form.errors.get('postal_code')">
                                            <b-form-input id="postalCode"
                                                          type="text"
                                                          v-model="form.postal_code" :state="form.errors.state('postal_code')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                            </b-col>
                            <b-col md="6" lg="6">
                                <b-form-row>
                                    <b-col md="6" lg="6">
                                        <b-form-group id="inputGroup20"
                                                      label="Email*"
                                                      label-for="email" :invalid-feedback="form.errors.get('email')">
                                            <b-form-input id="email"
                                                          type="email"
                                                          v-model="form.email" :state="form.errors.state('email')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col md="6" lg="6">
                                        <b-form-group id="inputGroup21"
                                                      label="Password"
                                                      label-for="password" :invalid-feedback="form.errors.get('password')">
                                            <b-form-input id="password"
                                                          type="password"
                                                          v-model="form.password" :state="form.errors.state('password')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group id="inputGroup9"
                                                      label="Phone Number*"
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
                                        <b-form-group id="inputGroup11"
                                                      label="Alternate Phone No."
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
                                <b-form-row>
                                    <b-col>
                                        <b-form-group id="inputGroup15"
                                                      label="Business Url"
                                                      label-for="busUrl" :invalid-feedback="form.errors.get('business_url')">
                                            <b-form-input id="busUrl"
                                                          type="text"
                                                          v-model="form.business_url" :state="form.errors.state('business_url')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group id="inputGroup16"
                                                      label="Facebook Url"
                                                      label-for="fbUrl" :invalid-feedback="form.errors.get('facebook_url')">
                                            <b-form-input id="fbUrl"
                                                          type="text"
                                                          v-model="form.facebook_url" :state="form.errors.state('facebook_url')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group id="inputGroup17"
                                                      label="Twitter Url"
                                                      label-for="twtUrl" :invalid-feedback="form.errors.get('twitter_url')">
                                            <b-form-input id="twtUrl"
                                                          type="text"
                                                          v-model="form.twitter_url" :state="form.errors.state('twitter_url')">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-form-row>
                                <b-form-row>
                                    <b-col>
                                        <b-form-group id="inputGroup19"
                                                      label="Account Status*"
                                                      label-for="acctStatus" :invalid-feedback="form.errors.get('account_status')">
                                            <b-form-select id="acctStatus"
                                                           v-model="form.account_status"
                                                           :options="accountStatusOptions"
                                                           :state="form.errors.state('account_status')">
                                            </b-form-select>
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
                        <b-btn variant="secondary" class="text-white" @click="goTo('UserList')">
                            <i class="fa fa-caret-left"></i>
                            Back to users list
                        </b-btn>
                    </b-form>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import * as AccountType from './../../constants/user-account-type';
    import * as AccountStatus from './../../constants/user-account-status';
    import MultiSelect from 'vue-multiselect';

    export default {
        components: {
            MultiSelect
        },
        data () {
            return {
                editingUser: {},
                form: new Form({
                    first_name: null,
                    middle_name: null,
                    last_name: null,
                    email: null,
                    password: null,
                    account_type: null,
                    company_name: null,
                    description: null,
                    phone_number: '',
                    alt_phone_number: '',
                    phone_number_ext: null,
                    alt_phone_number_ext: null,
                    street_address: null,
                    street_address2: null,
                    city: null,
                    state: null,
                    postal_code: null,
                    business_url: null,
                    facebook_url: null,
                    twitter_url: null,
                    account_status: null,
                    company_id: null,
                }),
                accountTypeOptions: [
                    {value: AccountType.ACCOUNT_TYPE_INDIVIDUAL, text: 'Individual'},
                    {value: AccountType.ACCOUNT_TYPE_COMPANY, text: 'Company'}
                ],
                accountStatusOptions: [
                    {value: AccountStatus.ACCOUNT_STATUS_ACTIVE, text: 'Active'},
                    {value: AccountStatus.ACCOUNT_STATUS_CANCELLED, text: 'Cancelled'},
                    {value: AccountStatus.ACCOUNT_STATUS_EXPIRED, text: 'Expired'},
                    {value: AccountStatus.ACCOUNT_STATUS_SUSPENDED, text: 'Suspended'}
                ],
                companyMultiSelectOptions: [],
                companyMultiSelectModel: null,
            };
        },
        mounted () {
            this.getUser();
        },
        computed: {
            userId () {
                return this.$route.params.id;
            },
            cardTitle () {
                return `Editing user: ${this.editingUser.name}`;
            },
            isCompany () {
                return this.form.account_type === AccountType.ACCOUNT_TYPE_COMPANY;
            },
            companyNameLabel () {
                let label = 'Company Name';
                if (this.isCompany) {
                    label = label + '*';
                }
                return label;
            }
        },
        methods: {
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
            getCompanies () {
                axios.get('/api/v1/company')
                  .then(response => {
                      this.companyMultiSelectOptions = response.data.data;
                  });
            },
            submit () {
                App.put(`/api/v1/user/${this.userId}`, this.form)
                  .then(response => {
                      console.log(response);
                      this.$emit('update-user', response.data);
                      this.editingUser = response.data;
                      this.notify('success', 'Successfully updated user');
                  })
                  .catch(error => {
                      console.log(error);
                  });
            },
            getUser () {
                axios.get(`/api/v1/user/${this.userId}`)
                  .then(response => {
                      this.editingUser = response.data;
                      _.extend(this.form, _.pick(response.data, [
                          'first_name', 'middle_name', 'last_name', 'email', 'account_type', 'company_id', 'company_name', 'description', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext', 'street_address', 'street_address2', 'city', 'state', 'postal_code', 'business_url', 'facebook_url', 'twitter_url', 'account_status'
                      ]));
                      this.getCompanies();
                  });
            },
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