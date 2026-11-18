<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RegistrationService;
use App\Service\RegistrationValidator;

final class AuthController extends AppController
{
    public function __construct(private readonly RegistrationService $registration)
    {
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
