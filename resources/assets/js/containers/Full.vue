<template>
    <div class="app">
        <vue-progress-bar/>
        <AppHeader/>
        <div class="app-body">
            <Sidebar :navItems="nav"/>
            <main class="main">
                <breadcrumb :list="list"/>
                <div class="container-fluid">
                    <router-view/>
                </div>
            </main>
            <AppAside/>
        </div>
        <AppFooter/>
        <notifications group="notification" style="top: 10px" position="top right" :max="3"/>
    </div>
</template>

<script>
    import nav from '../_nav';
    import { Header as AppHeader, Sidebar, Aside as AppAside, Footer as AppFooter, Breadcrumb } from '../components/';

    export default {
        name: 'full',
        components: {
            AppHeader,
            Sidebar,
            AppAside,
            AppFooter,
            Breadcrumb
        },
        data () {
            return {
                nav: nav.items
            };
        },
        computed: {
            name () {
                return this.$route.name;
            },
            list () {
                return this.$route.matched;
            }
        }
    };
</script>
