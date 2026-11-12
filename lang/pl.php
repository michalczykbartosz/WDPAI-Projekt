<?php

declare(strict_types=1);

return [
    // Layout
    'nav.menu' => 'Menu',
    'nav.home' => 'Strona główna',
    'nav.tournaments' => 'Turnieje',
    'nav.leaderboard' => 'Ranking',
    'nav.organizer' => 'Panel organizatora',
    'theme.to_light' => 'Przełącz na jasny motyw',
    'theme.to_dark' => 'Przełącz na ciemny motyw',
    'role.player' => 'Gracz',
    'role.organizer' => 'Organizator',
    'language.label' => 'Język',
    'language.pl' => 'Polski',
    'language.en' => 'Angielski',
    'footer.copyright' => '© 2026 DartArena. Projekt WDPAI.',

    // Tytuły stron
    'title.home' => 'DartArena – Platforma turniejów darta',
    'title.not_found' => '404 – Nie znaleziono strony – DartArena',
    'title.tournaments' => 'Turnieje – DartArena',
    'title.tournament' => '{name} – DartArena',
    'title.organizer_create' => 'Utwórz turniej – Panel organizatora',
    'title.leaderboard' => 'Ranking graczy – DartArena',
    'title.profile' => '{name} – Profil – DartArena',

    // Strona główna
    'home.hero.title_prefix' => 'Witaj w',
    'home.hero.description' => 'Platforma do organizacji turniejów darta. Zapisz się na turniej, śledź drabinkę i sprawdzaj statystyki.',
    'home.upcoming.title' => 'Nadchodzące turnieje',
    'home.upcoming.all' => 'Wszystkie turnieje →',
    'home.upcoming.empty' => 'Brak nadchodzących turniejów.',
    'home.top.title' => 'Top 3 graczy',
    'home.top.all' => 'Pełny ranking →',

    // Statystyki i kolumny tabel
    'stats.rank' => '#',
    'stats.player' => 'Zawodnik',
    'stats.matches' => 'Mecze',
    'stats.wins' => 'Wygrane',
    'stats.losses' => 'Przegrane',
    'stats.win_rate' => '% wygranych',
    'stats.avg3' => 'Średnia (3 lotki)',
    'stats.180s' => '180-ki',
    'stats.best_checkout' => 'Najlepszy checkout',
    'stats.date' => 'Data',
    'stats.tournament' => 'Turniej',
    'stats.opponent' => 'Przeciwnik',
    'stats.result' => 'Wynik',
    'stats.average' => 'Średnia',
    'stats.checkout' => 'Checkout',
    'stats.points' => 'Punkty',

    // Statusy turnieju i meczu
    'status.open' => 'Zapisy otwarte',
    'status.ongoing' => 'W trakcie',
    'status.finished' => 'Zakończony',
    'status.draft' => 'Szkic',
    'match.status.finished' => 'Zakończony',
    'match.status.pending' => 'Oczekuje',

    // Ranking
    'leaderboard.title' => 'Globalny ranking graczy',
    'leaderboard.description' => 'Ranking wyliczany na podstawie zagregowanych statystyk ze wszystkich turniejów.',
    'leaderboard.empty' => 'Brak danych do wyświetlenia rankingu.',

    // 404
    'not_found.message' => 'Przepraszamy, szukana strona nie istnieje lub została usunięta.',
    'not_found.suggestions' => 'Może szukasz:',

    // Lista turniejów
    'tournaments.title' => 'Eksplorator turniejów',
    'tournaments.column.name' => 'Nazwa',
    'tournaments.column.format' => 'Format',
    'tournaments.column.date' => 'Data',
    'tournaments.column.location' => 'Lokalizacja',
    'tournaments.column.registrations' => 'Zapisy',
    'tournaments.column.status' => 'Status',
    'tournaments.column.action' => 'Akcja',
    'tournaments.details' => 'Szczegóły',
    'tournaments.empty' => 'Brak turniejów spełniających kryteria.',
    'filter.format' => 'Format:',
    'filter.status' => 'Status:',
    'filter.all' => 'Wszystkie',
    'filter.submit' => 'Filtruj',

    // Szczegóły turnieju
    'tournament.info' => 'Informacje o turnieju',
    'tournament.format' => 'Format',
    'tournament.date' => 'Data',
    'tournament.location' => 'Lokalizacja',
    'tournament.status' => 'Status',
    'tournament.registered' => 'Zapisy',
    'tournament.register' => 'Zapisz się na turniej',
    'tournament.players' => 'Zapisani zawodnicy',
    'tournament.no_players' => 'Brak zapisanych zawodników.',

    // Drabinka
    'bracket.title' => 'Drabinka turniejowa (Single Elimination)',
    'bracket.round.quarterfinals' => 'Ćwierćfinały',
    'bracket.round.semifinals' => 'Półfinały',
    'bracket.round.final' => 'Finał',
    'bracket.round.number' => 'Runda {number}',
    'bracket.empty' => 'Drabinka nie została jeszcze wygenerowana.',
    'bracket.match' => 'Mecz #{id}',
    'bracket.vs' => 'vs',
    'bracket.tbd' => 'Do ustalenia',

    // Formularz wyniku meczu
    'result.title' => 'Wpisz wynik meczu',
    'result.match_id' => 'Mecz (ID):',
    'result.legs' => 'Wynik w legach',
    'result.legs_player1' => 'Gracz 1 – legi:',
    'result.legs_player2' => 'Gracz 2 – legi:',
    'result.stats_player1' => 'Statystyki gracza 1',
    'result.stats_player2' => 'Statystyki gracza 2',
    'result.avg3' => 'Średnia 3 lotek:',
    'result.180s' => 'Liczba 180-tek:',
    'result.checkout' => 'Najwyższy checkout:',
    'result.submit' => 'Zatwierdź wynik',

    // Profil zawodnika
    'profile.title' => 'Profil zawodnika',
    'profile.avatar_alt' => 'Awatar: {name}',
    'profile.member_since' => 'Członek od: {date}',
    'profile.change_avatar' => 'Zmień zdjęcie profilowe:',
    'profile.upload' => 'Prześlij',
    'profile.career' => 'Statystyki kariery',
    'profile.recent' => 'Ostatnie mecze',
    'profile.no_matches' => 'Brak historii meczów.',
    'profile.win' => 'W',
    'profile.loss' => 'P',

    // Panel organizatora
    'organizer.title' => 'Panel organizatora – Utwórz nowy turniej',
    'organizer.section.basic' => 'Podstawowe informacje',
    'organizer.section.game' => 'Parametry rozgrywki',
    'organizer.section.schedule' => 'Termin i lokalizacja',
    'organizer.name' => 'Nazwa turnieju:',
    'organizer.name_placeholder' => 'np. Puchar Krakowa 2026',
    'organizer.description' => 'Opis:',
    'organizer.description_placeholder' => 'Krótki opis turnieju...',
    'organizer.format' => 'Format gry:',
    'organizer.choose' => '— wybierz —',
    'organizer.max_players' => 'Maks. liczba graczy:',
    'organizer.legs_to_win' => 'Legi do wygrania meczu:',
    'organizer.date' => 'Data turnieju:',
    'organizer.deadline' => 'Zamknięcie zapisów:',
    'organizer.location' => 'Lokalizacja:',
    'organizer.location_placeholder' => 'np. Kraków, Dart Arena',
    'organizer.submit' => 'Utwórz turniej',
];
