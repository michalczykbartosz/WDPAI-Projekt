<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\FinishRule;
use App\Model\GameMatch;
use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;
use App\Model\UserRole;
use App\Repository\TournamentRepositoryInterface;
use App\Security\AccessDecision;
use App\Service\TournamentCreationService;
use App\Service\TournamentRegistrationException;
use App\Service\TournamentRegistrationService;
use App\Service\TournamentValidator;
use App\Service\ValidationException;
use DateTimeImmutable;

final class TournamentController extends AppController
{
    private const PUBLIC_STATUSES = [TournamentStatus::Open, TournamentStatus::Ongoing, TournamentStatus::Finished];

    /** Wartości startowe pustego formularza tworzenia turnieju. */
    private const CREATE_FORM_DEFAULTS = [
        'name' => '',
        'description' => '',
        'location' => '',
        'max_players' => '8',
        'starting_score' => '501',
        'finish_rule' => 'double_out',
        'legs_to_win' => '3',
        'starts_at' => '',
        'registration_deadline' => '',
    ];

    public function __construct(
        private readonly TournamentRepositoryInterface $tournaments,
        private readonly TournamentCreationService $creation,
        private readonly TournamentRegistrationService $registrations,
    ) {
        parent::__construct();
    }

    public function index(): void
    {
        $status = $this->parseStatusFilter($_GET['status'] ?? '');
        $startingScore = $this->parseFormatFilter($_GET['format'] ?? '');

        if ($status === false || $startingScore === false) {
            $this->badRequest();
            return;
        }

        $this->render('tournaments', [
            'pageTitle' => $this->translator->get('title.tournaments'),
            'activePage' => 'tournaments',
            'tournaments' => $this->tournaments->findPublished($status, $startingScore),
            'statusOptions' => self::PUBLIC_STATUSES,
            'formatOptions' => Tournament::STARTING_SCORES,
            'selectedStatus' => $status?->value ?? '',
            'selectedFormat' => $startingScore !== null ? (string) $startingScore : '',
        ]);
    }

    public function show(string $id): void
    {
        $tournamentId = $this->parseId($id);

        if ($tournamentId === null) {
            $this->badRequest();
            return;
        }

        $this->renderTournament($this->tournaments->findById($tournamentId));
    }

    public function showByCode(string $code): void
    {
        $this->renderTournament($this->tournaments->findByInviteCode($code));
    }

    public function create(): void
    {
        if ($this->denyUnlessRole(UserRole::Organizer)) {
            return;
        }

        $this->renderCreateForm([], self::CREATE_FORM_DEFAULTS);
    }

    public function store(): void
    {
        if ($this->denyUnlessRole(UserRole::Organizer)) {
            return;
        }

        $input = [];

        foreach (TournamentValidator::FIELDS as $field) {
            $input[$field] = $this->postString($field);
        }

        $organizerId = $this->auth->userId() ?? throw new \LogicException('Role guard passed without a logged-in user.');

        try {
            $tournamentId = $this->creation->create($organizerId, $input);
        } catch (ValidationException $exception) {
            http_response_code(422);
            $this->renderCreateForm($exception->errors, $input);
            return;
        }

        $this->session->flash('flash.tournament_created');
        $this->redirect('/tournaments/' . $tournamentId);
    }

    /**
     * Zapis zalogowanego gracza na turniej. Wywoływane przez Fetch API, zawsze odpowiada JSON-em.
     */
    public function register(string $id): void
    {
        $tournamentId = $this->parseId($id);

        if ($tournamentId === null) {
            $this->jsonError(400, 'bad_request', 'bad_request.message');
            return;
        }

        $decision = $this->guard->check(UserRole::Player);

        if ($decision === AccessDecision::Unauthenticated) {
            $this->jsonError(401, 'unauthorized', 'registration.error.unauthorized');
            return;
        }

        if ($decision === AccessDecision::Forbidden) {
            $this->jsonError(403, 'forbidden', 'registration.error.forbidden');
            return;
        }

        $userId = $this->auth->userId() ?? throw new \LogicException('Role guard passed without a logged-in user.');

        try {
            $tournament = $this->registrations->register($tournamentId, $userId);
        } catch (TournamentRegistrationException $exception) {
            $error = $exception->error;
            $this->jsonError($error->httpStatus(), $error->value, 'registration.error.' . $error->value);
            return;
        }

        $this->json([
            'status' => 'registered',
            'message' => $this->translator->get('registration.success'),
            'registeredPlayers' => $tournament->registeredPlayers,
            'maxPlayers' => $tournament->maxPlayers,
            'player' => ['displayName' => $this->auth->displayName()],
        ], 201);
    }

