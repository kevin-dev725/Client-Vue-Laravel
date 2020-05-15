<template>
    <register :user="user"/>
</template>

<script>
    import Register from './Register';
    export default {
        components: {
            Register
        },
        computed: {
            user() {
                return window.Store.user;
            }
        }
    }
</script>

<style scoped>

</style>