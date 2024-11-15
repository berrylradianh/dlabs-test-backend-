<?php

namespace Tests\Unit\Actions;

use App\Actions\Auth\AuthAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthActionTest extends TestCase
{
    use RefreshDatabase;

    protected AuthAction $authAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authAction = new AuthAction();
    }

    public function testRegister()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123'
        ];

        $user = $this->authAction->register($data);

        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertEquals('John Doe', $user->name);
    }

    public function testLoginSuccess()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $credentials = [
            'email' => $user->email,
            'password' => 'password123'
        ];

        $token = $this->authAction->login($credentials);

        $this->assertNotNull($token);
    }

    public function testLoginInvalidCredentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $credentials = [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authAction->login($credentials);
    }

    public function testLogoutSuccess()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        JWTAuth::setToken($token);

        $this->authAction->logout();

        $this->expectException(JWTException::class);
        JWTAuth::setToken($token)->authenticate();
    }

    public function testLogoutWithoutToken()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to logout');

        $this->authAction->logout();
    }
}
