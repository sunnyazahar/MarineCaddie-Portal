<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_shipments_to_login(): void
    {
        $response = $this->get('/shipments');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_agents_to_login(): void
    {
        $response = $this->get('/Agents');

        $response->assertRedirect('/login');
    }
}
