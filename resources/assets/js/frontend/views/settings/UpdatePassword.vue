<template>
    <b-form @submit.prevent="submit" class="form-update-password">
        <b-form-group horizontal
                      label-cols="4"
                      breakpoint="md"
                      label="New Password*"
                      label-class="text-md-right"
                      label-for="password"
                      :invalid-feedback="form.errors.get('password')">
            <b-form-input id="password"
                          type="password"
                          v-model="form.password"
                          :state="form.errors.state('password')"></b-form-input>
        </b-form-group>
        <b-form-group horizontal
                      label-cols="4"
                      breakpoint="md"
                      label="Confirm New Password*"
                      label-class="text-md-right"
                      label-for="password_confirmation"
                      :invalid-feedback="form.errors.get('password_confirmation')">
            <b-form-input id="password_confirmation"
                          type="password"
                          v-model="form.password_confirmation"
                          :state="form.errors.state('password_confirmation')"></b-form-input>
        </b-form-group>
        <div class="float-right">
            <b-btn variant="primary" type="submit" :disabled="form.busy">Update Password</b-btn>
        </div>
        <div class="clearfix"></div>
    </b-form>
</template>

<script>
    export default {
        data() {
            return {
                form: new window.Form({
                    password: null,
                    password_confirmation: null,
                })
            };
        },
        methods: {
            resetForm() {
                this.form.resetStatus();
                this.form.password = null;
                this.form.password_confirmation = null;
            },
            submit() {
                window.App.post(`/api/v1/auth/user/change-password`, this.form)
                    .then(() => {
                        this.notify('success', 'Your password has been updated.');
                        this.resetForm();
                    });
            }
        }
    }
</script>

<style scoped>

</style>