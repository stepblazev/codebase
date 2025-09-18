window.formatPrice = function (value) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 2
    }).format(value);
}

window.objectToFormData = function (obj, form = new FormData(), namespace = '') {
    for (let key in obj) {
        if (obj.hasOwnProperty(key)) {
            const value = obj[key];
            const formKey = namespace ? `${namespace}[${key}]` : key;

            if (typeof value === 'object' && !(value instanceof File)) {
                objectToFormData(value, form, formKey);
            } else {
                form.append(formKey, value);
            }
        }
    }
    return form;
}

window.debounce = function (func, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

window.parseDate = function (str) {
    const [day, month, year] = str.split('.');
    return new Date(year, month - 1, day);
}