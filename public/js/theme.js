/*
dark/light theme 
 */
(function () {
    const html = document.documentElement;
    const btn  = document.querySelector('.theme-toggle');
    const KEY  = 'dartarena-theme';

    // Motyw startowy ustawia już skrypt w <head>, tutaj tylko synchronizujemy ikonę.
    updateIcon();

    function toggleTheme() {
        const current = html.getAttribute('data-theme') || 'dark';
        const next    = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        try {
            localStorage.setItem(KEY, next);
        } catch (e) {
            // Brak dostępu do localStorage (np. tryb prywatny) – motyw działa do odświeżenia strony.
        }
        updateIcon();
    }

    function updateIcon() {
        if (!btn) return;
        const theme = html.getAttribute('data-theme') || 'dark';
        btn.textContent = theme === 'dark' ? '☀️' : '🌙';
        btn.setAttribute('aria-label',
            theme === 'dark' ? 'Przełącz na jasny motyw' : 'Przełącz na ciemny motyw'
        );
    }

    if (btn) {
        btn.addEventListener('click', toggleTheme);
    }
})();
