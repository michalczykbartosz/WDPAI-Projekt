/*
Reguły walidacji pojedynczego pola. Zwracają klucz komunikatu taki sam jak
w lang/*.php (z opcjonalnymi parametrami) albo null, gdy pole jest poprawne.
To tylko szybka podpowiedź dla użytkownika – ostatecznie dane sprawdza serwer.
 */
const DATETIME_PATTERN = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;

export function validateField(field, form) {
    // Hasła porównujemy dokładnie, resztę pól serwer i tak przycina (trim)
    const value = field.type === 'password' ? field.value : field.value.trim();

    if (value === '') {
        if (field.validity.badInput) {
            return { key: 'validation.datetime.invalid' };
        }

        return field.required ? { key: 'validation.required' } : null;
    }

    if (field.type === 'email' && field.validity.typeMismatch) {
        return { key: 'validation.email.invalid' };
    }

    // Długość w znakach, a nie w jednostkach UTF-16 – tak jak mb_strlen() w PHP
    const length = [...value].length;

    if (field.minLength > 0 && length < field.minLength) {
        return { key: 'validation.too_short', params: { min: field.minLength } };
    }

    if (field.maxLength > 0 && length > field.maxLength) {
        return { key: 'validation.too_long', params: { max: field.maxLength } };
    }

    if (field.type === 'datetime-local') {
        return validateDateTime(field, value, form);
    }

    const matchName = field.dataset.match;

    if (matchName && form.elements[matchName] && field.value !== form.elements[matchName].value) {
        return { key: field.dataset.matchMessage };
    }

    return null;
}

function validateDateTime(field, value, form) {
    const date = parseDateTime(value);

    if (date === null) {
        return { key: 'validation.datetime.invalid' };
    }

    if (field.dataset.future && date <= new Date()) {
        return { key: field.dataset.future };
    }

    const otherName = field.dataset.notAfter;
    const other = otherName && form.elements[otherName] ? parseDateTime(form.elements[otherName].value) : null;

    if (other !== null && date > other) {
        return { key: field.dataset.notAfterMessage };
    }

    return null;
}

function parseDateTime(value) {
    if (!DATETIME_PATTERN.test(value)) {
        return null;
    }

    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? null : date;
}
