window.HttpApi = {
    base: location.origin + '/api',
    get: function (action, data = {}) {
        const params = new URLSearchParams();
        params.append('action', action);

        for (let key of Object.keys(data)) {
            params.append(key, data[key]);
        }

        return fetch(`${this.base}?${params}`, {
            method: 'GET',
            credentials: "same-origin",
        });
    },
    post: function (action, formData = new FormData()) {
        if (!(formData instanceof FormData)) {
            formData = window.objectToFormData(formData);
        }
        return fetch(`${this.base}?action=${action}`, {
            method: 'POST',
            credentials: "same-origin",
            body: formData
        });
    }
}