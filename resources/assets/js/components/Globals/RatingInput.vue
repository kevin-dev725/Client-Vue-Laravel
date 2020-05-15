<template>
    <star-rating :border-width="3" :read-only="readOnly" :show-rating="false" :rating="value" @rating-selected="$emit('input', $event)" active-color="#20a8d8" border-color="lightgrey" inactive-color="#fff" :item-size="25"/>
</template>

<script>
    import {StarRating} from 'vue-rate-it';
    export default {
        props: ['value', 'readOnly'],
        components: {
            StarRating
        }
    }
</script>

<style scoped>

</style>
