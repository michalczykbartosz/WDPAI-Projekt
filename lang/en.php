<?php

declare(strict_types=1);

return [
    // Layout
    'nav.menu' => 'Menu',
    'nav.home' => 'Home',
    'nav.tournaments' => 'Tournaments',
    'nav.leaderboard' => 'Leaderboard',
    'nav.organizer' => 'Organizer panel',
    'theme.to_light' => 'Switch to light theme',
    'theme.to_dark' => 'Switch to dark theme',
    'role.player' => 'Player',
    'role.organizer' => 'Organizer',
    'language.label' => 'Language',
    'language.pl' => 'Polish',
    'language.en' => 'English',
    'footer.copyright' => '© 2026 DartArena. WDPAI project.',

    // Page titles
    'title.home' => 'DartArena – Darts tournament platform',
    'title.not_found' => '404 – Page not found – DartArena',
    'title.tournaments' => 'Tournaments – DartArena',
    'title.tournament' => '{name} – DartArena',
    'title.organizer_create' => 'Create tournament – Organizer panel',
    'title.leaderboard' => 'Player leaderboard – DartArena',
    'title.profile' => '{name} – Profile – DartArena',

    // Home page
    'home.hero.title_prefix' => 'Welcome to',
    'home.hero.description' => 'A platform for organising darts tournaments. Sign up for a tournament, follow the bracket and check your statistics.',
    'home.upcoming.title' => 'Upcoming tournaments',
    'home.upcoming.all' => 'All tournaments →',
    'home.upcoming.empty' => 'No upcoming tournaments.',
    'home.top.title' => 'Top 3 players',
    'home.top.all' => 'Full leaderboard →',

    // Statistics and table columns
    'stats.rank' => '#',
    'stats.player' => 'Player',
    'stats.matches' => 'Matches',
    'stats.wins' => 'Wins',
    'stats.losses' => 'Losses',
    'stats.win_rate' => 'Win rate',
    'stats.avg3' => '3-dart average',
    'stats.180s' => '180s',
    'stats.best_checkout' => 'Best checkout',
    'stats.date' => 'Date',
    'stats.tournament' => 'Tournament',
    'stats.opponent' => 'Opponent',
    'stats.result' => 'Result',
    'stats.average' => 'Average',
    'stats.checkout' => 'Checkout',

    // Tournament and match statuses
    'status.open' => 'Registration open',
    'status.ongoing' => 'In progress',
    'status.finished' => 'Finished',
    'match.status.finished' => 'Finished',
    'match.status.pending' => 'Pending',

    // Leaderboard
    'leaderboard.title' => 'Global player leaderboard',
    'leaderboard.description' => 'The ranking is calculated from aggregated statistics across all tournaments.',
    'leaderboard.empty' => 'No leaderboard data to display.',

    // 404
    'not_found.message' => 'Sorry, the page you are looking for does not exist or has been removed.',
    'not_found.suggestions' => 'Maybe you are looking for:',

    // Tournament list
    'tournaments.title' => 'Tournament explorer',
    'tournaments.column.name' => 'Name',
    'tournaments.column.format' => 'Format',
    'tournaments.column.date' => 'Date',
    'tournaments.column.location' => 'Location',
    'tournaments.column.registrations' => 'Registrations',
    'tournaments.column.status' => 'Status',
    'tournaments.column.action' => 'Action',
    'tournaments.details' => 'Details',
    'tournaments.empty' => 'No tournaments match the selected filters.',
    'filter.format' => 'Format:',
    'filter.status' => 'Status:',
    'filter.all' => 'All',
    'filter.submit' => 'Filter',

    // Tournament details
    'tournament.info' => 'Tournament information',
    'tournament.format' => 'Format',
    'tournament.date' => 'Date',
    'tournament.location' => 'Location',
    'tournament.status' => 'Status',
    'tournament.max_players' => 'Max players',
    'tournament.register' => 'Register for tournament',
    'tournament.players' => 'Registered players',
    'tournament.no_players' => 'No registered players.',

    // Bracket
    'bracket.title' => 'Tournament bracket (single elimination)',
    'bracket.round.quarterfinals' => 'Quarterfinals',
    'bracket.round.semifinals' => 'Semifinals',
    'bracket.round.final' => 'Final',
    'bracket.match' => 'Match #{id}',
    'bracket.vs' => 'vs',
    'bracket.tbd' => 'TBD',

    // Match result form
    'result.title' => 'Enter match result',
    'result.match_id' => 'Match (ID):',
    'result.legs' => 'Score in legs',
    'result.legs_player1' => 'Player 1 – legs:',
    'result.legs_player2' => 'Player 2 – legs:',
    'result.stats_player1' => 'Player 1 statistics',
    'result.stats_player2' => 'Player 2 statistics',
    'result.avg3' => '3-dart average:',
    'result.180s' => 'Number of 180s:',
    'result.checkout' => 'Highest checkout:',
    'result.submit' => 'Submit result',

    // Player profile
    'profile.title' => 'Player profile',
    'profile.avatar_alt' => 'Avatar: {name}',
    'profile.member_since' => 'Member since: {date}',
    'profile.change_avatar' => 'Change profile photo:',
    'profile.upload' => 'Upload',
    'profile.career' => 'Career statistics',
    'profile.recent' => 'Recent matches',
    'profile.no_matches' => 'No match history.',

    // Organizer panel
    'organizer.title' => 'Organizer panel – Create a new tournament',
    'organizer.section.basic' => 'Basic information',
    'organizer.section.game' => 'Game settings',
    'organizer.section.schedule' => 'Date and location',
    'organizer.name' => 'Tournament name:',
    'organizer.name_placeholder' => 'e.g. Krakow Cup 2026',
    'organizer.description' => 'Description:',
    'organizer.description_placeholder' => 'A short description of the tournament...',
    'organizer.format' => 'Game format:',
    'organizer.choose' => '— choose —',
    'organizer.max_players' => 'Max players:',
    'organizer.legs_to_win' => 'Legs to win a match:',
    'organizer.date' => 'Tournament date:',
    'organizer.deadline' => 'Registration closes:',
    'organizer.location' => 'Location:',
    'organizer.location_placeholder' => 'e.g. Krakow, Dart Arena',
    'organizer.submit' => 'Create tournament',
];
