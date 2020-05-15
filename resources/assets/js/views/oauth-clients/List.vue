<template>
    <div class="animated fadeIn">
        <b-row class="mb-3">
            <b-col>
                <search ref="search"
                        :page="page"
                        @results="setResultsData"
                        @reset="reset"
                        url="/api/v1/oauth-client"/>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <!--<router-view :oauthClients="oauthClients" @change-page="changePage" @editing-oauthClient="setEditingUser" @update-oauthClient="updateUser"/>-->
                <b-card title="OauthClients">
                    <div v-if="oauthClients.data.length" class="table-responsive">
                        <table class="table table-hover table-sm table-striped">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Secret</th>
                                <th scope="col">Redirect URL</th>
                                <th scope="col">PersonalAccessClient</th>
                                <th scope="col">PasswordClients</th>
                                <th scope="col">Revoked</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="oauthClient in oauthClients.data">
                                <td v-text="oauthClient.id"></td>
                                <td class="text-capitalize" v-text="oauthClient.name"></td>
                                <td v-text="oauthClient.secret"></td>
                                <td v-text="oauthClient.redirect"></td>
                                <td v-text="oauthClient.personal_access_client"></td>
                                <td v-text="oauthClient.password_client"></td>
                                <td v-text="oauthClient.revoked"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        No data.
                    </div>
                    <b-pagination v-if="oauthClients.meta.pagination.total_pages > 1"
                                  class="justify-content-center"
                                  size="md"
                                  :total-rows="oauthClients.meta.pagination.total"
                                  v-model="oauthClients.meta.pagination.current_page"
                                  :per-page="oauthClients.meta.pagination.per_page"
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
                oauthClients: {
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
            this.getOauthClients();
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
                return 'Viewing OauthClient: ' + this.viewUser.email;
            },
            viewUserAddress () {
                let oauthClient = this.viewUser;
                if (!oauthClient.street_address) {
                    return null;
                }
                return oauthClient.street_address + ', ' + oauthClient.city + ', ' + oauthClient.state + ' ' + oauthClient.postal_code;
            },
            viewUserPhone () {
                let oauthClient = this.viewUser;
                if (!oauthClient.phone_number_ext) {
                    return oauthClient.phone_number;
                }
                return oauthClient.phone_number + ',' + oauthClient.phone_number_ext;
            },
            viewUserAltPhone () {
                let oauthClient = this.viewUser;
                if (!oauthClient.alt_phone_number_ext) {
                    return oauthClient.alt_phone_number;
                }
                return oauthClient.alt_phone_number + ',' + oauthClient.alt_phone_number_ext;
            }
        },
        methods: {
            reset () {
                this.usedSearch = false;
                this.getOauthClients();
            },
            updateUser (oauthClient) {
                this.arrayDelete(this.oauthClients.data, this.editingUser, oauthClient);
                this.editingUser = null;
            },
            setEditingUser (oauthClient) {
                this.editingUser = oauthClient;
            },
            changePage (page) {
                this.page = page;
                this.getOauthClients();
            },
            setResultsData (data) {
                this.usedSearch = true;
                this.oauthClients = data;
                this.goTo('UserList');
            },
            getOauthClients () {
                if (this.usedSearch) {
                    return; //If used search bar then we use the search request on search component
                }
                axios.get(`/api/v1/oauth-clients?${this.query}`)
                  .then(response => {
                      this.oauthClients.data = response.data.data;
                      this.oauthClients.meta.pagination = response.data.meta.pagination;
                  });
            },
            showViewModal (oauthClient) {
                this.viewUser = oauthClient;
                this.$refs.viewModal.show();
            },
            sendEmail (e) {
                e.preventDefault();
                App.post(`/api/v1/oauthClient/${this.emailUser.id}/email`, this.emailForm)
                  .then(response => {
                      this.emailForm.clearFields();
                      this.notify('success', 'Successfully sent email.');
                      e.vueTarget.hide();
                  });
            },
            showEmailModal (oauthClient) {
                this.emailUser = oauthClient;
                this.$refs.emailModal.show();
            },
            editingUser (oauthClient) {
                this.goTo('UserSingle', {id: oauthClient.id});
            },
        },
        watch: {}
    };
</script>