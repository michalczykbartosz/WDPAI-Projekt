BEGIN;

CREATE TYPE user_role AS ENUM ('PLAYER', 'ORGANIZER');
CREATE TYPE tournament_status AS ENUM ('draft', 'open', 'ongoing', 'finished');
CREATE TYPE finish_rule AS ENUM ('double_out', 'single_out');
CREATE TYPE match_status AS ENUM ('pending', 'finished');

CREATE TABLE users (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email         VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          user_role    NOT NULL DEFAULT 'PLAYER',
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT users_email_format CHECK (email ~ '^[^@\s]+@[^@\s]+\.[^@\s]+$')
);

CREATE UNIQUE INDEX users_email_unique ON users (lower(email));

CREATE TABLE player_profiles (
    user_id      BIGINT       PRIMARY KEY REFERENCES users (id) ON DELETE CASCADE,
    display_name VARCHAR(50)  NOT NULL,
    club         VARCHAR(100),
    avatar_path  VARCHAR(255),
    created_at   TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at   TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT player_profiles_display_name_unique UNIQUE (display_name),
    CONSTRAINT player_profiles_display_name_not_blank CHECK (btrim(display_name) <> ''),
    CONSTRAINT player_profiles_avatar_relative CHECK (avatar_path IS NULL OR avatar_path LIKE '/uploads/%')
);

CREATE TABLE tournaments (
    id                    BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    organizer_id          BIGINT            NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    name                  VARCHAR(100)      NOT NULL,
    description           TEXT,
    location              VARCHAR(150)      NOT NULL,
    starts_at             TIMESTAMPTZ       NOT NULL,
    registration_deadline TIMESTAMPTZ       NOT NULL,
    max_players           SMALLINT          NOT NULL,
    starting_score        SMALLINT          NOT NULL DEFAULT 501,
    finish_rule           finish_rule       NOT NULL DEFAULT 'double_out',
    legs_to_win           SMALLINT          NOT NULL DEFAULT 3,
    invite_code           CHAR(6)           NOT NULL,
    status                tournament_status NOT NULL DEFAULT 'draft',
    created_at            TIMESTAMPTZ       NOT NULL DEFAULT now(),
    updated_at            TIMESTAMPTZ       NOT NULL DEFAULT now(),
    CONSTRAINT tournaments_name_not_blank CHECK (btrim(name) <> ''),
    CONSTRAINT tournaments_max_players_power_of_two CHECK (max_players IN (4, 8, 16, 32)),
    CONSTRAINT tournaments_starting_score_valid CHECK (starting_score IN (301, 501)),
    CONSTRAINT tournaments_legs_to_win_range CHECK (legs_to_win BETWEEN 1 AND 10),
    CONSTRAINT tournaments_deadline_before_start CHECK (registration_deadline <= starts_at),
    CONSTRAINT tournaments_invite_code_unique UNIQUE (invite_code),
    CONSTRAINT tournaments_invite_code_format CHECK (invite_code ~ '^[A-Z0-9]{6}$')
);

CREATE INDEX tournaments_organizer_idx ON tournaments (organizer_id);
CREATE INDEX tournaments_status_starts_idx ON tournaments (status, starts_at);

CREATE TABLE tournament_participants (
    tournament_id BIGINT      NOT NULL REFERENCES tournaments (id) ON DELETE CASCADE,
    user_id       BIGINT      NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    seed          SMALLINT,
    registered_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    PRIMARY KEY (tournament_id, user_id),
    CONSTRAINT tournament_participants_seed_positive CHECK (seed IS NULL OR seed >= 1),
    CONSTRAINT tournament_participants_seed_unique UNIQUE (tournament_id, seed)
);

CREATE INDEX tournament_participants_user_idx ON tournament_participants (user_id);

CREATE TABLE matches (
    id            BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tournament_id BIGINT       NOT NULL REFERENCES tournaments (id) ON DELETE RESTRICT,
    round         SMALLINT     NOT NULL,
    position      SMALLINT     NOT NULL,
    player1_id    BIGINT       REFERENCES users (id) ON DELETE RESTRICT,
    player2_id    BIGINT       REFERENCES users (id) ON DELETE RESTRICT,
    player1_legs  SMALLINT,
    player2_legs  SMALLINT,
    winner_id     BIGINT       REFERENCES users (id) ON DELETE RESTRICT,
    status        match_status NOT NULL DEFAULT 'pending',
    played_at     TIMESTAMPTZ,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT matches_slot_unique UNIQUE (tournament_id, round, position),
    CONSTRAINT matches_round_positive CHECK (round >= 1),
    CONSTRAINT matches_position_positive CHECK (position >= 1),
    CONSTRAINT matches_different_players CHECK (player1_id IS DISTINCT FROM player2_id OR player1_id IS NULL),
    CONSTRAINT matches_legs_non_negative CHECK (player1_legs >= 0 AND player2_legs >= 0),
    CONSTRAINT matches_no_draw CHECK (player1_legs IS DISTINCT FROM player2_legs OR player1_legs IS NULL),
    CONSTRAINT matches_winner_is_player CHECK (winner_id IS NULL OR winner_id = player1_id OR winner_id = player2_id),
    CONSTRAINT matches_finished_consistent CHECK (
        (status = 'finished' AND winner_id IS NOT NULL AND played_at IS NOT NULL)
        OR (status = 'pending' AND winner_id IS NULL AND player1_legs IS NULL AND player2_legs IS NULL)
    )
);

CREATE INDEX matches_player1_idx ON matches (player1_id);
CREATE INDEX matches_player2_idx ON matches (player2_id);
CREATE INDEX matches_winner_idx ON matches (winner_id);

CREATE TABLE match_statistics (
    match_id           BIGINT       NOT NULL REFERENCES matches (id) ON DELETE CASCADE,
    user_id            BIGINT       NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    three_dart_average NUMERIC(5, 2) NOT NULL,
    count_180s         SMALLINT     NOT NULL DEFAULT 0,
    highest_checkout   SMALLINT,
    PRIMARY KEY (match_id, user_id),
    CONSTRAINT match_statistics_average_range CHECK (three_dart_average BETWEEN 0 AND 180),
    CONSTRAINT match_statistics_180s_non_negative CHECK (count_180s >= 0),
    CONSTRAINT match_statistics_checkout_range CHECK (highest_checkout IS NULL OR highest_checkout BETWEEN 1 AND 180)
);

CREATE INDEX match_statistics_user_idx ON match_statistics (user_id);

COMMIT;
