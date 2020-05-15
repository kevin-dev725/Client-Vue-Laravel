<template>
    <b-form @submit.prevent="search" @reset="reset">
        <b-form-group
                id="searchField"
                :invalid-feedback="form.errors.get('search')"
                :state="form.errors.state('search')">
            <b-input-group>
                <b-input-group-prepend>
                    <b-button type="submit" variant="primary" title="Search">
                    <span class="align-middle">
                        <icon name="search" v-if="!form.busy"/>
                        <icon name="spinner" spin v-else/>
                    </span>
                        Search
                    </b-button>
                </b-input-group-prepend>
                <b-form-input type="search" :placeholder="placeholder" v-model="form[formInput]" :disabled="form.busy" required></b-form-input>
                <b-input-group-append>
                    <b-btn type="reset" variant="danger" title="Clear search">
                        <icon name="times"/>
                    </b-btn>
                </b-input-group-append>
            </b-input-group>
        </b-form-group>
    </b-form>
</template>

<script>
    export default {
        props: {
            page: {
                type: Number,
                default: 1
            },
            url: {
                type: String,
                default: '',
                Required: true
            },
            formInput: {
                type: String,
                default: 'keyword'
            },
            placeholder: {
                type: String,
                default: 'Search by name or by email ...'
            }
        },
        data () {
            return {
                form: new Form ({}),
                result: {
                    data: [],
                    meta: {
                        pagination: {}
                    }
                },
            };
        },
        mounted () {
            Vue.set(this.form, this.formInput);
        },
        computed: {
            pageQuery () {
                return 'page=' + this.page;
            },
            inputQuery () {
                if (!this.form[this.formInput]) {
                    return ``;
                }
                return `${this.formInput}=${this.form[this.formInput]}`;
            }
        },
        methods: {
            reset () {
                this.$emit('reset');
            },
            search () {
                this.form.startProcessing();
                axios.get(`${this.url}?${this.pageQuery}&${this.inputQuery}`)
                  .then(response => {
                      this.result = response.data;
                      this.$emit('results', response.data);
                      this.form.finishProcessing();
                  })
            }
        },
        watch: {
            page () {
                this.search();
            }
        }
    };
</script>