<?php

namespace Tests\Concerns;

use App\Models\User;

trait InteractsWithAuthenticatedUsers
{
    protected function actingAsVerified(User $user): static
    {
        return $this->actingAs($user)->withSession(['otp_verified' => true]);
    }

    protected function createAdminUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'Admin',
            'is_active' => true,
        ], $attributes));
    }

    protected function loginPayload(string $email = 'test@example.com', string $password = 'password'): array
    {
        return [
            'email' => $email,
            'password' => $password,
            'browser_latitude' => 28.6139,
            'browser_longitude' => 77.2090,
            'browser_location_accuracy' => 25,
        ];
    }
}
