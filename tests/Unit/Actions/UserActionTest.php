<?php

namespace Tests\Unit\Actions;

use App\Actions\User\UserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserActionTest extends TestCase
{
    protected $userAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userAction = new UserAction();
    }

    public function testGetAllUsers()
    {
        User::factory()->count(5)->create();
        $users = $this->userAction->getAllUsers(1, 5);

        $this->assertCount(5, $users);
    }

    public function testGetUserSuccess()
    {
        $user = User::factory()->create();
        $foundUser = $this->userAction->getUser($user->id);

        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testGetUserNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->userAction->getUser(999); // ID yang tidak ada
    }

    public function testCreateUser()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123'
        ];

        $user = $this->userAction->createUser($data);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('johndoe@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function testUpdateUser()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'password' => Hash::make('oldpassword')
        ]);

        $data = [
            'name' => 'New Name',
            'password' => 'newpassword'
        ];

        $updatedUser = $this->userAction->updateUser($user->id, $data);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertTrue(Hash::check('newpassword', $updatedUser->password));
    }

    public function testDeleteUser()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $this->userAction->deleteUser($userId);

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
