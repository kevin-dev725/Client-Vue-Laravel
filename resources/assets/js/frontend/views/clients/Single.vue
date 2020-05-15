<template>
    <b-card class="animated fadeIn"
            align="center"
            no-body>
        <b-card-body class="client-data">
            <b-row align-h="center">
                <b-col md="6"
                       sm="12">
                    <h3> {{ clientName }}
                        <b-btn variant="primary"
                               size="sm"
                               @click.prevent="showEditClientModal"
                               :disabled="deleting">
                            <i class="fa fa-edit"></i>
                        </b-btn>
                        <b-btn variant="danger"
                               size="sm"
                               @click.prevent="deleteClient"
                               :disabled="deleting">
                            <i class="fa fa-trash"></i>
                        </b-btn>
                    </h3>
                    <rating class="center"
                            :rating="client.avg_rating"
                            star
                            full/>
                    <p class="m-0">
                        <small>Average over {{ client.review_count }} ratings</small>
                    </p>
                    <address class="m-0">
                        <a :href="getAddressMapsLink"
                           target="_blank">
                            {{ client.street_address }} <br>
                            {{ client.city }}, {{ client.state }} {{ client.postal_code }} <br>
                        </a>
                        <a :href="'skype:'+client.phone_number">{{ client.phone_number }}</a>
                    </address>
                    <p class="m-0">
                        <a :href="'mailto:'+client.email">{{ client.email }}</a>
                    </p>
                    <b-btn variant="primary"
                           block
                           class="mt-3"
                           @click.prevent="showReviewClientModal()">
                        <icon name="pencil"/>
                        Write a new review
                    </b-btn>
                </b-col>
            </b-row>
        </b-card-body>
        <b-list-group flush
                      class="client-reviews">
            <b-list-group-item v-for="(review, i) in client.reviews.data"
                               :key="review.id">
                <div class="client-reviews-data clearfix">
                    <div class="float-left text-left">
                        <span class="d-block">{{ review.service_date | date_format }}</span>
                        <rating class="d-block"
                                :rating="review.star_rating"
                                star
                                full/>
                        <span class="d-block">By
                            <span v-if="review.user_id === user.id"
                                  class="review-user">
                               You
                            </span>
                            <router-link :to="{name: 'view-user', params: {id: review.user.id}}"
                                         v-else
                                         class="review-user">
                                {{ review.user | format_review_name }} <i class="fa fa-eye"></i>
                            </router-link>
                            <b-btn variant="primary"
                                   size="sm"
                                   @click.prevent="showReviewClientModal(review)">
                                <i v-if="review.user_id === user.id"
                                   class="fa fa-edit"></i>
                                <i v-else
                                   class="fa fa-eye"></i>
                            </b-btn>
                        </span>
                    </div>
                    <div class="float-right text-right">
                        <div class="client-rating">
                            Payment
                            <rating :rating="review.payment_rating"/>
                        </div>
                        <div class="client-rating">
                            Character
                            <rating :rating="review.character_rating"/>
                        </div>
                        <div class="client-rating">
                            Repeat
                            <rating :rating="review.repeat_rating"/>
                        </div>
                    </div>
                </div>

                <div class="client-reviews-comment">
                    <p v-text="review.comment"
                       class="mt-3 text-left"></p>
                </div>
            </b-list-group-item>
        </b-list-group>

        <b-modal ref="editClientModal"
                 no-close-on-backdrop
                 no-close-on-esc
                 :title="editClientModalTitle"
                 @ok="saveClient">
            <b-form>
                <b-form-group label="Organization name:"
                              label-for="orgName"
                              class="text-left"
                              :invalid-feedback="form.errors.get('organization_name')"
                              v-if="isClientTypeOrganization">
                    <b-form-input id="orgName"
                                  type="text"
                                  :state="form.errors.state('organization_name')"
                                  v-model="form.organization_name">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="First name:"
                              label-for="first_name"
                              :invalid-feedback="form.errors.get('first_name')"
                              class="text-left">
                    <b-form-input id="first_name"
                                  type="text"
                                  :state="form.errors.state('first_name')"
                                  v-model="form.first_name">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="Middle name:"
                              label-for="middle_name"
                              :invalid-feedback="form.errors.get('middle_name')"
                              class="text-left">
                    <b-form-input id="middle_name"
                                  type="text"
                                  :state="form.errors.state('middle_name')"
                                  v-model="form.middle_name">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="Last name:"
                              label-for="last_name"
                              :invalid-feedback="form.errors.get('last_name')"
                              class="text-left">
                    <b-form-input id="last_name"
                                  type="text"
                                  :state="form.errors.state('last_name')"
                                  v-model="form.last_name">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="Phone Number"
                              label-for="phone"
                              :invalid-feedback="form.errors.get('phone_number')"
                              description="if with extension add <code>ext. 1234</code>"
                              class="text-left">
                    <b-form-input id="phone"
                                  class="d-none"
                                  :state="form.errors.state('phone_number')">
                    </b-form-input>
                    <intl-tel-input v-model="form.phone_number"
                                    :has-error="form.errors.has('phone_number')"
                                    :extension="form.phone_number_ext"
                                    @with-extension="setExtensionPhone"/>
                </b-form-group>
                <b-form-group label="Street Address"
                              label-for="street"
                              class="text-left"
                              :invalid-feedback="form.errors.get('street_address')">
                    <b-form-input id="street"
                                  type="text"
                                  v-model="form.street_address"
                                  :state="form.errors.state('street_address')">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="City"
                              label-for="city"
                              :invalid-feedback="form.errors.get('city')"
                              class="text-left">
                    <b-form-input id="city"
                                  type="text"
                                  v-model="form.city"
                                  :state="form.errors.state('city')">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="State"
                              label-for="state"
                              class="text-left"
                              :invalid-feedback="form.errors.get('state')">
                    <b-form-select id="state"
                                   :options="states"
                                   v-model="form.state"
                                   :state="form.errors.state('state')">
                    </b-form-select>
                </b-form-group>
                <b-form-group label="Postal Code"
                              class="text-left"
                              label-for="postalCode"
                              :invalid-feedback="form.errors.get('postal_code')">
                    <b-form-input id="postalCode"
                                  type="text"
                                  v-model="form.postal_code"
                                  :state="form.errors.state('postal_code')">
                    </b-form-input>
                </b-form-group>
            </b-form>
            <span slot="modal-ok">
                <icon v-if="form.busy"
                      name="spinner"
                      spin/>
                <icon v-else
                      name="save"/>
                Save
            </span>
        </b-modal>

        <b-modal ref="reviewClientModal"
                 no-close-on-backdrop
                 no-close-on-sec
                 :title="reviewClientModalTitle"
                 :hide-footer="!canEditReview"
                 :busy="reviewForm.busy"
                 @ok="saveReviewClient">
            <b-form>
                <b-form-group label="Service Date:"
                              label-for="serviceDate"
                              class="text-left"
                              :invalid-feedback="reviewForm.errors.get('service_date')">
                    <b-form-input id="serviceDate"
                                  type="date"
                                  :state="reviewForm.errors.state('service_date')"
                                  :readonly="!canEditReview"
                                  v-model="reviewForm.service_date">
                    </b-form-input>
                </b-form-group>
                <b-form-group label="Star Rating:"
                              class="text-left"
                              :invalid-feedback="reviewForm.errors.get('star_rating')"
                              label-for="starRating">
                    <rating-input :read-only="!canEditReview"
                                  v-model="reviewForm.star_rating"/>
                </b-form-group>
                <b-form-group label="Payment Rating:"
                              class="text-left"
                              :invalid-feedback="reviewForm.errors.get('payment_rating')"
                              label-for="paymentRating">
                    <thumb-input :readonly="!canEditReview"
                                 v-model="reviewForm.payment_rating"/>
                </b-form-group>
                <b-form-group label="Character Rating:"
                              class="text-left"
                              :invalid-feedback="reviewForm.errors.get('character_rating')"
                              label-for="characterRating">
                    <thumb-input :readonly="!canEditReview"
                                 v-model="reviewForm.character_rating"/>
                </b-form-group>
                <b-form-group label="Repeat Rating:"
                              class="text-left"
                              :invalid-feedback="reviewForm.errors.get('repeat_rating')"
                              label-for="repeatRating">
                    <thumb-input :readonly="!canEditReview"
                                 v-model="reviewForm.repeat_rating"/>
                </b-form-group>
                <b-form-group label="Comment:"
                              class="text-left"
                              :invalid-feedback="reviewForm.errors.get('comment')"
                              label-for="comment">
                    <b-form-textarea id="comment"
                                     v-model="reviewForm.comment"
                                     :state="reviewForm.errors.state('comment')"
                                     :readonly="!canEditReview"
                                     :rows="3">
                    </b-form-textarea>
                </b-form-group>
            </b-form>
            <span slot="modal-ok">
                <icon v-if="reviewForm.busy"
                      name="spinner"
                      spin/>
                <icon v-else
                      name="save"/>
                Save
            </span>
        </b-modal>
        <b-modal ref="modalDelete"
                 title="Confirmation">
            <div class="text-left">Are you sure you want to delete client?</div>
            <div slot="modal-footer">
                <b-btn @click="$refs.modalDelete.hide()"
                       :disabled="deleting">Cancel
                </b-btn>
                <b-btn variant="danger"
                       @click="confirmDelete"
                       :disabled="deleting"><i class="fa"
                                               :class="{'fa-trash': !deleting, 'fa-spinner fa-spin': deleting}"></i>
                    Delete Client
                </b-btn>
            </div>
        </b-modal>
    </b-card>
