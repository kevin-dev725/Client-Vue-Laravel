<template>
    <span class="rating">
        <star-rating v-if="star" :read-only="true" :increment="0.5" :show-rating="false" :item-size="size" :rating="parseFloat(rating)" active-color="#20a8d8" inactive-color="#fff" border-color="lightgrey" :title="rating"/>
        <icon v-else v-for="(item, i) in loop" :title="rating" :name="getReviewRatingIcon(item)" :key="i"/>
    </span>
</template>

<script>
    import * as ReviewRating from './../../constants/review-rating';
    import {StarRating} from 'vue-rate-it';
    export default {
        components: {
            StarRating
        },
        props: {
            size: {
                type: Number,
                default: 15,
            },
            star: {
                type: Boolean,
                default: false
            },
            rating: {
                type: [String, Number],
                required: true
            },
            full: {
                type: Boolean,
                default: false
            }
        },
        data () {
            return {
                loop: 1,
                full_loop: 5
            };
        },
        mounted () {
            this.setLoop();
        },
        computed: {},
        methods: {
            setLoop () {
                if (this.star) {
                    this.loop = this.full ? this.full_loop : Math.floor(this.rating);
                }
            },
            getReviewRatingIcon (item) {
                let neutral = 'minus', thumbs_up = 'thumbs-o-up', thumbs_down = 'thumbs-o-down', star = 'star', star_o = 'star-o';
                if (this.star) {
                    if (this.full && this.rating < item) {
                        return star_o;
                    }
                    return star;
                }
                switch (this.rating) {
                    case ReviewRating.RATING_NO_OPINION:
                        return neutral;
                    case ReviewRating.RATING_THUMBS_UP:
                        return thumbs_up;
                    case ReviewRating.RATING_THUMBS_DOWN:
                        return thumbs_down;
                }
            }
        },
        watch: {
            rating () {
                this.setLoop();
            },
            full () {
                this.setLoop();
            }
        }
    };
</script>
