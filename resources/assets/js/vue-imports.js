window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

import Vue2Filters from 'vue2-filters';

Vue.use(Vue2Filters);

import VueProgressBar from 'vue-progressbar';

Vue.use(VueProgressBar, {
    color: '#20a8d8',
    failedColor: 'red',
    thickness: '3px',
});

import 'vue-awesome/icons';


Vue.mixin(require('./mixin'));

import Notifications from 'vue-notification';

Vue.use(Notifications);

import VueScrollTo from 'vue-scrollto';

Vue.use(VueScrollTo, {
    container: 'body',
    duration: 500,
    easing: 'ease',
    offset: 0,
    cancelable: true,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
});