</template>

<script>
    import * as ClientType from './../../../constants/client-type';
    import * as ReviewRating from './../../../constants/review-rating';
    import { StarRating } from 'vue-rate-it';
    import ThumbInput from '../../../components/Globals/ThumbsUpDownInput';
    import RatingInput from '../../../components/Globals/RatingInput';

    function getData (id) {
        window.Store.startLoading();
        return new Promise((resolve, reject) => {
            window.axios.get(`/api/v1/client/${id}?include=reviews.user`)
              .then(response => {
                  window.Store.finishLoading();
                  resolve(response);
              })
              .catch(errors => {
                  window.Store.failLoading();
                  reject(errors);
              });
        });
    }

    export default {
        beforeRouteEnter (to, from, next) {
            getData(to.params.id)
              .then(response => {
                  next(vm => {
                      vm.client = response.data;
                  });
              });
        },
        components: {
            StarRating, ThumbInput, RatingInput
        },
        data () {
            return {
                client: {
                    avg_rating: 0,
                    reviews: {
                        data: []
                    }
                },
                form: new Form({
                    organization_name: '',
                    first_name: '',
                    last_name: '',
                    middle_name: '',
                    phone_number: '',
                    phone_number_ext: '',
                    street_address: '',
                    city: '',
                    state: '',
                    postal_code: '',
                }),
                editingReview: null,
                reviewForm: new Form({
                    id: null,
                    service_date: null,
                    star_rating: 1,
                    payment_rating: ReviewRating.RATING_NO_OPINION,
                    character_rating: ReviewRating.RATING_NO_OPINION,
                    repeat_rating: ReviewRating.RATING_NO_OPINION,
                    comment: null,
                }),
                ratingOptions: [ReviewRating.RATING_NO_OPINION, ReviewRating.RATING_THUMBS_UP, ReviewRating.RATING_THUMBS_DOWN],
                starRatingOptions: [1, 2, 3, 4, 5],
                deleting: false,
            };
        },
        mounted () {
            this.getClient();
        },
        computed: {
            states () {
                return window.Store.states;
            },
            reviewClientModalTitle () {
                if (this.editingReview) {
                    if (this.canEditReview) {
                        return 'Editing Review ID: ' + this.editingReview.id;
                    }
                    return 'Review by ' + this.$options.filters.format_review_name(this.editingReview.user);
                }
                return 'Write a new review';
            },
            canEditReview () {
                if (!this.editingReview) {
                    return true;
                }
                return this.editingReview.user_id === this.user.id;
            },
            isClientTypeOrganization () {
                return this.client.client_type === ClientType.CLIENT_TYPE_ORGANIZATION;
            },
            editClientModalTitle () {
                return 'Editing Client ID: ' + this.client.id;
            },
            clientId () {
                return this.$route.params.id;
            },
            user () {
                return window.Store.user;
            },
            clientName () {
                let client = this.client;
                if (!client) return '';
                return client.client_type === ClientType.CLIENT_TYPE_ORGANIZATION ? client.organization_name : client.name;
            },
            getAddressMapsLink () {
                return `https://maps.google.com/?q=${Vue.options.filters.full_address(this.client)}`;
            }
        },
        methods: {
            showReviewClientModal (review = null) {
                this.editingReview = null;
                this.reviewForm.clearFields();
                this.reviewForm.resetStatus();
                if (review) {
                    this.editingReview = review;
                    window._.extend(this.reviewForm, window._.pick(review, ['id', 'service_date', 'star_rating', 'payment_rating', 'character_rating', 'repeat_rating', 'comment']));
                }
                this.$refs.reviewClientModal.show();
            },
            saveReviewClient (e) {
                if (this.reviewForm.busy) {
                    return;
                }
                e.preventDefault();
                if (this.editingReview) {
                    window.App.put(`/api/v1/review/${this.reviewForm.id}?include=user`, this.reviewForm)
                      .then(response => {
                          this.getClient();
                          e.vueTarget.hide();
                      });
                } else {
                    window.App.post(`/api/v1/client/${this.clientId}/review`, this.reviewForm)
                      .then(response => {
                          this.getClient();
                          e.vueTarget.hide();
                      });
                }
            },
            saveClient (e) {
                e.preventDefault();
                window.App.put(`/api/v1/client/${this.clientId}?basic_info=true&include=reviews.user`, this.form)
                  .then(response => {
                      this.client = response.data;
                      e.vueTarget.hide();
                  });
            },
            setExtensionPhone (data) {
                this.form.phone_number_ext = data;
            },
            showEditClientModal () {
                window._.extend(this.form, window._.pick(this.client, ['first_name', 'last_name', 'middle_name', 'phone_number', 'phone_number_ext', 'street_address', 'city', 'state', 'postal_code', 'organization_name']));
                this.$refs.editClientModal.show();
            },
            getClient () {
                window.axios.get(`/api/v1/client/${this.clientId}?include=reviews.user`)
                  .then(response => {
                      this.client = response.data;
                  });
            },
            deleteClient () {
                this.$refs.modalDelete.show();
            },
            confirmDelete () {
                this.deleting = true;
                window.axios.delete(`/api/v1/client/${this.clientId}`)
                  .then(() => {
                      this.notify('success', 'Client has been deleted.');
                      this.$refs.modalDelete.hide();
                      this.$router.go(-1);
                  });
            }
        },
        watch: {}
    };
</script>
