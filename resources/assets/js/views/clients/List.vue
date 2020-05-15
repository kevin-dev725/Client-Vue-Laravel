<template>
    <div class="animated fadeIn">
        <b-row class="mb-3">
            <b-col>
                <b-btn variant="secondary" class="text-white mb-3" @click="goTo('UserList')">
                    <i class="fa fa-caret-left"></i>
                    Back to users list
                </b-btn>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card title="Clients List">
                    <div v-if="clients.data.length" class="table-responsive">
                        <table class="table table-hover table-sm table-striped">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Client Type</th>
                                <th scope="col">Address</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(client, i) in clients.data">
                                <td>{{ client.id }}</td>
                                <td class="text-capitalize">{{ getName(client) }}</td>
                                <td class="text-capitalize" v-text="client.client_type"></td>
                                <td class="text-capitalize">{{ getFullAddress(client) }}</td>
                                <td class="text-lowercase" v-text="client.email"></td>
                                <td class="" v-text="client.phone_number"></td>
                                <td>
                                    <b-btn-group size="sm">
                                        <b-btn variant="primary" @click.prevent="showViewModal(client)" title="View">
                                            <icon name="eye"/>
                                        </b-btn>
                                        <b-btn variant="primary" title="Edit"
                                               :to="{name: 'UserClientEdit', params: {id: userId, clientId: client.id}}">
                                            <icon name="edit"/>
                                        </b-btn>
                                        <b-btn variant="primary" title="Reviews"
                                               :to="{name: 'ClientReviewsList', params: {userId: userId, clientId: client.id}}">
                                            <icon name="th-list"/>
                                        </b-btn>
                                        <b-btn variant="danger" @click.prevent="showDeleteModal(client)" title="Delete">
                                            <icon name="trash"/>
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
                    <b-pagination v-if="clients.meta.pagination.total_pages > 1"
                                  class="justify-content-center"
                                  size="md"
                                  :total-rows="clients.meta.pagination.total"
                                  v-model="clients.meta.pagination.current_page"
                                  :per-page="clients.meta.pagination.per_page"
                                  @input="changePage"
                    ></b-pagination>
                </b-card>
            </b-col>
        </b-row>
        <b-modal ref="deleteClientModal"
                 title="Delete client"
                 no-close-on-backdrop
                 no-close-on-esc
                 ok-variant="danger"
                 :busy="deleteForm.busy"
                 @ok="deleteClient">
            Are you sure you want to delete this client?
            <span slot="modal-ok">
                <icon v-if="deleteForm.busy" name="spinner" spin/>
                Delete
            </span>
        </b-modal>
        <b-modal ref="viewClientModal"
                 :title="viewModalTitle"
                 cancel-title="Close"
                 @ok="goTo('UserClientEdit', {id: userId, clientId: viewClient.id})">
            <b-form>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Client Type:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewClient.client_type"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Company Name:"
                              class="mb-0"
                              label-class="font-weight-bold"
                              v-if="viewClient.client_type === 'organization'">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewClient.organization_name"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Name:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewClient.name"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Email:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewClient.email"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Address:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewClientAddress" :rows="3"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Phone No:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewClientPhone"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Alt Phone No:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewClientAltPhone"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Billing Name:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewClientBillingName" :rows="3"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Billing Address:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-capitalize" v-text="viewClientBillingAddress" :rows="3"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Billing Phone No:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewClientBillingPhone"></p>
                </b-form-group>
                <b-form-group horizontal
                              :label-cols="3"
                              label="Billing Email:"
                              class="mb-0"
                              label-class="font-weight-bold">
                    <p class="mb-0 p-2 text-lowercase" v-text="viewClient.billing_email"></p>
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
    import * as ClientType from './../../constants/client-type';
    export default {
        data () {
            return {
                clients: {
                    data: [],
                    meta: {
                        pagination: {
                            count: 0,
                            current_page: 1,
                            links: null,
                            per_page: 15,
                            total: 0,
                            total_pages: 0
                        }
                    },
                },
                page: 1,
                gettingData: false,
                editingClient: {},
                deleteForm: new Form({
                    id: null
                }),
                deletingClient: null,
                viewClient: {},
            };
        },
        mounted () {
            this.getClients();
        },
        computed: {
            userId () {
                return this.$route.params.id;
            },
            query () {
                return 'page=' + this.page;
            },
            viewModalTitle () {
                return 'Viewing User: ' + this.viewClient.email;
            },
            viewClientAddress () {
                let client = this.viewClient;
                if (!client.street_address) {
                    return null;
                }
                return client.street_address + ', ' + client.city + ', ' + client.state + ' ' + client.postal_code;
            },
            viewClientBillingName () {
                let client = this.viewClient;
                if (!client.billing_first_name) {
                    return '';
                }
                if (client.billing_middle_name) {
                    return client.billing_first_name + ' ' + client.billing_middle_name + ' ' + client.billing_last_name;
                }
                return client.billing_first_name + ' ' + client.billing_last_name;
            },
            viewClientBillingAddress () {
                let client = this.viewClient;
                if (!client.billing_street_address) {
                    return null;
                }
                return client.billing_street_address + ', ' + client.billing_city + ', ' + client.billing_state + ' ' + client.billing_postal_code;
            },
            viewClientPhone () {
                let client = this.viewClient;
                if (!client.phone_number_ext) {
                    return client.phone_number;
                }
                return client.phone_number + ',' + client.phone_number_ext;
            },
            viewClientBillingPhone () {
                let client = this.viewClient;
                if (!client.billing_phone_number_ext) {
                    return client.billing_phone_number;
                }
                return client.billing_phone_number + ',' + client.billing_phone_number_ext;
            },
            viewClientAltPhone () {
                let client = this.viewClient;
                if (!client.alt_phone_number_ext) {
                    return client.alt_phone_number;
                }
                return client.alt_phone_number + ',' + client.alt_phone_number_ext;
            }
        },
        methods: {
            showViewModal (client) {
                this.viewClient = client;
                this.$refs.viewClientModal.show();
            },
            showDeleteModal (client) {
                this.deletingClient = client;
                this.deleteForm.id = client.id;
                this.$refs.deleteClientModal.show();
            },
            deleteClient (e) {
                e.preventDefault();
                App.delete(`/api/v1/client/${this.deleteForm.id}`, this.deleteForm)
                  .then(response => {
                      this.arrayDelete(this.clients.data, this.deletingClient);
                      this.$refs.deleteClientModal.hide();
                      this.notify('success', 'Successfully deleted client');
                      this.deletingClient = null;
                      this.deleteForm.clearFields();
                  });
            },
            getName (client) {
                if (client.client_type === ClientType.CLIENT_TYPE_ORGANIZATION) {
                    return client.organization_name;
                }
                return client.name;
            },
            getFullAddress (client) {
                if (!client.street_address) {
                    return '';
                }
                return client.street_address + ', ' + client.city + ', ' + client.state + ' ' + client.postal_code;
            },
            changePage (page) {
                this.page = page;
                this.getClients();
            },
            getClients () {
                this.gettingData = true;
                axios.get(`/api/v1/user/${this.userId}/clients?${this.query}`)
                  .then(response => {
                      this.clients = response.data;
                      this.gettingData = false;
                  })
            }
        },
        watch: {}
    };
</script>