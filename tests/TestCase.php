<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertTestsCannotTouchPersistentDatabases();
    }

    /**
     * Every test run must stay on SQLite :memory: from phpunit.xml.
     * Blocks the failure mode where PHPUnit loads .env MySQL and drops real tables.
     */
    protected function assertTestsCannotTouchPersistentDatabases(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException(
                'Tests refused: APP_ENV must be [testing]. Use `composer test` or `php artisan test`.'
            );
        }

        $connection = (string) config('database.default');
        $driver = (string) config("database.connections.{$connection}.driver");
        $database = (string) config("database.connections.{$connection}.database");

        if (in_array($driver, ['mysql', 'mariadb', 'pgsql', 'sqlsrv'], true)) {
            throw new RuntimeException(
                "Tests refused: refusing persistent driver [{$driver}] database [{$database}]. "
                .'phpunit.xml must force DB_CONNECTION=sqlite and DB_DATABASE=:memory:.'
            );
        }

        if ($driver === 'sqlite' && $database !== ':memory:' && ! str_contains((string) $database, ':memory:')) {
            throw new RuntimeException(
                "Tests refused: SQLite file DB [{$database}] is not allowed. Use :memory: only."
            );
        }
    }
}
