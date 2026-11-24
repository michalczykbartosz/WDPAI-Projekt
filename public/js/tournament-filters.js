/*
Filtrowanie listy turniejów na żywo przez Fetch API (GET, odpowiedź JSON).
Zmiana filtra od razu pobiera wyniki i podmienia tabelę bez przeładowania strony.
Bez JavaScriptu formularz działa jak zwykły GET z przyciskiem "Filtruj".
 */
const form = document.querySelector('form.filter-bar[data-live]');
const results = document.getElementById('tournaments-results');
const table = document.getElementById('tournaments-table');
const empty = document.getElementById('tournaments-empty');
const status = document.getElementById('tournaments-status');

// Odczekujemy chwilę po ostatnim znaku, żeby nie wysyłać żądania na każdą literę
const SEARCH_DELAY_MS = 300;

let activeRequest = null;
let searchTimer = null;

if (form && results && table && empty && status) {
    form.querySelector('button[type="submit"]').hidden = true;

    form.addEventListener('change', (event) => {
        if (event.target.tagName === 'SELECT') {
            load();
        }
    });

    form.addEventListener('input', (event) => {
        if (event.target.type === 'search') {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(load, SEARCH_DELAY_MS);
        }
    });

    // Enter w polu wyszukiwania – szukamy od razu, bez przeładowania strony
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(searchTimer);
        load();
    });
}

function buildUrl() {
    const params = new URLSearchParams();

    for (const [name, value] of new FormData(form)) {
        if (value.trim() !== '') {
            params.set(name, value.trim());
        }
    }

    const query = params.toString();

    return form.getAttribute('action') + (query !== '' ? '?' + query : '');
}

async function load() {
    const url = buildUrl();

    // Starsze żądanie przestaje być potrzebne – przerywamy je, żeby jego
    // spóźniona odpowiedź nie nadpisała nowszych wyników
    activeRequest?.abort();
    const request = new AbortController();
    activeRequest = request;

    results.setAttribute('aria-busy', 'true');
    status.textContent = form.dataset.labelLoading;

    let response;
    let data = null;

    try {
        response = await fetch(url, {
            headers: { 'Accept': 'application/json' },
            signal: request.signal,
        });

        if ((response.headers.get('Content-Type') ?? '').includes('application/json')) {
            data = await response.json();
        }
    } catch (error) {
        if (error.name !== 'AbortError') {
            finish(request, form.dataset.errorLoad);
        }
        return;
    }

    if (!response.ok || data === null || !Array.isArray(data.tournaments)) {
        finish(request, data?.message ?? form.dataset.errorLoad);
        return;
    }

    render(data.tournaments);
    history.replaceState(null, '', url);
    finish(request, form.dataset.labelResults.replace('{count}', String(data.count)));
}

function finish(request, message) {
    if (request !== activeRequest) {
        return;
    }

    results.removeAttribute('aria-busy');
    status.textContent = message;
}

function render(tournaments) {
    table.tBodies[0].replaceChildren(...tournaments.map(createRow));
    table.hidden = tournaments.length === 0;
    empty.hidden = tournaments.length > 0;
}

/** Wiersz budujemy przez textContent, więc dane z serwera nie są interpretowane jako HTML. */
function createRow(tournament) {
    const row = document.createElement('tr');

    row.insertCell().append(createLink(tournament.url, tournament.name));

    for (const text of [
        tournament.format,
        tournament.date,
        tournament.location,
        `${tournament.registeredPlayers} / ${tournament.maxPlayers}`,
    ]) {
        row.insertCell().textContent = text;
    }

    const badge = document.createElement('span');
    badge.className = 'badge badge--status-' + String(tournament.status).replace(/[^a-z]/g, '');
    badge.textContent = tournament.statusLabel;
    row.insertCell().append(badge);

    const details = createLink(tournament.url, form.dataset.labelDetails);
    details.className = 'btn btn--secondary';
    row.insertCell().append(details);

    return row;
}

function createLink(href, text) {
    const link = document.createElement('a');
    link.href = href;
    link.textContent = text;

    return link;
}
