<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\MatchResultException;
use App\Service\MatchResultService;
use App\Service\MatchScoreValidator;
use App\Service\ValidationException;

final class MatchController extends AppController
{
    public function __construct(private readonly MatchResultService $results)
    {
        parent::__construct();
    }

    /**
     * Zapis wyniku meczu wysłany przez Fetch API (JSON). Po sukcesie strona turnieju
     * odświeża się i pokazuje drabinkę z awansem zwycięzcy.
     */
    public function submitResult(string $id): void
    {
        $matchId = $this->parseId($id);

        if ($matchId === null) {
            $this->jsonError(400, 'bad_request', 'bad_request.message');
            return;
        }

        $userId = $this->auth->userId();
        $role = $this->auth->role();

        if ($userId === null || $role === null) {
            $this->jsonError(401, 'unauthorized', 'result.error.unauthorized');
            return;
        }

        $body = $this->jsonInput();
        $input = [];

        foreach (MatchScoreValidator::FIELDS as $field) {
            $value = $body[$field] ?? '';
            $input[$field] = is_string($value) || is_int($value) || is_float($value) ? trim((string) $value) : '';
        }

        try {
            $tournamentFinished = $this->results->submit($matchId, $userId, $role, $input);
        } catch (MatchResultException $exception) {
            $error = $exception->error;
            $this->jsonError($error->httpStatus(), $error->value, 'result.error.' . $error->value);
            return;
        } catch (ValidationException $exception) {
            $this->json([
                'error' => 'validation',
                'message' => $this->translator->get('result.error.validation'),
                'errors' => array_map(
                    fn (string $messageKey): string => $this->translator->get($messageKey, $exception->parameters),
                    $exception->errors,
                ),
            ], 422);
            return;
        }

        $messageKey = $tournamentFinished ? 'result.saved_final' : 'result.saved';
        $this->session->flash($messageKey);

        $this->json([
            'status' => 'saved',
            'message' => $this->translator->get($messageKey),
            'tournamentFinished' => $tournamentFinished,
        ], 201);
    }
}
