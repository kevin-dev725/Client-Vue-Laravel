<template>
    <div class="app flex-row align-items-center">
        <div class="container">
            <b-row class="justify-content-center">
                <b-col md="6">
                    <b-card-group>
                        <b-card no-body class="p-4">
                            <b-card-body>
                                <h1>Login</h1>
                                <p class="text-muted">Sign In to your account</p>
                                <b-form @submit.prevent="login">
                                    <b-form-group :state="form.errors.state('email')" :invalid-feedback="form.errors.get('email')">
                                        <b-input-group>
                                            <b-input-group-prepend>
                                                <b-input-group-text><i class="icon-user"></i></b-input-group-text>
                                            </b-input-group-prepend>
                                            <b-form-input type="text"
                                                          placeholder="Email"
                                                          v-model="form.email"
                                                          :state="form.errors.state('email')"></b-form-input>
                                        </b-input-group>
                                    </b-form-group>
                                    <b-form-group :state="form.errors.state('password')"
                                                  :invalid-feedback="form.errors.get('password')">
                                        <b-input-group>
                                            <b-input-group-prepend>
                                                <b-input-group-text><i class="icon-lock"></i></b-input-group-text>
                                            </b-input-group-prepend>
                                            <b-form-input type="password"
                                                          placeholder="Password"
                                                          v-model="form.password"
                                                          :state="form.errors.state('password')"></b-form-input>
                                        </b-input-group>
                                    </b-form-group>
                                    <b-row>
                                        <b-col cols="6">
                                            <b-button type="submit" variant="primary" class="px-4">
                                                <icon name="spinner" spin v-if="form.busy"/> Login</b-button>
                                        </b-col>
                                        <b-col cols="6" class="text-right">
                                            <b-button variant="link" class="px-0">Forgot password?</b-button>
                                        </b-col>
                                    </b-row>
                                </b-form>
                            </b-card-body>
                        </b-card>
                    </b-card-group>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Login',
        data () {
            return {
                form: new Form ({
                    email: '',
                    password: '',
                }),
            }
        },
        methods: {
            login () {
                App.post('/login', this.form)
                  .then(response => {
                      console.log(response);
                      location.reload();
                  })
                  .catch(error => {
                      console.log(error);
                  })
            }
        }
    };
</script>
