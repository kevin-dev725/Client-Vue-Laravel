<template>
    <div class="user-profile">
        <div v-if="!editing" class="text-center">
            <div class="avatar-container" ref="imgAvatarContainer">
                <img ref="imgAvatar" :src="avatar" @mouseover="canEdit ? $refs.imgAvatarContainer.classList.add('hover') : null" @mouseleave="canEdit ? $refs.imgAvatarContainer.classList.remove('hover') : null" @click="selectAvatar" class="img-fluid" :class="{'can-edit': canEdit}"/>
                <input type="file" accept="image/jpeg,image/png" class="d-none" ref="inputAvatar" @change="onChangeAvatar">
            </div>
            <h5 class="mt-3" v-text="user.name"></h5>
            <div>
                <span v-if="user.account_type === AccountType.ACCOUNT_TYPE_COMPANY">{{ user.company_name }}<br/></span>
                <span v-if="!isEmpty(user.full_street_address)">{{ user.full_street_address }}<br/></span>
                <span>{{ user.city }} {{ user.state }}<br/></span>
                <span v-if="!isEmpty(user.business_url)"><a :href="user.business_url" target="_blank">{{ user.business_url }}</a><br/></span>
                {{ user.email }}
            </div>
            <ul class="list-inline links mt-2">
                <li class="list-inline-item">
                    <a :href="'tel:' + user.phone_number"><i class="fa fa-2x fa-phone"></i></a>
                </li>
                <li class="list-inline-item">
                    <a :href="'sms:' + user.phone_number"><i class="fa fa-2x fa-commenting"></i></a>
                </li>
                <li class="list-inline-item">
                    <a :href="'mailto:' + user.email"><i class="fa fa-2x fa-envelope"></i></a>
                </li>
                <li class="list-inline-item">
                    <a :href="user.business_url" target="_blank"><i class="fa fa-2x fa-globe"></i></a>
                </li>
                <li class="list-inline-item">
                    <a :href="user.facebook_url" target="_blank"><i class="fa fa-2x fa-facebook-square"></i></a>
                </li>
                <li class="list-inline-item">
                    <a :href="user.twitter_url" target="_blank"><i class="fa fa-2x fa-twitter"></i></a>
                </li>
            </ul>
            <p v-text="user.description"></p>
            <p>
                Submitted {{ user.reviews_submitted }} reviews<br/>
                with an average rating of:<br/>
                <star-rating class="center" :read-only="true" :increment="0.5" :show-rating="false" :item-size="20" :rating="parseFloat(user.reviews_submitted_average)" :title="user.reviews_submitted_average"/>
            </p>
            <p>
                clientDomain member since {{ user.created_at | month_year }}.
            </p>
            <b-btn v-if="canEdit" variant="primary" size="sm" @click="edit"><i class="fa fa-pencil"></i> Edit Profile</b-btn>
        </div>
        <div v-else>
            <b-form @submit.prevent="submit">

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="First name*"
                              label-class="text-md-right"
                              label-for="first_name"
                              :invalid-feedback="form.errors.get('first_name')">
                    <b-form-input id="first_name" v-model="form.first_name" :state="form.errors.state('first_name')"></b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Middle name / initial"
                              label-class="text-md-right"
                              label-for="middle_name"
                              :invalid-feedback="form.errors.get('middle_name')">
                    <b-form-input id="middle_name" v-model="form.middle_name" :state="form.errors.state('middle_name')"></b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Last name*"
                              label-class="text-md-right"
                              label-for="last_name"
                              :invalid-feedback="form.errors.get('last_name')">
                    <b-form-input id="last_name" v-model="form.last_name" :state="form.errors.state('last_name')"></b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Description"
                              label-class="text-md-right"
                              label-for="description"
                              :invalid-feedback="form.errors.get('description')">
                    <b-form-textarea id="description"
                                     v-model="form.description"
                                     :rows="3"
                                     :state="form.errors.state('description')"
                                     :max-rows="6">
                    </b-form-textarea>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Phone Number*"
                              label-class="text-md-right"
                              label-for="phone"
                              description="if with extension add <code>ext. 1234</code>"
                              :invalid-feedback="form.errors.get('phone_number')">
                    <b-form-input class="d-none" :state="form.errors.state('phone_number')">
                    </b-form-input>
                    <intl-tel-input id="phone" v-model="form.phone_number" :has-error="form.errors.has('phone_number')" @with-extension="setExtensionPhone"/>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Alt Phone Number"
                              label-class="text-md-right"
                              label-for="alt_phone"
                              description="if with extension add <code>ext. 1234</code>"
                              :invalid-feedback="form.errors.get('alt_phone_number')">
                    <b-form-input class="d-none" :state="form.errors.state('alt_phone_number')">
                    </b-form-input>
                    <intl-tel-input id="alt_phone" v-model="form.alt_phone_number" :has-error="form.errors.has('alt_phone_number')" @with-extension="setExtensionAltPhone"/>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Street Address*"
                              label-class="text-md-right"
                              label-for="street_address"
                              :invalid-feedback="form.errors.get('street_address')">
                    <b-form-input id="street_address"
                                  type="text"
                                  v-model="form.street_address" :state="form.errors.state('street_address')">
                    </b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Street Address2*"
                              label-class="text-md-right"
                              label-for="street_address2"
                              :invalid-feedback="form.errors.get('street_address2')">
                    <b-form-input id="street_address2"
                                  type="text"
                                  v-model="form.street_address2" :state="form.errors.state('street_address2')">
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
                                  v-model="form.city" :state="form.errors.state('city')">
                    </b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="State*"
                              label-class="text-md-right"
                              label-for="state"
                              :invalid-feedback="form.errors.get('state')">
                    <b-form-input id="state"
                                  type="text"
                                  v-model="form.state" :state="form.errors.state('state')">
                    </b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Email Address*"
                              label-class="text-md-right"
                              label-for="email"
                              :invalid-feedback="form.errors.get('email')">
                    <b-form-input id="email" type="email" v-model="form.email" :state="form.errors.state('email')"></b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Business Website URL"
                              label-class="text-md-right"
                              label-for="business_url"
                              :invalid-feedback="form.errors.get('business_url')">
                    <b-form-input id="business_url" v-model="form.business_url" :state="form.errors.state('business_url')"></b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Facebook URL"
                              label-class="text-md-right"
                              label-for="facebook_url"
                              :invalid-feedback="form.errors.get('facebook_url')">
                    <b-form-input id="facebook_url" v-model="form.facebook_url" :state="form.errors.state('facebook_url')"></b-form-input>
                </b-form-group>

                <b-form-group horizontal
                              label-cols="4"
                              breakpoint="md"
                              label="Twitter URL"
                              label-class="text-md-right"
                              label-for="twitter_url"
                              :invalid-feedback="form.errors.get('twitter_url')">
                    <b-form-input id="twitter_url" v-model="form.twitter_url" :state="form.errors.state('twitter_url')"></b-form-input>
                </b-form-group>
                <div class="float-right">
                    <b-btn :disabled="form.busy" variant="primary" type="submit"><i class="fa fa-save"></i> Save</b-btn>
                    <b-btn :disabled="form.busy" @click="editing = false">Cancel</b-btn>
                </div>
                <div class="clearfix"></div>
            </b-form>
        </div>
    </div>
