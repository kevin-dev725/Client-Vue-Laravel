<template>
    <div>
        <div class="table-responsive">
            <table class="table b-table table-hover">
                <thead class="thead-light">
                <tr>
                    <th style="width: 25%">Log</th>
                    <th style="width: 50%">Description</th>
                    <!--<th>Properties</th>-->
                    <th style="width: 25%"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="!gettingData && activities.data.length" v-for="activity in activities.data">
                    <td>{{ activity.log_name }}</td>
                    <td>{{ activity.description }}</td>
                    <!--<td>{{ activity.properties }}</td>-->
                    <td>{{ activity.created_at | datetime }}</td>
                </tr>
                <tr v-if="!gettingData && !activities.data.length">
                    <td colspan="2">No Data.</td>
                </tr>
                <tr v-if="gettingData" class="text-center">
                    <td colspan="2">
                        <icon name="spinner" spin></icon>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-pagination v-if="activities.meta.pagination.total_pages > 1"
                      class="justify-content-center"
                      size="md"
                      :total-rows="activities.meta.pagination.total"
                      v-model="activities.meta.pagination.current_page"
                      :per-page="activities.meta.pagination.per_page"
                      @input="changePage"
        ></b-pagination>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                page: 1,
                gettingData: false,
                activities: {
                    data: [],
                    meta: {
                        pagination: {}
                    }
                }
            };
        },
        mounted () {
            this.getActivities();
        },
        computed: {
            query () {
                return `page=${this.page}`;
            }
        },
        methods: {
            changePage (page) {
                this.page = page;
                this.getActivities();
            },
            getActivities () {
                this.gettingData = true;
                axios.get(`/api/v1/activity?${this.query}`)
                  .then(response => {
                      this.activities = response.data;
                  })
                  .finally(() => {
                      this.gettingData = false;
                  })
            }
        },
        watch: {}
    };
</script>