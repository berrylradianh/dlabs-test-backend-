<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterSuccess()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe100@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('auth.register'), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);

        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    public function testRegisterValidationFails()
    {
        $data = [
            'name' => 'John Doe',
        ];

        $response = $this->postJson(route('auth.register'), $data);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'errors' => [
                'The email field is required.',
                'The password field is required.'
            ]
        ]);
    }


    public function testLoginSuccess()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $credentials = [
            'email' => $user->email,
            'password' => 'password123'
        ];

        $response = $this->postJson(route('auth.login'), $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);
    }

    public function testLoginInvalidCredentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $credentials = [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson(route('auth.login'), $credentials);

        $response->assertStatus(401);
        $response->assertJsonFragment(['errors' => 'Invalid credentials']);
    }

    public function testLogoutSuccess()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson(route('auth.logout'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'User logged out successfully']);
    }

    public function testLogoutWithoutToken()
    {
        $response = $this->postJson(route('auth.logout'));

        $response->assertStatus(500);
        $response->assertJsonFragment(['errors' => 'Failed to logout']);
    }
}
