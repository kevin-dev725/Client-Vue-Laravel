<template>
    <div>
        <b-btn href="/quickbooks" variant="primary">Import Customers from Quickbooks</b-btn>
        <p v-if="$route.query.quickbooks_import === 'success'" class="text-success mt-3">Import has been queued, it may be a few minutes until processing is complete.</p>
        <b-card title="Quickbook Imports" class="mt-3">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Import Date</th>
                        <th>Status</th>
                        <th>Skipped Import</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="imports.meta.loading">
                        <td class="text-center" colspan="4">
                            <icon name="spinner" spin/> Loading...
                        </td>
                    </tr>
                    <tr v-if="!imports.meta.loading && imports.data.length === 0">
                        <td class="text-center" colspan="4">
                            No data.
                        </td>
                    </tr>
                    <tr v-for="item in imports.data">
                        <td>{{ item.created_at | datetime }}</td>
                        <td>
                            <span class="badge" :class="{'badge-success': item.status === ImportStatus.FINSIHED, 'badge-info': item.status === ImportStatus.STARTED || item.status === ImportStatus.PENDING, 'badge-danger': item.status === ImportStatus.ERROR, 'badge-warning': item.status === ImportStatus.FINISHED_WITH_ERROR}">{{ item.status }}</span>
                        </td>
                        <td>
                            <div class="table-responsive">
                                <table class="table bg-white">
                                    <tbody>
                                    <tr class="bg-white" v-for="row in item.errors">
                                        <td>
                                            {{ row.customer }}
                                        </td>
                                        <td>
                                            <template v-for="(err, field, index) in row.error">
                                                <br v-if="index > 0"/>
                                                {{ field | titleCase }}: {{ err[0] }}
                                            </template>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--<template v-for="row in item.errors">
                                <h6>{{row.customer}}</h6>

                                <template v-for="(err, field, index) in row.error">
                                    <br v-if="index > 0"/>
                                    {{ field | titleCase }}: {{ err[0] }}
                                </template>
                            </template>-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </b-card>
    </div>
</template>

<script>
    import * as ImportStatus from '../../../constants/client-import-status';
    export default {
        data () {
            return {
                ImportStatus,
                imports: {
                    data: [],
                    meta: {
                        pagination: null,
                        loading: false,
                    }
                },
            };
        },
        mounted () {
            this.getImports();
        },
        computed: {},
        methods: {
            getImports(page = 1) {
                this.imports.meta.loading = true;
                window.axios.get('/api/v1/quickbooks-import', {
                    params: {
                        page,
                    }
                })
                  .then(response => {
                      this.imports.data = response.data.data;
                      this.imports.meta.pagination = response.data.meta.pagination;
                      this.imports.meta.loading = false;
                  })
                  .catch(() => {
                      this.imports.meta.loading = false;
                  });
            },
        },
        watch: {}
    };
</script>