<template>
    <b-card>
        <h4 slot="header" class="text-center">
            Profile
        </h4>
        <profile v-if="user" :user="user"/>
    </b-card>
</template>
<script>
    import Profile from '../settings/Profile';
    function getData(userId) {
        window.Store.startLoading();
        return new Promise((resolve, reject) => {
            window.axios.get(`/api/v1/user/${userId}`)
                .then((response) => {
                    window.Store.finishLoading();
                    resolve(response);
                })
                .catch((error) => {
                    window.Store.failLoading();
                    reject(error);
                });
        });
    }
    export default {
        components: {
            Profile
        },
        beforeRouteEnter(to, from, next) {
            getData(to.params.id)
                .then(response => {
                    next(vm => {
                        vm.setData(response);
                    });
                })
                .catch(() => {
                    next(false);
                });
        },
        beforeRouteUpdate(to, from, next) {
            getData(to.params.id)
                .then(response => {
                    this.setData(response);
                })
                .catch(() => {
                    next(false);
                });
        },
        data() {
            return {
                user: null,
            }
        },
        computed: {
            userId() {
                return this.$route.params.id;
            },
        },
        user() {

        },
        methods: {
            setData(response) {
                this.user = response.data;
            }
        }
    }
</script>

<style scoped>

</style>