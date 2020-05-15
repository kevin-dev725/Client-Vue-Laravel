<template>
    <div>
        <b-form-row>
            <b-col md="12">
                <b-form-group label="Account Type*"
                              label-for="clientType" :invalid-feedback="form.errors.get('client_type')">
                    <b-form-select id="clientType"
                                   v-model="form.client_type"
                                   :options="clientTypeOptions" :state="form.errors.state('client_type')">
                    </b-form-select>
                </b-form-group>
            </b-col>
            <b-col md="12" v-show="isOrganization">
                <b-form-group :label="'Organization Name' | appendRequired(isOrganization)"
                              label-for="organizationName" :invalid-feedback="form.errors.get('organization_name')">
                    <b-form-input id="organizationName"
                                  type="text"
                                  class="d-none"
                                  v-model="form.first_name" :state="form.errors.state('organization_name')">
                    </b-form-input>
                    <type-ahead :is-invalid="form.errors.has('organization_name')" src="/api/v1/client/organizations?keyword=:keyword"
                                :getResponse="response => response.data" :fetch="fetchOrganizations" v-model="form.organization_name" />
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
                <b-form-group label="First name*"
                              label-for="firstName"
                              :invalid-feedback="form.errors.get('first_name')">
                    <b-form-input id="firstName"
                                  type="text"
                                  v-model="form.first_name" :state="form.errors.state('first_name')">
                    </b-form-input>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
                <b-form-group label="Middle name / initial"
                              label-for="middleName"
                              :invalid-feedback="form.errors.get('middle_name')">
                    <b-form-input id="middleName"
                                  type="text"
                                  v-model="form.middle_name" :state="form.errors.state('middle_name')">
                    </b-form-input>
                </b-form-group>
            </b-col>
        </b-form-row>
        <b-form-row>
            <b-col>
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
    </div>
</template>

<script>
    import * as ClientType from './../../../../constants/client-type';
    import TypeAhead from '../../../../components/Globals/TypeAhead';
    export default {
        components: {
            TypeAhead,
        },
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
        data() {
            return {
                clientTypeOptions: [
                    {value: ClientType.CLIENT_TYPE_ORGANIZATION,  text: 'Organization'},
                    {value: ClientType.CLIENT_TYPE_INDIVIDUAL, text: 'Individual'}
                ],
                initialStarRatingOptions: [
                    '1', '2', '3', '4', '5'
                ],
                organizations: [],
            }
        },
        methods: {
            fetchOrganizations: function (url) {
                return window.axios.get(url)
            },
            setExtensionPhone (data) {
                this.form.phone_number_ext = data;
            },
            setExtensionAltPhone (data) {
                this.form.alt_phone_number_ext = data;
            },
        }
    }
</script>

<style scoped>

</style>
