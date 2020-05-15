<template>
    <div class="animated fadeIn">
        <b-row class="mb-3">
            <b-col>
                <b-btn variant="secondary" class="text-white mb-3" @click="backBtn">
                    <i class="fa fa-caret-left"></i>
                    Back to {{ backBtnText }} list
                </b-btn>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card :title="reviewsTitle">
                    <div v-if="reviews.data.length" class="table-responsive">
                        <table class="table table-hover table-sm table-striped">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">{{ instance.header }}</th>
                                <th scope="col">Service Date</th>
                                <th scope="col">Star Rating</th>
                                <th scope="col">Payment Rating</th>
                                <th scope="col">Character Rating</th>
                                <th scope="col">Repeat Rating</th>
                                <th scope="col">Comment</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="review in reviews.data">
                                <td>{{ review.id }}</td>
                                <td>{{ getName(review) }}</td>
                                <td>{{ review.service_date | date }}</td>
                                <td>
                                    <rating :rating="review.star_rating" star/>
                                </td>
                                <td>
                                    <rating :rating="review.payment_rating"/>
                                </td>
                                <td>
                                    <rating :rating="review.character_rating"/>
                                </td>
                                <td>
                                    <rating :rating="review.repeat_rating"/>
                                </td>
                                <td><span :title="review.comment">{{ review.comment | truncate(20)}}</span></td>
                                <td>
                                    <b-btn-group size="sm">
                                        <b-btn variant="primary" size="sm" @click="editReview(review)">
                                            <icon name="edit"/>
                                        </b-btn>
                                        <b-btn variant="danger" size="sm" @click="showDeleteReviewModal(review)">
                                            <icon name="trash"/>
                                        </b-btn>
                                    </b-btn-group>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        No Data.
                    </div>
                </b-card>
            </b-col>
        </b-row>
        <b-modal ref="editReviewModal"
                 title="Edit Review"
                 no-close-on-backdrop
                 no-close-on-esc
                 :busy="form.busy"
                 @ok="updateReview">
            <b-form>
                <b-form-group id="inputGroup1"
                              label="Service Date:"
                              label-for="serviceDate">
                    <b-form-input id="serviceDate"
                                  type="date"
                                  v-model="form.service_date">
                    </b-form-input>
                </b-form-group>
                <b-form-group id="inputGroup2"
                              label="Star Rating:"
                              label-for="starRating">
                    <b-form-select id="starRating"
                                   :options="starRatingOptions"
                                   v-model="form.star_rating">
                    </b-form-select>
                </b-form-group>
                <b-form-group id="inputGroup3"
                              label="Payment Rating:"
                              label-for="paymentRating">
                    <b-form-select id="paymentRating"
                                   :options="ratingOptions"
                                   v-model="form.payment_rating">
                    </b-form-select>
                </b-form-group>
                <b-form-group id="inputGroup4"
                              label="Character Rating:"
                              label-for="characterRating">
                    <b-form-select id="characterRating"
                                   :options="ratingOptions"
                                   v-model="form.character_rating">
                    </b-form-select>
                </b-form-group>
                <b-form-group id="inputGroup5"
                              label="Repeat Rating:"
                              label-for="repeatRating">
                    <b-form-select id="repeatRating"
                                   :options="ratingOptions"
                                   v-model="form.repeat_rating">
                    </b-form-select>
                </b-form-group>
                <b-form-group id="inputGroup6"
                              label="Comment:"
                              label-for="comment">
                    <b-form-textarea id="comment"
                                     v-model="form.comment"
                                     :rows="3">
                    </b-form-textarea>
                </b-form-group>
            </b-form>

            <span slot="modal-ok">
                <icon v-if="form.busy" name="spinner" spin/>
                <icon v-else name="save"/>
                Save
            </span>
        </b-modal>
        <b-modal ref="deleteReviewModal"
                 title="Delete review"
                 no-close-on-backdrop
                 no-close-on-esc
                 ok-variant="danger"
                 :busy="deleteForm.busy"
                 @ok="deleteReview">
            Are you sure you want to delete this review?
            <span slot="modal-ok">
                <icon v-if="deleteForm.busy" name="spinner" spin/>
                Delete
            </span>
        </b-modal>
    </div>
