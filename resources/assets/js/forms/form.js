window.Form = function (data, veeErrors = null) {
    let form = this;

    window.$.extend(this, data);

    /**
     * Create the form error helper instance.
     */
    this.veeErrors = veeErrors;
    this.errors = new window.FormErrors(this.veeErrors);

    this.busy = false;
    this.successful = false;

    this.getFormData = function () {
        let form_data = new FormData();
        for (let key in this) {
            if (this.hasOwnProperty(key) && typeof this[key] !== 'function') {
                let data = this[key];
                if (data === null) {
                    continue;
                }
                if (data instanceof Array) {
                    let arr = data;
                    for (let i = 0; i < arr.length; i++) {
                        form_data.append(key + '[]', arr[i]);
                    }
                } else {
                    form_data.append(key, this[key]);
                }
            }
        }
        return form_data;
    };

    this.feedback = function (field) {
        return {
            'is-danger': this.errors.has(field),
        };
    };

    function htmlEncode(value) {
        return window.$('<div/>').text(value).html();
    }

    this.help = function (field) {
        if (!this.errors.has(field)) return;
        return `<small class="form-help u-color-danger">${this.errors.get(field)}</small>`;
    };

    /**
     * Start processing the form.
     */
    this.startProcessing = function () {
        form.errors.forget();
        form.busy = true;
        form.successful = false;
    };

    /**
     * Finish processing the form.
     */
    this.finishProcessing = function (success = true) {
        form.busy = false;
        form.successful = success;
    };

    /**
     * Reset the errors and other state for the form.
     */
    this.resetStatus = function () {
        form.errors.forget();
        form.busy = false;
        form.successful = false;
    };


    /**
     * Set the errors on the form.
     */
    this.setErrors = function (errors) {
        form.busy = false;
        form.errors.set(errors);
    };

    this.clearFields = function () {
        window.$.extend(this, data);
    };
};
