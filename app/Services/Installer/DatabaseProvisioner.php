<?php

namespace App\Services\Installer;

use Illuminate\Support\Str;
use PDO;
use PDOException;

class DatabaseProvisioner
{
    /** Database identifiers cannot be bound as parameters, so only this safe set is accepted. */
    public const NAME_PATTERN = '/^[A-Za-z0-9_]{1,64}$/';

    public const HOST_PATTERN = '/^[A-Za-z0-9.\-:\[\]]{1,255}$/';

    /** Suggest a database name from the company name (e.g. "Acme Shipping LLC" → "acme_shipping_llc"). */
    public function suggestName(string $companyName): string
    {
        $slug = Str::of($companyName)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString();

        return Str::limit($slug !== '' ? $slug : 'portal', 64, '');
    }

    /**
     * Connects, creates the database when the MySQL user may, and makes sure it is empty.
     *
     * @return array{created: bool}
     *
     * @throws InstallerException
     */
    public function prepare(DatabaseCredentials $credentials): array
    {
        $this->assertValid($credentials);

        $server = $this->connect($credentials, null);
        $created = $this->tryCreateDatabase($server, $credentials->database);

        $pdo = $this->connectToDatabase($credentials, $created);

        $tableCount = (int) $pdo->query(
            'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()'
        )->fetchColumn();

        if ($tableCount > 0) {
            throw new InstallerException(
                "Database \"{$credentials->database}\" already has {$tableCount} table(s). Use an empty database so no existing data is overwritten."
            );
        }

        return ['created' => $created];
    }

    /** @throws InstallerException */
    public function assertValid(DatabaseCredentials $credentials): void
    {
        if (! preg_match(self::HOST_PATTERN, $credentials->host)) {
            throw new InstallerException('Database host contains invalid characters.');
        }
        if ($credentials->port < 1 || $credentials->port > 65535) {
            throw new InstallerException('Database port must be between 1 and 65535.');
        }
        if (! preg_match(self::NAME_PATTERN, $credentials->database)) {
            throw new InstallerException('Database name may only contain letters, numbers and underscores (max 64).');
        }
        if ($credentials->username === '' || strlen($credentials->username) > 80 || preg_match('/[\x00-\x1F]/', $credentials->username)) {
            throw new InstallerException('Database username is invalid.');
        }
    }

    private function tryCreateDatabase(PDO $server, string $database): bool
    {
        if ($this->databaseExists($server, $database)) {
            return false;
        }

        try {
            // Name already validated against NAME_PATTERN, so backtick quoting is safe.
            $server->exec('CREATE DATABASE `' . $database . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            return true;
        } catch (PDOException) {
            // Shared hosting (e.g. Hostinger) users cannot create databases; fall through
            // and try the database the user created in the hosting panel.
            return false;
        }
    }

    private function databaseExists(PDO $server, string $database): bool
    {
        $statement = $server->prepare('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
        $statement->execute([$database]);

        return (int) $statement->fetchColumn() > 0;
    }

    /** @throws InstallerException */
    private function connectToDatabase(DatabaseCredentials $credentials, bool $created): PDO
    {
        try {
            return $this->connect($credentials, $credentials->database);
        } catch (InstallerException $e) {
            if ($created) {
                throw $e;
            }

            throw new InstallerException(
                "Could not create or open database \"{$credentials->database}\". On shared hosting (e.g. Hostinger hPanel → Databases), "
                . 'create the database and assign this user to it, then enter that exact database name here.'
            );
        }
    }

    /** @throws InstallerException */
    private function connect(DatabaseCredentials $credentials, ?string $database): PDO
    {
        $dsn = 'mysql:host=' . $credentials->host . ';port=' . $credentials->port . ';charset=utf8mb4';
        if ($database !== null) {
            $dsn .= ';dbname=' . $database;
        }

        try {
            return new PDO($dsn, $credentials->username, $credentials->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (PDOException $e) {
            throw new InstallerException($this->friendlyConnectionError($e));
        }
    }

    private function friendlyConnectionError(PDOException $e): string
    {
        $code = (int) ($e->errorInfo[1] ?? 0);

        return match ($code) {
            1045 => 'Access denied: check the database username and password.',
            1044, 1049 => 'The user has no access to this database (or it does not exist).',
            2002, 2003, 2005 => 'Could not reach the database server: check host and port.',
            default => 'Database connection failed (error ' . ($code ?: $e->getCode()) . ').',
        };
    }
}
