<template>
    <b-navbar toggleable="md" variant="light">
        <b-container>
            <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
            <b-navbar-brand to="/">
                <img height="40" src="/images/logo.png" alt="Logo">
            </b-navbar-brand>

            <b-collapse is-nav id="nav_collapse">

                <b-navbar-nav v-if="user && !user.is_admin">
                    <b-nav-item to="/dashboard">Dashboard</b-nav-item>
                </b-navbar-nav>
                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    <b-nav-item class="ml-auto" to="/login" v-if="!user">Login</b-nav-item>
                    <b-nav-item class="ml-auto" to="/register" v-if="!user">Register</b-nav-item>

                    <b-nav-item-dropdown right v-if="user">
                        <!-- Using button-content slot -->
                        <template slot="button-content">
                            <em>{{ user.name }}</em>
                        </template>
                        <b-dropdown-item @click.prevent="logout">Signout</b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-container>
    </b-navbar>

    <!--<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <router-link class="navbar-brand" to="/">
                {{ appName }}
            </router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                &lt;!&ndash; Left Side Of Navbar &ndash;&gt;
                <ul class="navbar-nav mr-auto">

                </ul>

                &lt;!&ndash; Right Side Of Navbar &ndash;&gt;
                <ul class="navbar-nav ml-auto">
                    &lt;!&ndash; Authentication Links &ndash;&gt;
                    <li v-if="!user"><router-link class="nav-link" to="/login">Login</router-link></li>
                    <li v-if="!user"><router-link class="nav-link" to="/register">Register</router-link></li>
                    <li v-if="user" class="nav-item dropdown">
                        <router-link id="navbarDropdown" class="nav-link dropdown-toggle" to="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ user.name }} <span class="caret"></span>
                        </router-link>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <b-link class="dropdown-item" @click.prevent="logout">
                                Logout
                            </b-link>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>-->
</template>

<script>
    export default {
        data () {
            return {};
        },
        mounted () {

        },
        computed: {
            user () {
                return window.Store.user;
            },
            appName () {
                return window.Store.config.app.name;
            }
        },
        methods: {
            logout () {
                App.post('/logout', new Form())
                  .then(response => {
                      location.reload();
                  })
            }
        },
        watch: {}
    };
</script>