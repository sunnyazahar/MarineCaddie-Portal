<?php

namespace App\Services\Installer;

final class DatabaseCredentials
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $database,
        public readonly string $username,
        #[\SensitiveParameter]
        public readonly string $password,
    ) {
    }

    /**
     * @param  array{db_host: string, db_port: int|string, db_database: string, db_username: string, db_password?: string|null}  $input
     */
    public static function fromInput(array $input): self
    {
        return new self(
            trim($input['db_host']),
            (int) $input['db_port'],
            trim($input['db_database']),
            trim($input['db_username']),
            (string) ($input['db_password'] ?? ''),
        );
    }

    public function __debugInfo(): array
    {
        return ['host' => $this->host, 'port' => $this->port, 'database' => $this->database, 'username' => $this->username, 'password' => '***'];
    }
}
