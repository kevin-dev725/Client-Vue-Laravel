<template>
    <b-card align="center">
        <h4 slot="header">
            Clients List <br>
            <small v-if="summary.total !== null">
                <span v-if="summary.total === clients.data.length">{{ summary.total }} Clients</span>
                <span v-else-if="loadedClients">Showing {{clients.data.length}} of {{summary.total}} clients</span>
            </small>
        </h4>
        <search ref="search"
                :page="page"
                @results="setResultsData"
                @reset="reset"
                url="/api/v1/client" placeholder="Search by name / street / city or organization name ..."/>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th scope="col">Name / Organization</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Address</th>
                    <th scope="col">Average Rating</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="clients.data.length === 0">
                    <td colspan="4">No data.</td>
                </tr>
                <tr v-for="(client, i) in clients.data" @click.prevent="goTo('dashboard-clients-single', {id: client.id})" style="cursor: pointer">
                    <td class="text-capitalize">
                        {{ client.display_name }}
                    </td>
                    <td>
                        {{ client.phone_number}}
                    </td>
                    <td :title="client | full_address">
                        <div class="text-truncate">
                            {{ client | full_address }}
                        </div>
                    </td>
                    <td class="text-center">
                        <rating class="center" v-if="client.avg_rating" :rating="client.avg_rating" :title="client.avg_rating" star/>
                        <span class="text-capitalize" v-else>No Reviews</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <b-pagination slot="footer" v-if="clients.meta.pagination.total_pages > 1"
                      class="justify-content-center"
                      size="md"
                      :total-rows="clients.meta.pagination.total"
                      v-model="clients.meta.pagination.current_page"
                      :per-page="clients.meta.pagination.per_page"
                      @input="changePage"
        ></b-pagination>
    </b-card>
</template>

<script>
    export default {
        data () {
            return {
                clients: {
                    data: [],
                    meta: {
                        pagination: {
                            total: 0,
                        }
                    }
                },
                page: 1,
                summary: {
                    total: null,
                },
                loadedClients: false,
            };
        },
        mounted () {
            this.getClients();
            this.getSummary();
        },
        computed: {
            pageQuery () {
                return 'page=' + this.page;
            }
        },
        methods: {
            reset () {
                this.getClients();
            },
            setResultsData (data) {
                this.clients = data;
            },
            changePage (page) {
                this.page = page;
                this.getClients();
            },
            getClients () {
                axios.get(`/api/v1/client?${this.pageQuery}`)
                    .then(response => {
                        this.loadedClients = true;
                        this.clients = response.data;
                    })
            },
            getSummary() {
                window.axios.get(`/api/v1/client/summary`)
                    .then(response => {
                        this.summary = response.data;
                    });
            }
        },
        watch: {}
    };
</script>

<style lang="css" scoped>
    .table th, .table td {
        vertical-align: middle;
    }
</style>