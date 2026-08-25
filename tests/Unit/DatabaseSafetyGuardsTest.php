<?php

namespace Tests\Unit;

use RuntimeException;
use Tests\Concerns\CreatesRegressionSchema;
use Tests\TestCase;

class DatabaseSafetyGuardsTest extends TestCase
{
    public function test_phpunit_uses_sqlite_memory(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(
            'sqlite',
            config('database.connections.'.config('database.default').'.driver')
        );
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_regression_schema_guard_rejects_mysql(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.driver' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.database' => 'saf',
        ]);

        $probe = new class
        {
            use CreatesRegressionSchema;

            public function check(): void
            {
                $this->assertSafeTestingDatabase();
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Regression schema refused');
        $probe->check();
    }
}
