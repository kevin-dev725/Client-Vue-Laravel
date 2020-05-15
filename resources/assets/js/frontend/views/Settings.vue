<template>
    <div>
        <b-card>
            <h4 slot="header" class="text-center">
                Settings
            </h4>
            <download-app/>
            <h5 class="mt-5">Subscription</h5>
            <p v-if="user.is_free_account">
                Free Forever!
            </p>
            <template v-else>
                <div v-if="user.is_on_trial">
                    <span class="badge badge-warning">On Trial Period</span> <span v-if="trialEndsAt">Subscription needed before {{ trialEndsAt | datetime }}.</span><i v-else class="fa fa-spinner fa-spin"></i>
                    <subscribe-now/>
                </div>
                <template v-else>
                    <div v-if="user.is_subscribed_to_plan">
                        <div v-if="user.subscription.is_on_grace_period">
                            <span class="badge badge-warning">Cancelled</span> <span>Ends on {{ user.subscription.ends_at | date }}.</span>
                            <br/>
                            <b-btn class="mt-2" variant="success" size="sm" @click="resume"><i class="fa fa-check"></i> Resume Subscription</b-btn>
                        </div>
                        <div v-else>
                            <span class="badge badge-success">Subscribed</span> <span v-if="renewsAt">Automatically renews on {{ renewsAt | date }}.</span><i v-else class="fa fa-spinner fa-spin"></i>
                            <br/>
                            <b-btn class="mt-2" variant="danger" size="sm" @click="cancel"><i class="fa fa-times"></i> Cancel Subscription</b-btn>
                        </div>
                    </div>
                    <div v-else>
                        <span class="badge badge-danger">Cancelled</span> <span>Ended on {{ user.subscription.ends_at | date }}.</span>
                        <br/>
                        <b-btn class="mt-2" variant="success" size="sm" @click="resume"><i class="fa fa-check"></i> Resume Subscription</b-btn>
                    </div>
                </template>
            </template>
            <h5 class="mt-5">Update Password</h5>
            <update-password/>
            <b-modal ref="modalCancel" title="Confirmation">
                <div>Are you sure you want to cancel subscription?</div>
                <div slot="modal-footer">
                    <b-btn @click="$refs.modalCancel.hide()" :disabled="cancelling">Cancel</b-btn>
                    <b-btn variant="danger" @click="confirmCancel" :disabled="cancelling"><i class="fa" :class="{'fa-remove': !cancelling, 'fa-spinner fa-spin': cancelling}"></i> Cancel Subscription</b-btn>
                </div>
            </b-modal>
            <b-modal ref="modalResume" title="Confirmation">
                <div>Confirm resume subscription?</div>
                <div slot="modal-footer">
                    <b-btn @click="$refs.modalResume.hide()" :disabled="resuming">Cancel</b-btn>
                    <b-btn variant="success" @click="confirmResume" :disabled="resuming"><i class="fa" :class="{'fa-check': !resuming, 'fa-spinner fa-spin': resuming}"></i> Resume Subscription</b-btn>
                </div>
            </b-modal>
        </b-card>
        <b-card class="mt-3">
            <h4 slot="header" class="text-center">
                Profile
            </h4>
            <profile :user="user"/>
        </b-card>
    </div>
</template>

<script>
    import UpdatePassword from './settings/UpdatePassword';
    import Profile from './settings/Profile';
    import ImportCsv from './settings/ImportCSV';
    import ImportQuickbooks from './settings/ImportQuickbooks';
    import DownloadApp from  './settings/DownloadApp';
    import SubscribeNow from './settings/trial/SubscribeNow'

    export default {
        components: {
            UpdatePassword, Profile, ImportCsv, ImportQuickbooks, DownloadApp, SubscribeNow,
        },
        data() {
            return {
                subscription: null,
                cancelling: false,
                resuming: false,
            };
        },
        computed: {
            user() {
                return window.Store.user;
            },
            renewsAt() {
                if (this.subscription) {
                    return window.moment.unix(this.subscription.current_period_end);
                }
                return null;
            },
            trialEndsAt() {
                if (this.user.is_on_trial) {
                    return window.moment.utc(this.user.trial_ends_at);
                }
                return null;
            }
        },
        mounted() {
            this.getSubscription();
        },
        methods: {
            getSubscription() {
                window.axios.get('/api/v1/auth/user?include=stripe_subscription')
                    .then(response => {
                        this.subscription = response.data.stripe_subscription;
                    })
            },
            cancel() {
                this.$refs.modalCancel.show();
            },
            confirmCancel() {
                if (this.cancelling) {
                    return;
                }
                this.cancelling = true;
                window.axios.post(`/api/v1/subscription/cancel`)
                    .then(() => {
                        window.Store.getUser()
                            .then(() => {
                                this.$refs.modalCancel.hide();
                            })
                            .finally(() => {
                                this.cancelling = false;
                            });
                    })
                    .catch(() => {
                        this.cancelling = true;
                    });
            },
            resume() {
                this.$refs.modalResume.show();
            },
            confirmResume() {
                if (this.resuming) {
                    return;
                }
                this.resuming = true;
                window.axios.post(`/api/v1/subscription/resume`)
                    .then(() => {
                        window.Store.getUser(null, ['stripe_subscription'])
                            .then((user) => {
                                this.subscription = user.stripe_subscription;
                                this.$refs.modalResume.hide();
                            })
                            .finally(() => {
                                this.resuming = false;
                            });
                    })
                    .catch(() => {
                        this.resuming = true;
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .form-update-password, .form-import-csv {
        max-width: 600px;
    }
</style>