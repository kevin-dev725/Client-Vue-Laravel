<template>
    <div class="animated fadeIn">
        <b-row class="mb-3">
            <b-col>
                <search ref="search"
                        :page="page"
                        @results="setResultsData"
                        @reset="reset"
                        url="/api/v1/user"/>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <!--<router-view :users="users" @change-page="changePage" @editing-user="setEditingUser" @update-user="updateUser"/>-->
                <b-card title="Users">
                    <div v-if="users.data.length" class="table-responsive">
                        <table class="table table-hover table-sm table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Account Type</th>
                                <th scope="col">Email</th>
                                <th scope="col">Registered</th>
                                <th scope="col">Status</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users.data">
                                <td class="text-capitalize" v-text="user.name"></td>
                                <td class="text-capitalize" v-text="user.account_type"></td>
                                <td v-text="user.email"></td>
                                <td>{{ user.created_at | date }}</td>
                                <td class="text-capitalize" v-text="user.account_status"></td>
                                <td>
                                    <b-btn-group size="sm">
                                        <b-btn variant="primary" title="View" @click.prevent="showViewModal(user)">
                                            <icon name="eye"/>
                                        </b-btn>
                                        <b-btn variant="primary" title="Edit" @click.prevent="editingUser(user)" >
                                            <icon name="edit"/>
                                        </b-btn>
                                        <b-btn variant="primary" title="Clients"
                                               :to="{name: 'UserClientsList', params: {id: user.id}}">
                                            <icon name="users"/>
                                        </b-btn>
                                        <b-btn variant="primary" title="Reviews"
                                               :to="{name: 'UserReviewsList', params: {userId: user.id}}">
                                            <icon name="th-list"/>
                                        </b-btn>
                                        <b-btn variant="primary" title="Email" @click="showEmailModal(user)">
                                            <icon name="envelope"/>
                                        </b-btn>
                                    </b-btn-group>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        No data.
                    </div>
                    <b-pagination v-if="users.meta.pagination.total_pages > 1"
                                  class="justify-content-center"
                                  size="md"
                                  :total-rows="users.meta.pagination.total"
                                  v-model="users.meta.pagination.current_page"
                                  :per-page="users.meta.pagination.per_page"
                                  @input="changePage"
                    ></b-pagination>
                </b-card>
            </b-col>
        </b-row>
        <b-modal ref="emailModal"
                 :title="emailModalTitle"
                 no-close-on-backdrop
                 no-close-on-esc
                 centered
                 @ok="sendEmail">
            <b-form>
                <b-form-group id="inputGroup1"
                              label="Subject:"
                              label-for="subject">
                    <b-form-input id="subject"
                                  type="text"
                                  v-model="emailForm.subject">
                    </b-form-input>
                </b-form-group>
                <b-form-group id="inputGroup2"
                              label="Message:"
                              label-for="message">
                    <b-form-textarea id="message"
                                     v-model="emailForm.message"
                                     :rows="3">
                    </b-form-textarea>
                </b-form-group>
            </b-form>
            <span slot="modal-ok">
                <icon v-if="emailForm.busy" name="spinner" spin/>
                <icon v-else name="paper-plane"/>
                    Send
            </span>
        </b-modal>
        <b-modal ref="viewModal"
                 :title="viewModalTitle"
                 cancel-title="Close"
                 @ok="editingUser(viewUser)">
            <b-form>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Account Type:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewUser.account_type"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Company Name:"
                              class="mb-0"
                              label-class="font-weight-bold"
                              v-if="viewUser.account_type === 'company'">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewUser.company_name"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Name:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewUser.name"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Description:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 " v-text="viewUser.description" :rows="3"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Email:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewUser.email"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Address:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewUserAddress" :rows="3"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Phone No:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewUserPhone"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Alt Phone No:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewUserAltPhone"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Business Url:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-truncate"><a :href="viewUser.business_url" v-text="viewUser.business_url" target="_blank"></a></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Facebook Url:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-truncate"><a :href="viewUser.facebook_url" v-text="viewUser.facebook_url"></a></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Twitter Url:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-truncate"><a :href="viewUser.twitter_url" v-text="viewUser.twitter_url"></a></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Account Status:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-uppercase" v-text="viewUser.account_status"></p>
                </b-form-group>
            </b-form>
            <span slot="modal-ok">
                <icon name="edit"/>
                    Edit
            </span>
        </b-modal>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                emailForm: new Form({
                    subject: '',
                    message: ''
                }),
                emailUser: null,
                viewUser: {},
                users: {
                    data: [],
                    meta: {
                        pagination: {},
                    }
                },
                page: 1,
                usedSearch: false,
            };
        },
        mounted () {
            this.getUsers();
        },
        computed: {
            query () {
                return 'page=' + this.page;
            },
            emailModalTitle () {
                if (!this.emailUser) return;
                return 'Email: ' + this.emailUser.email;
            },
            viewModalTitle () {
                if (!this.viewUser) return;
                return 'Viewing User: ' + this.viewUser.email;
            },
            viewUserAddress () {
                let user = this.viewUser;
                if (!user.street_address) {
                    return null;
                }
                return user.street_address + ', ' + user.city + ', ' + user.state + ' ' + user.postal_code;
            },
            viewUserPhone () {
                let user = this.viewUser;
                if (!user.phone_number_ext) {
                    return user.phone_number;
                }
                return user.phone_number + ',' + user.phone_number_ext;
            },
            viewUserAltPhone () {
                let user = this.viewUser;
                if (!user.alt_phone_number_ext) {
                    return user.alt_phone_number;
                }
                return user.alt_phone_number + ',' + user.alt_phone_number_ext;
            }
        },
        methods: {
            reset () {
                this.usedSearch = false;
                this.getUsers();
            },
            updateUser (user) {
                this.arrayDelete(this.users.data, this.editingUser, user);
                this.editingUser = null;
            },
            setEditingUser (user) {
                this.editingUser = user;
            },
            changePage (page) {
                this.page = page;
                this.getUsers();
            },
            setResultsData (data) {
                this.usedSearch = true;
                this.users = data;
                this.goTo('UserList');
            },
            getUsers () {
                if (this.usedSearch) {
                    return; //If used search bar then we use the search request on search component
                }
                axios.get(`/api/v1/user?${this.query}`)
                  .then(response => {
                      this.users.data = response.data.data;
                      this.users.meta.pagination = response.data.meta.pagination;
                  });
            },
            showViewModal (user) {
                this.viewUser = user;
                this.$refs.viewModal.show();
            },
            sendEmail (e) {
                e.preventDefault();
                App.post(`/api/v1/user/${this.emailUser.id}/email`, this.emailForm)
                  .then(response => {
                      this.emailForm.clearFields();
                      this.notify('success', 'Successfully sent email.');
                      e.vueTarget.hide();
                  });
            },
            showEmailModal (user) {
                this.emailUser = user;
                this.$refs.emailModal.show();
            },
            editingUser (user) {
                this.goTo('UserSingle', {id: user.id});
            },
        },
        watch: {}
    };
</script>