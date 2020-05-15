window.Utils = {
    getFileImageData(input) {
        return new Promise((resolve, reject) => {
            if (input.files && input.files[0]) {
                let ext = $(input).val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) === -1) {
                    alert('Invalid file.');
                    $(input).val(null);
                    reject();
                } else {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        resolve(e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            } else {
                resolve(null);
            }
        });
    },
};
