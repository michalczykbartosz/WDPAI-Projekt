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
        <button class="hamburger" aria-label="Menu" onclick="document.querySelector('.navbar__links').classList.toggle('open')">☰</button>
        <ul class="navbar__links">
            <?php foreach ($navLinks as $page => $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']) ?>"<?= $page === $activePage ? ' class="active" aria-current="page"' : '' ?>><?= htmlspecialchars($link['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="navbar__actions">
            <button class="theme-toggle" onclick="toggleTheme()" aria-label="Przełącz motyw">🌙</button>
            <?php if (isset($roleLabels[$userRole])): ?>
                <span class="badge badge--<?= htmlspecialchars($userRole) ?>"><?= htmlspecialchars($roleLabels[$userRole]) ?></span>
            <?php endif; ?>
        </div>
    </div>
</header>
