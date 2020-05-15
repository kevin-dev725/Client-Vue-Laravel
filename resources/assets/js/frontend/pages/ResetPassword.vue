<template>
    <b-container class="pt-5">
        <b-row align-h="center">
            <b-col md="8">
                <b-card>
                    <div slot="header">
                        Reset Password
                    </div>
                    <b-alert show variant="success" v-if="success_request && !verifying_token" class="text-center">
                        <strong>Password reset token</strong> has been sent to your email <strong>{{form.email}}</strong><br>
                        Please check your email for the token.
                    </b-alert>
                    <b-alert show variant="success" v-if="password_change_success" class="text-center">
                        <strong>Congratulations!</strong> You have successfully changed your password! <br>
                        <b-btn class="mt-3" variant="primary" to="/login">Login to your account</b-btn>
                    </b-alert>

                    <h4 v-if="verifying_token" class="text-center">
                        <icon name="spinner" spin/>
                        Verifying Token
                    </h4>
                    <b-form @submit.prevent="submit" v-if="!verifying_token && !password_change_success">
                        <b-form-group
                                v-if="!show_token_field"
                                label="Email Address:"
                                label-for="email"
                                horizontal
                                label-cols="4"
                                label-class="text-md-right"
                                :invalid-feedback="form.errors.get('email')" >
                            <b-form-input id="email"
                                          type="email"
                                          v-model="form.email"
                                          :state="form.errors.state('email')"
                                          :disabled="token_verified"></b-form-input>
                        </b-form-group>

                        <b-form-group
                                v-if="show_token_field"
                                label="Token:"
                                label-for="token"
                                horizontal
                                label-cols="4"
                                label-class="text-md-right"
                                :invalid-feedback="form.errors.get('token')">
                            <b-form-input id="token"
                                          type="text"
                                          v-model="form.token"
                                          :state="form.errors.state('token')"></b-form-input>
                        </b-form-group>

                        <b-form-group
                                v-if="token_verified"
                                label="Password:"
                                label-for="password"
                                horizontal
                                label-cols="4"
                                label-class="text-md-right"
                                :invalid-feedback="form.errors.get('password')">
                            <b-form-input id="password"
                                          type="password"
                                          v-model="form.password"
                                          :state="form.errors.state('password')"></b-form-input>
                        </b-form-group>

                        <b-form-group
                                v-if="token_verified"
                                label="Confirm Password:"
                                label-for="password_confirmation"
                                horizontal
                                label-cols="4"
                                label-class="text-md-right"
                                :invalid-feedback="form.errors.get('password_confirmation')">
                            <b-form-input id="password_confirmation"
                                          type="password"
                                          v-model="form.password_confirmation"
                                          :state="form.errors.state('password_confirmation')"></b-form-input>
                        </b-form-group>

                        <b-form-group class="mb-0">
                            <b-form-row>
                                <b-col md="8" offset-md="4">
                                    <b-btn type="submit" variant="primary">
                                        <icon name="spinner" spin v-if="form.busy"/>
                                        Submit
                                    </b-btn>
                                </b-col>
                            </b-form-row>
                        </b-form-group>
                    </b-form>
                </b-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    export default {
        data () {
            return {
                form: new Form ({
                    email: this.$route.query.email,
                    token: this.$route.query.token,
                    password: null,
                    password_confirmation: null,
                }),
                success_request: false,
                show_token_field: false,
                token_verified: false,
                verifying_token: false,
                password_change_success: false,
            };
        },
        mounted () {
            Vue.set(this.form, 'email', this.$route.query.email);
            Vue.set(this.form, 'token', this.$route.query.token);
            if (this.queryToken && this.queryEmail) {
                this.submit();
            }
        },
        computed: {
            queryEmail () {
                return this.$route.query.email;
            },
            queryToken () {
                return this.$route.query.token;
            },
            queryUrl () {
                const url = {
                    request: 'request-reset',
                    verify: 'verify-token',
                    reset: 'reset'
                };

                if (!this.form.token) {
                    return url.request;
                }

                return (this.queryToken || this.form.token) && !this.token_verified ? url.verify : url.reset;
            }
        },
        methods: {
            submit () {
                if (this.queryUrl === 'verify-token') {
                    this.verifying_token = true;
                }
                console.log(this.queryUrl);
                window.App.post(`/api/v1/auth/password/${this.queryUrl}`, this.form)
                  .then(response => {
                      if (this.queryUrl === 'reset') {
                          this.password_change_success = true;
                          console.log('reset');
                          return;
                      }
                      if (this.queryUrl === 'request-reset') {
                          this.success_request = true; console.log(1);
                          this.show_token_field = true; console.log(2);
                          this.token_verified = false; console.log(3);
                          this.form.token = null;
                          console.log('request-reset');
                          return;
                      }
                      if (this.queryUrl === 'verify-token') {
                          this.token_verified = true;
                          this.verifying_token = false;
                          this.success_request = false;
                          this.show_token_field = false;
                          console.log('verify-token');
                      }
                  })
                  .catch(error => {
                      if (this.queryUrl === 'verify-token') {
                          if (this.form.errors.has('email')) {
                              console.log('has error');
                              this.form.token = null;
                          }
                          this.show_token_field = !this.form.errors.has('email');
                      }
                  })
                  .finally(() => {
                      this.verifying_token = false;
                  });
            }
        }
    };
</script>