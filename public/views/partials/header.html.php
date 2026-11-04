<?php
$navLinks = [
    'tournaments' => ['url' => '/tournaments', 'label' => 'nav.tournaments'],
    'leaderboard' => ['url' => '/leaderboard', 'label' => 'nav.leaderboard'],
    'organizer' => ['url' => '/organizer/create', 'label' => 'nav.organizer'],
];

$roles = ['player', 'organizer'];
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
            <button type="button" class="theme-toggle"
                    data-label-to-light="<?= $t('theme.to_light') ?>"
                    data-label-to-dark="<?= $t('theme.to_dark') ?>"
                    aria-label="<?= $t('theme.to_light') ?>">🌙</button>
            <?php if (in_array($userRole, $roles, true)): ?>
                <span class="badge badge--<?= htmlspecialchars($userRole) ?>"><?= $t('role.' . $userRole) ?></span>
            <?php endif; ?>
        </div>
    </div>
</header>
