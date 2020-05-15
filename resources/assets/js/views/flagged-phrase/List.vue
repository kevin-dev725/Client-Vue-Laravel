<template>
    <b-card title="Flagged Words and Phrases">
        <b-btn variant="primary" class="mb-3" v-b-modal.modalPhrase>
            <icon name="plus"/> Create</b-btn>
        <div v-if="items.length" class="table-responsive">
            <table class="table table-hover table-sm table-striped">
                <thead>
                <tr>
                    <th scope="col">Word/Phrase</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in items">
                    <td>{{ item.phrase }}</td>
                    <td>
                        <b-btn-group>
                            <b-btn variant="primary" size="sm" @click="edit(item)">
                                <icon name="edit"/></b-btn>
                            <b-btn variant="danger" size="sm" @click="showModalDeleteItem(item)">
                                <icon name="trash"/></b-btn>
                        </b-btn-group>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            No data.
        </div>
        <b-modal id="modalPhrase"
                 ref="modalPhrase"
                 :title="modalTitle"
                 no-close-on-backdrop
                 no-close-on-esc
                 @ok="submit"
                 @hidden="clearFields">
            <b-form>
                <b-form-group id="inputGroup1"
                              label="Word/Phrase"
                              label-for="phrase">
                    <b-form-input type="text" id="name" v-model="form.phrase"></b-form-input>
                </b-form-group>
            </b-form>

            <span slot="modal-ok">
                <icon v-if="form.busy" name="spinner" spin/>
                <icon v-else name="save"/>
                {{ modalSaveBtn }}
            </span>
        </b-modal>

        <b-modal id="modalDeleteItem"
                 ref="modalDeleteItem"
                 title="Delete Flagged Phrase"
                 no-close-on-backdrop
                 no-close-on-esc
                 ok-variant="danger"
                 @ok="deleteItem"
                 @hidden="clearFields">

            <p v-if="deletingItem">Are you sure you want to delete {{ deletingItem.name }}?</p>

            <span slot="modal-ok">
                <icon v-if="form.busy" name="spinner" spin/>
                <icon v-else name="trash"/>
                Delete
            </span>
        </b-modal>
    </b-card>
</template>

<script>
    export default {
        data () {
            return {
                items: [],
                form: new Form ({
                    phrase: null,
                }),
                editingItem: null,
                deletingItem: null,
            };
        },
        mounted () {
            this.getList();
        },
        computed: {
            modalSaveBtn () {
                return this.editingItem ? 'Save' : 'Create';
            },
            modalTitle () {
                return this.editingItem ? `Edit Flagged Phrase: ${this.editingItem.name}` : 'Create new flagged phrase';
            }
        },
        methods: {
            deleteItem (e) {
                e.preventDefault();
                App.delete(`/web-api/flagged-phrase/${this.deletingItem.id}`, this.form)
                    .then(() => {
                        this.form.clearFields();
                        this.arrayDelete(this.items, this.deletingItem);
                        this.notify('success', 'Successfully deleted flagged phrase.');
                        e.vueTarget.hide();
                    })
            },
            showModalDeleteItem (plan) {
                this.deletingItem = plan;
                this.$refs.modalDeleteItem.show();
            },
            clearFields () {
                this.form.clearFields();
                this.editingItem = null;
                this.deletingItem = null;
            },
            edit (plan) {
                this.editingItem = plan;
                _.extend(this.form, _.pick(plan, ['phrase']));
                this.$refs.modalPhrase.show();
            },
            submit (e) {
                e.preventDefault();
                let query = '/web-api/flagged-phrase', method = 'post', notificationMsg = 'Successfully added new flagged phrase.';
                if (this.editingItem) {
                    query = `/web-api/flagged-phrase/${this.editingItem.id}`;
                    method = 'put';
                    notificationMsg = 'Successfully updated flagged phrase.'
                }
                App[method](query, this.form)
                    .then(response => {
                        this.form.clearFields();
                        if (this.editingItem) {
                            this.arrayDelete(this.items, this.editingItem, response.data)
                        } else {
                            this.items.push(response.data);
                        }
                        this.notify('success', notificationMsg);
                        e.vueTarget.hide();
                    });
            },
            getList () {
                axios.get('/web-api/flagged-phrase')
                    .then(response => {
                        this.items = response.data.data;
                    })
            }
        },
        watch: {}
    };
</script>