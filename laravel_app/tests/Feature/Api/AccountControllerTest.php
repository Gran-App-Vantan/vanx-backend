<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::create([
            'name' => 'Test User',
            'user_path' => 'testuser',
            'password' => Hash::make('password'),
            'user_job' => 'player',
        ]);
        
        // Generate token for the test user
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_it_can_register_a_new_user()
    {
        $response = $this->postJson('/api/account/sign-up', [
            'auth' => [
                'name' => 'New User',
                'user_path' => 'newuser',
                'password' => 'password',
                'password_confirmation' => 'password',
                'checked_password' => 'password',
                'user_job' => 'player',
            ]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'messages',
                'authToken'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'user_path' => 'newuser',
            'user_job' => 'player',
        ]);
    }

    public function test_it_can_login_an_existing_user()
    {
        $response = $this->postJson('/api/account/login', [
            'auth' => [
                'user_path' => 'testuser',
                'password' => 'password',
            ]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'messages',
                'authToken'
            ]);
    }

    public function test_it_can_retrieve_authenticated_user_profile()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/account/profile/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => '取得に成功しました',
                'data' => [
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'user_path' => $this->user->user_path,
                    ],
                    'point' => null
                ]
            ]);
    }

    public function test_it_requires_authentication_for_protected_routes()
    {
        $response = $this->getJson('/api/account/profile/1');
        $response->assertStatus(401);
    }

    public function test_it_can_access_protected_route_with_valid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/account/profile/' . $this->user->id);

        $response->assertStatus(200);
    }
}
