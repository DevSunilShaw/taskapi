<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Get Sanctum token
        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    /** @test */
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'secret123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'token']);
        
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /** @test */
    public function test_user_can_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'token']);
    }

    /** @test */
    public function test_authenticated_user_can_create_task()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString()
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Test Task']);
        
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $this->user->id]);
    }

    /** @test */
    public function test_authenticated_user_can_fetch_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message', 'data']);
    }

    /** @test */
    public function test_authenticated_user_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->putJson("/api/tasks/{$task->id}", [
            'status' => 'completed'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'completed']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    /** @test */
    public function test_authenticated_user_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_access_tasks()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }
}