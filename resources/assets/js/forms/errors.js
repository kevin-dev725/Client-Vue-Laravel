window.FormErrors = function (veeErrors = null) {
    this.errors = {};
    this.veeErrors = veeErrors;

    this.validated = null;

    /**
     * Determine if the collection has any errors.
     */
    this.hasErrors = function () {
        return ! window._.isEmpty(this.errors) || (this.veeErrors && this.veeErrors.any());
    };


    /**
     * Determine if the collection has errors for a given field.
     */
    this.has = function (field) {
        return window._.indexOf(window._.keys(this.errors), field) > -1 || (this.veeErrors && this.veeErrors.has(field));
    };


    /**
     * Get all of the raw errors for the collection.
     */
    this.all = function () {
        return this.errors;
    };


    /**
     * Get all of the errors for the collection in a flat array.
     */
    this.flatten = function () {
        return window._.flatten(window._.toArray(this.errors));
    };


    /**
     * Get the first error message for a given field.
     */
    this.get = function (field) {
        if (this.veeErrors && this.veeErrors.has(field)) {
            return this.veeErrors.first(field);
        }
        if (this.has(field)) {
            return this.errors[field][0];
        }
    };

    this.allErrorsField = function (field) {
        if (this.has(field)){
            return this.errors[field];
        }
    };

    /**
     * Set the raw errors for the collection.
     */
    this.set = function (errors) {
        if (typeof errors === 'object') {
            this.errors = errors;
        } else {
            this.errors = {'form': ['Something went wrong. Please try again or contact customer support.']};
        }
    };


    /**
     * Forget all of the errors currently in the collection.
     */
    this.forget = function () {
        this.errors = {};
    };

    this.add = function (key, error) {
        Vue.set(this.errors, key, [error]);
    };

    this.state = function (field) {
        if (!this.validated && this.has(field)) {
            return false;
        }
        return null;
    };
};
