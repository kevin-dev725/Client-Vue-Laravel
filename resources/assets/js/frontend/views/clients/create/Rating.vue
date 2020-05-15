<template>
    <div>
        <b-form-row>
            <b-col>
                <b-form-group label="Star Rating:"
                              class="text-left"
                              :invalid-feedback="form.errors.get('review.star_rating')"
                              label-for="starRating">
                    <rating-input v-model="form.review.star_rating"/>
                </b-form-group>
                <b-form-group label="Payment Rating:"
                              class="text-left"
                              :invalid-feedback="form.errors.get('review.payment_rating')"
                              label-for="paymentRating">
                    <thumb-input v-model="form.review.payment_rating"/>
                </b-form-group>
                <b-form-group label="Character Rating:"
                              class="text-left"
                              :invalid-feedback="form.errors.get('review.character_rating')"
                              label-for="characterRating">
                    <thumb-input v-model="form.review.character_rating"/>
                </b-form-group>
                <b-form-group label="Repeat Rating:"
                              class="text-left"
                              :invalid-feedback="form.errors.get('review.repeat_rating')"
                              label-for="repeatRating">
                    <thumb-input v-model="form.review.repeat_rating"/>
                </b-form-group>
                <b-form-group label="Comment:"
                              class="text-left"
                              :invalid-feedback="form.errors.get('review.comment')"
                              label-for="comment">
                    <b-form-textarea id="comment"
                                     v-model="form.review.comment"
                                     :state="form.errors.state('review.comment')"
                                     :rows="3">
                    </b-form-textarea>
                </b-form-group>
            </b-col>
        </b-form-row>
    </div>
</template>

<script>
    import ThumbInput from '../../../../components/Globals/ThumbsUpDownInput';
    import RatingInput from '../../../../components/Globals/RatingInput';
    export default {
        components: {
            RatingInput, ThumbInput
        },
        props: {
            form: {
                type: Object,
                required: true,
            }
        }
    }
</script>

<style scoped>

</style>
