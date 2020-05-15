export default {
    items: [
        {
            name: 'Dashboard',
            url: '/backend/dashboard',
            icon: 'icon-speedometer',
        },
        {
            title: true,
            name: 'Users',
            class: '',
            wrapper: {
                element: '',
                attributes: {}
            }
        },
        {
            name: 'Users',
            url: '/backend/users',
            icon: 'icon-people',
        },
        {
            name: 'Flagged Words/Phrases',
            url: '/backend/flagged-phrases',
            icon: 'fa fa-exclamation-triangle',
        },
        {
            name: 'Flagged Reviews',
            url: '/backend/flagged-reviews',
            icon: 'fa fa-exclamation-triangle',
        },
        {
            name: 'Lien Records',
            url: '/backend/lien',
            icon: 'fa fa-file-o',
        },
        {
            name: 'Logout',
            icon: 'fa fa-lock',
            callback() {
                window.Store.logout();
            }
        },

        /*{
            name: 'Subscription Plans',
            url: '/backend/subscriptions',
            icon: 'icon-wallet',
        },*/
    ]
};
