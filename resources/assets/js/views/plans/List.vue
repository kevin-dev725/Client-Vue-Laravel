<template>
    <b-card title="Subscription Plans">
        <b-btn variant="primary" class="mb-3" v-b-modal.modalPlan>
            <icon name="plus"/> Create</b-btn>
        <div v-if="plans.length" class="table-responsive">
            <table class="table table-hover table-sm table-striped">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Stripe ID</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="plan in plans">
                    <td>{{ plan.name }}</td>
                    <td>{{ plan.price }}</td>
                    <td>{{ plan.stripe_id }}</td>
                    <td>
                        <b-btn-group>
                            <b-btn variant="primary" size="sm" @click="edit(plan)">
                                <icon name="edit"/></b-btn>
                            <b-btn variant="danger" size="sm" @click="showModalDeletePlan(plan)">
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
        <b-modal id="modalPlan"
                 ref="modalPlan"
                 :title="modalTitle"
                 no-close-on-backdrop
                 no-close-on-esc
                 @ok="submit"
                 @hidden="clearFields">
            <b-form>
                <b-form-group id="inputGroup1"
                        label="Name"
                        label-for="name">
                    <b-form-input type="text" id="name" v-model="form.name"></b-form-input>
                </b-form-group>
                <b-form-group id="inputGroup2"
                        label="Price"
                        label-for="price">
                    <b-form-input type="number" id="price" v-model.number="form.price"></b-form-input>
                </b-form-group>
                <b-form-group id="inputGroup3"
                        label="Stripe ID"
                        label-for="stripeId">
                    <b-form-input type="text" id="stripeId" v-model="form.stripe_id"></b-form-input>
                </b-form-group>
            </b-form>

            <span slot="modal-ok">
                <icon v-if="form.busy" name="spinner" spin/>
                <icon v-else name="save"/>
                {{ modalSaveBtn }}
            </span>
        </b-modal>

        <b-modal id="modalDeletePlan"
                 ref="modalDeletePlan"
                 title="Delete Subscription Plan"
                 no-close-on-backdrop
                 no-close-on-esc
                 ok-variant="danger"
                 @ok="deletePlan"
                 @hidden="clearFields">

            <p v-if="deletingPlan">Are you sure you want to delete {{ deletingPlan.name }} plan?</p>

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
                plans: [],
                form: new Form ({
                    name: null,
                    price: 0,
                    stripe_id: null,
                }),
                editingPlan: null,
                deletingPlan: null,
            };
        },
        mounted () {
            this.getPlans();
        },
        computed: {
            modalSaveBtn () {
                return this.editingPlan ? 'Save' : 'Create';
            },
            modalTitle () {
                return this.editingPlan ? `Edit Subscription Plan: ${this.editingPlan.name}` : 'Create new Subscription Plan';
            }
        },
        methods: {
            deletePlan (e) {
                e.preventDefault();
                App.delete(`/api/v1/plan/${this.deletingPlan.id}`, this.form)
                  .then(response => {
                      this.form.clearFields();
                      this.arrayDelete(this.plans, this.deletingPlan);
                      this.notify('success', 'Successfully deleted subscription plan.');
                      e.vueTarget.hide();
                  })
            },
            showModalDeletePlan (plan) {
                this.deletingPlan = plan;
                this.$refs.modalDeletePlan.show();
            },
            clearFields () {
                this.form.clearFields();
                this.editingPlan = null;
                this.deletingPlan = null;
            },
            edit (plan) {
                this.editingPlan = plan;
                _.extend(this.form, _.pick(plan, ['name', 'price', 'stripe_id']));
                this.$refs.modalPlan.show();
            },
            submit (e) {
                e.preventDefault();
                let query = '/api/v1/plan', method = 'post', notificationMsg = 'Successfully added new subscription plan.';
                if (this.editingPlan) {
                    query = `/api/v1/plan/${this.editingPlan.id}`;
                    method = 'put';
                    notificationMsg = 'Successfully updated subscription plan.'
                }
                    App[method](query, this.form)
                      .then(response => {
                          this.form.clearFields();
                          if (this.editingPlan) {
                              this.arrayDelete(this.plans, this.editingPlan, response.data)
                          } else {
                              this.plans.push(response.data);
                          }
                          this.notify('success', notificationMsg);
                          e.vueTarget.hide();
                      });
            },
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