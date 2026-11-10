<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Database;

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
        $statement = $this->database->connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    /**
     * @param array<string, int|string> $parameters
     * @return array<string, mixed>|null
     */
    protected function fetchOne(string $sql, array $parameters = []): ?array
    {
        return $this->fetchAll($sql, $parameters)[0] ?? null;
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
