/*
Zapis na turniej jednym kliknięciem przez Fetch API (POST, odpowiedź JSON).
Podczas żądania przycisk jest zablokowany (stan ładowania), a po odpowiedzi
aktualizujemy licznik miejsc i listę zawodników bez przeładowania strony.
 */
const button = document.getElementById('btn-register');
const feedback = document.getElementById('register-feedback');

// Błędy, po których ponowne kliknięcie nic nie zmieni
const FINAL_ERRORS = ['registration_closed', 'tournament_full', 'already_registered', 'not_found'];

if (button && feedback && button.dataset.url) {
    button.addEventListener('click', register);
}

async function register() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    setLoading(true);
    showFeedback('', null);

    let response;
    let data;

    try {
        response = await fetch(button.dataset.url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken,
            },
        });
        data = await readJson(response);
    } catch (e) {
        setLoading(false);
        showFeedback(button.dataset.errorNetwork, 'error');
        return;
    }

    if (!response.ok) {
        const finalError = data !== null && FINAL_ERRORS.includes(data.error);
        setLoading(false, finalError);
        showFeedback(data?.message ?? button.dataset.errorUnknown, 'error');
        return;
    }

    button.removeAttribute('aria-busy');
    button.textContent = button.dataset.labelDone;
    showFeedback(data.message, 'success');
    updateRegisteredCount(data.registeredPlayers);
    addPlayerRow(data.player.displayName);
}

/** Zwraca obiekt z odpowiedzi albo null, gdy serwer nie odesłał poprawnego JSON-a. */
async function readJson(response) {
    const contentType = response.headers.get('Content-Type') ?? '';

    if (!contentType.includes('application/json')) {
        return null;
    }

    try {
        return await response.json();
    } catch (e) {
        return null;
    }
}

function setLoading(isLoading, keepDisabled = false) {
    button.disabled = isLoading || keepDisabled;

    if (isLoading) {
        button.dataset.labelIdle ??= button.textContent;
        button.textContent = button.dataset.labelLoading;
        button.setAttribute('aria-busy', 'true');
    } else {
        button.textContent = button.dataset.labelIdle ?? button.textContent;
        button.removeAttribute('aria-busy');
    }
}

function showFeedback(text, type) {
    feedback.textContent = text;
    feedback.classList.toggle('register-feedback--success', type === 'success');
    feedback.classList.toggle('register-feedback--error', type === 'error');
}

function updateRegisteredCount(count) {
    const counter = document.getElementById('registered-count');

    if (counter && Number.isInteger(count)) {
        counter.textContent = String(count);
    }
}

function addPlayerRow(displayName) {
    const table = document.getElementById('players-table');
    const empty = document.getElementById('players-empty');

    if (!table || typeof displayName !== 'string') {
        return;
    }

    const row = table.tBodies[0].insertRow();

    // textContent zamiast innerHTML – nazwa gracza nie zostanie potraktowana jak HTML
    for (const text of ['–', displayName, '–']) {
        row.insertCell().textContent = text;
    }

    row.cells[0].className = 'rank-cell';
    table.hidden = false;

    if (empty) {
        empty.hidden = true;
    }
}
