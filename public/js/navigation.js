/*
Rozwijane menu mobilne. Stan aria-expanded informuje czytniki ekranu,
czy lista linków jest aktualnie widoczna.
 */
(function () {
    const button = document.querySelector('.hamburger');
    const links = document.getElementById('main-navigation');

    if (!button || !links) {
        return;
    }

    button.addEventListener('click', function () {
        const isOpen = links.classList.toggle('open');
        button.setAttribute('aria-expanded', String(isOpen));
    });
})();
