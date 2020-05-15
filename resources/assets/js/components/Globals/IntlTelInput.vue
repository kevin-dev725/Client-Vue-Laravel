<template>
    <input class="form-control" :class="{'is-invalid' : hasError}" type="tel" ref="phone" :disabled="disabled">
</template>

<script>
    export default {
        props: {
            value: {
                Type: String,
                default: ''
            },
            hasError: {
                Type: Boolean,
                default: false
            },
            disabled: {
                Type: Boolean,
                default: false
            },
            initialCountry: {
                Type: String,
                default: 'auto'
            },
            extension: {
                Type: [String, Number],
                default: null
            }
        },
        data () {
            return {};
        },
        mounted () {
            this.initIntlTelInput();
        },
        computed: {
            input () {
                return window.$(this.$refs.phone);
            },
            mobileFull () {
                return (this.extension) ? `${this.value} ext. ${this.extension}` : this.value;
            },
            computedId () {
                let text = "";
                let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                for (let i = 0; i < 8; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                return text;
            }
        },
        methods: {
            initIntlTelInput () {
                this.input.intlTelInput({
                    preferredCountries: ['us', 'au'],
                    initialCountry: this.initialCountry,
                    geoIpLookup: cb => {
                        window.$.get('https://ipinfo.io', () => {}, 'jsonp').always( resp => {
                            let countryCode = (resp && resp.country) ? resp.country : '';
                            cb(countryCode);
                        });
                    },
                    utilsScript: '/js/plugins/intl-tel-input/utils.js'
                });

                this.input.change((e) => {
                    let number = this.getNumber(), extension = this.getExtension();

                    this.$emit('input', number);
                    this.$emit('with-extension', extension);
                    // this.input.intlTelInput('setNumber', number);
                });
                this.input.parent().attr('id', this.computedId);
            },
            getNumber () {
                return this.input.intlTelInput('getNumber');
            },
            getExtension () {
                return this.input.intlTelInput('getExtension');
            },
            destroy () {
                return this.input.intlTelInput('destroy');
            }
        },
        watch: {
            value (number) {
                if (number) {
                    this.input.intlTelInput('setNumber', this.mobileFull);
                }
            }
        },
        beforeDestroy () {
            this.destroy();
            $('#'+this.computedId).remove();
        }
    };
</script>

<style lang="scss">
    .intl-tel-input {
        width: 100%;
    }
</style>