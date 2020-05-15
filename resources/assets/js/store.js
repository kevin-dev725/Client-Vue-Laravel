import Vue from 'vue';

export default new Vue({
    data: {
        user: App.state.user,
        config: App.state.config,
        csrf: App.state.csrf,
        app: null,
        countries: [],
        states: []
    },
    computed: {
        countrySelectOptions() {
            return this.countries.map(item => {
                return {
                    value: item.id,
                    text: item.name
                };
            });
        }
    },
    methods: {
        init() {
            this.getCountries();
            this.getStates();
        },
        logout () {
            axios.post('/logout')
              .then(response => {
                  location.reload();
              });
        },
        getCountries() {
            window.axios.get(`/api/v1/country`)
                .then(response => {
                    this.countries = response.data.data;
                });
        },
        getStates() {
            window.axios.get(`/api/v1/state?include=counties`)
                .then(response => {
                    this.states = response.data.data.map(state => ({
                        value: state.iso_3166_2,
                        text: state.name,
                        counties: state.counties.data.map(county => ({
                            value: county.name,
                            text: county.name,
                        }))
                    }))
                });
        },
        getUser (callback = null, includes = []) {
            includes.push('subscription');
            return new Promise((resolve, reject) => {
                window.axios.get(`/api/v1/auth/user`, {
                    params: {
                        include: includes.join(',')
                    }
                })
                    .then(response => {
                        if (!this.user) {
                            this.user = response.data;
                        } else {
                            $.extend(this.user, response.data);
                        }
                        if (callback) {
                            callback();
                        }
                        resolve(this.user);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getProgress() {
            if (!this.app || this.app.$children.length === 0) {
                return null;
            }
            return this.app.$children[0].$Progress;
        },
        startLoading() {
            if (!this.getProgress()) {
                return;
            }
            this.getProgress().start();
        },
        finishLoading() {
            if (!this.getProgress()) {
                return;
            }
            this.getProgress().finish();
        },
        failLoading() {
            if (!this.getProgress()) {
                return;
            }
            this.getProgress().fail();
        },
        getGeoIp() {
            return new Promise((resolve, reject) => {
                $.getJSON('https://freegeoip.net/json/')
                    .then(response => {
                        resolve(response);
                    })
                    .fail(response => {
                        reject(response);
                    });
            });
        }
    }
})