<template>
    <div>
        <b-card>
            <h4 slot="header" class="text-center">
                Import Clients
            </h4>
            <h5 class="mt-5">Quickbooks Connect</h5>
            <import-quickbooks/>
            <h5 class="mt-5">Import Clients from CSV</h5>
            <import-csv/>
        </b-card>
    </div>
</template>

<script>
    import ImportCsv from './settings/ImportCSV';
    import ImportQuickbooks from './settings/ImportQuickbooks';
    export default {
        components: {
            ImportCsv, ImportQuickbooks
        },
        data() {
            return {

            };
        },
        computed: {
            user() {
                return window.Store.user;
            },
        },
        mounted() {

        },
        methods: {

        }
    }
</script>

<style lang="scss" scoped>
    .form-update-password, .form-import-csv {
        max-width: 600px;
    }
</style>