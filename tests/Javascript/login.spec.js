import {mount} from '../../node_modules/@vue/test-utils/dist/vue-test-utils';
import Login from '../../resources/assets/js/frontend/pages/Login';

describe('Login', () => {
    it('says that it is a login component', () => {
        let wrapper = mount(Login);
        expect(wrapper.html()).toContain('Login Form');
    });
    it('shows login form inputs', () => {
        let wrapper = mount(Login);
        expect(wrapper.contains('form input#email[type=email]')).toBe(true);
        expect(wrapper.contains('form input#password[type=password]')).toBe(true);
        expect(wrapper.contains('form input#remember[type=checkbox]')).toBe(true);
    });
    it('shows login form submit button', () => {
        let wrapper = mount(Login);
        expect(wrapper.contains('form button[type=submit]')).toBe(true);
    });
    it('shows forgot password link', () => {
        let wrapper = mount(Login);
        expect(wrapper.find('form a').text()).toBe('Forgot Your Password?');
    });
});