</template>

<script>
    import * as ReviewRating from '../../constants/review-rating';
    import * as ClientType from '../../constants/client-type';

    export default {
        data () {
            return {
                user: {},
                client: {},
                reviews: {
                    data: [],
                    meta: {
                        pagination: {

                        }
                    }
                },
                form: new Form({
                    service_date: null,
                    star_rating: 0,
                    payment_rating: null,
                    character_rating: null,
                    repeat_rating: null,
                    comment: null,
                }),
                editingReview: null,
                starRatingOptions: [1, 2, 3, 4, 5],
                ratingOptions: [ReviewRating.RATING_NO_OPINION, ReviewRating.RATING_THUMBS_UP, ReviewRating.RATING_THUMBS_DOWN],
                deleteForm: new Form({
                    id: null,
                }),
                deletingReview: null,
                instance: {
                    header: '',
                },
                backBtnText: ''
            };
        },
        mounted () {
            if ('userId' in this.$route.params) {
                this.getUserReviews();
                this.instance.header = 'Client Name';
                this.backBtnText = 'users';
            }
            if ('clientId' in this.$route.params) {
                this.getClientReviews();
                this.instance.header = 'User Name';
                this.backBtnText = 'clients';
            }
        },
        computed: {
            userId () {
                return this.$route.params.userId;
            },
            clientId () {
                return this.$route.params.clientId;
            },
            reviewsTitle () {
                let title = '';
                if (this.userId) {
                    title = 'Reviews by: ' + this.user.name
                }
                if (this.clientId) {
                    let name = this.client.client_type === ClientType.CLIENT_TYPE_ORGANIZATION ? this.client.organization_name : this.client.name;
                    title = 'Reviews on: ' + name;
                }
                return title;
            }
        },
        methods: {
            backBtn () {
                if (this.clientId) {
                    this.goTo('UserClientsList', {id: this.userId});
                }
                if (this.userId) {
                    this.goTo('UserList');
                }
            },
            getName (review) {
                if ('client' in review) {
                    return review.client.name;
                }
                if ('user' in review) {
                    return review.user.name;
                }
            },
            deleteReview (e) {
                e.preventDefault();
                App.delete(`/api/v1/review/${this.deleteForm.id}`, this.deleteForm)
                  .then(response => {
                      this.arrayDelete(this.user.reviews.data, this.deletingReview);
                      this.$refs.deleteReviewModal.hide();
                      this.notify('success', 'Successfully deleted review');
                      this.deletingReview = null;
                      this.deleteForm.clearFields();
                  });
            },
            showDeleteReviewModal (review) {
                this.deletingReview = review;
                this.deleteForm.id = review.id;
                this.$refs.deleteReviewModal.show();
            },
            updateReview (e) {
                e.preventDefault();
                App.put(`/api/v1/review/${this.editingReview.id}?include=client`, this.form)
                  .then(response => {
                      console.log(response);
                      this.arrayDelete(this.user.reviews.data, this.editingReview, response.data);
                      this.$refs.editReviewModal.hide();
                      this.notify('success', 'Successfully updated review');
                      this.editingReview = null;
                  })
            },
            editReview (review) {
                _.extend(this.form, _.pick(review, ['service_date', 'star_rating', 'payment_rating', 'character_rating', 'repeat_rating', 'comment']));
                this.editingReview = review;
                this.$refs.editReviewModal.show();
            },
            getUserReviews () {
                axios.get(`/api/v1/user/${this.userId}?include=reviews.client`)
                  .then(response => {
                      this.user = response.data;
                      this.reviews = response.data.reviews;
                  });
            },
            getClientReviews () {
                axios.get(`/api/v1/client/${this.clientId}?include=reviews.user`)
                  .then(response => {
                      this.client = response.data;
                      this.reviews = response.data.reviews;
                  });
            }
        },
        watch: {}
    };
</script>