<template>
    <div>
        <b-card class="animated fadeIn" align="center">
            <h4 slot="header">
                Search <br>
            </h4>
            <p>How would you like to search for a client? </p>
            <b-row align-h="center">
                <b-col md="12" lg="4">
                    <b-btn variant="primary" :to="{name: 'dashboard-clients-search', query: { search_by: searchBy.SEARCH_BY_PHONE }}" exact block class="mb-3"><i class="fa fa-phone"></i> By Phone Number</b-btn>
                </b-col>
                <b-col md="12" lg="4">
                    <b-btn variant="primary" :to="{name: 'dashboard-clients-search', query: { search_by: searchBy.SEARCH_BY_EMAIL }}" exact block class="mb-3"><i class="fa fa-at"></i> By Email Address</b-btn>
                </b-col>
                <b-col md="12" lg="4">
                    <b-btn variant="primary" :to="{name: 'dashboard-clients-search', query: { search_by: searchBy.SEARCH_BY_NAME }}"  exact block class="mb-3"><i class="fa fa-address-card"></i> By Name and Address</b-btn>
                </b-col>
            </b-row>
            <b-row align-h="center">
                <b-col sm="8">
                    <b-form v-if="routeSearchBy" @submit.prevent="search">
                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_PHONE"
                                      label="Enter the client's phone number:"
                                      label-for="phone"
                                      :invalid-feedback="form.errors.get('phone_number')"
                                      description="if with extension add <code>ext. 1234</code>">
                            <b-form-input id="phone"
                                          class="d-none"
                                          :state="form.errors.state('phone_number')">
                            </b-form-input>
                            <intl-tel-input v-model="form.phone_number"
                                            :has-error="form.errors.has('phone_number')"
                                            :extension="form.phone_number_ext"
                                            @with-extension="setExtensionPhone"/>
                        </b-form-group>

                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_EMAIL"
                                      label="Enter the client's email address:"
                                      label-for="email"
                                      :invalid-feedback="form.errors.get('email')">
                            <b-form-input id="email"
                                          type="email"
                                          v-model="form.email"
                                          :state="form.errors.state('email')">
                            </b-form-input>
                        </b-form-group>

                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_NAME"
                                      label="Enter the client's details:" >
                        </b-form-group>

                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_NAME"
                                      label="Last name"
                                      label-for="last_name"
                                      :invalid-feedback="form.errors.get('last_name')">
                            <b-form-input id="last_name"
                                          type="text"
                                          v-model="form.last_name"
                                          :state="form.errors.state('last_name')">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_NAME"
                                      label="First name"
                                      label-for="first_name"
                                      :invalid-feedback="form.errors.get('first_name')">
                            <b-form-input id="first_name"
                                          type="text"
                                          v-model="form.first_name"
                                          :state="form.errors.state('first_name')">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_NAME"
                                      label="City"
                                      label-for="city"
                                      :invalid-feedback="form.errors.get('city')">
                            <b-form-input id="city"
                                          type="text"
                                          v-model="form.city"
                                          :state="form.errors.state('city')">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_NAME"
                                      label="State"
                                      label-for="state"
                                      :invalid-feedback="form.errors.get('state')">
                            <b-form-input id="state"
                                          type="text"
                                          v-model="form.state"
                                          :state="form.errors.state('state')">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group v-if="routeSearchBy === searchBy.SEARCH_BY_NAME"
                                      label="Street address"
                                      label-for="street_address"
                                      :invalid-feedback="form.errors.get('street_address')">
                            <b-form-input id="street_address"
                                          type="text"
                                          v-model="form.street_address"
                                          :state="form.errors.state('street_address')">
                            </b-form-input>
                        </b-form-group>

                        <b-btn type="submit" variant="primary" block :disabled="form.busy">
                            <icon v-if="form.busy" name="spinner" spin/>
                            <icon v-else name="search"/>
                            Search</b-btn>
                    </b-form>
                </b-col>
            </b-row>
        </b-card>

        <b-card class="mt-3 mb-3" align="center" id="results" v-if="searched" no-body>
            <h4 slot="header">
                Search results for <br>
                <small>{{ searchedModel }}</small> <br>
                <small>{{ searchedModel2 }}</small>
            </h4>
            <b-list-group flush class="client-reviews">
                <b-list-group-item v-if="!results.data.length">
                    <h5>No search results</h5>
                </b-list-group-item>
                <b-list-group-item v-for="(review, i) in results.data" :key="review.id">
                    <div class="client-data">
                        <h4 class="text-left">{{ formatReviewClientName(review.client) }}</h4>
                    </div>
                    <div class="client-reviews-data clearfix">
                        <div class="float-left text-left">
                            <span class="d-block">{{ review.service_date | date_format }}</span>
                            <rating class="d-block" :rating="review.star_rating" star full/>
                            <span class="d-block">By
                            <span v-if="review.user_id === user.id" class="review-user">
                               You
                            </span>
                            <router-link :to="{name: 'view-user', params: {id: review.user.id}}" v-else class="review-user">
                                {{ review.user | format_review_name }} <i class="fa fa-eye"></i>
                            </router-link>
                        </span>
                        </div>
                        <div class="float-right text-right">
                            <div class="client-rating">
                                Payment <rating :rating="review.payment_rating"/>
                            </div>
                            <div class="client-rating">
                                Character <rating :rating="review.character_rating"/>
                            </div>
                            <div class="client-rating">
                                Repeat <rating :rating="review.repeat_rating"/>
                            </div>
                        </div>
                    </div>
                    <div class="client-reviews-comment">
                        <p v-text="review.comment" class="mt-3 text-left"></p>
                    </div>
                </b-list-group-item>
            </b-list-group>
        </b-card>
    </div>
