import Vue from 'vue';
import Router from 'vue-router';

// App Container
import Full from './../containers/Full';

// Front page
import Welcome from './../views/Welcome';

// Dashboard
import Dashboard from './../views/Dashboard';
import ClientsList from './../views/clients/List';
import ClientsSingle from './../views/clients/Single';
import ClientsCreate from './../views/clients/Create';
import ClientsSearch from '../views/clients/Search';
import UserView from './../views/user/View';
import UserSettings from './../views/Settings';

// Plans
import Pricing from './../views/plans/Pricing';
import Billing from './../views/plans/Billing';

// Auth
import Login from './../pages/Login';
import Register from './../pages/Register';
import FinishRegister from './../pages/FinishRegister';
import ResetPassword from './../pages/ResetPassword';

// Subscription
import Subscription from './../views/user/Subscription';

Vue.use(Router);

function operate (guards, from, to, lastNext, i) {
    let guard = guards[i];
    if (guards.length === i + 1) {
        guard(from, to, lastNext)
    } else {
        guard(from, to, function (nextArg) {
            switch (typeof (nextArg)) {
                case 'undefined':
                    operate(guards, from, to, lastNext, i + 1);
                    break;
                case 'object':
                    lastNext(nextArg);
                    break;
                case 'boolean':
                    lastNext(nextArg);
                    break;
                case 'string':
                    lastNext(nextArg);
                    break
            }
        })
    }
}

function GuardsCheck (ListOfGuards) {
    return function (from, to, next) {
        operate(ListOfGuards, from, to, next, 0)
    }
}

function guestMiddleware (to, from, next) {
    if (window.Store.user) {
        next({name: 'Dashboard'})
    }
    next();
}

function authMiddleware (to, from, next) {
    if (!window.Store.user) {
        next({name: 'Login'});
    }
    next();
}

function finishedSignup (to, from, next) {
    if (!window.Store.user.finished_signup && !window.Store.user.is_admin) {
        next({name: 'finish-signup'});
    }
    next();
}

function subscribedMiddleware (to, from, next) {
    let user = window.Store.user;
    if (user.is_admin) {
        next();
        return;
    }
    if (!user || (user && !user.is_free_account && !user.is_on_trial && !user.is_subscribed_to_plan)) {
        next({name: 'subscription'});
    } else {
        next();
    }
}

export default new Router({
    mode: 'history',
    linkActiveClass: 'open active',
    scrollBehavior: () => ({y: 0}),
    routes: [
        {
            path: '/',
            component: Full,
            children: [
                {
                    path: '',
                    name: 'Welcome',
                    component: Welcome,
                },
                {
                    path: '/dashboard',
                    redirect: '/dashboard/clients',
                    name: 'Dashboard',
                    component: Dashboard,
                    beforeEnter: GuardsCheck([authMiddleware, finishedSignup, subscribedMiddleware]),
                    children: [
                        {
                            path: 'clients',
                            name: 'dashboard-clients',
                            component: ClientsList
                        },
                        {
                            path: 'clients/:id/profile',
                            name: 'dashboard-clients-single',
                            component: ClientsSingle
                        },
                        {
                            path: 'clients/create',
                            name: 'dashboard-clients-create',
                            component: ClientsCreate
                        },
                        {
                            path: 'clients/search',
                            name: 'dashboard-clients-search',
                            component: ClientsSearch,
                        },
                        {
                            path: 'clients/import',
                            name: 'dashboard-clients-import',
                            component: require('../views/ImportClients')
                        },
                        {
                            path: 'user/:id',
                            name: 'view-user',
                            component: UserView,
                        },
                        {
                            path: 'settings',
                            name: 'settings',
                            component: UserSettings
                        },
                    ]
                },
                {
                    path: '/plans',
                    redirect: '/plans/pricing',
                    name: 'Plans',
                    component: {
                        render: c => c('router-view'),
                    },
                    children: [
                        {
                            path: 'pricing',
                            name: 'plans-pricing',
                            component: Pricing
                        },
                        {
                            path: ':id/billing',
                            name: 'plans-billing',
                            component: Billing
                        }
                    ]
                },
                {
                    path: '/login',
                    name: 'Login',
                    component: Login,
                    beforeEnter: guestMiddleware,
                },
                {
                    path: '/register',
                    name: 'Register',
                    component: Register,
                    beforeEnter: guestMiddleware,
                },
                {
                    path: '/register/finish',
                    name: 'finish-signup',
                    component: FinishRegister,
                    beforeEnter: GuardsCheck([authMiddleware, function (to, from, next) {
                        if (window.Store.user.finished_signup) {
                            next({
                                name: 'Dashboard'
                            })
                        } else {
                            next();
                        }
                    }]),
                },
                {
                    path: '/reset-password',
                    name: 'reset-password',
                    component: ResetPassword,
                },
                {
                    path: '/subscription',
                    name: 'subscription',
                    component: Subscription,
                    beforeEnter: GuardsCheck([authMiddleware, function (to, from, next) {
                        if (window.Store.user.is_free_account || window.Store.user.is_on_trial || window.Store.user.is_subscribed_to_plan) {
                            next({
                                name: 'Dashboard',
                            });
                        } else {
                            next();
                        }
                    }])
                }
            ]
        },
    ]
});
