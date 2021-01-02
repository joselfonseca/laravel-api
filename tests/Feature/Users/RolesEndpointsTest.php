<?php

namespace Tests\Feature\Users;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolesEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    public function test_it_list_roles()
    {
        Role::factory()->count(10)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/roles');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'Administrator'],
            ],
            'meta' => [
                'pagination' => [

                ],
            ],
        ]);
    }

    public function test_it_list_paginated_roles()
    {
        Role::factory()->count(30)->create();
        Passport::actingAs(User::first());
        $response = $this->json('GET', 'api/roles?limit=10');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'Administrator'],
            ],
            'meta' => [
                'pagination' => [

                ],
            ],
        ]);
        $jsonResponse = json_decode($response->getContent(), true);
        $queryString = explode('?', $jsonResponse['meta']['pagination']['links']['next']);
        parse_str($queryString[1], $result);
        $this->assertArrayHasKey('limit', $result);
        $this->assertEquals('10', $result['limit']);
    }

    public function test_it_prevents_unauthorized_roles_listing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->json('GET', 'api/roles');
        $response->assertStatus(403);
    }

    public function test_it_can_create_a_role()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/roles', [
            'name' => 'Guest',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', [
            'name' => 'Guest',
        ]);
    }

    public function test_it_validates_input_for_roles()
    {
        Passport::actingAs(User::first());
        $response = $this->json('POST', 'api/roles', [

        ]);
        $response->assertStatus(422);
    }

    public function test_it_validates_permissions_to_create_a_role()
    {
        Passport::actingAs(User::factory()->create());
        $response = $this->json('POST', 'api/roles', [
            'name' => 'Guest',
        ]);
        $response->assertStatus(403);
    }

    public function test_it_shows_role_info()
    {
        Passport::actingAs(User::first());
        $role = Role::first();
        $response = $this->json('GET', 'api/roles/'.$role->uuid);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $role->uuid,
                'name' => $role->name,
                'permissions' => [

                ],
            ],
        ]);
    }

    public function test_it_updates_a_role()
    {
        Passport::actingAs(User::first());
        $role = Role::first();
        $response = $this->json('PUT', 'api/roles/'.$role->uuid, [
            'name' => 'New Role Name',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $role->uuid,
                'name' => 'New Role Name',
                'permissions' => [

                ],
            ],
        ]);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'New Role Name',
        ]);
    }

    public function test_it_validates_roles_input_for_update()
    {
        Passport::actingAs(User::first());
        $role = Role::first();
        $response = $this->json('PUT', 'api/roles/'.$role->uuid, [

        ]);
        $response->assertStatus(422);
    }

    public function test_it_validates_permission_to_update_role()
    {
        Passport::actingAs(User::factory()->create());
        $role = Role::first();
        $response = $this->json('PUT', 'api/roles/'.$role->uuid, [

        ]);
        $response->assertStatus(403);
    }

    public function test_it_can_delete_a_role()
    {
        Passport::actingAs(User::first());
        $role = Role::factory()->create();
        $response = $this->json('DELETE', 'api/roles/'.$role->uuid, [

        ]);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_it_validates_permission_to_delete_role()
    {
        Passport::actingAs(User::factory()->create());
        $role = Role::factory()->create();
        $response = $this->json('DELETE', 'api/roles/'.$role->uuid, [

        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_it_can_create_roles_with_permissions()
    {
        Passport::actingAs(User::first());
        $permissions = Permission::factory()->count(3)->create();
        $response = $this->json('POST', 'api/roles', [
            'name' => 'Guest',
            'permissions' => $permissions->pluck('uuid')->toArray(),
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', [
            'name' => 'Guest',
        ]);
        $role = Role::where('name', 'Guest')->first();
        $permissions->each(function ($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        });
    }

    public function test_it_can_remove_permissions_from_role_if_empty_array_is_sent()
    {
        Passport::actingAs(User::first());
        $permissions = Permission::factory()->count(3)->create();
        $role = Role::factory()->create()->syncPermissions($permissions);
        $response = $this->json('PUT', 'api/roles/'.$role->uuid, [
            'name' => 'Guest',
            'permissions' => [],
        ]);
        $response->assertStatus(200);
        $permissions->each(function ($permission) use ($role) {
            $this->assertDatabaseMissing('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        });
    }

    public function test_it_can_sync_permissions_for_created_role()
    {
        Passport::actingAs(User::first());
        $permissions = Permission::factory()->count(5)->create();
        $role = Role::factory()->create()->syncPermissions($permissions);
        $this->assertCount(5, $role->permissions);
        $newPermissions = $permissions->take(2);
        $response = $this->json('PUT', 'api/roles/'.$role->uuid, [
            'name' => 'Guest',
            'permissions' => $newPermissions->pluck('uuid')->toArray(),
        ]);
        $response->assertStatus(200);
        $newPermissions->each(function ($permission) use ($role) {
            $this->assertDatabaseHas('role_has_permissions', [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        });
        $this->assertCount(2, $role->fresh()->permissions);
    }
}
