<template>
    <div class="animated fadeIn frontend-welcome frontend-content mb-5 mt-5">
        <b-container>
            <b-row align-h="center">
                <b-col>
                    <b-card :title="appName" sub-title="Welcome Page" align="center">

                    </b-card>
                </b-col>
            </b-row>
        </b-container>
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