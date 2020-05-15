<template>
    <div class="app">
        <vue-progress-bar/>
        <div class="app-body">
            <app-header/>
            <main class="main">
                <b-container fluid class="h-100">
                    <router-view/>
                </b-container>
            </main>
        </div>
        <notifications group="notification" style="top: 10px" position="top right" :max="3"/>
    </div>
</template>

<script>
    import { Header as AppHeader } from './../components';
    export default {
        components: {
            AppHeader
        },
        name: 'full',
        data () {
            return {};
        },
        computed: {}
    };
</script>
