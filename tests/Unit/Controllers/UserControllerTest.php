<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexWithPagination()
    {
        User::factory()->count(10)->create();

        $response = $this->getJson(route('user.index', ['page' => 1, 'size' => 5]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name']
            ],
            'page',
            'size',
            'total'
        ]);

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(10, $response->json('total'));
    }

    public function testShowSuccess()
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('user.show', $user->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function testShowNotFound()
    {
        $response = $this->getJson(route('user.show', 999));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'errors' => 'User not found',
            'message' => 'Not Found',
            'status' => 404
        ]);
    }

    public function testStoreWithValidData()
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'password' => 'password123',
            'age' => 25
        ];

        $response = $this->postJson(route('user.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'email']
        ]);

        $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com']);
    }

    public function testStoreWithInvalidData()
    {
        $data = [
            'name' => 'Invalid User',
            'password' => 'password123'
        ];

        $response = $this->postJson(route('user.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Validation errors occurred.',
            'errors' => [
               'The email field is required.',
               'The age field is required.'
            ]
        ]);
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $data = ['name' => 'Updated Name', 'password' => 'newpassword'];

        $response = $this->putJson(route('user.update', $user->id), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);

        $user->refresh();
        $this->assertTrue(password_verify('newpassword', $user->password));
    }


    public function testUpdateWithInvalidData()
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $data = ['email' => 'invalid-email-format'];

        $response = $this->putJson(route('user.update', $user->id), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testUpdateNotFound()
    {
        $data = ['name' => 'Non Existent User'];

        $response = $this->putJson(route('user.update', 999), $data);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'errors' => 'User not found',
            'message' => 'Not Found',
            'status' => 404
        ]);
    }

    public function testDestroySuccess()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson(route('user.destroy', $user->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson(route('user.destroy', 999));

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'errors' => 'User not found',
            'message' => 'Not Found',
            'status' => 404
        ]);
    }

    public function testDestroyTwice()
    {
        $user = User::factory()->create();

        $response1 = $this->deleteJson(route('user.destroy', $user->id));
        $response1->assertStatus(204);

        $response2 = $this->deleteJson(route('user.destroy', $user->id));
        $response2->assertStatus(404);
        $response2->assertJsonFragment([
            'errors' => 'User not found',
            'message' => 'Not Found',
            'status' => 404
        ]);
    }
}
