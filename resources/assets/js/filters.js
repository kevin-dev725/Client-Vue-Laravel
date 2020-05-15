Vue.filter('concat', (values, glue = ',') => {
    let s = '';
    values.forEach(value => {
        if (s !== '') {
            s += glue + ' ';
        }
        s += value;
    });
    return s;
});
/**
 * Format the given date.
 */
Vue.filter('date', value => {
    return moment.utc(value).local().format('MMMM Do, YYYY')
});

/**
 * Format the given date.
 */
Vue.filter('month_year', value => {
    return moment.utc(value).local().format('MMMM YYYY')
});

Vue.filter('utc_from_now', function (value) {
    return moment.utc(value).fromNow();
});

/**
 * Format the given date as a timestamp.
 */
Vue.filter('datetime', value => {
    return moment.utc(value).local().format('MMMM Do, YYYY h:mm A');
});

Vue.filter('utc_time', value =>{
    return moment.utc(value).format('hh:mm a');
});


Vue.filter('local_date', value =>{
    return moment.utc(value).local().format('MMMM Do, YYYY');
});
Vue.filter('local_time', value =>{
    return moment.utc(value).local().format('hh:mm a');
});


/**
 * Format the given date into a relative time.
 */
Vue.filter('relative', value => {
    moment.locale('en', {
        relativeTime : {
            future: "in %s",
            past:   "%s",
            s:  "1s",
            m:  "m",
            mm: "%dm",
            h:  "1h",
            hh: "%dh",
            d:  "1d",
            dd: "%dd",
            M:  "1m",
            MM: "%dm",
            y:  "1y",
            yy: "%dy"
        }
    });

    return moment.utc(value).local().fromNow();
});

Vue.filter('truncate', function(value, length) {
    if (!value) return;

    if(value.length < length) {
        return value;
    }

    length = length - 3;

    return value.substring(0, length) + '...';
});

Vue.filter('full_address', (data) => {
    if (!data.street_address) {
        return '';
    }
    return data.street_address + ', ' + data.city + ', ' + data.state + ' ' + data.postal_code;
});

import * as ClientType from './constants/client-type';

Vue.filter('client_name', (data) => {
    if (data.client_type === ClientType.CLIENT_TYPE_ORGANIZATION) {
        return data.organization_name;
    }
    return data.name;
});

Vue.filter('date_format', (value, format = 'MM/DD/YYYY') => {
    return moment.utc(value).local().format(format);
});

Vue.filter('format_review_name', (value) => {
    return value.first_name + ' ' + value.last_name.charAt(0) + '.';
});

Vue.filter('appendRequired', (text, comp) => {
    if (comp) {
        return text + '*'
    }
    return text;
});

Vue.filter('titleCase', function (str) {
    return str.split('_').map(function (item) {
        return item.charAt(0).toUpperCase() + item.substring(1);
    }).join(' ');
});
