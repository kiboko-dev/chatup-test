<?php

namespace Tests\Feature;

use AllowDynamicProperties;
use App\Models\User;
use Tests\TestCase;

#[AllowDynamicProperties] class AuthTest extends TestCase
{

    public function test_registration($random = 10): void
    {
        $response = $this
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('/api/v1/auth/register', [
                'email' => "john10@doe.com",
                'password' => 'password',
                'lastName' => 'Doe',
                'firstName' => 'John'
            ]);

        $response->assertStatus(302);
    }

    public function test_login($random = 10): void
    {
        $user = User::first();

        $response = $this
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $response->assertStatus(302);
    }

    public function test_get_users()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ])->withToken('1|QHVu1gHcqfAqxEsu5VHCxSc4FCv5T476hccYTUA5dbd8b07e')
            ->get('api/v1/users');

        $response->assertStatus(200);
    }
}
