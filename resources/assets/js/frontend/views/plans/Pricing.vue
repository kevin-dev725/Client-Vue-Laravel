<template>
    <div class="frontend-content frontend-plans animated fadeIn pt-5 pb-5">
        <b-container>
            <b-row>
                <b-col v-for="(plan, i) in plans" :key="plan.id">
                    <b-card :title="plan.name" :sub-title="plan.price | currency" body-class="text-center">
                        <b-btn :to="{name: 'plans-billing', params: {id: plan.id}}" variant="primary" block>Select</b-btn>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    export default {
        beforeRouteLeave (to, from, next) {
            if (to.name !== 'plans-billing') return next();

            if (!window.Store.user) {
                next('/login');
            } else {
                next();
            }
        },
        data () {
            return {
                plans: []
            };
        },
        mounted () {
            this.getPlans();
        },
        computed: {},
        methods: {
            getPlans () {
                axios.get('/api/v1/plan')
                  .then(response => {
                      this.plans = response.data.data;
                  })
            }
        },
        watch: {}
    };
</script>