module.exports = {
    /**
     * Helper method for making POST HTTP requests.
     */
    post: function (uri, form, beforeSend = null) {
        return window.App.sendForm('post', uri, form, beforeSend);
    },
    postData: function (uri, form, beforeSend = null) {
        return window.App.sendFormData('post', uri, form, beforeSend);
    },

    get(uri, form){
        return window.App.sendForm('get', uri, form);
    },


    /**
     * Helper method for making PUT HTTP requests.
     */
    put: function (uri, form, beforeSend = null) {
        return window.App.sendForm('put', uri, form, beforeSend);
    },
    putData: function (uri, form, beforeSend = null) {
        return window.App.sendFormData('post', uri, form, form_data => {
            form_data.append('_method', 'put');
            if (beforeSend) {
                beforeSend(form_data);
            }
        });
    },


    /**
     * Helper method for making DELETE HTTP requests.
     */
    delete: function (uri, form) {
        return window.App.sendForm('delete', uri, form);
    },


    /**
     * Send the form to the back-end server.
     *
     * This function will clear old errors, update "busy" status, etc.
     */
    sendFormData: function (method, uri, form, beforeSend = null) {
        return new Promise(function (resolve, reject) {
            form.startProcessing();

            let form_data = form.getFormData();
            if (beforeSend){
                beforeSend(form_data);
            }
            window.axios[method](uri, form_data)
                .then(function (response) {
                    form.errors.validated = true;
                    form.finishProcessing();
                    resolve(response);
                })
                .catch(function (errors) {
                    form.busy = false;
                    if (errors.response) {
                        if (errors.response.data && errors.response.data.errors) {
                            form.errors.validated = false;
                            form.errors.set(errors.response.data.errors);
                        }
                        reject(errors.response.data);
                    } else {
                        form.errors.validated = false;
                        form.errors.set(errors.message);
                        reject(errors.message);
                    }
                })
            ;
        });
    },

    sendForm: function (method, uri, form, beforeSend = null) {
        return new Promise(function (resolve, reject) {
            form.startProcessing();
            let data = JSON.parse(JSON.stringify(form));
            if (beforeSend) {
                beforeSend(data);
            }
            window.axios[method](uri, data)
                .then(function (response) {
                    form.errors.validated = true;
                    form.finishProcessing();
                    resolve(response);
                })
                .catch(function (errors) {
                    form.busy = false;
                    if (errors.response) {
                        if (errors.response.data && errors.response.data.errors) {
                            form.errors.validated = false;
                            form.errors.set(errors.response.data.errors);
                        }
                        reject(errors.response);
                    } else {
                        form.errors.validated = false;
                        form.errors.set(errors.message);
                        reject(errors.message);
                    }
                })
            ;
        });
    }


};
