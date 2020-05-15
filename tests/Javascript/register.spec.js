import {mount} from '../../node_modules/@vue/test-utils/dist/vue-test-utils';
import Register from '../../resources/assets/js/frontend/pages/Register';

window.Store = new window.Vue({
    data: {
        config: {
            app: {
                env: 'testing'
            },
            services: {
                stripe: {
                    plan: {
                        price: 100,
                    }
                }
            },
            keys: {
                stripe: process.env.STRIPE_KEY
            },
            settings: {
                free_account_on_register_enabled: false,
            }
        }
    }
});

describe('Register', () => {
    let wrapper;
    beforeEach(() => {
        moxios.install(window.axios);
        wrapper = mount(Register);
    });
    afterEach(() => {
        moxios.uninstall(window.axios);
    });
    moxios.stubRequest('/api/v1/company', {
        data: []
    });
    it('says that it is a register component', () => {
        expect(wrapper.html()).toContain('Login Details');
        expect(wrapper.html()).toContain('Contact Details');
        expect(wrapper.html()).toContain('Membership');
        expect(wrapper.html()).not.toContain('membership is free!');
        expect(wrapper.html()).toContain('Privacy Policy / Terms of Use');
    });
    it('says that it hides membership section when free account is enabled', () => {
        window.Store.config.settings.free_account_on_register_enabled = true;
        expect(wrapper.html()).toContain('Login Details');
        expect(wrapper.html()).toContain('Contact Details');
        expect(wrapper.html()).not.toContain('Membership');
        expect(wrapper.html()).toContain('membership is free!');
        expect(wrapper.html()).toContain('Privacy Policy / Terms of Use');
    });
    it('shows form inputs', () => {
        // expect(wrapper.contains('form input[type=radio][name=account_type][value=individual]')).toBe(true);
        // expect(wrapper.contains('form input[type=radio][name=account_type][value=company]')).toBe(true);
        expect(wrapper.contains('form input#email[type=email]')).toBe(true);
        expect(wrapper.contains('form input#password[type=password]')).toBe(true);
        expect(wrapper.contains('form input#password_confirmation[type=password]')).toBe(true);
        expect(wrapper.contains('form input#first_name[type=text]')).toBe(true);
        expect(wrapper.contains('form input#last_name[type=text]')).toBe(true);
        expect(wrapper.contains('form input#middle_name[type=text]')).toBe(true);
        expect(wrapper.contains('form input#street[type=text]')).toBe(true);
        expect(wrapper.contains('form input#street2[type=text]')).toBe(true);
        expect(wrapper.contains('form input#city[type=text]')).toBe(true);
        expect(wrapper.contains('form input#postal_code[type=text]')).toBe(true);
        expect(wrapper.contains('form input#phone[type=tel]')).toBe(true);
        expect(wrapper.contains('form input#alt_phone[type=tel]')).toBe(true);
    });
    it('shows submit button', () => {
        expect(wrapper.contains('form button[type=submit]')).toBe(true);
    });
    it('submits data after clicking submit button', (done) => {
        wrapper.vm.register();
        moxios.wait(function () {
            let request = moxios.requests.mostRecent();
            request.respondWith({
                status: 200,
                response: {
                    id: 1,
                    email: 'test@user.com'
                }
            }).then(function () {
                expect(wrapper.vm.form.successful).toBe(true);
                done()
            })
        });
    });
});