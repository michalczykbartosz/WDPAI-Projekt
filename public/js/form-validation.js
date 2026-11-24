/*
Walidacja formularzy po stronie przeglądarki dla formularzy z atrybutem data-validate.
Błędy wyświetlamy tym samym markupem co serwer (p.form-error, aria-invalid,
aria-describedby), a komunikaty bierzemy z tych samych słowników co PHP.
 */
import { validateField } from './validation/rules.js';

const messages = readMessages();

document.querySelectorAll('form[data-validate]').forEach(setUpForm);

function readMessages() {
    const element = document.getElementById('validation-messages');

    try {
        return element ? JSON.parse(element.textContent) : {};
    } catch (e) {
        return {};
    }
}

function translate({ key, params = {} }) {
    let text = messages[key] ?? key;

    for (const [name, value] of Object.entries(params)) {
        text = text.replaceAll('{' + name + '}', String(value));
    }

    return text;
}

function isField(element) {
    return ['INPUT', 'SELECT', 'TEXTAREA'].includes(element.tagName)
        && element.name !== ''
        && !element.disabled
        && !['hidden', 'submit', 'button'].includes(element.type);
}

function setUpForm(form) {
    // Pola zmienione przez użytkownika. Błąd z serwera (np. zajęty e-mail) przy
    // niezmienionym polu zostaje, bo przeglądarka nie może go sama sprawdzić.
    const dirty = new Set();
    const fields = () => [...form.elements].filter(isField);

    form.addEventListener('input', (event) => {
        if (!isField(event.target)) {
            return;
        }

        dirty.add(event.target.name);

        // Poprawiamy błąd na bieżąco dopiero, gdy pole już go pokazuje
        if (event.target.getAttribute('aria-invalid') === 'true') {
            check(event.target);
        }

        // Pola zależne, np. powtórzone hasło albo termin zapisów względem startu
        fields()
            .filter((field) => dirty.has(field.name)
                && (field.dataset.match === event.target.name || field.dataset.notAfter === event.target.name))
            .forEach(check);
    });

    form.addEventListener('focusout', (event) => {
        // Przy przejściu prosto na przycisk wysyłania nie walidujemy tutaj: nowy komunikat
        // przesunąłby przycisk spod kursora i kliknięcie by przepadło. Sprawdzi to submit.
        const next = event.relatedTarget;

        if (next && next.form === form && next.type === 'submit') {
            return;
        }

        if (isField(event.target) && dirty.has(event.target.name)) {
            check(event.target);
        }
    });

    form.addEventListener('submit', (event) => {
        const invalid = fields().filter((field) => !check(field));

        if (invalid.length > 0) {
            event.preventDefault();
            invalid[0].focus();
        }
    });

    function check(field) {
        const error = validateField(field, form);

        if (error !== null) {
            showError(field, translate(error));
            return false;
        }

        if (dirty.has(field.name)) {
            clearError(field);
        }

        return true;
    }
}

function showError(field, text) {
    const errorId = field.name + '-error';
    let element = document.getElementById(errorId);

    if (!element) {
        element = document.createElement('p');
        element.className = 'form-error';
        element.id = errorId;
        field.insertAdjacentElement('afterend', element);
    }

    element.textContent = text;
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', errorId);
}

function clearError(field) {
    const errorId = field.name + '-error';
    document.getElementById(errorId)?.remove();

    if (field.getAttribute('aria-describedby') === errorId) {
        field.removeAttribute('aria-describedby');
        field.removeAttribute('aria-invalid');
    }
}
