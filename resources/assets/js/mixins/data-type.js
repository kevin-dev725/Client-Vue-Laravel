export default {
    methods: {
        isString(value) {
            return typeof value === 'string';
        },
        isArray(value) {
            return Array.isArray(value);
        },
        isObject(value) {
            return typeof value === 'object';
        },
    }
}
