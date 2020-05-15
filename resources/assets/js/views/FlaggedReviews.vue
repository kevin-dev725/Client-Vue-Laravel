<template>
    <b-card title="Flagged Reviews">
        <div v-if="items.length" class="table-responsive">
            <table class="table table-hover table-sm table-striped">
                <thead>
                <tr>
                    <th scope="col">Date of Review</th>
                    <th scope="col">User Email Address</th>
                    <th scope="col">Flagged Word/Phrase</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in items">
                    <td>{{ item.created_at | date }}</td>
                    <td>{{ item.user.email }}</td>
                    <td>{{ item.flagged_phrase }}</td>
                    <td>
                        <b-btn-group>
                            <b-btn variant="primary" size="sm" @click="viewItem(item)">
                                <icon name="eye"/></b-btn>
                        </b-btn-group>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            No data.
        </div>

        <b-modal ref="modalViewReview"
                 title="View Review"
                 no-close-on-backdrop
                 no-close-on-esc
                 :busy="form.busy">
            <b-form v-if="viewingItem" @submit.prevent="submit">
                <div class="form-group row">
                    <label for="service_date" class="col-sm-4 col-form-label">Review Date:</label>
                    <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" id="service_date" :value="$options.filters.date(viewingItem.created_at)">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="client_name" class="col-sm-4 col-form-label">Client:</label>
                    <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" id="client_name" :value="viewingItem.client.name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="reviewer_name" class="col-sm-4 col-form-label">Reviewer:</label>
                    <div class="col-sm-8">
                        <p class="form-control-plaintext" id="reviewer_name">
                            <a href="#" title="View User Profile" @click.prevent="viewUser(viewingItem.user)">{{ viewingItem.user.name }} ({{ viewingItem.user.email }})</a>
                        </p>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="star_rating" class="col-sm-4 col-form-label">Star Rating:</label>
                    <div class="col-sm-8">
                        <rating id="star_rating" :rating="form.star_rating" star/>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="payment_rating" class="col-sm-4 col-form-label">Payment Rating:</label>
                    <div class="col-sm-8">
                        <rating id="payment_rating" :rating="form.payment_rating"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="character_rating" class="col-sm-4 col-form-label">Character Rating:</label>
                    <div class="col-sm-8">
                        <rating id="character_rating" :rating="form.character_rating"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="repeat_rating" class="col-sm-4 col-form-label">Repeat Rating:</label>
                    <div class="col-sm-8">
                        <rating id="repeat_rating" :rating="form.repeat_rating"/>
                    </div>
                </div>
                <b-form-group id="inputGroup6"
                              label="Comment:"
                              label-for="comment">
                    <b-form-textarea id="comment"
                                     required
                                     v-model="form.comment"
                                     :rows="3">
                    </b-form-textarea>
                </b-form-group>
                <button class="d-none" type="submit" ref="btnSubmit"></button>
            </b-form>
            <template slot="modal-footer">
                <button class="btn btn-primary" :disabled="form.busy || deleteReviewForm.busy" @click="$refs.btnSubmit.click()">
                    <icon v-if="form.busy" name="spinner" spin/><icon v-else name="save"/> Save Changes
                </button>
                <button class="btn btn-danger" :disabled="form.busy || deleteReviewForm.busy" @click="deleteItem(viewingItem)">
                    <icon v-if="deleteReviewForm.busy" name="spinner" spin/><icon v-else name="trash"/> Delete Review
                </button>
            </template>
        </b-modal>
    </b-card>
</template>

<script>
    export default {
        data () {
            return {
                items: [],
                form: new Form ({
                    service_date: null,
                    star_rating: 0,
                    payment_rating: null,
                    character_rating: null,
                    repeat_rating: null,
                    comment: null,
                }),
                deleteReviewForm: new Form(),
                viewingItem: null,
                deletingItem: null,
            };
        },
        mounted () {
            this.getList();
        },
        computed: {

        },
        methods: {
            deleteItem (item) {
                App.delete(`/api/v1/review/${item.id}`, this.deleteReviewForm)
                    .then(() => {
                        this.arrayDelete(this.items, item);
                        this.notify('success', 'Successfully deleted review.');
                        this.$refs.modalViewReview.hide();
                    })
            },
            clearFields () {
                this.form.clearFields();
                this.viewingItem = null;
            },
            viewItem(item) {
                this.viewingItem = item;
                _.extend(this.form, _.pick(item, ['service_date', 'star_rating', 'payment_rating', 'character_rating', 'repeat_rating', 'comment']));
                this.$refs.modalViewReview.show();
            },
            submit () {
                App.put(`/api/v1/review/${this.viewingItem.id}?include=user`, this.form)
                    .then(response => {
                        this.notify('success', 'Successfully updated review.');
                        if (response.data.flagged_phrase) {
                            _.extend(this.viewingItem, _.pick(response.data, ['service_date', 'star_rating', 'payment_rating', 'character_rating', 'repeat_rating', 'comment']));
                        } else {
                            this.arrayDelete(this.items, this.viewingItem);
                        }
                        this.$refs.modalViewReview.hide();
                    });
            },
            getList () {
                axios.get('/web-api/review/flagged?include=user,client')
                    .then(response => {
                        this.items = response.data.data;
                    })
            },
            viewUser(user) {
                window.location = `/dashboard/user/${user.id}`;
            }
        },
        watch: {}
    };
</script>