import Vue from 'vue';
import Router from 'vue-router';
// Containers
import Full from './../containers/Full';
// Views
import Dashboard from './../views/Dashboard';
import Users from './../views/Users';
// Users
import UserList from './../views/users/List';
import UserSingle from './../views/users/Edit';
// Clients
import ClientsList from '../views/clients/List';
import ClientEdit from '../views/clients/Edit';
// Reviews
import ReviewsList from '../views/reviews/List';
// Plans
// Auth
import Login from './../views/pages/Login';
// Error Pages

Vue.use(Router);

function guestMiddleware (to, from, next) {
    if (window.Store.user) {
        next({name: 'Dashboard'})
    }
    next();
}

function authMiddleware (to, from, next) {
    if (window.Store.user) {
        if (window.Store.user.is_admin) {
            next();
        } else {
            location = '/';
        }
    } else {
        next({name: 'Login'});
    }
}

export default new Router({
    mode: 'history',
    linkActiveClass: 'open active',
    scrollBehavior: () => ({y: 0}),
    routes: [
        {
            path: '/backend/',
            redirect: '/backend/dashboard',
            name: 'Home',
            component: Full,
            beforeEnter: authMiddleware,
            children: [
                {
                    path: 'dashboard',
                    name: 'Dashboard',
                    component: Dashboard
                },
                {
                    path: 'users',
                    component: {
                        render: c => c('router-view'),
                    },
                    meta: {
                        label: 'Users'
                    },
                    children: [
                        {
                            path: '',
                            name: 'UserList',
                            component: UserList,
                            meta: {
                                label: 'List'
                            }
                        },
                        {
                            path: ':id',
                            name: 'UserSingle',
                            component: UserSingle,
                            meta: {
                                label: 'Edit'
                            }
                        },
                        {
                            path: ':userId/reviews',
                            name: 'UserReviewsList',
                            component: ReviewsList,
                            meta: {
                                label: 'Reviews'
                            }
                        },
                        {
                            path: ':id/clients',
                            name: 'UserClientsList',
                            component: ClientsList,
                            meta: {
                                label: 'Clients List'
                            }
                        },
                        {
                            path: ':id/clients/:clientId/edit',
                            name: 'UserClientEdit',
                            component: ClientEdit,
                            meta: {
                                label: 'Client Edit'
                            }
                        },
                        {
                            path: ':userId/clients/:clientId/review',
                            name: 'ClientReviewsList',
                            component: ReviewsList,
                            meta: {
                                label: 'Client Reviews'
                            }
                        }
                    ]
                },
                {
                    path: 'flagged-phrases',
                    component: {
                        render: c => c('router-view'),
                    },
                    meta: {
                        label: 'Flagged Phrases'
                    },
                    children: [
                        {
                            path: '',
                            name: 'FlaggedPhraseList',
                            component: require('../views/flagged-phrase/List'),
                            meta: {
                                label: 'List'
                            }
                        },
                    ]
                },
                {
                    path: 'flagged-reviews',
                    component: require('../views/FlaggedReviews'),
                    meta: {
                        label: 'Flagged Reviews'
                    },
                },
                {
                    path: 'lien',
                    component: require('../views/lien/Index'),
                    meta: {
                        label: 'Lien Records'
                    },
                },
                /*{
                    path: 'subscriptions',
                    component: Plans,
                    meta: {
                        label: 'Subscription Plans'
                    },
                    children: [
                        {
                            path: '',
                            name: 'PlanList',
                            component: PlanList,
                            meta: {
                                label: 'List'
                            }
                        }
                    ]
                }*/
            ]
        },
        {
            path: '/backend/login',
            name: 'Login',
            component: Login,
            beforeEnter: guestMiddleware
        }
    ]
});
