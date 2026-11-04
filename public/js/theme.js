/*
dark/light theme 
 */
(function () {
    const html = document.documentElement;
    const btn  = document.querySelector('.theme-toggle');
    const KEY  = 'dartarena-theme';

    const saved = localStorage.getItem(KEY);
    if (saved) {
        html.setAttribute('data-theme', saved);
    }
    updateIcon();

    function toggleTheme() {
        const current = html.getAttribute('data-theme') || 'dark';
        const next    = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem(KEY, next);
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
