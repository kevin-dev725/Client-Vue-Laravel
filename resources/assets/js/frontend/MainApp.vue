<template>
    <router-view/>
</template>

<script>
    export default {
        name: 'app',
        mounted() {
            window.Store.init();
        },
    }
</script>
