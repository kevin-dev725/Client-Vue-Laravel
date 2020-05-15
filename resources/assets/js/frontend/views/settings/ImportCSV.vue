<template>
    <div>
        <b-form @submit.prevent="submit" class="form-import-csv">
            <div class="row mt-3">
                <div class="col-md-8 offset-md-4">
                    <a href="/csv/client-import-template.csv" target="_blank"><icon name="download"></icon> Download Template</a>
                </div>
            </div>
            <b-form-group
                    horizontal
                    label-cols="4"
                    breakpoint="md"
                    label="Enter csv file"
                    label-class="text-md-right"
                    label-for="file"
                    class="mt-2"
                    :invalid-feedback="form.errors.get('file')"
                    :state="form.errors.state('file')"
            >
                <b-form-file id="file" v-model="form.file" accept=".csv" :state="form.errors.state('file')" placeholder="Choose a file..."></b-form-file>
            </b-form-group>
            <b-form-group class="mb-0" v-if="success">
                <b-form-row>
                    <b-col md="8" offset-md="4">
                        <span class="text-success">Import has been queued, it may be a few minutes until processing is complete.</span>
                    </b-col>
                </b-form-row>
            </b-form-group>
            <div class="float-right">
                <b-btn variant="primary" type="submit" :disabled="form.busy">
                    <icon name="spinner" spin v-if="form.busy"/>
                    Import</b-btn>
            </div>
            <div class="clearfix"></div>
        </b-form>
        <b-card title="CSV Imports" class="mt-3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Import Date</th>
                    <th>Status</th>
                    <th>Errors</th>
                    <th>Row</th>
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
                        <span class="badge" :class="{'badge-success': item.status === ImportStatus.FINSIHED, 'badge-info': item.status === ImportStatus.STARTED || item.status === ImportStatus.PENDING, 'badge-danger': item.status === ImportStatus.ERROR}">{{ item.status }}</span>
                    </td>
                    <td>
                        <template v-if="item.errors">
                            <template v-if="isString(item.errors)">
                                {{ item.errors }}
                            </template>
                            <template v-else-if="isArray(item.errors)" v-for="(error, index) in item.errors">
                                <br v-if="index > 0"/>
                                {{ error }}
                            </template>
                            <template v-else-if="isObject(item.errors)" v-for="(error, field, index) in item.errors">
                                <br v-if="index > 0"/>
                                {{ field | titleCase }}: {{ error[0] }}
                            </template>
                        </template>
                    </td>
                    <td>
                        <span v-if="item.status === ImportStatus.ERROR && item.invalid_row">
                            {{ item.invalid_row.row_index + 1 }}
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
            <b-pagination v-if="imports.meta.pagination" @change="gotoPage" :disabled="imports.meta.loading" size="md" :total-rows="imports.meta.pagination.total" v-model="imports.meta.pagination.current_page" :per-page="imports.meta.pagination.per_page"/>
        </b-card>
    </div>
</template>

<script>
    import * as ImportStatus from '../../../constants/client-import-status';
    import data_type_mixin  from '../../../mixins/data-type'

    export default {
        mixins: [data_type_mixin],
        data () {
            return {
                ImportStatus,
                form: new Form ({
                    file: null,
                }),
                success: false,
                imports: {
                    data: [],
                    meta: {
                        pagination: null,
                        loading: false,
                    }
                },
                tableFields: [
                    {
                        key: 'created_at',
                        label: 'Import Date'
                    },
                    'status',
                    'errors',
                    {
                        label: 'Row',
                        key: 'row'
                    }
                ]
            };
        },
        mounted () {
            this.getImports();
        },
        computed: {

        },
        methods: {
            submit () {
                this.success = false;
                window.App.postData('/api/v1/client/import-csv', this.form)
                    .then(() => {
                        this.success = true;
                        this.notify('success', 'Successfully uploaded csv file.');
                        this.reloadPage();
                    })
            },
            reloadPage() {
                this.getImports(this.imports.meta.pagination.current_page);
            },
            getImports(page = 1) {
                this.imports.meta.loading = true;
                window.axios.get('/api/v1/client-import', {
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
            gotoPage(page) {
                this.getImports(page);
            },
        },
        watch: {}
    };
</script>