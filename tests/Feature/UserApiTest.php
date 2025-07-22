<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_user()
    {
        $userData = [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+84987654321',
            'role' => 'user'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'User registered successfully'
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user_id',
                        'username',
                        'email',
                        'first_name',
                        'last_name',
                        'phone',
                        'is_active',
                        'is_verified',
                        'role',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_on_registration()
    {
        $response = $this->postJson('/api/v1/register', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['username', 'email', 'password', 'first_name', 'last_name']);
    }

    /** @test */
    public function it_prevents_duplicate_email_registration()
    {
        $userData = [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];

        // Register first user
        $this->postJson('/api/v1/register', $userData);

        // Try to register with same email
        $userData['username'] = 'janedoe';
        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_login_user()
    {
        // First create a user
        $userData = [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+84987654321'
        ];
        $this->postJson('/api/v1/register', $userData);

        // Now try to login
        $loginData = [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!'
        ];

        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful'
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'token'
                    ]
                ]);
    }

    /** @test */
    public function it_rejects_invalid_login_credentials()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
    }
}