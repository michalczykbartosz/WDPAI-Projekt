<?php
$navLinks = [
    'tournaments' => ['url' => '/tournaments', 'label' => 'nav.tournaments'],
    'leaderboard' => ['url' => '/leaderboard', 'label' => 'nav.leaderboard'],
    'organizer' => ['url' => '/organizer/create', 'label' => 'nav.organizer'],
];

if ($currentUserName === null) {
    $navLinks['login'] = ['url' => '/login', 'label' => 'nav.login'];
    $navLinks['register'] = ['url' => '/register', 'label' => 'nav.register'];
}
?>
<header class="navbar">
    <div class="container">
        <a href="/" class="navbar__brand">🎯 DartArena</a>
        <button type="button" class="hamburger" aria-label="<?= $t('nav.menu') ?>" aria-controls="main-navigation" aria-expanded="false">☰</button>
        <ul class="navbar__links" id="main-navigation">
            <?php foreach ($navLinks as $page => $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']) ?>"<?= $page === $activePage ? ' class="active" aria-current="page"' : '' ?>><?= $t($link['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="navbar__actions">
            <form action="/language" method="post" class="language-switcher" aria-label="<?= $t('language.label') ?>">
                <?php require __DIR__ . '/csrf-field.html.php'; ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentPath) ?>">
                <?php foreach ($supportedLocales as $code): ?>
                    <button type="submit" name="locale" value="<?= htmlspecialchars($code) ?>"
                            class="language-switcher__option" lang="<?= htmlspecialchars($code) ?>"
                            aria-label="<?= $t('language.' . $code) ?>"
                            aria-pressed="<?= $code === $locale ? 'true' : 'false' ?>"><?= htmlspecialchars(strtoupper($code)) ?></button>
                <?php endforeach; ?>
            </form>
            <button type="button" class="theme-toggle"
                    data-label-to-light="<?= $t('theme.to_light') ?>"
                    data-label-to-dark="<?= $t('theme.to_dark') ?>"
                    aria-label="<?= $t('theme.to_light') ?>">🌙</button>
            <?php if ($currentUserName !== null && $currentUserRole !== null): ?>
                <span class="navbar__user"><?= htmlspecialchars($currentUserName) ?></span>
                <span class="badge badge--<?= strtolower($currentUserRole->value) ?>"><?= $t('role.' . strtolower($currentUserRole->value)) ?></span>
                <form action="/logout" method="post" class="logout-form">
                    <?php require __DIR__ . '/csrf-field.html.php'; ?>
                    <button type="submit" class="logout-form__button"><?= $t('nav.logout') ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>
