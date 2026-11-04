<?php
$navLinks = [
    'tournaments' => ['url' => '/tournaments', 'label' => 'Turnieje'],
    'leaderboard' => ['url' => '/leaderboard', 'label' => 'Ranking'],
    'organizer' => ['url' => '/organizer/create', 'label' => 'Panel organizatora'],
];

$roleLabels = [
    'player' => 'Gracz',
    'organizer' => 'Organizator',
];
?>
<header class="navbar">
    <div class="container">
        <a href="/" class="navbar__brand">🎯 DartArena</a>
        <button type="button" class="hamburger" aria-label="Menu" aria-controls="main-navigation" aria-expanded="false">☰</button>
        <ul class="navbar__links" id="main-navigation">
            <?php foreach ($navLinks as $page => $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']) ?>"<?= $page === $activePage ? ' class="active" aria-current="page"' : '' ?>><?= htmlspecialchars($link['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="navbar__actions">
            <button type="button" class="theme-toggle" aria-label="Przełącz motyw">🌙</button>
            <?php if (isset($roleLabels[$userRole])): ?>
                <span class="badge badge--<?= htmlspecialchars($userRole) ?>"><?= htmlspecialchars($roleLabels[$userRole]) ?></span>
            <?php endif; ?>
        </div>
    </div>
</header>
