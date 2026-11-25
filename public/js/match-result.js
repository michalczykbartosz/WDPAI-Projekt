/*
Wpisywanie wyniku meczu z poziomu drabinki. Przycisk "Wpisz wynik" przy meczu
otwiera okno dialogowe z nazwiskami graczy; identyfikator meczu bierzemy z przycisku,
więc nikt nie wpisuje go ręcznie. Wynik idzie przez Fetch API jako JSON, a po
zapisie odświeżamy stronę, żeby pokazać drabinkę z awansem zwycięzcy.
 */
const dialog = document.getElementById('result-dialog');
const form = document.getElementById('result-form');
const generalError = document.getElementById('result-error');

let matchId = null;

if (dialog && form && generalError) {
    document.querySelectorAll('[data-result-match]').forEach((button) => {
        button.addEventListener('click', () => open(button));
    });

    dialog.querySelector('[data-close]').addEventListener('click', () => dialog.close());
    form.addEventListener('submit', submit);
}

function open(button) {
    matchId = button.dataset.resultMatch;
    form.reset();
    clearErrors();

    for (const player of [1, 2]) {
        dialog.querySelector(`[data-player-name="${player}"]`).textContent = button.dataset['player' + player];
    }

    dialog.showModal();
}

async function submit(event) {
    event.preventDefault();
    clearErrors();
    setBusy(true);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let response;
    let data = null;

    try {
        response = await fetch(`/matches/${encodeURIComponent(matchId)}/result`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken,
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form))),
        });

        if ((response.headers.get('Content-Type') ?? '').includes('application/json')) {
            data = await response.json();
        }
    } catch (error) {
        setBusy(false);
        showGeneralError(dialog.dataset.errorNetwork);
        return;
    }

    if (response.ok) {
        // Komunikat o zapisie pokaże się po odświeżeniu (flash z sesji)
        window.location.reload();
        return;
    }

    setBusy(false);

    for (const [field, message] of Object.entries(data?.errors ?? {})) {
        showFieldError(field, message);
    }

    showGeneralError(data?.message ?? dialog.dataset.errorUnknown);
    (form.querySelector('[aria-invalid="true"]') ?? generalError).focus();
}

function setBusy(isBusy) {
    const button = form.querySelector('button[type="submit"]');

    button.disabled = isBusy;
    button.toggleAttribute('aria-busy', isBusy);
    button.dataset.labelIdle ??= button.textContent;
    button.textContent = isBusy ? dialog.dataset.labelSaving : button.dataset.labelIdle;
}

function showGeneralError(message) {
    generalError.textContent = message;
    generalError.hidden = false;
}

function showFieldError(name, message) {
    const field = form.elements[name];

    if (!field) {
        return;
    }

    const error = document.createElement('p');
    error.className = 'form-error';
    error.id = name + '-error';
    error.textContent = message;
    field.insertAdjacentElement('afterend', error);
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', error.id);
}

function clearErrors() {
    generalError.hidden = true;
    generalError.textContent = '';
    // Usuwamy tylko błędy pól – ogólny komunikat (#result-error) ma tę samą klasę i zostaje w DOM
    form.querySelectorAll('.form-error').forEach((error) => {
        if (error !== generalError) {
            error.remove();
        }
    });
    form.querySelectorAll('[aria-invalid]').forEach((field) => {
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
    });
}
