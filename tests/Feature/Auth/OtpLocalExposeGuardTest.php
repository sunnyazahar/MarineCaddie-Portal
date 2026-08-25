<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\OtpController;
use Illuminate\Http\Request;
use ReflectionMethod;
use Tests\TestCase;

class OtpLocalExposeGuardTest extends TestCase
{
    public function test_live_marinecaddie_host_never_exposes_local_otp(): void
    {
        $this->app['env'] = 'local';

        $request = Request::create('https://portal.marinecaddie.com/otp', 'GET');
        $this->app->instance('request', $request);

        $controller = app(OtpController::class);
        $method = new ReflectionMethod(OtpController::class, 'shouldExposeLocalOtp');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($controller));
    }

    public function test_localhost_can_expose_local_otp_outside_production(): void
    {
        $this->app['env'] = 'local';
        config(['app.url' => 'http://localhost/laravel']);

        $request = Request::create('http://localhost/laravel/otp', 'GET');
        $this->app->instance('request', $request);

        $controller = app(OtpController::class);
        $method = new ReflectionMethod(OtpController::class, 'shouldExposeLocalOtp');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($controller));
    }
}
