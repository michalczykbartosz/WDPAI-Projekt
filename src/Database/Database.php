<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

final class Database
{
    private ?PDO $connection = null;

    public function __construct(private readonly DatabaseConfig $config)
    {
    }

    public function connection(): PDO
    {
        if ($this->connection === null) {
            $this->connection = $this->connect();
        }

        return $this->connection;
    }

    private function connect(): PDO
    {
        try {
            return new PDO($this->config->dsn(), $this->config->user, $this->config->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new \RuntimeException('Could not connect to the database.', 0, $exception);
        }
    }
}