</template>

<script>
    import * as SearchBy from './../../../constants/review-search-by';
    import * as ClientType from './../../../constants/client-type';
    export default {
        data () {
            return {
                form: new Form ({
                    search_by: null,
                    email: this.$route.query.email,
                    first_name: this.$route.query.first_name,
                    last_name: this.$route.query.last_name,
                    city: this.$route.query.city,
                    state: this.$route.query.state,
                    street_address: this.$route.query.street_address,
                    phone_number: this.$route.query.phone_number,
                    phone_number_ext: this.$route.query.phone_number_ext,
                }),
                results: {
                    data: [],
                    meta: {
                        pagination: {

                        }
                    }
                },
                searched: false,
                searchedModel: '',
                searchedModel2: '',
            };
        },
        mounted () {
            if (!this.routeSearchBy) {
                this.$router.push({name: 'dashboard-clients-search', query: { search_by: this.searchBy.SEARCH_BY_PHONE } });
            } else {
                this.search();
            }
        },
        computed: {
            user () {
                return window.Store.user;
            },
            routeSearchBy () {
                return this.$route.query.search_by;
            },
            searchBy () {
                return SearchBy;
            },
            query () {
                let query = {
                    search_by: this.routeSearchBy
                };
                switch (this.routeSearchBy) {
                    case SearchBy.SEARCH_BY_PHONE:
                        query.phone_number = this.form.phone_number;
                        query.phone_number_ext = this.form.phone_number_ext;
                        break;
                    case SearchBy.SEARCH_BY_EMAIL:
                        query.email = this.form.email;
                        break;
                    case SearchBy.SEARCH_BY_NAME:
                        query.first_name = this.form.first_name;
                        query.last_name = this.form.last_name;
                        query.city = this.form.city;
                        query.state = this.form.state;
                        query.street_address = this.form.street_address;
                        break;
                }

                return query;
            }
        },
        methods: {
            setSearchedModel () {
                switch (this.routeSearchBy) {
                    case SearchBy.SEARCH_BY_PHONE:
                        let ext = ' ext. ' + this.query.phone_number_ext;
                        this.searchedModel = this.query.phone_number;
                        this.searchedModel2 = this.query.phone_number_ext ? ext : '';
                        break;
                    case SearchBy.SEARCH_BY_EMAIL:
                        this.searchedModel = this.query.email;
                        break;
                    case SearchBy.SEARCH_BY_NAME:
                        let city = this.query.city ? this.query.city : '';
                        let state = this.query.state ? this.query.state : '';

                        this.searchedModel = this.query.first_name + ' ' + this.query.last_name + ' ';
                        this.searchedModel2 = city + ' ' + state;
                        break;
                }
            },
            formatReviewClientName (client) {
                let isClientOrganization = client.client_type === ClientType.CLIENT_TYPE_ORGANIZATION;
                let name = isClientOrganization ? client.organization_name : client.name;
                let address = client.city + ' ' + client.state;
                return name + ', ' + address;
            },
            setExtensionPhone (data) {
                this.form.phone_number_ext = data;
            },
            search () {
                this.searched = false;
                this.form.startProcessing();
                window.axios.get(`/api/v1/review?include=client,user`, {
                    params: this.query
                })
                  .then(response => {
                      console.log(response);
                      this.searched = true;
                      this.setSearchedModel();
                      this.results = response.data;
                      this.$nextTick( () => {
                          this.$scrollTo('#results', 1000, { offset: -15 });
                      });
                  })
                  .catch(error => {
                      this.searched = true;
                      this.$nextTick( () => {
                          this.$scrollTo('#results', 1000, { offset: -15 });
                      });
                  })
                    .finally(() => {
                        this.$router.push({query: this.query});
                        this.form.finishProcessing();
                    });
            }
        },
        watch: {}
    };
</script>