BEGIN;

INSERT INTO users (id, email, password_hash, role) OVERRIDING SYSTEM VALUE VALUES
    (1, 'organizer@dartarena.local', '$2y$10$8HgukwwNkdDkYzzaNTeshOufuYQ5BIJa3j0NTUacyFacOilgYtc9m', 'ORGANIZER'),
    (2, 'player@dartarena.local',    '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (3, 'adam.nowak@dartarena.local',          '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (4, 'piotr.wisniewski@dartarena.local',    '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (5, 'krzysztof.zielinski@dartarena.local', '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (6, 'tomasz.lewandowski@dartarena.local',  '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (7, 'marcin.wojcik@dartarena.local',       '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (8, 'pawel.kaminski@dartarena.local',      '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER'),
    (9, 'lukasz.szymanski@dartarena.local',    '$2y$10$ruebzauNsOW5ezTfyx8Vte8k2GiTJAnDNXp8EP7Y6TF7RURRy2Qiu', 'PLAYER');

INSERT INTO player_profiles (user_id, display_name, club) VALUES
    (2, 'Jan Kowalski',        'Kraków Darts Club'),
    (3, 'Adam Nowak',          'Kraków Darts Club'),
    (4, 'Piotr Wiśniewski',    'Bull''s Eye Warszawa'),
    (5, 'Krzysztof Zieliński', 'Triple Twenty Gdańsk'),
    (6, 'Tomasz Lewandowski',  'Bull''s Eye Warszawa'),
    (7, 'Marcin Wójcik',       NULL),
    (8, 'Paweł Kamiński',      'Kraków Darts Club'),
    (9, 'Łukasz Szymański',    NULL);

INSERT INTO tournaments (id, organizer_id, name, description, location, starts_at, registration_deadline, max_players, starting_score, finish_rule, legs_to_win, invite_code, status) OVERRIDING SYSTEM VALUE VALUES
    (1, 1, 'Puchar Krakowa 2026', 'Otwarty turniej steel tip dla graczy z Małopolski.', 'Kraków, Dart Arena', '2026-11-04 18:00+01', '2026-11-03 23:59+01', 8, 501, 'double_out', 3, 'KRK026', 'ongoing'),
    (2, 1, 'Liga Zimowa – Runda 1', 'Pierwsza runda ligi zimowej.', 'Warszawa, Bull''s Eye Pub', '2026-12-01 18:00+01', '2026-11-28 23:59+01', 8, 501, 'double_out', 3, 'ZIMA01', 'open'),
    (3, 1, 'Turniej Halloweenowy 2026', 'Szybki turniej na 4 graczy.', 'Gdańsk, Triple Twenty Club', '2026-10-31 19:00+01', '2026-10-30 23:59+01', 4, 301, 'double_out', 3, 'HALL26', 'finished'),
    (4, 1, 'Grand Prix Gdańska', NULL, 'Gdańsk, Triple Twenty Club', '2026-12-12 12:00+01', '2026-12-08 23:59+01', 16, 501, 'double_out', 4, 'GPGD26', 'draft');

INSERT INTO tournament_participants (tournament_id, user_id, seed, registered_at) VALUES
    (1, 2, 1, '2026-10-20 10:00+02'),
    (1, 3, 2, '2026-10-20 11:15+02'),
    (1, 4, 3, '2026-10-21 09:30+02'),
    (1, 5, 4, '2026-10-22 18:00+02'),
    (1, 6, 5, '2026-10-23 20:45+02'),
    (1, 7, 6, '2026-10-24 12:10+02'),
    (1, 8, 7, '2026-10-25 16:00+02'),
    (1, 9, 8, '2026-10-26 08:20+01'),
    (2, 2, NULL, '2026-11-02 09:00+01'),
    (2, 3, NULL, '2026-11-02 10:30+01'),
    (2, 5, NULL, '2026-11-03 14:00+01'),
    (2, 8, NULL, '2026-11-03 19:40+01'),
    (2, 9, NULL, '2026-11-04 21:05+01'),
    (3, 4, 1, '2026-10-25 12:00+02'),
    (3, 6, 2, '2026-10-25 13:00+02'),
    (3, 2, 3, '2026-10-26 09:00+01'),
    (3, 7, 4, '2026-10-27 17:30+01');

INSERT INTO matches (id, tournament_id, round, position, player1_id, player2_id, player1_legs, player2_legs, winner_id, status, played_at) OVERRIDING SYSTEM VALUE VALUES
    (1,  1, 1, 1, 2,    9,    3,    1,    2,    'finished', '2026-11-04 18:30+01'),
    (2,  1, 1, 2, 4,    7,    3,    0,    4,    'finished', '2026-11-04 18:30+01'),
    (3,  1, 1, 3, 6,    5,    2,    3,    5,    'finished', '2026-11-04 19:15+01'),
    (4,  1, 1, 4, 3,    8,    NULL, NULL, NULL, 'pending',  NULL),
    (5,  1, 2, 1, 2,    4,    NULL, NULL, NULL, 'pending',  NULL),
    (6,  1, 2, 2, 5,    NULL, NULL, NULL, NULL, 'pending',  NULL),
    (7,  1, 3, 1, NULL, NULL, NULL, NULL, NULL, 'pending',  NULL),
    (8,  3, 1, 1, 4,    7,    3,    1,    4,    'finished', '2026-10-31 19:20+01'),
    (9,  3, 1, 2, 2,    6,    2,    3,    6,    'finished', '2026-10-31 19:25+01'),
    (10, 3, 2, 1, 4,    6,    3,    2,    4,    'finished', '2026-10-31 20:30+01');

INSERT INTO match_statistics (match_id, user_id, three_dart_average, count_180s, highest_checkout) VALUES
    (1,  2, 87.20, 2, 96),
    (1,  9, 74.90, 0, 40),
    (2,  4, 95.30, 3, 121),
    (2,  7, 68.40, 0, NULL),
    (3,  6, 82.10, 1, 64),
    (3,  5, 84.60, 2, 110),
    (8,  4, 91.00, 2, 164),
    (8,  7, 70.20, 0, 32),
    (9,  2, 85.00, 1, 100),
    (9,  6, 88.40, 2, 170),
    (10, 4, 93.50, 4, 141),
    (10, 6, 86.00, 1, 80);

SELECT setval(pg_get_serial_sequence('users', 'id'), (SELECT max(id) FROM users));
SELECT setval(pg_get_serial_sequence('tournaments', 'id'), (SELECT max(id) FROM tournaments));
SELECT setval(pg_get_serial_sequence('matches', 'id'), (SELECT max(id) FROM matches));

COMMIT;
