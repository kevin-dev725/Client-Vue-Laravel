<template>
    <div class="animated fadeIn">
        <b-card style="max-width: 500px" title="Import Lien Records">
            <b-form @submit.prevent="submit()">
                <b-form-group :invalid-feedback="form.errors.get('state')"
                              label="State*"
                              label-for="state">
                    <b-form-select :options="states"
                                   :state="form.errors.state('state')"
                                   id="state"
                                   v-model="form.state">
                    </b-form-select>
                </b-form-group>
                <b-form-group :invalid-feedback="form.errors.get('county')"
                              label="County*"
                              label-for="county">
                    <b-form-select :options="countyOptions"
                                   :state="form.errors.state('county')"
                                   id="county"
                                   v-model="form.county">
                    </b-form-select>
                </b-form-group>
                <b-form-group :invalid-feedback="form.errors.get('file')"
                              label="Xls(x) file*"
                              label-for="file">
                    <b-form-file accept=".xls,.xlsx" class="mt-3"
                                 drop-placeholder="Drop file here..."
                                 id="file"
                                 placeholder="Choose a file or drop it here..."
                                 v-model="form.file"></b-form-file>
                </b-form-group>
                <b-form-group>
                    <b-btn :disabled="form.busy" class="float-right" type="submit" variant="primary">
                        <i class="fa fa-upload" v-if="!form.busy"></i>
                        <icon name="spinner" spin v-else/>
                        Upload
                    </b-btn>
                </b-form-group>
            </b-form>
        </b-card>
    </div>
</template>
<script>
    export default {
        data: () => ({
            form: new Form({
                file: null,
                state: null,
                county: null,
            })
        }),
        computed: {
            states() {
                return Store.states
            },
            countyOptions() {
                if (!this.form.state) {
                    return [];
                }
                return this.states.find(state => state.value === this.form.state)
                    .counties;
            }
        },
        methods: {
            submit() {
                App.postData(`/web-api/lien/import`, this.form)
                    .then(() => {
                        this.notify('success', 'Lien records import have been queued.');
                        this.clear();
                    });
            },
            clear() {
                this.form.file = null;
                this.form.state = null;
                this.form.county = null;
            }
        }
    }
</script>
