// Vendors
import Icon from 'vue-awesome/components/Icon';
Vue.component('icon', Icon);

// App
import Rating from './components/Globals/Rating';
Vue.component('rating', Rating);
import IntlTelInput from './components/Globals/IntlTelInput';
Vue.component('intl-tel-input', IntlTelInput);
import Search from './components/Globals/Search';
Vue.component('search', Search);

import VueFormWizard from 'vue-form-wizard';
import 'vue-form-wizard/dist/vue-form-wizard.min.css';
Vue.use(VueFormWizard);
