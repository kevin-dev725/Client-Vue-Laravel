<template>
    <div>
        <b-form-row>
            <b-col>
                <b-form-group :label="'Billing First name' | appendRequired(isOrganization)"
                              label-for="bfirstName"
                              :invalid-feedback="form.errors.get('billing_first_name')">
                    <b-form-input id="bfirstName"
                                  type="text"
                                  v-model="form.billing_first_name" :state="form.errors.state('billing_first_name')" :disabled="!isOrganization">
                    </b-form-input>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
                <b-form-group label="Billing Middle name / initial"
                              label-for="bmiddleName" :invalid-feedback="form.errors.get('billing_middle_name')">
                    <b-form-input id="bmiddleName"
                                  type="text"
                                  v-model="form.billing_middle_name" :state="form.errors.state('billing_middle_name')" :disabled="!isOrganization">
                    </b-form-input>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
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
                <b-form-group :label="'Billing Zip Code' | appendRequired(isOrganization)"
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
    </div>
</template>

<script>
    import * as ClientType from './../../../../constants/client-type';
    export default {
        props: {
            form: {
                type: Object,
                required: true,
            }
        },
        computed: {
            isOrganization () {
                return this.form.client_type === ClientType.CLIENT_TYPE_ORGANIZATION;
            }
        },
        methods: {
            setExtensionBillingPhone (data) {
                this.form.billing_phone_number_ext = data;
            },
        }
    }
</script>

<style scoped>

</style>