</template>

<script>
    import * as AccountType from '../../../constants/user-account-type';
    import {StarRating} from 'vue-rate-it';
    import IntlTelInput from './../../../components/Globals/IntlTelInput';
    export default {
        components: {
            StarRating, IntlTelInput
        },
        props: {
            user: {
                type: Object,
                required: true,
            }
        },
        data() {
            return {
                AccountType,
                editing: false,
                form: new window.Form({
                    company_name: '',
                    first_name: '',
                    last_name: '',
                    description: null,
                    phone_number: '',
                    phone_number_ext: '',
                    alt_phone_number: '',
                    alt_phone_number_ext: '',
                    street_address: '',
                    street_address2: '',
                    city: '',
                    state: '',
                    email: '',
                    business_url: null,
                    facebook_url: null,
                    twitter_url: null,
                    country_id: null,
                }),
                formAvatar: new window.Form({
                    avatar: null,
                }),
                avatarImage: null,
            }
        },
        computed: {
            isCompany() {
                return this.user.account_type === AccountType.ACCOUNT_TYPE_COMPANY;
            },
            avatar() {
                if (this.avatarImage) {
                    return this.avatarImage;
                } else if (this.user.avatar_path) {
                    return this.user.avatar_path;
                }
                return '/images/avatar-default.png';
            },
            authUser() {
                return window.Store.user;
            },
            canEdit() {
                return this.user.id === this.authUser.id;
            },
            countries() {
                return window.Store.countrySelectOptions;
            }
        },
        methods: {
            edit() {
                this.form.resetStatus();
                this.editing = true;
                window.$.extend(this.form, window._.pick(this.user, ['first_name', 'last_name', 'middle_name', 'street_address', 'street_address2', 'city', 'state', 'email', 'business_url', 'twitter_url', 'facebook_url', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext', 'description', 'country_id']));
            },
            setExtensionPhone (data) {
                this.form.phone_number_ext = data;
            },
            setExtensionAltPhone (data) {
                this.form.alt_phone_number_ext = data;
            },
            submit() {
                window.App.post(`/api/v1/auth/user/update-profile`, this.form)
                    .then((response) => {
                        this.notify('success', 'Changes has been saved.');
                        window.$.extend(this.user, response.data);
                        this.editing = false;
                    });
            },
            selectAvatar() {
                if (!this.canEdit) {
                    return;
                }
                if (this.formAvatar.busy) {
                    return;
                }
                this.$refs.inputAvatar.click();
            },
            onChangeAvatar(e) {
                window.Utils.getFileImageData(e.target)
                    .then(response => {
                        this.avatarImage = response;
                        this.formAvatar.avatar = e.target.files[0];
                        window.App.postData(`/api/v1/auth/user/avatar`, this.formAvatar)
                            .then(response => {
                                window.$.extend(this.user, response.data);
                                this.formAvatar.avatar = null;
                                this.notify('success', 'Profile picture has been updated.');
                            });
                    });
            }
        }
    }
</script>
