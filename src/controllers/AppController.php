<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\UserRole;
use App\Security\AccessDecision;
use App\Security\AuthSession;
use App\Security\CsrfTokenManager;
use App\Security\RoleGuard;
use App\Security\SessionManager;
use App\Service\LocalePreference;
use App\Service\Translator;

abstract class AppController
{
    private const VIEWS_DIR = __DIR__ . '/../../public/views/';
    private const LANG_DIR = __DIR__ . '/../../lang';

    /** Wartości domyślne dla wspólnego layoutu, nadpisywane przez kontrolery. */
    private const LAYOUT_DEFAULTS = [
        'activePage' => '',
    ];

    protected readonly SessionManager $session;
    protected readonly AuthSession $auth;
    protected readonly RoleGuard $guard;
    protected readonly Translator $translator;

    public function __construct()
    {
        $this->session = new SessionManager();
        $this->auth = new AuthSession($this->session);
        $this->guard = new RoleGuard($this->auth);
        $locale = (new LocalePreference($this->session))->current();
        $this->translator = new Translator($locale, self::LANG_DIR);
    }

    public function notFound(): void
    {
        $this->renderError(404, 'not_found');
    }

    public function forbidden(): void
    {
        $this->renderError(403, 'forbidden');
    }

    protected function badRequest(): void
    {
        $this->renderError(400, 'bad_request');
    }

    protected function unauthorized(): void
    {
        $this->renderError(401, 'unauthorized', [['url' => '/login', 'label' => 'nav.login']]);
    }

    /**
     * Wpuszcza tylko zalogowanych użytkowników z jedną z podanych ról.
     * Gość dostaje 401, użytkownik z inną rolą 403. Zwraca true, gdy dostęp
     * zablokowano i strona błędu została już wyrenderowana.
     */
    protected function denyUnlessRole(UserRole ...$allowedRoles): bool
    {
        $decision = $this->guard->check(...$allowedRoles);

        if ($decision === AccessDecision::Granted) {
            return false;
        }

        if ($decision === AccessDecision::Unauthenticated) {
            $this->unauthorized();
        } else {
            $this->renderError(403, 'access_denied');
        }

        return true;
    }

    /** @param array<string, mixed> $data */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * Błąd dla żądań Fetch: kod błędu dla JavaScriptu i przetłumaczony komunikat dla użytkownika.
     */
    protected function jsonError(int $statusCode, string $errorCode, string $messageKey): void
    {
        $this->json([
            'error' => $errorCode,
            'message' => $this->translator->get($messageKey),
        ], $statusCode);
    }

    /** Żądania Fetch wysyłają nagłówek Accept: application/json. */
    protected function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path, true, 303);
    }

    /**
     * Zwraca pole tekstowe z formularza. Wartości innego typu (np. tablice
     * podesłane jako pole[]) traktujemy jak puste.
     */
    protected function postString(string $name, bool $trim = true): string
    {
        $value = $_POST[$name] ?? '';

        if (!is_string($value)) {
            return '';
        }

        return $trim ? trim($value) : $value;
    }

    /**
     * Zamienia identyfikator z adresu na dodatnią liczbę całkowitą.
     * Zwraca null dla wartości typu "abc", "0", "-5", "+5", "1.5" albo liczby
     * większej niż PHP_INT_MAX.
     */
    protected function parseId(string $value): ?int
    {
        if (!ctype_digit($value)) {
            return null;
        }

        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return $id === false ? null : $id;
    }

    protected function render(string $template, array $variables = []): void
    {
        // Strony zawierają dane sesji (token CSRF, zalogowany użytkownik), więc przeglądarka
        // nie może ich trzymać w pamięci podręcznej – inaczej po wylogowaniu przycisk Wstecz
        // pokazałby chronioną stronę z cache.
        header('Cache-Control: no-store');

        $variables = array_merge(self::LAYOUT_DEFAULTS, $variables);
        $variables['locale'] = $this->translator->locale();
        $variables['csrfToken'] = (new CsrfTokenManager($this->session))->token();
        $variables['flashMessage'] = $this->session->pullFlash();
        $variables['currentUserName'] = $this->auth->displayName();
        $variables['currentUserRole'] = $this->auth->role();
        $variables['navLinks'] = $this->navigationLinks();
        $variables['validationMessages'] = $this->translator->messagesWithPrefix('validation.');
        $variables['supportedLocales'] = Translator::SUPPORTED_LOCALES;
        $variables['currentPath'] = $_SERVER['REQUEST_URI'] ?? '/';
        $variables['t'] = $this->createViewTranslator();
        $variables['content'] = $this->renderTemplate($template, $variables);

        echo $this->renderTemplate('layout', $variables);
    }

    /**
     * Pozycje menu zależą od roli zapisanej w sesji: gość widzi logowanie i rejestrację,
     * gracz link do własnego profilu, a organizator panel tworzenia turniejów.
     *
     * @return array<string, array{url: string, label: string}>
     */
    private function navigationLinks(): array
    {
        $links = [
            'tournaments' => ['url' => '/tournaments', 'label' => 'nav.tournaments'],
            'leaderboard' => ['url' => '/leaderboard', 'label' => 'nav.leaderboard'],
        ];

        $userId = $this->auth->userId();

        return match ($userId !== null ? $this->auth->role() : null) {
            UserRole::Organizer => $links + [
                'organizer' => ['url' => '/organizer/create', 'label' => 'nav.organizer'],
            ],
            UserRole::Player => $links + [
                'profile' => ['url' => '/players/' . $userId, 'label' => 'nav.my_profile'],
            ],
            null => $links + [
                'login' => ['url' => '/login', 'label' => 'nav.login'],
                'register' => ['url' => '/register', 'label' => 'nav.register'],
            ],
        };
    }

    /** @param list<array{url: string, label: string}> $extraLinks */
    private function renderError(int $statusCode, string $errorKey, array $extraLinks = []): void
    {
        // Żądania Fetch dostają błąd jako JSON, a nie stronę HTML
        if ($this->wantsJson()) {
            $this->jsonError($statusCode, $errorKey, $errorKey . '.message');
            return;
        }

        http_response_code($statusCode);

        $this->render('error', [
            'pageTitle' => $this->translator->get('title.' . $errorKey),
            'statusCode' => $statusCode,
            'errorKey' => $errorKey,
            'suggestedLinks' => [
                ...$extraLinks,
                ['url' => '/', 'label' => 'nav.home'],
                ['url' => '/tournaments', 'label' => 'nav.tournaments'],
                ['url' => '/leaderboard', 'label' => 'nav.leaderboard'],
            ],
        ]);
    }

    /**
     * Widoki dostają funkcję $t(), która od razu koduje tekst do HTML,
     * więc tłumaczenia i podstawiane parametry są bezpieczne przy wypisywaniu.
     */
    private function createViewTranslator(): \Closure
    {
        return fn (string $key, array $parameters = []): string => htmlspecialchars(
            $this->translator->get($key, $parameters),
            ENT_QUOTES,
            'UTF-8',
        );
    }

    private function renderTemplate(string $template, array $variables): string
    {
        $templatePath = self::VIEWS_DIR . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        extract($variables, EXTR_SKIP);

        ob_start();

        try {
            require $templatePath;
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        return (string) ob_get_clean();
    }
}
