<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Database;
use PDOStatement;

abstract class PdoRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    /**
     * @param array<string, int|string> $parameters
     * @return list<array<string, mixed>>
     */
    protected function fetchAll(string $sql, array $parameters = []): array
    {
        return $this->execute($sql, $parameters)->fetchAll();
    }

    /**
     * @param array<string, int|string> $parameters
     * @return array<string, mixed>|null
     */
    protected function fetchOne(string $sql, array $parameters = []): ?array
    {
        return $this->fetchAll($sql, $parameters)[0] ?? null;
    }

    /** @param array<string, int|string> $parameters */
    protected function execute(string $sql, array $parameters = []): PDOStatement
    {
        $statement = $this->database->connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }

    /**
     * Wykonuje operację w transakcji: wszystkie zapytania zapisują się razem
     * albo żadne (rollBack przy dowolnym błędzie).
     *
     * @template T
     * @param callable(): T $operation
     * @return T
     */
    protected function transaction(callable $operation): mixed
    {
        $connection = $this->database->connection();
        $connection->beginTransaction();

        try {
            $result = $operation();
            $connection->commit();

            return $result;
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }

    protected static function nullableInt(mixed $value): ?int
    {
        return $value !== null ? (int) $value : null;
    }

    protected static function nullableFloat(mixed $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }
}
