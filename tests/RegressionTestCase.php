<?php

namespace Tests;

use Tests\Concerns\CreatesRegressionSchema;
use Tests\Concerns\InteractsWithAuthenticatedUsers;

abstract class RegressionTestCase extends TestCase
{
    use CreatesRegressionSchema;
    use InteractsWithAuthenticatedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createRegressionSchema();
    }

    protected function tearDown(): void
    {
        $this->dropRegressionSchema();

        parent::tearDown();
    }
}
