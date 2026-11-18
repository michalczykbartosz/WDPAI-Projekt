<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LoginService;
use App\Service\RegistrationService;
use App\Service\RegistrationValidator;

final class AuthController extends AppController
{
    public function __construct(
        private readonly RegistrationService $registration,
        private readonly LoginService $login,
    ) {
        parent::__construct();
    }

    public function showRegistrationForm(): void
    {
        $this->renderRegistrationForm([], ['email' => '', 'display_name' => '']);
    }

    public function register(): void
    {
        $email = mb_strtolower($this->postString('email'));
        $displayName = $this->postString('display_name');

        $errors = $this->registration->registerPlayer(
            $email,
            $displayName,
            $this->postString('password', trim: false),
            $this->postString('password_confirmation', trim: false),
        );

        if ($errors !== []) {
            http_response_code(422);
            $this->renderRegistrationForm($errors, ['email' => $email, 'display_name' => $displayName]);
            return;
        }

        $this->session->flash('flash.registered');
        $this->redirect('/');
    }

    public function showLoginForm(): void
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $this->renderLoginForm(null, '');
    }

    public function login(): void
    {
        $email = mb_strtolower($this->postString('email'));
        $password = $this->postString('password', trim: false);

        $user = $email !== '' && $password !== ''
            ? $this->login->authenticate($email, $password)
            : null;

        if ($user === null) {
            // Ten sam komunikat dla złego e-maila i złego hasła – nie zdradzamy, które konto istnieje
            http_response_code(401);
            $this->renderLoginForm('auth.invalid_credentials', $email);
            return;
        }

        $this->auth->logIn($user);
        $this->session->flash('flash.logged_in');
        $this->redirect('/');
    }

    public function logout(): void
    {
        $this->auth->logOut();
        $this->redirect('/');
    }

    private function renderLoginForm(?string $error, string $oldEmail): void
    {
        $this->render('login', [
            'pageTitle' => $this->translator->get('title.login'),
            'activePage' => 'login',
            'error' => $error,
            'oldEmail' => $oldEmail,
        ]);
    }

    /**
     * @param array<string, string> $errors
     * @param array{email: string, display_name: string} $old
     */
    private function renderRegistrationForm(array $errors, array $old): void
    {
        $this->render('register', [
            'pageTitle' => $this->translator->get('title.register'),
            'activePage' => 'register',
            'errors' => $errors,
            'old' => $old,
            'passwordMinLength' => RegistrationValidator::PASSWORD_MIN_LENGTH,
            'displayNameMinLength' => RegistrationValidator::DISPLAY_NAME_MIN_LENGTH,
            'displayNameMaxLength' => RegistrationValidator::DISPLAY_NAME_MAX_LENGTH,
        ]);
    }
}
