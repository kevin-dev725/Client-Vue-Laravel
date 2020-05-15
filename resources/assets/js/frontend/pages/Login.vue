<template>
    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Login Form</div>
                    <div class="card-body">
                        <b-form @submit.prevent="login">
                            <b-form-group label="Email Address"
                                          horizontal
                                          label-cols="4"
                                          label-for="email"
                                          label-class="text-md-right"
                                          :invalid-feedback="form.errors.get('email')">
                                <b-form-input id="email" type="email" autofocus v-model="form.email" :state="form.errors.state('email')"></b-form-input>
                            </b-form-group>

                            <b-form-group label="Password"
                                          horizontal
                                          label-cols="4"
                                          label-for="password"
                                          label-class="text-md-right"
                                          :invalid-feedback="form.errors.get('password')">
                                <b-form-input id="password" type="password" v-model="form.password" :state="form.errors.state('password')"></b-form-input>
                            </b-form-group>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <b-form-checkbox id="remember"
                                                     v-model="form.remember">
                                        Remember me
                                    </b-form-checkbox>
                                    <b-link class="float-right" to="/reset-password">
                                        Forgot Your Password?
                                    </b-link>
                                    <div class="clearfix"></div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-12 login-form">
                                    <b-btn type="submit" variant="primary">
                                        <i class="fa fa-spinner fa-spin" v-if="form.busy"></i>
                                        Login
                                    </b-btn>
                                    <br>
                                    <span class="ml-2 mr-2">Or</span>
                                    <br>
                                    <a href="/social/facebook" class="btn btn-facebook icon-left"><i class="fa fa-facebook"></i> Login with Facebook</a>
                                    <a href="/social/google" class="btn btn-google-plus icon-left"><i class="fa fa-google"></i> Login with Google</a>
                                </div>
                            </div>
                        </b-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.login-form {
    text-align: center;
    
}
.login-form .btn {
    width:250px;
    margin: 10px;
}
</style>
<script>
    export default {
        data () {
            return {
                form: new window.Form ({
                    email: null,
                    password: null,
                    remember: false
                })
            };
        },
        mounted () {

        },
        computed: {},
        methods: {
            login() {
                App.post('/login', this.form)
                    .then(response => {
                        if (response.data.user.is_admin) {
                            location = '/backend/dashboard';
                        } else {
                            window.location = '/dashboard/settings';
                        }
                    })
            }
        },
        watch: {}
    };
</script>