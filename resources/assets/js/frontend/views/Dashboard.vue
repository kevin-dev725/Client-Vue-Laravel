<template>
    <div class="animated fadeIn frontend-content frontend-dashboard mb-5 mt-2 mt-sm-5">
        <b-container class="main-container">
            <b-row>
                <b-col v-if="!user.is_admin" md="3" sm="12" class="mb-3 d-none d-md-block">
                    <b-list-group>
                        <b-list-group-item to="/dashboard/clients" class="d-flex align-items-center" exact>
                            <icon class="mr-1" name="users"/>
                            Client List
                        </b-list-group-item>
                        <b-list-group-item to="/dashboard/clients/create" class="d-flex align-items-center">
                            <icon class="mr-1" name="user-plus"/>
                            Add new Client
                        </b-list-group-item>
                        <b-list-group-item to="/dashboard/clients/import" class="d-flex align-items-center">
                            <icon class="mr-1" name="upload"/>
                            Import Clients
                        </b-list-group-item>
                        <b-list-group-item to="/dashboard/clients/search" class="d-flex align-items-center">
                            <icon class="mr-1" name="search"/>
                            Search
                        </b-list-group-item>
                        <b-list-group-item :to="{name: 'settings'}" href="#" class="d-flex align-items-center" exact>
                            <icon class="mr-1" name="cog"/>
                            Settings
                        </b-list-group-item>
                    </b-list-group>
                </b-col>
                <b-col :md="user.is_admin ? 12 : 9">
                    <router-view/>
                </b-col>
            </b-row>
        </b-container>
        <div class="mobile-menu d-sm-block d-md-none fixed-bottom">
            <div class="row text-center">
                <router-link class="mobile-menu-item col" :to="{name: 'dashboard-clients'}">
                    <icon class="mr-1" name="users" scale="2"/>
                </router-link>
                <router-link class="mobile-menu-item col" :to="{name: 'dashboard-clients-create'}">
                    <icon class="mr-1" name="user-plus" scale="2"/>
                </router-link>
                <router-link class="mobile-menu-item col" :to="{name: 'dashboard-clients-search'}">
                    <icon class="mr-1" name="search" scale="2"/>
                </router-link>
                <router-link class="mobile-menu-item col" :to="{name: 'settings'}">
                    <icon class="mr-1" name="cog" scale="2"/>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {};
        },
        mounted () {

        },
        computed: {
            user() {
                return window.Store.user;
            }
        },
        methods: {},
        watch: {}
    };
</script>