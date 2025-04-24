<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Reflection;
use App\Models\User;
use Illuminate\Support\Str;

class ReflectionControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test__reflections_index_returns_ok(): void
    {
        $user = User::factory()->create();

        $reflections = Reflection::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/reflections');

        $response->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'quote',
                        'response',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ],
                ]);
    }

    public function test_store_reflections(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => 'Test quote',
            'response' => 'Test response',
        ];

        $response = $this->postJson('/api/reflections', $data);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'quote',
                    'response',
                    'user_id',
                    'created_at',
                    'updated_at',
                ]);

        $this->assertDatabaseHas('reflections', [
            'quote' => 'Test quote',
            'response' => 'Test response',
            'user_id' => $user->id,
        ]);
    }

    public function test_store_reflections_ng(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => Str::random(256),
            'response' => 'Test response',
        ];

        $response = $this->postJson('/api/reflections', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['quote']);

        $this->assertDatabaseMissing('reflections', [
            'quote' => $data['quote'],
            'response' => 'Test response',
            'user_id' => $user->id,
        ]);
    }
    public function test_store_reflections_response_ng(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => 'Test quote',
            'response' => Str::random(10001),
        ];

        $response = $this->postJson('/api/reflections', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['response']);

        $this->assertDatabaseMissing('reflections', [
            'quote' => $data['quote'],
            'response' => $data['response'],
            'user_id' => $user->id,
        ]);
    }

    public function test_update_reflections(): void
    {
        $user = User::factory()->create();

        $reflection = Reflection::factory()->for($user)->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => 'Updated quote',
            'response' => 'Updated response',
        ];

        $response = $this->putJson("/api/reflections/{$reflection->id}", $data);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'quote',
                    'response',
                    'user_id',
                    'created_at',
                    'updated_at',
                ]);

        $this->assertDatabaseHas('reflections', [
            'id' => $reflection->id,
            'quote' => 'Updated quote',
            'response' => 'Updated response',
            'user_id' => $user->id,
        ]);
    }

    public function test_update_reflections_other_user(): void
    {
        $user = User::factory()->create();

        $reflection = Reflection::factory()->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => 'Updated quote',
            'response' => 'Updated response',
        ];

        $response = $this->putJson("/api/reflections/{$reflection->id}", $data);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Unauthorized',
                ]);

        $this->assertDatabaseMissing('reflections', [
            'id' => $reflection->id,
            'quote' => 'Updated quote',
            'response' => 'Updated response',
            'user_id' => $user->id,
        ]);
    }

    public function test_update_reflections_ng(): void
    {
        $user = User::factory()->create();

        $reflection = Reflection::factory()->for($user)->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => Str::random(256),
            'response' => 'Updated response',
        ];

        $response = $this->putJson("/api/reflections/{$reflection->id}", $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['quote']);

        $this->assertDatabaseMissing('reflections', [
            'id' => $reflection->id,
            'quote' => 'Updated quote',
            'response' => 'Updated response',
            'user_id' => $user->id,
        ]);
    }

    public function test_update_reflections_response_ng(): void
    {
        $user = User::factory()->create();

        $reflection = Reflection::factory()->for($user)->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'quote' => 'Updated quote',
            'response' => Str::random(10001),
        ];

        $response = $this->putJson("/api/reflections/{$reflection->id}", $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['response']);

        $this->assertDatabaseMissing('reflections', [
            'id' => $reflection->id,
            'quote' => 'Updated quote',
            'response' => $data['response'],
            'user_id' => $user->id,
        ]);
    }

    public function test_delete_reflections(): void
    {
        $reflection = Reflection::factory()->create();

        $this->actingAs($reflection->user, 'sanctum');

        $response = $this->deleteJson('/api/reflections/' . $reflection->id);

        $response->assertStatus(204)
                ->assertNoContent();

        $this->assertDatabaseMissing('reflections', [
            'id' => $reflection->id,
        ]);
    }

    public function test_delete_reflections_other_user(): void
    {
        $user = User::factory()->create();

        $reflection = Reflection::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson('/api/reflections/' . $reflection->id);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Unauthorized',
                ]);

        $this->assertDatabaseHas('reflections', [
            'id' => $reflection->id,
        ]);
    }
}
