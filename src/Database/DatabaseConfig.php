<?php

declare(strict_types=1);

namespace App\Database;

final class DatabaseConfig
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $name,
        public readonly string $user,
        public readonly string $password,
    ) {
    }

    public static function fromEnvironment(): self
    {
        return new self(
            self::requireVariable('DB_HOST'),
            (int) (getenv('DB_PORT') ?: 5432),
            self::requireVariable('DB_NAME'),
            self::requireVariable('DB_USER'),
            self::requireVariable('DB_PASSWORD'),
        );
    }

    public function dsn(): string
    {
        return sprintf('pgsql:host=%s;port=%d;dbname=%s', $this->host, $this->port, $this->name);
    }

    private static function requireVariable(string $name): string
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            throw new \RuntimeException('Missing environment variable: ' . $name);
        }

        return $value;
    }
}