    /**
     * @param array<string, string> $errors
     * @param array<string, string> $old
     */
    private function renderCreateForm(array $errors, array $old): void
    {
        $this->render('organizer-create', [
            'pageTitle' => $this->translator->get('title.organizer_create'),
            'activePage' => 'organizer',
            'errors' => $errors,
            'old' => $old,
            'startingScoreOptions' => Tournament::STARTING_SCORES,
            'finishRuleOptions' => FinishRule::cases(),
            'maxPlayersOptions' => Tournament::MAX_PLAYERS_OPTIONS,
            'legsToWinOptions' => Tournament::LEGS_TO_WIN_OPTIONS,
            'nameMinLength' => TournamentValidator::NAME_MIN_LENGTH,
            'nameMaxLength' => TournamentValidator::NAME_MAX_LENGTH,
            'descriptionMaxLength' => TournamentValidator::DESCRIPTION_MAX_LENGTH,
            'locationMaxLength' => TournamentValidator::LOCATION_MAX_LENGTH,
        ]);
    }

    private function renderTournament(?Tournament $tournament): void
    {
        if ($tournament === null || $tournament->status === TournamentStatus::Draft) {
            $this->notFound();
            return;
        }

        $players = $this->tournaments->findParticipants($tournament->id);

        $this->render('tournament-detail', [
            'pageTitle' => $this->translator->get('title.tournament', ['name' => $tournament->name]),
            'activePage' => 'tournaments',
            'tournament' => $tournament,
            'players' => $players,
            'registrationState' => $this->registrationState($tournament, $players),
            'rounds' => $this->groupByRound($this->tournaments->findMatches($tournament->id), $tournament->maxPlayers),
        ]);
    }

    /**
     * Stan przycisku zapisu: 'open', 'guest', kod błędu z TournamentRegistrationError
     * albo null, gdy przycisku nie pokazujemy (organizator).
     *
     * @param list<Participant> $players
     */
    private function registrationState(Tournament $tournament, array $players): ?string
    {
        $userId = $this->auth->userId();

        if ($userId !== null && $this->auth->role() !== UserRole::Player) {
            return null;
        }

        $alreadyRegistered = $userId !== null
            && in_array($userId, array_map(static fn (Participant $player): int => $player->userId, $players), true);

        $error = $this->registrations->check($tournament, $alreadyRegistered, new DateTimeImmutable());

        if ($error !== null) {
            return $error->value;
        }

        return $userId === null ? 'guest' : 'open';
    }

    /**
     * Pusty parametr oznacza brak filtra (null), nieprawidłowa wartość to false.
     */
    private function parseStatusFilter(mixed $value): TournamentStatus|null|false
    {
        if ($value === '') {
            return null;
        }

        $status = is_string($value) ? TournamentStatus::tryFrom($value) : null;

        return in_array($status, self::PUBLIC_STATUSES, true) ? $status : false;
    }

    private function parseFormatFilter(mixed $value): int|null|false
    {
        if ($value === '') {
            return null;
        }

        $startingScore = is_string($value) ? $this->parseId($value) : null;

        return in_array($startingScore, Tournament::STARTING_SCORES, true) ? $startingScore : false;
    }

    /**
     * @param list<GameMatch> $matches
     * @return list<array{number: int, label: string, matches: list<GameMatch>}>
     */
    private function groupByRound(array $matches, int $maxPlayers): array
    {
        $totalRounds = (int) log($maxPlayers, 2);
        $rounds = [];

        foreach ($matches as $match) {
            $rounds[$match->round] ??= [
                'number' => $match->round,
                'label' => $this->roundLabel($match->round, $totalRounds),
                'matches' => [],
            ];
            $rounds[$match->round]['matches'][] = $match;
        }

        return array_values($rounds);
    }

    private function roundLabel(int $round, int $totalRounds): string
    {
        return match ($totalRounds - $round) {
            0 => $this->translator->get('bracket.round.final'),
            1 => $this->translator->get('bracket.round.semifinals'),
            2 => $this->translator->get('bracket.round.quarterfinals'),
            default => $this->translator->get('bracket.round.number', ['number' => $round]),
        };
    }
}
