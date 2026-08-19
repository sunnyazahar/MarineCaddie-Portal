<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\RegressionTestCase;

class LoginFlowTest extends RegressionTestCase
{
    public function test_login_page_is_accessible_to_guests(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    public function test_valid_credentials_log_in_with_testing_otp_bypass(): void
    {
        $user = User::factory()->create([
            'email' => 'regression@marinecaddie.test',
            'is_active' => true,
        ]);

        $response = $this->post('/login', $this->loginPayload($user->email));

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(session('otp_verified'));
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'regression@marinecaddie.test',
            'is_active' => true,
        ]);

        $response = $this->from('/login')->post('/login', $this->loginPayload('regression@marinecaddie.test', 'wrong-password'));

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        User::factory()->create([
            'email' => 'inactive@marinecaddie.test',
            'is_active' => false,
        ]);

        $response = $this->from('/login')->post('/login', $this->loginPayload('inactive@marinecaddie.test'));

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
