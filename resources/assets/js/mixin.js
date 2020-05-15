module.exports = {
    methods: {
        arrayDelete (arr, object, replace = null) {
            if (replace) {
                arr.splice(arr.indexOf(object), 1, replace);
            } else {
                arr.splice(arr.indexOf(object), 1);
            }
        },
        arrayHas (arr, object) {
            return arr.indexOf(object) > -1;
        },
        arrayHasAny (arr, objects) {
            for (let i = 0; i < objects.length; i++) {
                if (this.arrayHas(arr, objects[i])) {
                    return true;
                }
            }
            return false;
        },
        arraySwap (arr, first_index, second_index) {
            let temp = arr[first_index];
            let temp1 = arr[second_index];
            if (first_index < second_index) {
                this.arrayDelete(arr, temp1);
                arr.splice(first_index, 1, temp1);
                arr.splice(second_index, 0, temp);
            } else {
                this.arrayDelete(arr, temp);
                arr.splice(second_index, 1, temp);
                arr.splice(first_index, 0, temp1);
            }
        },
        strip (html) {
            let tmp = document.createElement('DIV');
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || '';
        },
        goTo (name, params = {}, query = {}, hash = '') {
            this.$router.push({name: name, params: params, query: query, hash: hash});
        },
        getAvatar (user) {
            let avatar = '/images/blank-profile-photo.png';

            if (user.social_image_url && !user.avatar_path) {
                let img = user.social_image_url.split('?');
                if (user.social_image_url.search('graph.facebook.com') !== -1) {
                    avatar = `${img[0]}?width=150&height=150`;
                } else if (user.social_image_url.search('google') !== -1) {
                    avatar = `${img[0]}?sz=150`;
                } else {
                    avatar = user.social_image_url;
                }
            } else if (user.avatar_path) {
                avatar = user.avatar_path;
            }

            return avatar;
        },
        notify (type, message) {
            this.$notify({
                group: 'notification',
                type: type,
                title: 'Notification',
                text: message,
                closeable: true,
                duration: 4000
            });
        },
        isEmpty(value) {
            return !value || value.trim() === '';
        }
    }
};
