<?php

namespace Tests\Feature\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UsersEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    public function test_it_responds_unauthenticated_for_list_users()
    {
        $response = $this->json('GET', 'api/users');
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
            'status_code' => 401,
        ]);
    }

    public function test_it_list_users()
    {
        User::factory()->count(30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/users');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $jsonResponse);
        $this->assertArrayHasKey('meta', $jsonResponse);
        $this->assertArrayHasKey('pagination', $jsonResponse['meta']);
        $this->assertEquals(31, $jsonResponse['meta']['pagination']['total']);
        $this->assertEquals(20, $jsonResponse['meta']['pagination']['count']);
        $this->assertCount(20, $jsonResponse['data']);
        $this->assertArrayHasKey('id', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('name', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('email', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('data', $jsonResponse['data'][0]['roles']);
    }

    public function test_it_list_users_with_custom_limit()
    {
        User::factory()->count(30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/users?limit=10');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $jsonResponse);
        $this->assertArrayHasKey('meta', $jsonResponse);
        $this->assertArrayHasKey('pagination', $jsonResponse['meta']);
        $this->assertEquals(31, $jsonResponse['meta']['pagination']['total']);
        $this->assertEquals(10, $jsonResponse['meta']['pagination']['count']);
        $this->assertCount(10, $jsonResponse['data']);
        $this->assertArrayHasKey('id', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('name', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('email', $jsonResponse['data'][0]);
        $this->assertArrayHasKey('data', $jsonResponse['data'][0]['roles']);
        $queryString = explode('?', $jsonResponse['meta']['pagination']['links']['next']);
        parse_str($queryString[1], $result);
        $this->assertArrayHasKey('limit', $result);
        $this->assertEquals('10', $result['limit']);
    }

    public function test_it_validates_permission_for_listing_users()
    {
        User::factory()->count(30)->create();
        $user = User::factory()->create([
            'email' => 'me@example.com',
        ]);
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/users');
        $response->assertStatus(403);
    }

    public function test_it_gets_single_user()
    {
        $user = User::first();
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/users/'.$user->uuid);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $jsonResponse = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $jsonResponse);
        $this->assertArrayHasKey('id', $jsonResponse['data']);
        $this->assertArrayHasKey('name', $jsonResponse['data']);
        $this->assertArrayHasKey('email', $jsonResponse['data']);
        $this->assertArrayHasKey('data', $jsonResponse['data']['roles']);
    }

    public function test_it_creates_user()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/users/', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_it_validates_input_for_creation()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/users', [
            'name' => 'Some User',
            'email' => 'some@email.com',
            'password' => '123456789qq',
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', [
            'name' => 'Some User',
            'email' => 'some@email.com',
        ]);
    }

    public function test_it_can_partially_update_a_user()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'name' => 'Jose Fonseca',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'id' => $user->id,
        ]);
    }

    public function test_it_can_update_a_user()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('PUT', 'api/users/'.$user->uuid, [
            'name' => 'Jose Fonseca',
            'email' => $user->email,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'id' => $user->id,
        ]);
    }

    public function test_it_can_update_a_user_with_different_email()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('PUT', 'api/users/'.$user->uuid, [
            'name' => 'Jose Fonseca',
            'email' => 'jose.fonseca@somedomain.com',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'Jose Fonseca',
            'id' => $user->id,
            'email' => 'jose.fonseca@somedomain.com',
        ]);
    }

    public function test_it_validates_input_to_update_a_user()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'name' => '',
        ]);
        $response->assertStatus(422);
    }

    public function test_it_validates_taken_email()
    {
        Passport::actingAs(User::first());
        $user1 = User::factory()->create();
        User::factory()->create(['email' => 'some@email.com']);
        $response = $this->json('PATCH', 'api/users/'.$user1->uuid, [
            'email' => 'some@email.com',
        ]);
        $response->assertStatus(422);
    }

    public function test_it_validates_taken_email_full_entity()
    {
        Passport::actingAs(User::first());
        $user1 = User::factory()->create();
        User::factory()->create(['email' => 'some@email.com']);
        $response = $this->json('PUT', 'api/users/'.$user1->uuid, [
            'name' => 'New Name',
            'email' => 'some@email.com',
        ]);
        $response->assertStatus(422);
    }

    public function test_it_validates_input_to_update_a_user_full_entity()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('PUT', 'api/users/'.$user->uuid, [
            'name' => 'Some Name',
            'email' => '',
        ]);
        $response->assertStatus(422);
    }

    public function test_it_can_delete_a_user()
    {
        Passport::actingAs(User::first());
        $user = User::factory()->create();
        $response = $this->json('DELETE', 'api/users/'.$user->uuid, [
        ]);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
        // just to make sure there is a user record for the soft delete
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function test_it_protects_the_user_from_being_deleted_by_user_with_no_permission()
    {
        $user = User::factory()->create([
            'email' => 'me@example.com',
        ]);
        $user2 = User::factory()->create([
            'email' => 'me2@example.com',
        ]);
        Passport::actingAs($user);
        $response = $this->json('DELETE', 'api/users/'.$user2->uuid, [
        ]);
        $response->assertStatus(403);
    }

    public function test_it_can_create_user_with_associated_role()
    {
        Passport::actingAs(User::first());
        $roles = Role::factory()->count(2)->create();
        $response = $this->json('POST', 'api/users/', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'roles' => $roles->pluck('uuid')->toArray(),
        ]);
        $response->assertStatus(201);
        $user = User::where('email', 'john@example.com')->first();
        $roles->each(function ($role) use ($user) {
            $this->assertDatabaseHas('model_has_roles', [
                'model_id' => $user->id,
                'role_id' => $role->id,
            ]);
        });
    }

    public function test_it_updates_users_roles()
    {
        Passport::actingAs(User::first());
        $roles = Role::factory()->count(2)->create();
        $user = User::factory()->create();
        $this->assertCount(0, $user->roles);
        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'roles' => $roles->pluck('uuid')->toArray(),
        ]);
        $response->assertStatus(200);
        $roles->each(function ($role) use ($user) {
            $this->assertDatabaseHas('model_has_roles', [
                'model_id' => $user->id,
                'role_id' => $role->id,
            ]);
        });
        $this->assertCount(2, $user->fresh()->roles);
    }

    public function test_it_deletes_users_roles_if_empty_array_sent()
    {
        Passport::actingAs(User::first());
        $roles = Role::factory()->count(2)->create();
        $user = User::factory()->create()->syncRoles($roles);
        $this->assertCount(2, $user->roles);
        $response = $this->json('PATCH', 'api/users/'.$user->uuid, [
            'roles' => [],
        ]);
        $response->assertStatus(200);
        $roles->each(function ($role) use ($user) {
            $this->assertDatabaseMissing('model_has_roles', [
                'model_id' => $user->id,
                'role_id' => $role->id,
            ]);
        });
        $this->assertCount(0, $user->fresh()->roles);
    }

    public function test_it_responds_404_if_user_not_found()
    {
        Passport::actingAs(User::first());
        $roles = Role::factory()->count(2)->create();
        $user = User::factory()->create()->syncRoles($roles);
        $this->assertCount(2, $user->roles);
        $response = $this->get('api/users/some-id-that-is-not-here');
        $response->assertStatus(404);
    }
}
