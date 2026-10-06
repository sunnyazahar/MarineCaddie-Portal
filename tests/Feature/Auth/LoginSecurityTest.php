<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\RegressionTestCase;

class LoginSecurityTest extends RegressionTestCase
{
    private const VICTIM_EMAIL = 'victim@marinecaddie.test';

    private const SQL_INJECTION_PAYLOADS = [
        "' OR '1'='1",
        "' OR '1'='1' -- ",
        "' OR 1=1 #",
        "\" OR \"\"=\"",
        "' OR ''='",
        "admin' --",
        "victim@marinecaddie.test' --",
        "victim@marinecaddie.test'/*",
        "victim@marinecaddie.test' OR '1'='1",
        "') OR ('1'='1",
        "' UNION SELECT 1,2,3,4,5,6,7,8,9,10 -- ",
        "'; DROP TABLE users; -- ",
        "'; UPDATE users SET password='x' WHERE '1'='1'; -- ",
        "' AND SLEEP(5) -- ",
        "1' OR SLEEP(5)#",
        "%27%20OR%201%3D1--",
        "' OR 1=1 LIMIT 1 -- ",
        "\\' OR 1=1 -- ",
        "' OR is_active=1 -- ",
        "*",
        '%',
    ];

    private User $victim;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->softDeletes());
        }

        $this->victim = User::factory()->create([
            'email' => self::VICTIM_EMAIL,
            'password' => Hash::make('Correct-Horse-Battery-9'),
            'is_active' => true,
        ]);
    }

    public function test_sql_injection_in_email_never_logs_in(): void
    {
        foreach (self::SQL_INJECTION_PAYLOADS as $payload) {
            foreach (['anything', "' OR '1'='1", 'Correct-Horse-Battery-9'] as $password) {
                $this->assertLoginRejected(['email' => $payload, 'password' => $password], "email={$payload}");
            }
        }

        $this->assertUsersTableUntouched();
    }

    public function test_sql_injection_in_password_never_logs_in(): void
    {
        foreach (self::SQL_INJECTION_PAYLOADS as $payload) {
            $this->assertLoginRejected(['email' => self::VICTIM_EMAIL, 'password' => $payload], "password={$payload}");
        }

        $this->assertUsersTableUntouched();
    }

    public function test_non_string_and_array_credentials_are_rejected(): void
    {
        $payloads = [
            ['email' => [self::VICTIM_EMAIL], 'password' => 'anything'],
            ['email' => ['$ne' => ''], 'password' => ['$ne' => '']],
            ['email' => self::VICTIM_EMAIL, 'password' => ['Correct-Horse-Battery-9']],
            ['email' => self::VICTIM_EMAIL, 'password' => true],
            ['email' => self::VICTIM_EMAIL, 'password' => 0],
            ['email' => self::VICTIM_EMAIL, 'password' => null],
            ['email' => self::VICTIM_EMAIL],
            ['email' => self::VICTIM_EMAIL, 'password' => ''],
            ['email' => '', 'password' => ''],
            ['email' => self::VICTIM_EMAIL, 'password' => 'anything', 'is_active' => 0, 'id' => $this->victim->id],
        ];

        foreach ($payloads as $index => $payload) {
            Cache::flush();
            $this->postJson('/login', $payload)->assertStatus(422);
            $this->assertGuest();
            Cache::flush();
            $this->post('/login', $payload);
            $this->assertGuest();
        }
    }

    public function test_oversized_credentials_are_rejected_before_any_lookup(): void
    {
        $this->postJson('/login', ['email' => str_repeat('a', 256) . '@x.test', 'password' => 'x'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->postJson('/login', ['email' => self::VICTIM_EMAIL, 'password' => str_repeat('p', 1025)])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertGuest();
    }

    public function test_wrong_password_unknown_email_and_inactive_user_get_the_same_error(): void
    {
        User::factory()->create(['email' => 'inactive@marinecaddie.test', 'is_active' => false]);

        $messages = [];
        foreach ([
            [self::VICTIM_EMAIL, 'wrong-password'],
            ['nobody@marinecaddie.test', 'Correct-Horse-Battery-9'],
            ['inactive@marinecaddie.test', 'password'],
        ] as [$email, $password]) {
            Cache::flush();
            $messages[] = $this->postJson('/login', ['email' => $email, 'password' => $password])
                ->assertStatus(422)
                ->json('errors.email.0');
        }

        $this->assertCount(1, array_unique($messages));
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', ['email' => self::VICTIM_EMAIL, 'password' => 'wrong-' . $i]);
        }

        $this->postJson('/login', ['email' => self::VICTIM_EMAIL, 'password' => 'Correct-Horse-Battery-9'])
            ->assertStatus(429);
        $this->assertGuest();
    }

    public function test_correct_password_still_requires_otp_before_any_protected_page(): void
    {
        config(['app.local_otp_bypass' => false]);

        $this->post('/login', ['email' => self::VICTIM_EMAIL, 'password' => 'Correct-Horse-Battery-9'])
            ->assertRedirect(route('otp.show'));
        $this->assertAuthenticatedAs($this->victim);
        $this->assertNotTrue(session('otp_verified'));

        foreach ($this->protectedGetUris() as $uri) {
            $this->get($uri)->assertRedirect(route('otp.show'));
        }
    }

    public function test_guests_are_redirected_to_login_from_every_protected_page(): void
    {
        foreach ($this->protectedGetUris() as $uri) {
            $this->get($uri)->assertRedirect(route('login'));
        }

        $this->assertGuest();
    }

    public function test_otp_rejects_injection_and_locks_after_five_wrong_codes(): void
    {
        config(['app.local_otp_bypass' => false]);

        $this->post('/login', ['email' => self::VICTIM_EMAIL, 'password' => 'Correct-Horse-Battery-9']);
        $this->get(route('otp.show'))->assertOk();

        foreach (["' OR '1'='1", '123456 OR 1=1', '1e5000', ' 12345', '12345678'] as $payload) {
            $this->post(route('otp.verify'), ['otp' => $payload])->assertSessionHasErrors('otp');
        }

        $realOtp = (string) session('login_otp_local');
        $this->assertMatchesRegularExpression('/^\d{6}$/', $realOtp);
        $wrong = $realOtp === '000000' ? '111111' : '000000';

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('otp.verify'), ['otp' => $wrong])->assertSessionHasErrors('otp');
        }

        $this->post(route('otp.verify'), ['otp' => $realOtp])->assertSessionHasErrors('otp');
        $this->assertNotTrue(session('otp_verified'));
        $this->get('/dashboard')->assertRedirect(route('otp.show'));
        $this->assertNotNull($this->victim->fresh()->otp_blocked_until);
    }

    public function test_deactivated_user_cannot_log_in_through_password_reset(): void
    {
        $this->createPasswordResetTokensTable();
        $this->victim->forceFill(['is_active' => false])->save();
        $token = Password::broker()->createToken($this->victim);
        $hashBefore = $this->victim->fresh()->password;

        $this->post('/password/reset', [
            'token' => $token,
            'email' => self::VICTIM_EMAIL,
            'password' => 'New-Password-123!',
            'password_confirmation' => 'New-Password-123!',
        ]);

        $this->assertGuest();
        $this->assertSame($hashBefore, $this->victim->fresh()->password);
    }

    public function test_active_user_password_reset_still_goes_through_otp(): void
    {
        config(['app.local_otp_bypass' => false]);
        $this->createPasswordResetTokensTable();
        $token = Password::broker()->createToken($this->victim);

        $this->post('/password/reset', [
            'token' => $token,
            'email' => self::VICTIM_EMAIL,
            'password' => 'New-Password-123!',
            'password_confirmation' => 'New-Password-123!',
        ])->assertRedirect('/otp');

        $this->assertAuthenticatedAs($this->victim);
        $this->get('/dashboard')->assertRedirect(route('otp.show'));
    }

    public function test_existing_session_ends_when_user_is_deactivated(): void
    {
        $this->post('/login', ['email' => self::VICTIM_EMAIL, 'password' => 'Correct-Horse-Battery-9'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->victim);

        $this->victim->forceFill(['is_active' => false])->save();
        $this->app['auth']->forgetGuards();

        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_remember_me_cookie_stops_working_when_user_is_deactivated(): void
    {
        $response = $this->post('/login', [
            'email' => self::VICTIM_EMAIL,
            'password' => 'Correct-Horse-Battery-9',
            'remember' => 'on',
        ]);
        $recallerName = $this->app['auth']->guard()->getRecallerName();
        $recaller = $response->getCookie($recallerName, true, false);
        $this->assertNotNull($recaller, 'Login with "remember" should set the recaller cookie.');

        $this->restartBrowserSession();
        $this->withCookie($recallerName, $recaller->getValue())->get(route('otp.show'));
        $this->assertAuthenticatedAs($this->victim);

        $this->victim->forceFill(['is_active' => false])->save();
        $this->restartBrowserSession();
        $this->withCookie($recallerName, $recaller->getValue())->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    private function assertLoginRejected(array $payload, string $label): void
    {
        Cache::flush();
        $response = $this->post('/login', $payload);

        $this->assertGuest();
        $this->assertNotSame(500, $response->getStatusCode(), "Server error for {$label}");
        $this->assertFalse(
            $response->isRedirect(url('/dashboard')) || $response->isRedirect(route('otp.show')),
            "Login bypassed for {$label}"
        );
    }

    private function assertUsersTableUntouched(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertSame(1, User::query()->count());
        $this->assertTrue(Hash::check('Correct-Horse-Battery-9', (string) $this->victim->fresh()->password));
    }

    /**
     * Every parameterless GET page that sits behind the OTP gate.
     *
     * @return list<string>
     */
    private function protectedGetUris(): array
    {
        $uris = [];

        foreach (Route::getRoutes() as $route) {
            $middleware = $route->gatherMiddleware();
            if (! in_array('GET', $route->methods(), true)
                || ! in_array('otp.verified', $middleware, true)
                || str_contains($route->uri(), '{')
            ) {
                continue;
            }

            $uris[] = '/' . ltrim($route->uri(), '/');
        }

        $this->assertNotEmpty($uris);

        return $uris;
    }

    private function restartBrowserSession(): void
    {
        $this->flushSession();
        $this->app['auth']->forgetGuards();
    }

    private function createPasswordResetTokensTable(): void
    {
        if (Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
}
